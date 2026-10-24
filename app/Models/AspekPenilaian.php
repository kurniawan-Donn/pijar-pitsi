<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AspekPenilaian extends Model
{
    protected $table = 'aspek_penilaians';

    protected $fillable = [
        'nama',
        'bobot',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'bobot' => 'integer',
        'urutan' => 'integer',
        'aktif' => 'boolean',
    ];

    /**
     * Aspek aktif sesuai urutan tampil.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true)->orderBy('urutan')->orderBy('id');
    }
}
