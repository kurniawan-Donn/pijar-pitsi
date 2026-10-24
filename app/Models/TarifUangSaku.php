<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tarif uang saku yang berlaku mulai tanggal tertentu.
 */
class TarifUangSaku extends Model
{
    protected $table = 'tarif_uang_sakus';

    protected $fillable = [
        'berlaku_mulai',
        'tarif_hari_penuh',
        'tarif_setengah_hari',
        'dibuat_oleh',
    ];

    protected $casts = [
        'berlaku_mulai' => 'date',
        'tarif_hari_penuh' => 'integer',
        'tarif_setengah_hari' => 'integer',
    ];

    /**
     * Tarif yang berlaku pada tanggal tertentu. Bila tabel kosong, dipakai
     * tarif awal dari config (tidak disimpan).
     */
    public static function berlakuPada(DateTimeInterface $tanggal): self
    {
        return self::query()
            // whereDate: kolom bisa tersimpan sebagai "Y-m-d 00:00:00".
            ->whereDate('berlaku_mulai', '<=', $tanggal->format('Y-m-d'))
            ->orderByDesc('berlaku_mulai')
            ->first()
            ?? new self([
                'berlaku_mulai' => '2000-01-01',
                'tarif_hari_penuh' => (int) config('absensi.uang_saku.hari_penuh'),
                'tarif_setengah_hari' => (int) config('absensi.uang_saku.setengah_hari'),
            ]);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
