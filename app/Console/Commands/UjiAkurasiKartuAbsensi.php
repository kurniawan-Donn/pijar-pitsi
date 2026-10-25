<?php

namespace App\Console\Commands;

use App\Models\ScanKartuAbsensi;
use App\Services\BacaKartuAbsensi;
use App\Services\GagalBacaKartuException;
use App\Services\GeminiKartuAbsensi;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;

/**
 * Uji akurasi pembacaan kartu absensi dengan foto contoh di docs/reverensi (memanggil Gemini sungguhan).
 * Kunci jawaban: docs/rencana-fitur.md bagian 10.1 (dikonfirmasi pemilik).
 */
#[Signature('kartu:uji-akurasi
    {--ulang=1 : Berapa kali setiap foto dibaca (melihat konsistensi)}
    {--harga-masuk=0.30 : USD per 1 juta token masukan}
    {--harga-keluar=2.50 : USD per 1 juta token keluaran (termasuk token berpikir)}
    {--kurs=16500 : Rupiah per USD}')]
#[Description('Uji akurasi baca kartu absensi dengan dua foto contoh (memanggil Gemini, berbayar)')]
class UjiAkurasiKartuAbsensi extends Command
{
    /**
     * Foto contoh (Oktober 2026) beserta kunci jawabannya: tanggal => [masuk, keluar].
     *
     * @var array<string, array{string, string, array<int, array{?string, ?string}>}>
     */
    private const CONTOH = [
        'depan' => ['docs/reverensi/kartu-absensi-depan.webp', ScanKartuAbsensi::SISI_AWAL, [
            1 => ['07:57', '16:30'], 2 => ['08:06', '16:40'], 5 => ['08:06', '16:36'], 7 => ['08:04', '16:41'],
            8 => ['07:57', '16:38'], 9 => ['08:06', '16:42'], 12 => ['07:57', '16:41'], 13 => ['08:10', '16:43'],
            14 => ['08:02', null], 15 => ['08:05', '16:37'],
        ]],
        'belakang' => ['docs/reverensi/kartu-absensi-belakang.webp', ScanKartuAbsensi::SISI_AKHIR, [
            16 => ['07:54', '16:54'], 19 => ['07:58', '16:37'], 20 => ['07:58', '16:35'], 21 => ['08:08', '16:37'],
            22 => ['07:47', '16:45'], 23 => ['08:05', null],
        ]],
    ];

    public function handle(BacaKartuAbsensi $baca, GeminiKartuAbsensi $gemini): int
    {
        $this->info('Model: '.config('services.gemini.model'));
        $totalBenar = $totalNilai = $totalMasuk = $totalKeluar = $jumlahPanggilan = 0;

        foreach (self::CONTOH as $nama => [$path, $sisi, $kunci]) {
            if (! is_file(base_path($path))) {
                $this->error("Foto contoh {$path} tidak ditemukan.");

                return self::FAILURE;
            }

            $scan = new ScanKartuAbsensi(['tahun' => 2026, 'bulan' => 10, 'sisi' => $sisi]);
            $jpeg = $baca->siapkanFoto(new UploadedFile(base_path($path), basename($path), null, null, true));

            for ($i = 1; $i <= (int) $this->option('ulang'); $i++) {
                try {
                    $bacaan = $gemini->baca($jpeg, $baca->prompt($scan));
                } catch (GagalBacaKartuException $e) {
                    $this->error("{$nama}: {$e->getMessage()}");

                    return self::FAILURE;
                }

                $hasil = collect($baca->validasi($bacaan['baris'], $scan))->keyBy('tanggal');
                [$awal, $akhir] = $scan->rentang();
                $baris = [];
                $benar = $nilai = 0;

                foreach (range($awal, $akhir) as $hari) {
                    $harap = $kunci[$hari] ?? [null, null];
                    $dapat = [$hasil[$hari]['jam_masuk'] ?? null, $hasil[$hari]['jam_keluar'] ?? null];

                    if ($harap === [null, null] && $dapat === [null, null]) {
                        continue;
                    }

                    // Dinilai per jam (masuk dan keluar terpisah), termasuk jam yang seharusnya kosong.
                    foreach ([0, 1] as $k) {
                        $nilai++;
                        $benar += $harap[$k] === $dapat[$k] ? 1 : 0;
                    }

                    $cocok = $harap === $dapat;
                    $baris[] = [
                        $hari,
                        ($harap[0] ?? '–').' – '.($harap[1] ?? '–'),
                        ($dapat[0] ?? '–').' – '.($dapat[1] ?? '–'),
                        isset($hasil[$hari]) ? round($hasil[$hari]['keyakinan'] * 100).'%'.($hasil[$hari]['ragu'] ? ' ragu' : '') : '',
                        $cocok ? 'benar' : 'SALAH',
                    ];
                }

                $this->newLine();
                $this->line("<options=bold>Sisi {$nama} (percobaan {$i})</>: {$benar}/{$nilai} jam benar (".round($benar / max(1, $nilai) * 100, 1).'%) · token '.$bacaan['token_masuk'].' masuk / '.$bacaan['token_keluar'].' keluar');
                $this->table(['Tgl', 'Kunci', 'Hasil', 'Yakin', ''], $baris);

                $totalBenar += $benar;
                $totalNilai += $nilai;
                $totalMasuk += (int) $bacaan['token_masuk'];
                $totalKeluar += (int) $bacaan['token_keluar'];
                $jumlahPanggilan++;
            }
        }

        $biayaUsd = ($totalMasuk / $jumlahPanggilan) * (float) $this->option('harga-masuk') / 1_000_000
            + ($totalKeluar / $jumlahPanggilan) * (float) $this->option('harga-keluar') / 1_000_000;

        $this->newLine();
        $this->info('Akurasi keseluruhan: '.$totalBenar.'/'.$totalNilai.' jam ('.round($totalBenar / max(1, $totalNilai) * 100, 1).'%)');
        $this->info(sprintf('Rata-rata per scan: %d token masuk, %d token keluar ≈ US$%.5f ≈ Rp%s',
            $totalMasuk / $jumlahPanggilan, $totalKeluar / $jumlahPanggilan, $biayaUsd,
            number_format($biayaUsd * (float) $this->option('kurs'), 0, ',', '.')));

        return self::SUCCESS;
    }
}
