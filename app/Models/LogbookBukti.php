<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * File bukti kegiatan untuk satu hari logbook (disk privat "local").
 */
class LogbookBukti extends Model
{
    protected $table = 'logbook_buktis';

    protected $fillable = [
        'logbook_harian_id',
        'path',
        'nama_asli',
        'mime',
        'ukuran',
    ];

    protected $casts = [
        'ukuran' => 'integer',
    ];

    public function logbookHarian(): BelongsTo
    {
        return $this->belongsTo(LogbookHarian::class);
    }

    public function ukuranTampil(): string
    {
        return $this->ukuran >= 1048576
            ? number_format($this->ukuran / 1048576, 1, ',', '.').' MB'
            : max(1, (int) round($this->ukuran / 1024)).' KB';
    }
}
