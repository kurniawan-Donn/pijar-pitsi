<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanWebsite extends Model
{
    protected $table = 'pengaturan_websites';

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | INFORMASI TERBARU
        |--------------------------------------------------------------------------
        */

        'informasi_terbaru_judul',
        'informasi_terbaru_deskripsi',

        /*
        |--------------------------------------------------------------------------
        | PENGUMUMAN
        |--------------------------------------------------------------------------
        */

        'pengumuman_judul',
        'pengumuman_deskripsi',

        'pengumuman_card1_judul',
        'pengumuman_card1_deskripsi',

        'pengumuman_card2_judul',
        'pengumuman_card2_deskripsi',

        'pengumuman_card3_judul',
        'pengumuman_card3_deskripsi',

        /*
        |--------------------------------------------------------------------------
        | PERSYARATAN MAGANG
        |--------------------------------------------------------------------------
        */

        'persyaratan_judul',
        'persyaratan_deskripsi',

        'persyaratan1_judul',
        'persyaratan1_deskripsi',

        'persyaratan2_judul',
        'persyaratan2_deskripsi',

        'persyaratan3_judul',
        'persyaratan3_deskripsi',

        /*
        |--------------------------------------------------------------------------
        | ALUR MAGANG
        |--------------------------------------------------------------------------
        */

        'alur_judul',
        'alur_deskripsi',

        'alur1_judul',
        'alur1_deskripsi',

        'alur2_judul',
        'alur2_deskripsi',

        'alur3_judul',
        'alur3_deskripsi',

        'alur4_judul',
        'alur4_deskripsi',

        /*
        |--------------------------------------------------------------------------
        | DATA LAMA
        |--------------------------------------------------------------------------
        | Tetap dipertahankan supaya tidak mengganggu database lama.
        |--------------------------------------------------------------------------
        */

        'tentang_section_judul',
        'tentang_judul',
        'tentang_deskripsi',

        'tentang_card1_judul',
        'tentang_card1_deskripsi',

        'tentang_card2_judul',
        'tentang_card2_deskripsi',

        'tentang_card3_judul',
        'tentang_card3_deskripsi',

        'pendaftaran_judul',

        /*
        |--------------------------------------------------------------------------
        | PENDAFTARAN GOOGLE FORM
        |--------------------------------------------------------------------------
        */

        'google_form_url',

    ];
}
