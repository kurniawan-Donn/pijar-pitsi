<?php

namespace App\Models;

use Database\Factories\DokumentasiFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Foto dokumentasi kegiatan yang tampil sebagai galeri di beranda.
 */
class Dokumentasi extends Model
{
    /** @use HasFactory<DokumentasiFactory> */
    use HasFactory;

    public const FOLDER = 'dokumentasi';

    protected $table = 'dokumentasis';

    protected $fillable = [
        'judul',
        'keterangan',
        'tanggal_kegiatan',
        'urutan',
        'tampil',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'date',
            'urutan' => 'integer',
            'tampil' => 'boolean',
        ];
    }

    public function scopeTampil(Builder $query): Builder
    {
        return $query->where('tampil', true);
    }

    public function scopeBerurutan(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('id');
    }

    /**
     * Urutan untuk dokumentasi baru: paling akhir.
     */
    public static function urutanBerikutnya(): int
    {
        return (int) self::max('urutan') + 1;
    }

    /**
     * URL foto besar (lightbox). asset() mengikuti alamat yang sedang dibuka.
     */
    public function urlFoto(): string
    {
        return asset('storage/'.$this->foto_path);
    }

    /**
     * URL thumbnail untuk galeri.
     */
    public function urlThumbnail(): string
    {
        return asset('storage/'.$this->thumbnail_path);
    }

    /**
     * Tanggal kegiatan yang dibaca manusia, mis. "12 Agustus 2026".
     */
    public function labelTanggal(): ?string
    {
        return $this->tanggal_kegiatan?->translatedFormat('d F Y');
    }
}
