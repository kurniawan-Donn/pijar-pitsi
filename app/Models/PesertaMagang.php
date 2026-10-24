<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesertaMagang extends Model
{
    protected $table = 'peserta_magangs';

    protected $fillable = [
        'nama',
        'nim',
        'asal_kampus',
        'program_studi',
        'periode_mulai',
        'periode_selesai',
        'pembimbing',
        'status',
        'google_sheet_url',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    public function absensis(): HasMany
    {
        return $this->hasMany(
            Absensi::class,
            'peserta_magang_id'
        );
    }
}