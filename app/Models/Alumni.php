<?php

namespace App\Models;

use Database\Factories\AlumniFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Alumni magang di website publik: dari peserta (dengan persetujuan di
 * feedback) atau diinput manual untuk alumni lama.
 */
class Alumni extends Model
{
    /** @use HasFactory<AlumniFactory> */
    use HasFactory;

    /**
     * Folder foto di disk "public" (hanya foto alumni yang ditampilkan).
     */
    public const FOLDER_FOTO = 'alumni';

    /**
     * Nomor pertanyaan skala feedback yang menjadi rating (kepuasan keseluruhan).
     */
    public const NOMOR_SKOR_RATING = 10;

    protected $table = 'alumnis';

    protected $fillable = [
        'peserta_magang_id',
        'nama',
        'universitas',
        'program_studi',
        'divisi',
        'periode',
        'rating',
        'judul_project',
        'deskripsi_project',
        'testimoni',
        'urutan',
        'tampil',
    ];

    protected $casts = [
        'rating' => 'integer',
        'urutan' => 'integer',
        'tampil' => 'boolean',
    ];

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    /**
     * Alumni yang boleh tampil di website: dicentang admin, dan untuk alumni
     * dari peserta, pesertanya masih bersedia.
     */
    public function scopePublik(Builder $query): Builder
    {
        return $query->where('tampil', true)
            ->where(fn (Builder $sub) => $sub
                ->whereNull('peserta_magang_id')
                ->orWhereHas('pesertaMagang.feedback', fn (Builder $feedback) => $feedback->where('bersedia_tampil_alumni', true)));
    }

    public function scopeBerurutan(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('id');
    }

    public static function urutanBerikutnya(): int
    {
        return (int) self::max('urutan') + 1;
    }

    public function dariPeserta(): bool
    {
        return $this->peserta_magang_id !== null;
    }

    /**
     * Peserta masih menyetujui ditampilkan (selalu true untuk alumni manual).
     */
    public function disetujuiPeserta(): bool
    {
        return ! $this->dariPeserta() || (bool) $this->pesertaMagang?->feedback?->bersedia_tampil_alumni;
    }

    public function tampilDiWebsite(): bool
    {
        return $this->tampil && $this->disetujuiPeserta();
    }

    /**
     * Rating 1–5: dari feedback (kepuasan keseluruhan) untuk alumni dari
     * peserta, atau isian admin untuk alumni manual. Bukan nilai penilaian.
     */
    public function nilaiRating(): ?int
    {
        if (! $this->dariPeserta()) {
            return $this->rating;
        }

        $skor = $this->pesertaMagang?->feedback?->skor[self::NOMOR_SKOR_RATING] ?? null;

        return $skor === null ? null : (int) $skor;
    }

    public function urlFoto(): ?string
    {
        // asset() mengikuti alamat yang sedang dibuka; Storage::url() terikat APP_URL.
        return $this->foto_path ? asset('storage/'.$this->foto_path) : null;
    }

    /**
     * Isian awal dari data peserta (admin tinggal melengkapi project).
     *
     * @return array<string, mixed>
     */
    public static function isianDariPeserta(PesertaMagang $peserta): array
    {
        $peserta->loadMissing(['biodata', 'feedback', 'penempatans.divisi']);
        $biodata = $peserta->biodata;

        $periode = $peserta->periode_mulai && $peserta->periode_selesai
            ? $peserta->periode_mulai->translatedFormat('F').' – '.$peserta->periode_selesai->translatedFormat('F Y')
            : null;

        return [
            'peserta_magang_id' => $peserta->id,
            'nama' => $biodata?->nama_lengkap ?: $peserta->nama,
            'universitas' => $biodata?->perguruan_tinggi ?: $peserta->asal_kampus,
            'program_studi' => $biodata?->fakultas_jurusan ?: $peserta->program_studi,
            'divisi' => $peserta->penempatans->sortBy('periode')->last()?->divisi?->nama,
            'periode' => $periode,
            'testimoni' => $peserta->feedback?->paling_disukai,
        ];
    }
}
