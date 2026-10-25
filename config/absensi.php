<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Credential Google Sheets
    |--------------------------------------------------------------------------
    |
    | Hanya dipakai untuk mengimpor riwayat logbook dari buku kerja lama
    | (Logbook Harian → Impor). Path file JSON service account; jangan pernah
    | commit file ini. Spreadsheet peserta harus dibagikan ke email service
    | account.
    |
    */

    // "?:" supaya nilai kosong di .env tetap memakai path bawaan.
    'credentials_path' => env('GOOGLE_SHEETS_CREDENTIALS')
        ?: storage_path('app/google/service-account.json'),

    /*
    |--------------------------------------------------------------------------
    | Uang saku (upah kedatangan)
    |--------------------------------------------------------------------------
    |
    | Hari penuh = ada jam pagi masuk & siang keluar; setengah hari = hanya
    | satu sesi lengkap (lihat RekapAbsensi::jenisKehadiran). Nilai dalam rupiah.
    |
    | Hanya tarif AWAL: dipakai sekali saat migration tabel tarif_uang_sakus.
    | Tarif yang berlaku diatur admin di halaman Pengaturan Magang.
    |
    */

    'uang_saku' => [
        'hari_penuh' => (int) env('UANG_SAKU_HARI_PENUH', 50000),
        'setengah_hari' => (int) env('UANG_SAKU_SETENGAH_HARI', 25000),
    ],

];
