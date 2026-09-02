<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'perusahaans';

    protected $fillable = [
        'nama_perusahaan',
        'nama',
        'alamat',
        'kota',
        'no_telepon',
        'telepon',
        'email',
        'website',
        'nama_pembimbing',
        'jabatan_pembimbing',
        'deskripsi',
    ];
}