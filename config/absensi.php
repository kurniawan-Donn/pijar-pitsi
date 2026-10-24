<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Credential Google Sheets
    |--------------------------------------------------------------------------
    |
    | Path file JSON service account. Jangan pernah commit file ini.
    | Spreadsheet peserta harus dibagikan (Editor) ke email service account.
    |
    */

    // "?:" supaya nilai kosong di .env tetap memakai path bawaan.
    'credentials_path' => env('GOOGLE_SHEETS_CREDENTIALS')
        ?: storage_path('app/google/service-account.json'),

    /*
    |--------------------------------------------------------------------------
    | Layout sheet absensi
    |--------------------------------------------------------------------------
    |
    | B11 = No            G11 = Siang Keluar
    | C11 = Tanggal       H11 = Activity
    | D11 = Pagi Masuk    I11 = Output
    | E11 = Pagi Keluar   J11 = Lokasi
    | F11 = Siang Masuk   K11 = Action
    |
    | Baris header, baris pertama data, dan baris terakhir yang dibaca
    | bisa disesuaikan lewat .env bila template sheet berubah.
    |
    */

    'header_row' => (int) env('ABSENSI_HEADER_ROW', 11),

    'first_data_row' => (int) env('ABSENSI_FIRST_DATA_ROW', 13),

    // Baris 13–43 = tanggal 1–31. Di bawahnya ada footer total & uang saku
    // yang tidak boleh ikut terbaca sebagai data.
    'last_row' => (int) env('ABSENSI_LAST_ROW', 43),

    'first_column' => env('ABSENSI_FIRST_COLUMN', 'B'),

    // Urutan kolom mulai dari first_column.
    'columns' => [
        'no',
        'tanggal',
        'pagi_masuk',
        'pagi_keluar',
        'siang_masuk',
        'siang_keluar',
        'activity',
        'output',
        'lokasi',
        'action',
    ],

    // Kolom waktu yang boleh diubah admin dari MAGENTA (harus berurutan).
    'editable_columns' => [
        'pagi_masuk',
        'pagi_keluar',
        'siang_masuk',
        'siang_keluar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Uang saku (upah kedatangan)
    |--------------------------------------------------------------------------
    |
    | Mengikuti footer template sheet: hari penuh = sesi pagi & siang lengkap,
    | setengah hari = hanya satu sesi lengkap. Nilai dalam rupiah.
    |
    */

    'uang_saku' => [
        'hari_penuh' => (int) env('UANG_SAKU_HARI_PENUH', 50000),
        'setengah_hari' => (int) env('UANG_SAKU_SETENGAH_HARI', 25000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Nilai kolom Action (verifikasi logbook)
    |--------------------------------------------------------------------------
    |
    | Harus sama persis dengan pilihan dropdown di sheet.
    |
    */

    'action_options' => ['Diterima', 'Ditolak', 'Review'],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Lama (detik) hasil baca Google Sheets disimpan, supaya halaman detail
    | peserta tidak selalu memanggil API Google. Tombol "Muat ulang data"
    | menghapus cache ini.
    |
    */

    'cache_seconds' => (int) env('ABSENSI_CACHE_SECONDS', 300),

];
