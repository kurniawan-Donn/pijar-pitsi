<?php

namespace App\Models;

use Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Pertanyaan yang sering diajukan di website publik.
 */
class Faq extends Model
{
    /** @use HasFactory<FaqFactory> */
    use HasFactory;

    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'kategori',
        'urutan',
        'tampil',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'tampil' => 'boolean',
    ];

    public function scopeTampil(Builder $query): Builder
    {
        return $query->where('tampil', true);
    }

    public function scopeBerurutan(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('id');
    }

    /**
     * Urutan untuk FAQ baru: paling bawah.
     */
    public static function urutanBerikutnya(): int
    {
        return (int) self::max('urutan') + 1;
    }
}
