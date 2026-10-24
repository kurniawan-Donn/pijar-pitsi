<?php

namespace App\Services;

/**
 * Perhitungan rekap absensi dari baris Google Sheets.
 *
 * Status baris:
 * - Activity mengandung "sakit"           → sakit
 * - Activity mengandung "izin" / "ijin"   → izin
 * - Ada minimal satu waktu absensi terisi → hadir
 * - Selain itu                            → belum diisi
 *
 * Kehadiran (hanya untuk baris hadir), mengikuti footer template sheet:
 * - Hari penuh     : sesi pagi (masuk + keluar) dan sesi siang lengkap
 * - Setengah hari  : hanya satu sesi yang lengkap
 * - Tidak lengkap  : ada waktu terisi, tapi tidak ada sesi yang lengkap
 */
class RekapAbsensi
{
    private const KOLOM_WAKTU = ['pagi_masuk', 'pagi_keluar', 'siang_masuk', 'siang_keluar'];

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{tercatat: int, hadir: int, izin: int, sakit: int, belum_diisi: int, hari_penuh: int, setengah_hari: int, tidak_lengkap: int}
     */
    public static function hitung(array $rows): array
    {
        $rekap = [
            'tercatat' => count($rows),
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'belum_diisi' => 0,
            'hari_penuh' => 0,
            'setengah_hari' => 0,
            'tidak_lengkap' => 0,
        ];

        foreach ($rows as $row) {
            $status = self::statusBaris($row);
            $rekap[$status]++;

            if ($status === 'hadir') {
                $rekap[self::jenisKehadiran($row)]++;
            }
        }

        return $rekap;
    }

    /**
     * Total uang saku dari rekap: hari penuh × tarif penuh + setengah hari × tarif setengah.
     *
     * @param  array{hari_penuh: int, setengah_hari: int}  $rekap
     */
    public static function uangSaku(array $rekap, int $tarifHariPenuh, int $tarifSetengahHari): int
    {
        return $rekap['hari_penuh'] * $tarifHariPenuh
            + $rekap['setengah_hari'] * $tarifSetengahHari;
    }

    /**
     * Status satu baris absensi: hadir, izin, sakit, atau belum_diisi.
     *
     * @param  array<string, mixed>  $row
     */
    public static function statusBaris(array $row): string
    {
        $activity = strtolower(trim((string) ($row['activity'] ?? '')));

        if (str_contains($activity, 'sakit')) {
            return 'sakit';
        }

        if (str_contains($activity, 'izin') || str_contains($activity, 'ijin')) {
            return 'izin';
        }

        foreach (self::KOLOM_WAKTU as $kolom) {
            if (self::terisi($row, $kolom)) {
                return 'hadir';
            }
        }

        return 'belum_diisi';
    }

    /**
     * Jenis kehadiran: hari_penuh, setengah_hari, atau tidak_lengkap.
     *
     * @param  array<string, mixed>  $row
     */
    public static function jenisKehadiran(array $row): string
    {
        $sesiPagi = self::terisi($row, 'pagi_masuk') && self::terisi($row, 'pagi_keluar');
        $sesiSiang = self::terisi($row, 'siang_masuk') && self::terisi($row, 'siang_keluar');

        return match (true) {
            $sesiPagi && $sesiSiang => 'hari_penuh',
            $sesiPagi || $sesiSiang => 'setengah_hari',
            default => 'tidak_lengkap',
        };
    }

    /**
     * Ubah isi sel jam (mis. "07.30", "7:30", "07:30:00") menjadi format HH:MM.
     * Nilai yang tidak dikenali dikembalikan sebagai string kosong.
     */
    public static function normalisasiJam(mixed $nilai): string
    {
        $nilai = str_replace('.', ':', trim((string) $nilai));

        if (! preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $nilai, $cocok)) {
            return '';
        }

        [$jam, $menit] = [(int) $cocok[1], (int) $cocok[2]];

        if ($jam > 23 || $menit > 59) {
            return '';
        }

        return sprintf('%02d:%02d', $jam, $menit);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function terisi(array $row, string $kolom): bool
    {
        return trim((string) ($row[$kolom] ?? '')) !== '';
    }
}
