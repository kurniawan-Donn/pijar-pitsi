<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenilaianPeserta extends Model
{
    protected $table = 'penilaian_pesertas';

    protected $fillable = [
        'peserta_magang_id',
        'nilai_akhir',
        'predikat',
        'catatan_pembimbing',
        'kekuatan',
        'aspek_perbaikan',
        'langkah_perbaikan',
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
     * Rata-rata nilai per kelompok (sheet PENILAIAN MHS MAGANG).
     *
     * @param  iterable<array{kelompok: string, bobot_kelompok: int|float, nilai: int|float}>  $nilaiAspek
     * @return array<string, array{rata_rata: float, total: int|float, jumlah: int, bobot: int|float}>
     */
    public static function rataPerKelompok(iterable $nilaiAspek): array
    {
        $kelompok = [];

        foreach ($nilaiAspek as $aspek) {
            $nama = $aspek['kelompok'];
            $kelompok[$nama] ??= ['total' => 0, 'jumlah' => 0, 'bobot' => $aspek['bobot_kelompok']];
            $kelompok[$nama]['total'] += $aspek['nilai'];
            $kelompok[$nama]['jumlah']++;
        }

        foreach ($kelompok as $nama => $data) {
            $kelompok[$nama]['rata_rata'] = round($data['total'] / $data['jumlah'], 2);
        }

        return $kelompok;
    }

    /**
     * Nilai akhir = Σ(rata-rata kelompok × bobot kelompok) ÷ Σbobot,
     * mis. (rata-rata Hard Skill × 60%) + (rata-rata Soft Skill × 40%).
     *
     * @param  iterable<array{kelompok: string, bobot_kelompok: int|float, nilai: int|float}>  $nilaiAspek
     */
    public static function hitungNilaiAkhir(iterable $nilaiAspek): float
    {
        $totalBobot = 0;
        $totalNilai = 0;

        foreach (self::rataPerKelompok($nilaiAspek) as $kelompok) {
            $totalBobot += $kelompok['bobot'];
            $totalNilai += ($kelompok['total'] / $kelompok['jumlah']) * $kelompok['bobot'];
        }

        return $totalBobot > 0 ? round($totalNilai / $totalBobot, 2) : 0.0;
    }

    /**
     * Nama predikat untuk sebuah nilai akhir (tabel predikat_penilaians).
     */
    public static function predikatUntuk(float $nilai): string
    {
        return PredikatPenilaian::untuk($nilai)?->nama ?? '-';
    }

    /**
     * Nilai per kelompok dari detail yang tersimpan; detail lama tanpa
     * kelompok dikumpulkan sebagai "Aspek".
     *
     * @return array<string, array{rata_rata: float, total: int|float, jumlah: int, bobot: int|float}>
     */
    public function nilaiPerKelompok(): array
    {
        return self::rataPerKelompok($this->details->map(fn (PenilaianDetail $detail) => [
            'kelompok' => $detail->kelompok ?? 'Aspek',
            'bobot_kelompok' => $detail->bobot_kelompok ?? 100,
            'nilai' => $detail->nilai,
        ]));
    }
}
