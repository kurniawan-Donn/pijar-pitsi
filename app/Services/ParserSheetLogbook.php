<?php

namespace App\Services;

use App\Models\LogbookHarian;
use Carbon\CarbonImmutable;
use DateInterval;
use DateTimeInterface;

/**
 * Membaca satu tab sheet bulanan buku kerja magang (dari Google Sheets atau
 * file .xlsx) menjadi baris logbook. Dua format didukung; posisi kolom
 * dicari dari baris judul, bukan nomor baris tetap:
 *
 * - Format lama (Juni–Agustus): No, Tanggal, Pagi, Siang, Keterangan, Paraf
 * - Format baru (September dst.): No, Tanggal, Pagi, Siang, Activity, Output, Lokasi, Action
 *
 * Aturan pemetaan:
 * - Ada jam terisi → hadir. Tanpa jam: teks mengandung "sakit" → sakit,
 *   "izin/ijin" → izin, "sabtu/minggu/libur/cuti" → libur.
 * - Status hari hadir dari kolom Action (Diterima/Ditolak/Review) atau
 *   Paraf "v" (diterima); selain itu menunggu. Hari libur/izin/sakit di
 *   sheet lama dianggap diterima.
 * - Jam yang tidak terbaca (mis. "??" atau tanggal) dikosongkan dan dicatat
 *   sebagai peringatan.
 */
class ParserSheetLogbook
{
    /**
     * @param  array<int, array<int, mixed>>  $rows  sel per baris (indeks kolom 0 = A)
     * @return array{format: ?string, baris: array<int, array<string, mixed>>, peringatan: array<int, string>}
     */
    public function parse(array $rows): array
    {
        $judul = $this->cariBarisJudul($rows);

        if ($judul === null) {
            return ['format' => null, 'baris' => [], 'peringatan' => []];
        }

        [$indeksJudul, $kolom, $format] = $judul;
        $baris = [];
        $peringatan = [];

        // Data dimulai dua baris di bawah judul (baris kedua berisi Masuk/Keluar).
        for ($i = $indeksJudul + 2; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (str_starts_with(mb_strtolower($this->teks($row[$kolom['no']] ?? '')), 'total')) {
                break;
            }

            $tanggal = $this->tanggal($row[$kolom['tanggal']] ?? null);

            if (! $tanggal) {
                continue;
            }

            $label = $tanggal->translatedFormat('d M Y');
            $jam = [];

            foreach (['pagi_masuk', 'pagi_keluar', 'siang_masuk', 'siang_keluar'] as $field) {
                $mentah = $row[$kolom[$field]] ?? null;
                $tafsiran = $this->jamDariTanggalSalah($mentah);
                $nilai = $tafsiran ?? $this->jam($mentah);

                if ($tafsiran !== null) {
                    $peringatan[] = "{$label}: jam ".str_replace('_', ' ', $field)." tersimpan sebagai tanggal, ditafsirkan {$tafsiran}. Mohon dicek.";
                } elseif ($nilai === null) {
                    $peringatan[] = "{$label}: jam ".str_replace('_', ' ', $field)." \"{$this->teksMentah($mentah)}\" tidak terbaca, dikosongkan.";
                    $nilai = '';
                }

                $jam[$field] = $nilai;
            }

            $activity = $this->teks($row[$kolom['activity']] ?? '');
            $output = isset($kolom['output']) ? $this->teks($row[$kolom['output']] ?? '') : '';
            $lokasi = isset($kolom['lokasi']) ? $this->teks($row[$kolom['lokasi']] ?? '') : '';
            $adaJam = implode('', $jam) !== '';

            if (! $adaJam && $activity === '' && $output === '') {
                continue; // belum diisi
            }

            $kehadiran = $adaJam ? LogbookHarian::HADIR : $this->kehadiranDariTeks($activity);

            if ($kehadiran === LogbookHarian::HADIR && RekapAbsensi::jenisKehadiran($jam) === 'tidak_lengkap') {
                $peringatan[] = "{$label}: jam hadir tidak lengkap, tidak dihitung uang saku sampai diperbaiki.";
            }

            $baris[] = [
                'tanggal' => $tanggal->toDateString(),
                'kehadiran' => $kehadiran,
                ...array_map(fn (string $nilai) => $nilai === '' ? null : $nilai, $jam),
                'activity' => $activity ?: null,
                'output' => $output ?: null,
                'lokasi' => $lokasi ?: null,
                'status_verifikasi' => $kehadiran === LogbookHarian::HADIR
                    ? $this->status($format, $row, $kolom)
                    : LogbookHarian::DITERIMA,
            ];
        }

        return ['format' => $format, 'baris' => $baris, 'peringatan' => $peringatan];
    }

    /**
     * Cari baris yang memuat judul "Tanggal" dan "Pagi".
     *
     * @param  array<int, array<int, mixed>>  $rows
     * @return array{0: int, 1: array<string, int>, 2: string}|null
     */
    private function cariBarisJudul(array $rows): ?array
    {
        foreach (array_slice($rows, 0, 30, true) as $i => $row) {
            $judul = array_map(fn ($sel) => mb_strtolower($this->teks($sel)), $row);
            $cari = fn (string $nama) => ($posisi = array_search($nama, $judul, true)) === false ? null : $posisi;

            $tanggal = $cari('tanggal');
            $pagi = $cari('pagi');
            $siang = $cari('siang');

            if ($tanggal === null || $pagi === null || $siang === null) {
                continue;
            }

            $activity = $cari('activity');
            $format = $activity !== null ? 'baru' : 'lama';

            $kolom = array_filter([
                'no' => $cari('no') ?? max(0, $tanggal - 1),
                'tanggal' => $tanggal,
                'pagi_masuk' => $pagi,
                'pagi_keluar' => $pagi + 1,
                'siang_masuk' => $siang,
                'siang_keluar' => $siang + 1,
                'activity' => $activity ?? $cari('keterangan'),
                'output' => $cari('output'),
                'lokasi' => $cari('lokasi'),
                'action' => $cari('action'),
                'paraf' => $cari('paraf'),
            ], fn ($posisi) => $posisi !== null);

            if (! isset($kolom['activity'])) {
                continue;
            }

            return [$i, $kolom, $format];
        }

        return null;
    }

    /**
     * Tanggal dari objek tanggal, angka seri Excel/Sheets, atau teks.
     */
    private function tanggal(mixed $nilai): ?CarbonImmutable
    {
        if ($nilai instanceof DateTimeInterface) {
            return CarbonImmutable::instance($nilai)->startOfDay();
        }

        if (is_numeric($nilai) && (float) $nilai >= 20000 && (float) $nilai < 80000) {
            return CarbonImmutable::create(1899, 12, 30)->addDays((int) floor((float) $nilai));
        }

        $teks = $this->teks($nilai);

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $teks, $cocok)) {
            [, $tahun, $bulan, $hari] = $cocok;
        } elseif (preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/', $teks, $cocok)) {
            [, $hari, $bulan, $tahun] = $cocok;
        } else {
            return null;
        }

        return checkdate((int) $bulan, (int) $hari, (int) $tahun)
            ? CarbonImmutable::create((int) $tahun, (int) $bulan, (int) $hari)
            : null;
    }

    /**
     * Jam HH:MM, '' bila kosong, atau null bila ada isi tapi tidak terbaca.
     */
    private function jam(mixed $nilai): ?string
    {
        if ($nilai === null || $nilai === '') {
            return '';
        }

        // Sel waktu Excel terbaca sebagai tanggal 30/31-12-1899 + jam.
        if ($nilai instanceof DateTimeInterface) {
            return (int) $nilai->format('Y') <= 1900 ? $nilai->format('H:i') : null;
        }

        if ($nilai instanceof DateInterval) {
            return $nilai->d === 0 && $nilai->h < 24 ? sprintf('%02d:%02d', $nilai->h, $nilai->i) : null;
        }

        // Pecahan hari (0.3388… = 08:08); angka >= 1 adalah tanggal yang salah ketik.
        if (is_int($nilai) || is_float($nilai) || (is_string($nilai) && preg_match('/^\d+\.\d+$/', trim($nilai)) && (float) $nilai < 1)) {
            $angka = (float) $nilai;

            if ($angka <= 0 || $angka >= 1) {
                return null;
            }

            $menit = (int) round($angka * 1440);

            return sprintf('%02d:%02d', intdiv($menit, 60) % 24, $menit % 60);
        }

        $teks = str_replace(';', ':', $this->teks($nilai));

        if ($teks === '') {
            return '';
        }

        $hasil = RekapAbsensi::normalisasiJam($teks);

        return $hasil === '' ? null : $hasil;
    }

    /**
     * Jam bertitik yang diketik di Excel berlocale Indonesia (mis. "08.04")
     * berubah menjadi tanggal 8 April. Hari & bulan tanggal itu dikembalikan
     * menjadi jam & menit (08:04). Selain pola itu, null.
     */
    private function jamDariTanggalSalah(mixed $nilai): ?string
    {
        $tanggal = match (true) {
            $nilai instanceof DateTimeInterface && (int) $nilai->format('Y') > 1900 => CarbonImmutable::instance($nilai),
            is_numeric($nilai) && (float) $nilai >= 20000 && (float) $nilai < 80000 => CarbonImmutable::create(1899, 12, 30)->addDays((int) floor((float) $nilai)),
            default => null,
        };

        if (! $tanggal || $tanggal->day > 23 || $tanggal->format('H:i') !== '00:00') {
            return null;
        }

        return sprintf('%02d:%02d', $tanggal->day, $tanggal->month);
    }

    private function kehadiranDariTeks(string $teks): string
    {
        $teks = mb_strtolower($teks);

        return match (true) {
            str_contains($teks, 'sakit') => LogbookHarian::SAKIT,
            (bool) preg_match('/\b(izin|ijin)\b/u', $teks) => LogbookHarian::IZIN,
            (bool) preg_match('/\b(sabtu|minggu|libur|cuti)\b/u', $teks) => LogbookHarian::LIBUR,
            default => LogbookHarian::HADIR,
        };
    }

    /**
     * @param  array<int, mixed>  $row
     * @param  array<string, int>  $kolom
     */
    private function status(string $format, array $row, array $kolom): string
    {
        if ($format === 'baru' && isset($kolom['action'])) {
            $action = Logbook::actionBaku($this->teks($row[$kolom['action']] ?? ''));

            return $action ? strtolower($action) : LogbookHarian::MENUNGGU;
        }

        if (isset($kolom['paraf']) && in_array(mb_strtolower($this->teks($row[$kolom['paraf']] ?? '')), ['v', '✓', '√'], true)) {
            return LogbookHarian::DITERIMA;
        }

        return LogbookHarian::MENUNGGU;
    }

    /**
     * Teks sel yang sudah dirapikan (spasi & karakter tak terlihat dibuang).
     */
    private function teks(mixed $nilai): string
    {
        if ($nilai instanceof DateTimeInterface || $nilai instanceof DateInterval || is_array($nilai)) {
            return '';
        }

        $teks = preg_replace('/[\x{200B}-\x{200D}\x{2060}\x{FEFF}]/u', '', (string) $nilai);

        return trim(preg_replace('/\s+/u', ' ', $teks));
    }

    private function teksMentah(mixed $nilai): string
    {
        return match (true) {
            $nilai instanceof DateTimeInterface => $nilai->format('d/m/Y'),
            $nilai instanceof DateInterval => $nilai->format('%d hari %h:%I'),
            default => mb_substr((string) $nilai, 0, 20),
        };
    }
}
