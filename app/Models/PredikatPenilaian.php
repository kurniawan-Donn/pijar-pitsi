<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Predikat nilai akhir; berlaku untuk nilai >= batas_bawah.
 */
class PredikatPenilaian extends Model
{
    protected $table = 'predikat_penilaians';

    protected $fillable = [
        'batas_bawah',
        'nama',
        'keterangan',
    ];

    protected $casts = [
        'batas_bawah' => 'float',
    ];

    /**
     * Predikat untuk sebuah nilai akhir (null bila tabel kosong).
     */
    public static function untuk(float $nilai): ?self
    {
        return self::where('batas_bawah', '<=', round($nilai, 2))->orderByDesc('batas_bawah')->first()
            ?? self::orderBy('batas_bawah')->first();
    }

    /**
     * Rentang tampilan, mis. "20,01 – 40".
     */
    public function rentang(?self $berikutnya): string
    {
        $angka = fn (float $nilai) => rtrim(rtrim(number_format($nilai, 2, ',', ''), '0'), ',');

        return $angka($this->batas_bawah).' – '.($berikutnya ? $angka($berikutnya->batas_bawah - 0.01) : '100');
    }
}
