<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswas';

    protected $fillable = [
        'nim',
        'nama',
        'email',
        'asal_kampus',
        'no_hp',
        'program_studi',
        'fakultas',
        'alamat',
    ];

    public function pengajuanMagangs(): HasMany
    {
        return $this->hasMany(PengajuanMagang::class);
    }
}
