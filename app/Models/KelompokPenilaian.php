<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kelompok aspek penilaian (mis. Hard Skill 60%, Soft Skill 40%).
 * Nilai kelompok = rata-rata aspek aktif di dalamnya.
 */
class KelompokPenilaian extends Model
{
    protected $table = 'kelompok_penilaians';

    protected $fillable = [
        'nama',
        'bobot',
        'urutan',
    ];

    protected $casts = [
        'bobot' => 'integer',
        'urutan' => 'integer',
    ];

    public function aspeks(): HasMany
    {
        return $this->hasMany(AspekPenilaian::class)->orderBy('urutan')->orderBy('id');
    }

    public function aspekAktif(): HasMany
    {
        return $this->aspeks()->where('aktif', true);
    }
}
