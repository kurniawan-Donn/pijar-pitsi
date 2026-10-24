<?php

namespace App\Services;

/**
 * Perhitungan rekap absensi dari baris Google Sheets.
 *
 * Aturan:
 * - Activity mengandung "sakit"           → sakit
 * - Activity mengandung "izin" / "ijin"   → izin
 * - Ada minimal satu waktu absensi terisi → hadir
 * - Selain itu                            → belum diisi
 */
class RekapAbsensi
{
    private const KOLOM_WAKTU = ['pagi_masuk', 'pagi_keluar', 'siang_masuk', 'siang_keluar'];

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{tercatat: int, hadir: int, izin: int, sakit: int, belum_diisi: int}
     */
    public static function hitung(array $rows): array
    {
        $rekap = ['tercatat' => count($rows), 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'belum_diisi' => 0];

        foreach ($rows as $row) {
            $rekap[self::statusBaris($row)]++;
        }

        return $rekap;
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
            if (trim((string) ($row[$kolom] ?? '')) !== '') {
                return 'hadir';
            }
        }

        return 'belum_diisi';
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
}
