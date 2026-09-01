<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    /**
     * Nama tabel database.
     */
    protected $table = 'perusahaans';

    /**
     * Kolom yang boleh diisi melalui create()
     * dan update().
     */
    protected $fillable = [
        'nama',
        'alamat',
        'kota',
        'telepon',
        'email',
        'website',
        'deskripsi',
    ];
}