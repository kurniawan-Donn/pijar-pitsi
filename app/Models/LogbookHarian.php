<?php

namespace App\Models;

use App\Services\RekapAbsensi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu hari logbook peserta (format sheet bulanan September).
 */
class LogbookHarian extends Model
{
    public const HADIR = 'hadir';

    public const IZIN = 'izin';

    public const SAKIT = 'sakit';

    public const LIBUR = 'libur';

    /**
     * @var array<string, string>
     */
    public const KEHADIRAN = [
        self::HADIR => 'Hadir',
        self::IZIN => 'Izin',
        self::SAKIT => 'Sakit',
        self::LIBUR => 'Libur',
    ];

    public const MENUNGGU = 'menunggu';

    public const DITERIMA = 'diterima';

    public const DITOLAK = 'ditolak';

    public const REVIEW = 'review';

    /**
     * Status verifikasi; tiga terakhir sama dengan pilihan Action di sheet.
     *
     * @var array<string, string>
     */
    public const STATUS_VERIFIKASI = [
        self::MENUNGGU => 'Menunggu',
        self::DITERIMA => 'Diterima',
        self::DITOLAK => 'Ditolak',
        self::REVIEW => 'Review',
    ];

    public const KOLOM_JAM = ['pagi_masuk', 'pagi_keluar', 'siang_masuk', 'siang_keluar'];

    /**
     * Sumber jam: diketik peserta atau dibaca dari kartu absensi.
     */
    public const SUMBER_MANUAL = 'manual';

    public const SUMBER_SCAN = 'scan';

    protected $table = 'logbook_harians';

    protected $guarded = ['id'];

    protected $attributes = [
        'status_verifikasi' => self::MENUNGGU,
        'sumber' => 'aplikasi',
        'sumber_jam' => self::SUMBER_MANUAL,
        'di_luar_batas_isi' => false,
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'diverifikasi_pada' => 'datetime',
            'jam_scan_asli' => 'array',
            'di_luar_batas_isi' => 'boolean',
        ];
    }

    public function scanKartu(): BelongsTo
    {
        return $this->belongsTo(ScanKartuAbsensi::class, 'scan_kartu_absensi_id');
    }

    public function dariScan(): bool
    {
        return $this->sumber_jam === self::SUMBER_SCAN;
    }

    /**
     * Jam masuk/keluar sekarang berbeda dari nilai asli hasil scan.
     */
    public function diubahSetelahScan(): bool
    {
        if (! $this->dariScan() || ! is_array($this->jam_scan_asli)) {
            return false;
        }

        return ($this->jam_scan_asli['pagi_masuk'] ?? '') !== $this->jam('pagi_masuk')
            || ($this->jam_scan_asli['siang_keluar'] ?? '') !== $this->jam('siang_keluar');
    }

    /**
     * Hadir tetapi activity belum diisi (mis. dibuat dari scan kartu): belum boleh diterima admin.
     */
    public function activityBelumDiisi(): bool
    {
        return $this->kehadiran === self::HADIR && blank($this->activity);
    }

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    public function buktis(): HasMany
    {
        return $this->hasMany(LogbookBukti::class);
    }

    public function izin(): BelongsTo
    {
        return $this->belongsTo(IzinPeserta::class, 'izin_peserta_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /**
     * Jam dalam format HH:MM (kosong bila tidak diisi).
     */
    public function jam(string $kolom): string
    {
        return RekapAbsensi::normalisasiJam($this->getAttribute($kolom));
    }

    /**
     * hari_penuh, setengah_hari, atau tidak_lengkap (hanya bermakna untuk hadir).
     */
    public function jenisKehadiran(): string
    {
        return RekapAbsensi::jenisKehadiran(collect(self::KOLOM_JAM)->mapWithKeys(fn ($kolom) => [$kolom => $this->jam($kolom)])->all());
    }

    /**
     * Peserta boleh mengubah: bukan bagian izin/sakit dan belum diterima.
     * (Batas tanggal dicek terpisah oleh KalenderLogbook.)
     */
    public function bisaDiubahPeserta(): bool
    {
        return $this->izin_peserta_id === null && $this->status_verifikasi !== self::DITERIMA;
    }

    public function labelKehadiran(): string
    {
        return self::KEHADIRAN[$this->kehadiran] ?? $this->kehadiran;
    }

    public function labelStatus(): string
    {
        return self::STATUS_VERIFIKASI[$this->status_verifikasi] ?? $this->status_verifikasi;
    }
}
