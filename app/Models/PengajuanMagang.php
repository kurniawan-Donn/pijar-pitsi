<?php

namespace App\Models;

use Database\Factories\PengajuanMagangFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengajuanMagang extends Model
{
    /** @use HasFactory<PengajuanMagangFactory> */
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    /**
     * Label status untuk ditampilkan di UI.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        self::STATUS_MENUNGGU => 'Menunggu',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_DITOLAK => 'Ditolak',
    ];

    protected $fillable = [
        'mahasiswa_id',
        'perusahaan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'posisi',
        'status',
        'keterangan',
        'dokumen_path',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function pesertaMagang(): HasOne
    {
        return $this->hasOne(PesertaMagang::class);
    }

    /**
     * Filter berdasarkan status (diabaikan bila kosong / tidak dikenal).
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! array_key_exists((string) $status, self::STATUS_LABELS)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Pencarian nama atau NIM mahasiswa.
     */
    public function scopeCari(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $query;
        }

        return $query->whereHas('mahasiswa', function (Builder $mahasiswa) use ($keyword) {
            $mahasiswa->where('nama', 'like', "%{$keyword}%")
                ->orWhere('nim', 'like', "%{$keyword}%");
        });
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * Nomor pengajuan yang mudah dibaca, contoh: MG-000012.
     */
    public function nomor(): string
    {
        return 'MG-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * ID pengajuan dari nomor yang diketik pendaftar ("MG-000012", "mg000012").
     */
    public static function idDariNomor(string $nomor): ?int
    {
        if (! preg_match('/^MG-?(\d{1,10})$/i', trim($nomor), $cocok)) {
            return null;
        }

        return (int) $cocok[1] ?: null;
    }

    /**
     * Tampilkan surat pengantar dari disk privat; 404 bila file tidak ada.
     */
    public function responDokumen(): StreamedResponse
    {
        abort_unless(
            $this->dokumen_path && Storage::disk('local')->exists($this->dokumen_path),
            404,
            'Dokumen tidak ditemukan.'
        );

        $extension = pathinfo($this->dokumen_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->response(
            $this->dokumen_path,
            "surat-pengantar-{$this->nomor()}.{$extension}"
        );
    }
}
