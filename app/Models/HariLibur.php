<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Hari libur nasional / cuti bersama.
 */
class HariLibur extends Model
{
    protected $table = 'hari_liburs';

    protected $fillable = [
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
