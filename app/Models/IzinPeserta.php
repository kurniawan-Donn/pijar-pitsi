<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Izin / sakit untuk satu rentang tanggal dengan satu bukti.
 */
class IzinPeserta extends Model
{
    /**
     * @var array<string, string>
     */
    public const JENIS = [
        LogbookHarian::IZIN => 'Izin',
        LogbookHarian::SAKIT => 'Sakit',
    ];

    /**
     * @var array<string, string>
     */
    public const STATUS = [
        LogbookHarian::MENUNGGU => 'Menunggu',
        LogbookHarian::DITERIMA => 'Diterima',
        LogbookHarian::DITOLAK => 'Ditolak',
    ];

    protected $table = 'izin_pesertas';

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => LogbookHarian::MENUNGGU,
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'diverifikasi_pada' => 'datetime',
            'bukti_ukuran' => 'integer',
        ];
    }

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(LogbookHarian::class, 'izin_peserta_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function jumlahHari(): int
    {
        return (int) $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    public function labelJenis(): string
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function labelStatus(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function rentang(): string
    {
        return $this->tanggal_mulai->equalTo($this->tanggal_selesai)
            ? $this->tanggal_mulai->translatedFormat('d M Y')
            : $this->tanggal_mulai->translatedFormat('d M').' – '.$this->tanggal_selesai->translatedFormat('d M Y');
    }
}
