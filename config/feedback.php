<?php

/*
|--------------------------------------------------------------------------
| Feedback mahasiswa magang
|--------------------------------------------------------------------------
|
| Mengikuti sheet "FEEDBACK MHS MAGANG" buku kerja magang. Teks pertanyaan
| boleh diubah di sini, tetapi urutan/jumlah pertanyaan skala jangan
| diubah setelah ada jawaban (skor disimpan menurut nomor pertanyaan).
|
*/

return [

    // Feedback dibuka sekian bulan sebelum tanggal selesai magang.
    'dibuka_bulan_sebelum_selesai' => 1,

    'skala' => [
        5 => 'Sangat Setuju',
        4 => 'Setuju',
        3 => 'Cukup',
        2 => 'Tidak Setuju',
        1 => 'Sangat Tidak Setuju',
    ],

    'pertanyaan_skala' => [
        1 => 'Saya mendapatkan penjelasan mengenai tujuan dan ruang lingkup pekerjaan pada awal magang.',
        2 => 'Pembimbing memberikan arahan yang jelas selama pelaksanaan magang.',
        3 => 'Pembimbing mudah dihubungi ketika saya membutuhkan bantuan atau konsultasi.',
        4 => 'Tugas yang diberikan sesuai dengan bidang studi atau kompetensi saya.',
        5 => 'Saya memperoleh pengalaman dan pengetahuan baru selama magang.',
        6 => 'Lingkungan kerja mendukung proses belajar dan pengembangan diri.',
        7 => 'Rekan kerja bersikap kooperatif dan membantu selama kegiatan magang.',
        8 => 'Fasilitas kerja yang diberikan mendukung pelaksanaan tugas magang.',
        9 => 'Saya memperoleh umpan balik (feedback) terhadap hasil pekerjaan saya.',
        10 => 'Secara keseluruhan saya puas terhadap pelaksanaan program magang di perusahaan ini.',
    ],

    // [kolom database => pertanyaan]
    'pertanyaan_terbuka' => [
        'paling_disukai' => 'Hal apa yang paling Anda sukai selama mengikuti program magang?',
        'kendala' => 'Kendala atau kesulitan yang Anda alami selama magang?',
        'perlu_diperbaiki' => 'Apa yang menurut Anda perlu diperbaiki dari program magang ini?',
        'saran' => 'Saran untuk meningkatkan kualitas pembimbingan dan pelaksanaan program magang.',
    ],

    'pertanyaan_rekomendasi' => 'Apakah Anda bersedia merekomendasikan program magang di perusahaan ini kepada mahasiswa lain?',

    'rekomendasi' => [
        'sangat_bersedia' => 'Sangat Bersedia',
        'bersedia' => 'Bersedia',
        'ragu' => 'Ragu-ragu',
        'tidak_bersedia' => 'Tidak Bersedia',
    ],

    'pertanyaan_evaluasi' => 'Bagaimana Anda menilai pengalaman magang secara keseluruhan?',

    'pernyataan' => 'Saya menyatakan bahwa seluruh jawaban pada formulir ini diberikan secara jujur sesuai dengan pengalaman selama mengikuti program magang.',

];
