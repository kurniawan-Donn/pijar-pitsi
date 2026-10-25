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
        'buat_akun_peserta' => 'Buat akun peserta',
        'reset_password_peserta' => 'Reset password peserta',
        'ubah_status_akun_peserta' => 'Ubah status akun peserta',
        'hapus_akun_peserta' => 'Hapus akun peserta',
        'ubah_pengaturan_magang' => 'Ubah pengaturan magang',
        'tambah_tarif_uang_saku' => 'Tambah tarif uang saku',
        'hapus_tarif_uang_saku' => 'Hapus tarif uang saku',
        'tambah_hari_libur' => 'Tambah hari libur',
        'hapus_hari_libur' => 'Hapus hari libur',
        'simpan_divisi' => 'Simpan divisi',
        'hapus_divisi' => 'Hapus divisi',
        'ubah_penempatan' => 'Ubah penempatan divisi',
        'ajukan_biodata' => 'Kirim biodata',
        'ubah_program_biodata' => 'Ubah detail program biodata',
        'tinjau_biodata' => 'Tinjau biodata',
        'verifikasi_logbook_harian' => 'Verifikasi logbook harian',
        'verifikasi_izin' => 'Verifikasi izin/sakit',
        'buka_kunci_logbook' => 'Buka kunci logbook',
        'impor_logbook' => 'Impor riwayat logbook',
        'hapus_impor_logbook' => 'Hapus hasil impor logbook',
        'kirim_feedback' => 'Kirim feedback magang',
        'terbitkan_surat' => 'Terbitkan surat keterangan',
        'terbitkan_ulang_surat' => 'Terbitkan ulang surat keterangan',
        'hapus_pengguna' => 'Hapus pengguna',
        'ubah_pengguna' => 'Ubah pengguna',
        'hapus_banner' => 'Hapus banner',
        'ubah_password' => 'Ganti password',
        'ganti_foto_profil' => 'Ganti foto profil',
        'hapus_foto_profil' => 'Hapus foto profil',
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
