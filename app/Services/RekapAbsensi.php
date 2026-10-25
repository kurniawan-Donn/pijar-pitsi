<?php

namespace App\Services;

/**
 * Aturan jam kerja harian (dipakai logbook, rekap periode, dan impor sheet).
 *
 * Kehadiran (hanya untuk hari hadir), mengikuti cara buku kerja diisi:
 * - Hari penuh     : ada jam pagi masuk dan jam siang keluar (pulang sore);
 *                    pagi keluar & siang masuk (istirahat) boleh kosong
 * - Setengah hari  : hanya satu sesi lengkap, yaitu pagi masuk + pagi keluar
 *                    (pulang siang) atau siang masuk + siang keluar (datang siang)
 * - Tidak lengkap  : selain itu, mis. hanya jam masuk
 */
class RekapAbsensi
{
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
            self::terisi($row, 'pagi_masuk') && self::terisi($row, 'siang_keluar') => 'hari_penuh',
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
