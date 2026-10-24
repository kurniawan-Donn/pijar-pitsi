<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Surat keterangan magang. Syarat terbit: biodata disetujui, penilaian
 * sudah diisi, dan feedback sudah dikirim. Setelah terbit, feedback peserta
 * terkunci dan surat bisa diunduh peserta.
 */
class SuratKeterangan extends Model
{
    public const DRAFT = 'draft';

    public const TERBIT = 'terbit';

    private const BULAN_ROMAWI = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    protected $table = 'surat_keterangans';

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => self::DRAFT,
        'tampilkan_nilai' => false,
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'tanggal_surat' => 'date',
            'tampilkan_nilai' => 'boolean',
            'diterbitkan_pada' => 'datetime',
            'tahun' => 'integer',
            'urut' => 'integer',
        ];
    }

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }

    public function sudahTerbit(): bool
    {
        return $this->status === self::TERBIT;
    }

    /**
     * Syarat terbit yang belum terpenuhi.
     *
     * @return array<int, string>
     */
    public static function syaratBelumTerpenuhi(PesertaMagang $peserta): array
    {
        $peserta->loadMissing(['biodata', 'penilaian', 'feedback']);

        return array_values(array_filter([
            $peserta->status !== 'selesai' ? 'Status peserta belum "selesai".' : null,
            $peserta->biodata?->status !== BiodataPeserta::STATUS_DISETUJUI ? 'Biodata belum disetujui.' : null,
            ! $peserta->penilaian ? 'Penilaian akhir belum diisi.' : null,
            ! $peserta->feedback ? 'Peserta belum mengirim feedback.' : null,
        ]));
    }

    /**
     * Nomor urut berikutnya pada satu tahun.
     */
    public static function urutBerikutnya(int $tahun): int
    {
        return (int) self::where('tahun', $tahun)->max('urut') + 1;
    }

    /**
     * Nomor surat dari format di Pengaturan Magang,
     * mis. "{urut}/SKM/{bulan_romawi}/{tahun}" → "007/SKM/XI/2026".
     */
    public static function formatNomor(string $format, int $urut, DateTimeInterface $tanggal): string
    {
        return strtr($format, [
            '{urut}' => sprintf('%03d', $urut),
            '{bulan_romawi}' => self::BULAN_ROMAWI[(int) $tanggal->format('n')],
            '{bulan}' => $tanggal->format('m'),
            '{tahun}' => $tanggal->format('Y'),
        ]);
    }

    /**
     * Salinan isi surat saat diterbitkan.
     *
     * @return array<string, mixed>
     */
    public static function snapshot(PesertaMagang $peserta, CarbonImmutable $tanggalSurat, bool $tampilkanNilai): array
    {
        $peserta->loadMissing(['biodata', 'penilaian', 'penempatans.divisi']);
        $pengaturan = PengaturanMagang::ambil();
        $perusahaan = Perusahaan::query()->first();
        $biodata = $peserta->biodata;

        return [
            'nama' => $biodata?->nama_lengkap ?: $peserta->nama,
            'nim' => $biodata?->nim ?: $peserta->nim,
            'perguruan_tinggi' => $biodata?->perguruan_tinggi ?: $peserta->asal_kampus,
            'fakultas_jurusan' => $biodata?->fakultas_jurusan ?: $peserta->program_studi,
            'nomor_peserta' => $peserta->nomor_peserta,
            'periode_mulai' => $peserta->periode_mulai?->toDateString(),
            'periode_selesai' => $peserta->periode_selesai?->toDateString(),
            'divisi' => $peserta->penempatans->sortBy('periode')->map(fn (PenempatanPeserta $penempatan) => $penempatan->divisi?->nama)->filter()->unique()->values()->all(),
            'nilai_akhir' => $tampilkanNilai ? $peserta->penilaian?->nilai_akhir : null,
            'predikat' => $tampilkanNilai ? $peserta->penilaian?->predikat : null,
            'perusahaan' => [
                'nama' => $perusahaan?->nama_perusahaan ?: ($perusahaan?->nama ?: config('app.name')),
                'alamat' => $perusahaan?->alamat,
                'kota' => $perusahaan?->kota,
                'telepon' => $perusahaan?->no_telepon ?? $perusahaan?->telepon,
                'email' => $perusahaan?->email,
                'website' => $perusahaan?->website,
            ],
            'kota_surat' => $pengaturan->kota_surat ?: $perusahaan?->kota,
            'penanda_tangan' => [
                'nama' => $pengaturan->menyetujui_nama,
                'jabatan' => $pengaturan->menyetujui_jabatan,
            ],
            'tanggal_surat' => $tanggalSurat->toDateString(),
        ];
    }
}
