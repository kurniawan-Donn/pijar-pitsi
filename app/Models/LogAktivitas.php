<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LogAktivitas extends Model
{
    /**
     * Label aksi untuk ditampilkan.
     *
     * @var array<string, string>
     */
    public const AKSI = [
        'ubah_status_pengajuan' => 'Ubah status pengajuan',
        'hapus_pengajuan' => 'Hapus pengajuan',
        'buat_peserta' => 'Buat peserta dari pengajuan',
        'ubah_absensi' => 'Ubah absensi',
        'verifikasi_logbook' => 'Verifikasi logbook',
        'simpan_penilaian' => 'Simpan penilaian',
        'ubah_aspek_penilaian' => 'Ubah aspek penilaian',
        'hapus_peserta' => 'Hapus peserta',
        'hapus_pengguna' => 'Hapus pengguna',
        'ubah_pengguna' => 'Ubah pengguna',
        'hapus_banner' => 'Hapus banner',
        'ubah_password' => 'Ganti password',
    ];

    public const UPDATED_AT = null;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'nama_pengguna',
        'aksi',
        'subjek_type',
        'subjek_id',
        'keterangan',
        'data',
        'ip_address',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Catat satu aktivitas oleh pengguna yang sedang login.
     *
     * @param  array<string, mixed>  $data  detail tambahan (mis. nilai lama & baru)
     */
    public static function catat(string $aksi, ?Model $subjek, string $keterangan, array $data = []): self
    {
        $user = auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'nama_pengguna' => $user?->name,
            'aksi' => $aksi,
            'subjek_type' => $subjek?->getMorphClass(),
            'subjek_id' => $subjek?->getKey(),
            'keterangan' => $keterangan,
            'data' => $data ?: null,
            'ip_address' => request()?->ip(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjek(): MorphTo
    {
        return $this->morphTo();
    }

    public function labelAksi(): string
    {
        return self::AKSI[$this->aksi] ?? $this->aksi;
    }
}
