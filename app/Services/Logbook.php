<?php

namespace App\Services;

/**
 * Logbook kegiatan harian dari baris Google Sheets.
 *
 * Verifikasi pembimbing tersimpan di kolom Action sheet
 * (lihat config absensi.action_options).
 */
class Logbook
{
    /**
     * Logbook hanya berisi hari yang punya catatan kegiatan / output / lokasi.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public static function baris(array $rows): array
    {
        return array_values(array_filter(
            $rows,
            fn (array $row) => trim(($row['activity'] ?? '').($row['output'] ?? '').($row['lokasi'] ?? '')) !== ''
        ));
    }

    /**
     * Jumlah hari per status verifikasi.
     *
     * @param  array<int, array<string, mixed>>  $logbook
     * @return array<string, int> total, belum, dan satu kunci per pilihan Action
     */
    public static function ringkasan(array $logbook): array
    {
        $ringkasan = ['total' => count($logbook), 'belum' => 0]
            + array_fill_keys(config('absensi.action_options'), 0);

        foreach ($logbook as $row) {
            $action = self::actionBaku($row['action'] ?? '');
            $action === null ? $ringkasan['belum']++ : $ringkasan[$action]++;
        }

        return $ringkasan;
    }

    /**
     * Cocokkan isi kolom Action dengan pilihan baku (tanpa membedakan huruf besar/kecil).
     */
    public static function actionBaku(?string $nilai): ?string
    {
        foreach (config('absensi.action_options') as $pilihan) {
            if (strcasecmp(trim((string) $nilai), $pilihan) === 0) {
                return $pilihan;
            }
        }

        return null;
    }
}
