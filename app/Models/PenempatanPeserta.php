<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Divisi & mentor peserta untuk satu periode (kunci Y-m, lihat PeriodeMagang).
 */
class PenempatanPeserta extends Model
{
    protected $table = 'penempatan_pesertas';

    protected $fillable = [
        'peserta_magang_id',
        'periode',
        'divisi_id',
        'mentor_nama',
        'mentor_jabatan',
    ];

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
}
