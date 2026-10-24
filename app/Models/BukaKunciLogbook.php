<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tanggal logbook yang dibuka admin melewati batas isi mundur.
 */
class BukaKunciLogbook extends Model
{
    protected $table = 'buka_kunci_logbooks';

    protected $fillable = [
        'peserta_magang_id',
        'tanggal',
        'berlaku_sampai',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'berlaku_sampai' => 'date',
    ];

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }
}
