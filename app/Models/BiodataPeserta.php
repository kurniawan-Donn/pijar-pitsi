<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Biodata peserta (sheet PROFIL MAHASISWA + rekening uang saku).
 *
 * Alur: draft → diajukan → disetujui / perlu_revisi. Peserta hanya bisa
 * mengubah saat draft atau perlu_revisi. Detail program disiapkan admin.
 */
class BiodataPeserta extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_DIAJUKAN = 'diajukan';

    public const STATUS_PERLU_REVISI = 'perlu_revisi';

    public const STATUS_DISETUJUI = 'disetujui';

    /**
     * @var array<string, string>
     */
    public const STATUS = [
        self::STATUS_DRAFT => 'Belum dikirim',
        self::STATUS_DIAJUKAN => 'Menunggu ditinjau',
        self::STATUS_PERLU_REVISI => 'Perlu revisi',
        self::STATUS_DISETUJUI => 'Disetujui',
    ];

    /**
     * @var array<string, string>
     */
    public const JENIS_KELAMIN = [
        'laki-laki' => 'Laki-laki',
        'perempuan' => 'Perempuan',
    ];

    /**
     * @var array<string, string>
     */
    public const JENIS_PROGRAM = [
        'mandiri' => 'Mahasiswa Magang Mandiri',
        'wajib_kampus' => 'Mahasiswa Magang Wajib dari Kampus',
        'mbkm' => 'MBKM / Kampus Merdeka',
        'lainnya' => 'Lainnya',
    ];

    /**
     * Field yang diisi peserta.
     *
     * @var array<int, string>
     */
    public const FIELD_PESERTA = [
        'nama_lengkap', 'nama_panggilan', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'nik',
        'alamat_ktp', 'alamat_domisili', 'tautan_maps_domisili', 'no_hp', 'email',
        'tautan_portofolio', 'tautan_instagram',
        'perguruan_tinggi', 'fakultas_jurusan', 'nim', 'semester', 'ipk',
        'dosen_pembimbing_nama', 'dosen_pembimbing_kontak', 'dosen_pembimbing_email',
        'keterampilan', 'bahasa', 'riwayat_kesehatan',
        'no_rekening', 'nama_bank', 'atas_nama_rekening',
        'pernyataan_setuju', 'pernyataan_tempat',
    ];

    /**
     * Field detail program yang disiapkan admin (terkunci untuk peserta).
     *
     * @var array<int, string>
     */
    public const FIELD_PROGRAM = [
        'posisi_dilamar', 'jenis_program', 'jenis_program_lainnya', 'durasi_bulan', 'mentor_magang',
    ];

    /**
     * Field wajib sebelum biodata dikirim, juga dasar persentase progres.
     *
     * @var array<string, string>
     */
    public const FIELD_WAJIB = [
        'nama_lengkap' => 'Nama lengkap',
        'jenis_kelamin' => 'Jenis kelamin',
        'tempat_lahir' => 'Tempat lahir',
        'tanggal_lahir' => 'Tanggal lahir',
        'nik' => 'NIK',
        'alamat_ktp' => 'Alamat KTP',
        'alamat_domisili' => 'Alamat domisili',
        'no_hp' => 'No. WhatsApp / HP',
        'email' => 'Email',
        'perguruan_tinggi' => 'Perguruan tinggi',
        'fakultas_jurusan' => 'Fakultas / jurusan',
        'nim' => 'NIM',
        'semester' => 'Semester',
        'ipk' => 'IPK',
        'dosen_pembimbing_nama' => 'Nama dosen pembimbing',
        'dosen_pembimbing_kontak' => 'Kontak dosen pembimbing',
        'keterampilan' => 'Keterampilan',
        'bahasa' => 'Bahasa yang dikuasai',
        'no_rekening' => 'Nomor rekening',
        'nama_bank' => 'Nama bank',
        'atas_nama_rekening' => 'Atas nama rekening',
    ];

    protected $table = 'biodata_pesertas';

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'pernyataan_setuju' => false,
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'pernyataan_tanggal' => 'date',
            'pernyataan_setuju' => 'boolean',
            'semester' => 'integer',
            'ipk' => 'decimal:2',
            'durasi_bulan' => 'integer',
            'keterampilan' => 'array',
            'bahasa' => 'array',
            'nik' => 'encrypted',
            'no_rekening' => 'encrypted',
            'riwayat_kesehatan' => 'encrypted:array',
            'diajukan_pada' => 'datetime',
            'ditinjau_pada' => 'datetime',
        ];
    }

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class);
    }

    public function peninjau(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditinjau_oleh');
    }

    /**
     * Biodata peserta, atau draft baru (belum disimpan) yang terisi dari
     * data pendaftaran & data peserta yang sudah ada.
     */
    public static function untuk(PesertaMagang $peserta): self
    {
        if ($peserta->biodata) {
            return $peserta->biodata;
        }

        $mahasiswa = $peserta->pengajuanMagang?->mahasiswa;

        return new self([
            'peserta_magang_id' => $peserta->id,
            'nama_lengkap' => $peserta->nama,
            'nim' => $peserta->nim ?: $mahasiswa?->nim,
            'perguruan_tinggi' => $peserta->asal_kampus ?: $mahasiswa?->asal_kampus,
            'fakultas_jurusan' => collect([$mahasiswa?->fakultas, $peserta->program_studi ?: $mahasiswa?->program_studi])->filter()->implode(' / ') ?: null,
            'email' => $mahasiswa?->email ?? $peserta->user?->email,
            'no_hp' => $mahasiswa?->no_hp,
            'alamat_domisili' => $mahasiswa?->alamat,
            'mentor_magang' => $peserta->pembimbing,
        ]);
    }

    public function bisaDiubahPeserta(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PERLU_REVISI], true);
    }

    public function labelStatus(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    /**
     * Field wajib yang masih kosong.
     *
     * @return array<string, string> [field => label]
     */
    public function fieldWajibKosong(): array
    {
        return array_filter(
            self::FIELD_WAJIB,
            fn (string $label, string $field) => ! $this->terisi($field),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Progres pengisian field wajib.
     *
     * @return array{terisi: int, total: int, persen: int}
     */
    public function progres(): array
    {
        $total = count(self::FIELD_WAJIB);
        $terisi = $total - count($this->fieldWajibKosong());

        return [
            'terisi' => $terisi,
            'total' => $total,
            'persen' => (int) floor($terisi / $total * 100),
        ];
    }

    /**
     * NIK dengan bagian tengah disamarkan, mis. 3518********0002.
     */
    public function nikTersamar(): ?string
    {
        $nik = (string) $this->nik;

        if ($nik === '') {
            return null;
        }

        return strlen($nik) <= 8
            ? str_repeat('*', strlen($nik))
            : substr($nik, 0, 4).str_repeat('*', strlen($nik) - 8).substr($nik, -4);
    }

    public function labelJenisProgram(): ?string
    {
        if ($this->jenis_program === 'lainnya') {
            return $this->jenis_program_lainnya ?: 'Lainnya';
        }

        return self::JENIS_PROGRAM[$this->jenis_program] ?? null;
    }

    private function terisi(string $field): bool
    {
        $nilai = $this->getAttribute($field);

        if (is_array($nilai)) {
            return count(array_filter($nilai, fn ($item) => trim((string) $item) !== '')) > 0;
        }

        return trim((string) $nilai) !== '';
    }
}
