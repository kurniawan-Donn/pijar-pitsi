<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianDetail extends Model
{
    protected $table = 'penilaian_details';

    protected $fillable = [
        'aspek_penilaian_id',
        'nama_aspek',
        'bobot',
        'nilai',
        'kelompok',
        'bobot_kelompok',
    ];

    protected $casts = [
        'bobot' => 'integer',
        'nilai' => 'integer',
        'bobot_kelompok' => 'integer',
    ];

    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(PenilaianPeserta::class, 'penilaian_peserta_id');
    }

    public function aspek(): BelongsTo
    {
        return $this->belongsTo(AspekPenilaian::class, 'aspek_penilaian_id');
    }
}
