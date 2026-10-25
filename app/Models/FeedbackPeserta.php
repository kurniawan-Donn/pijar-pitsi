<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Feedback peserta tentang program magang. Dibuka mulai sebulan sebelum
 * tanggal selesai magang (config feedback.dibuka_bulan_sebelum_selesai)
 * dan boleh diedit/dikirim ulang.
 */
class FeedbackPeserta extends Model
{
    protected $table = 'feedback_pesertas';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'skor' => 'array',
            'pernyataan' => 'boolean',
            'bersedia_tampil_alumni' => 'boolean',
            'dikirim_pada' => 'datetime',
            'jumlah_kirim' => 'integer',
        ];
    }

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    /**
     * Tanggal feedback mulai bisa diisi, atau null bila tanggal selesai magang belum diatur.
     */
    public static function dibukaPada(PesertaMagang $peserta): ?CarbonImmutable
    {
        if (! $peserta->periode_selesai) {
            return null;
        }

        return CarbonImmutable::instance($peserta->periode_selesai)
            ->startOfDay()
            ->subMonthsNoOverflow((int) config('feedback.dibuka_bulan_sebelum_selesai', 1));
    }

    public static function sudahDibuka(PesertaMagang $peserta): bool
    {
        $dibuka = self::dibukaPada($peserta);

        return $dibuka !== null && CarbonImmutable::today()->gte($dibuka);
    }

    /**
     * Feedback terkunci setelah surat keterangan diterbitkan.
     */
    public static function terkunci(PesertaMagang $peserta): bool
    {
        return (bool) $peserta->suratKeterangan?->sudahTerbit();
    }

    /**
     * Rata-rata skor 1–5.
     */
    public function rataRata(): float
    {
        $skor = array_map('intval', (array) $this->skor);

        return $skor ? round(array_sum($skor) / count($skor), 2) : 0.0;
    }

    public function labelRekomendasi(): string
    {
        return config('feedback.rekomendasi')[$this->rekomendasi] ?? $this->rekomendasi;
    }
}
