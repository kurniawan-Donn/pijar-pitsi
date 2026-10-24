<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function pengajuanMagangs(): HasMany
    {
        return $this->hasMany(PengajuanMagang::class);
    }
}
