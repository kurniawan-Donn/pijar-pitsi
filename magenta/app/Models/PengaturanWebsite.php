<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanWebsite extends Model
{
    protected $table = 'pengaturan_websites';

    protected $fillable = [
        'tentang_judul',
        'tentang_deskripsi',

        'tentang_card1_judul',
        'tentang_card1_deskripsi',

        'tentang_card2_judul',
        'tentang_card2_deskripsi',

        'tentang_card3_judul',
        'tentang_card3_deskripsi',
    ];
}