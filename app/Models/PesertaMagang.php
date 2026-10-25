<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PesertaMagang extends Model
{
    protected $table = 'peserta_magangs';

    protected $fillable = [
        'nama',
        'nim',
        'asal_kampus',
        'program_studi',
        'periode_mulai',
        'periode_selesai',
        'pembimbing',
        'status',
        'google_sheet_url',
        'pengajuan_magang_id',
        'user_id',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    /**
     * Nomor peserta dibuat otomatis saat data peserta dibuat.
     */
    protected static function booted(): void
    {
        static::creating(function (PesertaMagang $peserta) {
            $peserta->nomor_peserta ??= self::nomorPesertaBaru($peserta->periode_mulai ?? now());
        });

        // Draft surat keterangan dibuat otomatis saat magang selesai.
        static::saved(function (PesertaMagang $peserta) {
            if ($peserta->status === 'selesai' && ($peserta->wasRecentlyCreated || $peserta->wasChanged('status'))) {
                SuratKeterangan::firstOrCreate(['peserta_magang_id' => $peserta->id]);
            }
        });
    }

    /**
     * Nomor berikutnya untuk bulan mulai magang: MG{tahun}{bulan}{urut 3 digit}.
     */
    public static function nomorPesertaBaru(DateTimeInterface $mulai): string
    {
        $awalan = 'MG'.$mulai->format('Ym');

        $terakhir = self::where('nomor_peserta', 'like', $awalan.'%')->max('nomor_peserta');
        $urut = $terakhir ? (int) substr($terakhir, strlen($awalan)) + 1 : 1;

        return sprintf('%s%03d', $awalan, $urut);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function biodata(): HasOne
    {
        return $this->hasOne(BiodataPeserta::class);
    }

    public function suratKeterangan(): HasOne
    {
        return $this->hasOne(SuratKeterangan::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(FeedbackPeserta::class);
    }

    /**
     * Data alumni di website (bila admin sudah menambahkannya).
     */
    public function alumni(): HasOne
    {
        return $this->hasOne(Alumni::class);
    }

    public function logbookHarians(): HasMany
    {
        return $this->hasMany(LogbookHarian::class);
    }

    public function izins(): HasMany
    {
        return $this->hasMany(IzinPeserta::class);
    }

    public function bukaKunciLogbooks(): HasMany
    {
        return $this->hasMany(BukaKunciLogbook::class);
    }

    public function penempatans(): HasMany
    {
        return $this->hasMany(PenempatanPeserta::class);
    }

    /**
     * Penempatan yang berlaku untuk suatu periode: baris periode itu, atau
     * baris periode terakhir sebelumnya (rotasi berlanjut sampai diubah).
     */
    public function penempatanUntuk(string $kunciPeriode): ?PenempatanPeserta
    {
        return $this->penempatans
            ->filter(fn (PenempatanPeserta $penempatan) => $penempatan->periode <= $kunciPeriode)
            ->sortByDesc('periode')
            ->first();
    }

    public function penilaian(): HasOne
    {
        return $this->hasOne(PenilaianPeserta::class);
    }

    public function pengajuanMagang(): BelongsTo
    {
        return $this->belongsTo(PengajuanMagang::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(
            Absensi::class,
            'peserta_magang_id'
        );
    }
}
