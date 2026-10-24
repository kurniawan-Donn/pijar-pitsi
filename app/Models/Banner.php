<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'tombol_teks',
        'tombol_link',
        'status',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Scope untuk mengambil banner yang aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query
            ->where('status', 'aktif')
            ->orderBy('urutan', 'asc');
    }
}
