<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\ScanKartuAbsensiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu kali scan foto kartu absensi (satu sisi, satu bulan). Hasil yang disimpan
 * sudah divalidasi server; respons mentah AI tidak disimpan.
 */
class ScanKartuAbsensi extends Model
{
    /** @use HasFactory<ScanKartuAbsensiFactory> */
    use HasFactory;

    public const SISI_AWAL = '1-15';

    public const SISI_AKHIR = '16-31';

    /**
     * @var array<string, string>
     */
    public const SISI = [
        self::SISI_AWAL => 'Depan (tanggal 1–15)',
        self::SISI_AKHIR => 'Belakang (tanggal 16–31)',
    ];

    public const DIPRATINJAU = 'dipratinjau';

    public const DISIMPAN = 'disimpan';

    public const GAGAL = 'gagal';

    public const FOLDER = 'scan-kartu';

    protected $table = 'scan_kartu_absensis';

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => self::DIPRATINJAU,
    ];

    protected function casts(): array
    {
        return [
            'hasil' => 'array',
            'tahun' => 'integer',
            'bulan' => 'integer',
            'jumlah_terbaca' => 'integer',
            'jumlah_disimpan' => 'integer',
            'token_masuk' => 'integer',
            'token_keluar' => 'integer',
            'disimpan_pada' => 'datetime',
        ];
    }

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(LogbookHarian::class, 'scan_kartu_absensi_id');
    }

    /**
     * Tanggal pertama dan terakhir sisi ini pada bulan kartu.
     *
     * @return array{int, int}
     */
    public static function rentangSisi(string $sisi, int $tahun, int $bulan): array
    {
        $hariTerakhir = CarbonImmutable::create($tahun, $bulan, 1)->daysInMonth;

        return $sisi === self::SISI_AWAL ? [1, 15] : [16, $hariTerakhir];
    }

    /**
     * @return array{int, int}
     */
    public function rentang(): array
    {
        return self::rentangSisi($this->sisi, $this->tahun, $this->bulan);
    }

    public function tanggal(int $hari): CarbonImmutable
    {
        return CarbonImmutable::create($this->tahun, $this->bulan, $hari)->startOfDay();
    }

    public function labelBulan(): string
    {
        return CarbonImmutable::create($this->tahun, $this->bulan, 1)->translatedFormat('F Y');
    }

    public function labelSisi(): string
    {
        return self::SISI[$this->sisi] ?? $this->sisi;
    }

    public function sudahDisimpan(): bool
    {
        return $this->status === self::DISIMPAN;
    }
}
