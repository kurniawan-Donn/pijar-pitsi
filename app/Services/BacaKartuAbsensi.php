<?php

namespace App\Services;

use App\Models\HariLibur;
use App\Models\LogAktivitas;
use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use App\Models\ScanKartuAbsensi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Isi jam logbook dari foto kartu absensi:
 * foto → Gemini → validasi → pratinjau → (konfirmasi peserta) → logbook.
 * Hasil scan tidak pernah tersimpan ke logbook tanpa konfirmasi peserta.
 */
class BacaKartuAbsensi
{
    /**
     * Di bawah nilai ini baris ditandai meragukan (kuning).
     */
    public const AMBANG_YAKIN = 0.8;

    public const SISI_FOTO = 1600;

    public function __construct(
        private GeminiKartuAbsensi $gemini,
        private KalenderLogbook $kalender,
    ) {}

    /**
     * Simpan foto, baca dengan AI, validasi, lalu catat sebagai scan berstatus "dipratinjau".
     * Scan yang gagal tetap tercatat (berstatus "gagal") agar ikut dihitung dalam batas harian.
     */
    public function scan(PesertaMagang $peserta, string $sisi, int $tahun, int $bulan, UploadedFile $foto): ScanKartuAbsensi
    {
        $jpeg = $this->siapkanFoto($foto);
        $path = ScanKartuAbsensi::FOLDER.'/'.$peserta->id.'/'.Str::random(24).'.jpg';
        Storage::disk('local')->put($path, $jpeg);

        $scan = ScanKartuAbsensi::create([
            'peserta_magang_id' => $peserta->id,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'sisi' => $sisi,
            'foto_path' => $path,
            'status' => ScanKartuAbsensi::GAGAL,
        ]);

        try {
            $bacaan = $this->gemini->baca($jpeg, $this->prompt($scan));
        } catch (GagalBacaKartuException $e) {
            LogAktivitas::catat('scan_kartu_absensi', $peserta, "Scan kartu {$scan->labelSisi()} {$scan->labelBulan()} gagal dibaca", [
                'sisi' => $sisi, 'bulan' => sprintf('%04d-%02d', $tahun, $bulan), 'berhasil' => false,
            ]);

            throw $e;
        }

        $hasil = $this->validasi($bacaan['baris'], $scan);

        $scan->update([
            'status' => ScanKartuAbsensi::DIPRATINJAU,
            'hasil' => $hasil,
            'jumlah_terbaca' => count($hasil),
            'model' => $bacaan['model'],
            'token_masuk' => $bacaan['token_masuk'],
            'token_keluar' => $bacaan['token_keluar'],
        ]);

        LogAktivitas::catat('scan_kartu_absensi', $peserta, "Scan kartu {$scan->labelSisi()} {$scan->labelBulan()}: ".count($hasil).' tanggal terbaca', [
            'sisi' => $sisi, 'bulan' => sprintf('%04d-%02d', $tahun, $bulan), 'terbaca' => count($hasil), 'berhasil' => true,
        ]);

        return $scan;
    }

    /**
     * Instruksi untuk AI, termasuk kalender bulan itu sebagai petunjuk memasangkan jam ke tanggal.
     */
    public function prompt(ScanKartuAbsensi $scan): string
    {
        [$awal, $akhir] = $scan->rentang();
        $libur = $this->liburNasional($scan);

        $kalender = collect(range($awal, $akhir))->map(function (int $hari) use ($scan, $libur) {
            $tanggal = $scan->tanggal($hari);
            $catatan = $libur[$tanggal->toDateString()] ?? ($tanggal->isWeekend() ? 'akhir pekan' : null);

            return "- {$hari}: ".$tanggal->locale('id')->translatedFormat('l').($catatan ? " (libur: {$catatan}, biasanya kosong)" : '');
        })->implode("\n");

        $sisi = $scan->sisi === ScanKartuAbsensi::SISI_AWAL ? 'DEPAN' : 'BELAKANG';

        return <<<PROMPT
        Anda membaca foto KARTU ABSENSI mesin ceklok (cetakan dot-matrix tipis). Foto ini sisi {$sisi} kartu untuk tanggal {$awal}–{$akhir} {$scan->labelBulan()}.

        Tabel kartu: kolom Tgl, Pagi (Masuk, Keluar), Siang (Masuk, Keluar), Lembur (Masuk, Keluar), Jam.
        Baca HANYA dua kolom: "Pagi Masuk" (jam_masuk) dan "Siang Keluar" (jam_keluar).
        Abaikan Pagi Keluar, Siang Masuk, Lembur, kolom Jam, dan semua tulisan tangan.

        Setiap cetakan diawali kode tanggal kecil (1–2 digit, kadang miring atau terpotong), lalu jam.
        Contoh: "19 7:54" berarti tanggal 19 jam 07:54. Kode tanggal ini petunjuk paling kuat.

        Cetakan sering MELESET dari barisnya: bisa naik/turun ke baris tanggal sebelah atau berada di antara dua baris.
        Pasangkan setiap jam ke tanggal yang paling masuk akal dengan mempertimbangkan, berurutan:
        1. kode tanggal di depan jam;
        2. urutan cetakan dari atas ke bawah (tanggal selalu naik);
        3. satu tanggal paling banyak satu jam masuk dan satu jam keluar;
        4. jam masuk biasanya pagi (sekitar 06:00–10:00), jam keluar sore (sekitar 15:00–20:00);
        5. kalender berikut; hari libur biasanya kosong:
        {$kalender}

        Kembalikan hanya tanggal yang punya minimal satu cetakan jam. Tulis jam dalam format HH:MM 24 jam.
        Bila jam masuk atau keluar memang tidak ada, isi null. Jangan menebak jam yang tidak terlihat.
        Isi "keyakinan" 0–1 untuk setiap baris; turunkan dan jelaskan di "catatan" bila cetakan meleset, buram, atau terpotong.
        PROMPT;
    }

    /**
     * Validasi hasil AI di server. Kembalikan baris per tanggal (terurut) yang lolos, dengan tanda ragu.
     *
     * @param  array<int, array<string, mixed>>  $baris
     * @return array<int, array{tanggal: int, jam_masuk: ?string, jam_keluar: ?string, keyakinan: float, ragu: bool, catatan: array<int, string>}>
     */
    public function validasi(array $baris, ScanKartuAbsensi $scan): array
    {
        [$awal, $akhir] = $scan->rentang();
        $hasil = [];

        foreach ($baris as $item) {
            $tanggal = filter_var($item['tanggal'] ?? null, FILTER_VALIDATE_INT);

            if ($tanggal === false || $tanggal < $awal || $tanggal > $akhir) {
                continue; // di luar sisi kartu atau tidak ada di bulan ini
            }

            $catatanAi = filled($item['catatan'] ?? null) ? [mb_substr(trim((string) $item['catatan']), 0, 160)] : [];
            $masalah = [];
            $masuk = $this->normalisasiJam($item['jam_masuk'] ?? null, $masalah, 'masuk');
            $keluar = $this->normalisasiJam($item['jam_keluar'] ?? null, $masalah, 'keluar');

            if ($masuk === null && $keluar === null) {
                continue;
            }

            if ($masuk && $keluar && $masuk >= $keluar) {
                $masalah[] = 'Jam masuk tidak lebih awal dari jam keluar.';
            }

            if ($masuk && $masuk > '12:00') {
                $masalah[] = 'Jam masuk siang hari, periksa kembali.';
            }

            if ($keluar && $keluar < '11:00') {
                $masalah[] = 'Jam keluar pagi hari, periksa kembali.';
            }

            $keyakinan = max(0.0, min(1.0, (float) ($item['keyakinan'] ?? 0)));
            $baru = [
                'tanggal' => $tanggal,
                'jam_masuk' => $masuk,
                'jam_keluar' => $keluar,
                'keyakinan' => round($keyakinan, 2),
                'catatan' => [...$masalah, ...$catatanAi],
                'masalah' => $masalah !== [],
            ];

            if (isset($hasil[$tanggal])) {
                // Dua bacaan untuk tanggal yang sama: pakai yang paling yakin, tandai ragu.
                $pemenang = $hasil[$tanggal]['keyakinan'] >= $baru['keyakinan'] ? $hasil[$tanggal] : $baru;
                $pemenang['catatan'][] = 'Ada lebih dari satu bacaan untuk tanggal ini.';
                $pemenang['masalah'] = true;
                $hasil[$tanggal] = $pemenang;

                continue;
            }

            $hasil[$tanggal] = $baru;
        }

        ksort($hasil);

        return array_values(array_map(function (array $baris) {
            $baris['ragu'] = $baris['masalah'] || $baris['keyakinan'] < self::AMBANG_YAKIN;
            unset($baris['masalah']);

            return $baris;
        }, $hasil));
    }

    /**
     * Baris tabel pratinjau untuk semua tanggal di sisi kartu, dengan keadaan logbook saat ini.
     *
     * @return array<int, array<string, mixed>>
     */
    public function pratinjau(ScanKartuAbsensi $scan): array
    {
        [$awal, $akhir] = $scan->rentang();
        $hasil = collect($scan->hasil ?? [])->keyBy('tanggal');
        $peserta = $scan->pesertaMagang;

        $hari = $this->kalender->hari($peserta, ['mulai' => $scan->tanggal($awal), 'selesai' => $scan->tanggal($akhir)]);

        return array_map(function (array $h) use ($hasil) {
            /** @var LogbookHarian|null $logbook */
            $logbook = $h['logbook'];
            $bacaan = $hasil->get($h['tanggal']->day);

            $status = match (true) {
                $h['akses'] === KalenderLogbook::DI_LUAR => 'di_luar',
                $h['akses'] === KalenderLogbook::BELUM_TIBA => 'belum_tiba',
                $logbook?->izin_peserta_id !== null && $logbook !== null => 'izin',
                $logbook?->status_verifikasi === LogbookHarian::DITERIMA => 'diterima',
                default => 'bisa',
            };

            $catatan = $bacaan['catatan'] ?? [];

            if ($bacaan && $status === 'izin') {
                $catatan[] = 'Tanggal ini sudah tercatat '.mb_strtolower($logbook->labelKehadiran()).'; tidak diubah. Batalkan izinnya dulu bila ternyata hadir.';
            }

            if ($bacaan && $h['libur']) {
                $catatan[] = 'Hari libur ('.$h['libur'].'), periksa apakah tanggalnya benar.';
            }

            $jamLama = $logbook && ($logbook->jam('pagi_masuk') !== '' || $logbook->jam('siang_keluar') !== '')
                ? ['jam_masuk' => $logbook->jam('pagi_masuk'), 'jam_keluar' => $logbook->jam('siang_keluar')]
                : null;

            return [
                'tanggal' => $h['tanggal'],
                'bacaan' => $bacaan,
                'logbook' => $logbook,
                'status' => $status,
                'bisa' => $status === 'bisa',
                'lewat_batas' => $status === 'bisa' && $h['akses'] === KalenderLogbook::LEWAT_BATAS,
                'libur' => $h['libur'],
                'jam_lama' => $jamLama,
                'ragu' => $bacaan && ($bacaan['ragu'] || $h['libur'] || $status === 'izin'),
                'catatan' => $catatan,
            ];
        }, $hari);
    }

    /**
     * Simpan baris yang dipilih peserta ke logbook. Kembalikan jumlah tanggal yang tersimpan.
     *
     * @param  array<int|string, array<string, mixed>>  $input  baris[tanggal] = [pakai, jam_masuk, jam_keluar, pilihan]
     */
    public function simpan(ScanKartuAbsensi $scan, array $input): int
    {
        $baris = collect($this->pratinjau($scan))->keyBy(fn (array $b) => $b['tanggal']->day);
        $kesalahan = [];
        $simpan = [];

        foreach ($input as $hari => $isian) {
            $hari = (int) $hari;
            $b = $baris->get($hari);

            if (! $b || empty($isian['pakai']) || ! $b['bisa']) {
                continue; // tanggal terkunci, belum tiba, izin, atau tidak dipilih
            }

            if ($b['jam_lama'] && ! in_array($isian['pilihan'] ?? null, ['baru', 'lama'], true)) {
                $kesalahan["baris.{$hari}.pilihan"] = "Tanggal {$hari}: pilih memakai jam lama atau jam dari kartu.";

                continue;
            }

            if (($isian['pilihan'] ?? null) === 'lama') {
                continue;
            }

            $catatanJam = [];
            $masuk = $this->normalisasiJam($isian['jam_masuk'] ?? null, $catatanJam, 'masuk');
            $keluar = $this->normalisasiJam($isian['jam_keluar'] ?? null, $catatanJam, 'keluar');

            if ($catatanJam || ($masuk === null && $keluar === null)) {
                $kesalahan["baris.{$hari}.jam_masuk"] = "Tanggal {$hari}: isi jam dengan format JJ:MM.";

                continue;
            }

            if ($masuk && $keluar && $masuk >= $keluar) {
                $kesalahan["baris.{$hari}.jam_keluar"] = "Tanggal {$hari}: jam masuk harus lebih awal dari jam keluar.";

                continue;
            }

            $simpan[$hari] = [$b, $masuk, $keluar];
        }

        if ($kesalahan) {
            throw ValidationException::withMessages($kesalahan);
        }

        if (! $simpan) {
            throw ValidationException::withMessages(['baris' => 'Pilih minimal satu tanggal yang akan disimpan.']);
        }

        DB::transaction(function () use ($scan, $simpan) {
            foreach ($simpan as $hari => [$b, $masuk, $keluar]) {
                $logbook = $b['logbook'] ?? new LogbookHarian([
                    'peserta_magang_id' => $scan->peserta_magang_id,
                    'tanggal' => $b['tanggal']->toDateString(),
                ]);

                $logbook->fill([
                    'kehadiran' => LogbookHarian::HADIR,
                    // Kartu hanya mencatat Pagi Masuk dan Siang Keluar.
                    'pagi_masuk' => $masuk,
                    'pagi_keluar' => null,
                    'siang_masuk' => null,
                    'siang_keluar' => $keluar,
                    'sumber_jam' => LogbookHarian::SUMBER_SCAN,
                    'scan_kartu_absensi_id' => $scan->id,
                    'jam_scan_asli' => [
                        'pagi_masuk' => $b['bacaan']['jam_masuk'] ?? '',
                        'siang_keluar' => $b['bacaan']['jam_keluar'] ?? '',
                    ],
                    'di_luar_batas_isi' => $b['lewat_batas'],
                    // Setiap perubahan kembali menunggu verifikasi admin.
                    'status_verifikasi' => LogbookHarian::MENUNGGU,
                    'catatan_verifikasi' => null,
                ])->save();
            }

            $scan->update([
                'status' => ScanKartuAbsensi::DISIMPAN,
                'jumlah_disimpan' => count($simpan),
                'disimpan_pada' => now(),
            ]);
        });

        LogAktivitas::catat('simpan_scan_kartu_absensi', $scan->pesertaMagang, "Jam {$scan->labelBulan()} ({$scan->labelSisi()}) dari kartu absensi disimpan: ".count($simpan).' tanggal', [
            'sisi' => $scan->sisi, 'bulan' => sprintf('%04d-%02d', $scan->tahun, $scan->bulan), 'disimpan' => count($simpan),
            'diubah_peserta' => collect($simpan)->filter(fn ($s) => ($s[0]['bacaan']['jam_masuk'] ?? null) !== $s[1] || ($s[0]['bacaan']['jam_keluar'] ?? null) !== $s[2])->count(),
            'di_luar_batas' => collect($simpan)->filter(fn ($s) => $s[0]['lewat_batas'])->count(),
        ]);

        return count($simpan);
    }

    /**
     * "7:57", "07.57", "16:30" → "07:57"/"16:30"; selain itu null (dicatat di $catatan).
     *
     * @param  array<int, string>  $catatan
     */
    public function normalisasiJam(mixed $jam, array &$catatan, string $jenis): ?string
    {
        $jam = trim((string) $jam);

        if ($jam === '' || in_array(strtolower($jam), ['null', '-', '--'], true)) {
            return null;
        }

        if (! preg_match('/^(\d{1,2})[:.](\d{2})$/', $jam, $m) || (int) $m[1] > 23 || (int) $m[2] > 59) {
            $catatan[] = "Jam {$jenis} \"".mb_substr($jam, 0, 10).'" tidak valid.';

            return null;
        }

        return sprintf('%02d:%02d', $m[1], $m[2]);
    }

    /**
     * Putar sesuai orientasi kamera, perkecil, simpan ulang sebagai JPG (EXIF terbuang).
     */
    public function siapkanFoto(UploadedFile $foto): string
    {
        $gambar = @imagecreatefromstring((string) file_get_contents($foto->getRealPath()));

        if ($gambar === false) {
            throw ValidationException::withMessages(['foto' => 'Foto tidak dapat dibaca. Gunakan JPG, PNG, atau WebP.']);
        }

        if (function_exists('exif_read_data') && in_array(strtolower($foto->getClientOriginalExtension()), ['jpg', 'jpeg'], true)) {
            $sudut = match ((int) (@exif_read_data($foto->getRealPath())['Orientation'] ?? 1)) {
                3 => 180,
                6 => -90,
                8 => 90,
                default => 0,
            };

            $gambar = $sudut ? (imagerotate($gambar, $sudut, 0) ?: $gambar) : $gambar;
        }

        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $skala = min(1, self::SISI_FOTO / max($lebar, $tinggi));
        $hasil = imagecreatetruecolor(max(1, (int) round($lebar * $skala)), max(1, (int) round($tinggi * $skala)));
        imagefill($hasil, 0, 0, (int) imagecolorallocate($hasil, 255, 255, 255));
        imagecopyresampled($hasil, $gambar, 0, 0, 0, 0, imagesx($hasil), imagesy($hasil), $lebar, $tinggi);

        ob_start();
        imagejpeg($hasil, null, 85);

        return (string) ob_get_clean();
    }

    /**
     * @return array<string, string> tanggal (Y-m-d) => keterangan libur nasional
     */
    private function liburNasional(ScanKartuAbsensi $scan): array
    {
        [$awal, $akhir] = $scan->rentang();

        return HariLibur::whereDate('tanggal', '>=', $scan->tanggal($awal)->toDateString())
            ->whereDate('tanggal', '<=', $scan->tanggal($akhir)->toDateString())
            ->get()
            ->mapWithKeys(fn (HariLibur $libur) => [$libur->tanggal->toDateString() => $libur->keterangan])
            ->all();
    }
}
