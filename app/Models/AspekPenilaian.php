<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AspekPenilaian extends Model
{
    protected $table = 'aspek_penilaians';

    protected $fillable = [
        'nama',
        'bobot',
        'urutan',
        'aktif',
        'kelompok_penilaian_id',
    ];

    protected $casts = [
        'bobot' => 'integer',
        'urutan' => 'integer',
        'aktif' => 'boolean',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(KelompokPenilaian::class, 'kelompok_penilaian_id');
    }

    /**
     * Aspek aktif sesuai urutan tampil.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true)->orderBy('urutan')->orderBy('id');
    }
}
