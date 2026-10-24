<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenilaianPeserta extends Model
{
    /**
     * Batas bawah nilai untuk setiap predikat.
     *
     * @var array<string, int>
     */
    public const PREDIKAT = [
        'A' => 85,
        'B' => 75,
        'C' => 65,
        'D' => 50,
        'E' => 0,
    ];

    protected $table = 'penilaian_pesertas';

    protected $fillable = [
        'peserta_magang_id',
        'nilai_akhir',
        'predikat',
        'catatan_pembimbing',
        'dinilai_oleh',
        'dinilai_pada',
    ];

    protected $casts = [
        'nilai_akhir' => 'float',
        'dinilai_pada' => 'datetime',
    ];

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenilaianDetail::class)->orderBy('id');
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }

    /**
     * Nilai akhir = Σ(nilai × bobot) ÷ Σbobot, dibulatkan 2 desimal.
     *
     * @param  iterable<array{nilai: int|float, bobot: int|float}>  $nilaiAspek
     */
    public static function hitungNilaiAkhir(iterable $nilaiAspek): float
    {
        $totalBobot = 0;
        $totalNilai = 0;

        foreach ($nilaiAspek as $aspek) {
            $totalBobot += $aspek['bobot'];
            $totalNilai += $aspek['nilai'] * $aspek['bobot'];
        }

        return $totalBobot > 0 ? round($totalNilai / $totalBobot, 2) : 0.0;
    }

    /**
     * Predikat huruf untuk sebuah nilai akhir.
     */
    public static function predikatUntuk(float $nilai): string
    {
        foreach (self::PREDIKAT as $predikat => $batasBawah) {
            if ($nilai >= $batasBawah) {
                return $predikat;
            }
        }

        return 'E';
    }
}
