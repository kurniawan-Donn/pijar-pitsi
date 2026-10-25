<?php

namespace App\Services;

use App\Models\BiodataPeserta;
use App\Models\FeedbackPeserta;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PengaturanMagang;
use App\Models\PesertaMagang;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Data dashboard peserta: logbook hari ini, rekap periode berjalan, logbook
 * yang perlu diperbaiki, biodata, penempatan, kalender mini, dan pengingat.
 */
class RingkasanPeserta
{
    /**
     * Tanggal yang akan terkunci dalam sekian hari ke depan ditandai "hampir terkunci".
     */
    public const HARI_PERINGATAN_KUNCI = 2;

    /**
     * Warna kalender mini: [kunci => label].
     */
    public const LEGENDA = [
        'diterima' => 'Diterima',
        'menunggu' => 'Menunggu / Review',
        'ditolak' => 'Ditolak',
        'izin' => 'Izin / Sakit',
        'libur' => 'Libur',
        'belum' => 'Belum diisi',
        'terkunci' => 'Terkunci',
    ];

    public function __construct(private KalenderLogbook $kalender) {}

    /**
     * @return array<string, mixed>
     */
    public function untuk(PesertaMagang $peserta): array
    {
        $peserta->loadMissing(['biodata', 'feedback', 'suratKeterangan', 'pengajuanMagang.mahasiswa', 'user']);

        $hariIni = CarbonImmutable::today();
        $periode = $this->kalender->pilihPeriode($peserta);
        $hari = $periode ? $this->kalender->hari($peserta, $periode) : [];
        $penempatan = $periode ? $peserta->penempatanUntuk($periode['kunci']) : null;

        return [
            'hari_ini' => $this->hariIni($peserta, $hari, $hariIni),
            'periode' => $periode,
            'rekap' => $this->kalender->rekap($hari),
            'kalender' => $this->kalender($hari),
            'perlu_diperbaiki' => LogbookHarian::where('peserta_magang_id', $peserta->id)
                ->whereIn('status_verifikasi', [LogbookHarian::DITOLAK, LogbookHarian::REVIEW])
                ->orderByDesc('tanggal')
                ->limit(10)
                ->get(),
            'hampir_terkunci' => $this->hampirTerkunci($peserta, $hariIni),
            'izin_menunggu' => IzinPeserta::where('peserta_magang_id', $peserta->id)
                ->where('status', LogbookHarian::MENUNGGU)
                ->orderBy('tanggal_mulai')
                ->get(),
            'biodata' => BiodataPeserta::untuk($peserta),
            'divisi' => $penempatan?->divisi?->nama,
            'mentor' => $penempatan?->mentor_nama ?: $peserta->pembimbing,
            'sisa_hari' => $this->sisaHari($peserta, $hariIni),
            'feedback' => [
                'dibuka' => FeedbackPeserta::sudahDibuka($peserta),
                'dibuka_pada' => FeedbackPeserta::dibukaPada($peserta),
                'terkirim' => (bool) $peserta->feedback,
                'terkunci' => FeedbackPeserta::terkunci($peserta),
            ],
            'surat_terbit' => (bool) $peserta->suratKeterangan?->sudahTerbit(),
        ];
    }

    /**
     * Status logbook hari ini. "wajib" false bila hari libur atau di luar masa magang.
     *
     * @param  array<int, array<string, mixed>>  $hari
     * @return array{tanggal: CarbonImmutable, wajib: bool, libur: ?string, logbook: ?LogbookHarian}
     */
    private function hariIni(PesertaMagang $peserta, array $hari, CarbonImmutable $hariIni): array
    {
        $baris = collect($hari)->first(fn (array $item) => $item['tanggal']->equalTo($hariIni));

        return [
            'tanggal' => $hariIni,
            'wajib' => $baris !== null && $baris['akses'] === KalenderLogbook::BISA && ! $baris['libur'],
            'libur' => $baris['libur'] ?? null,
            'logbook' => $baris['logbook'] ?? null,
        ];
    }

    /**
     * Satu sel per tanggal periode berjalan, dengan kunci warna LEGENDA.
     *
     * @param  array<int, array<string, mixed>>  $hari
     * @return array<int, array{tanggal: CarbonImmutable, status: ?string, hari_ini: bool, bisa_diisi: bool}>
     */
    private function kalender(array $hari): array
    {
        $hariIni = CarbonImmutable::today();

        return array_map(fn (array $baris) => [
            'tanggal' => $baris['tanggal'],
            'status' => $this->statusKalender($baris),
            'hari_ini' => $baris['tanggal']->equalTo($hariIni),
            'bisa_diisi' => $baris['akses'] === KalenderLogbook::BISA,
        ], $hari);
    }

    /**
     * @param  array<string, mixed>  $baris
     */
    private function statusKalender(array $baris): ?string
    {
        /** @var LogbookHarian|null $logbook */
        $logbook = $baris['logbook'];

        if ($baris['akses'] === KalenderLogbook::DI_LUAR) {
            return null;
        }

        if ($logbook) {
            return match (true) {
                $logbook->kehadiran === LogbookHarian::LIBUR => 'libur',
                $logbook->status_verifikasi === LogbookHarian::DITOLAK => 'ditolak',
                $logbook->kehadiran !== LogbookHarian::HADIR => 'izin',
                $logbook->status_verifikasi === LogbookHarian::DITERIMA => 'diterima',
                default => 'menunggu',
            };
        }

        return match (true) {
            (bool) $baris['libur'] => 'libur',
            $baris['akses'] === KalenderLogbook::BELUM_TIBA => null,
            $baris['akses'] === KalenderLogbook::LEWAT_BATAS => 'terkunci',
            default => 'belum',
        };
    }

    /**
     * Hari kerja yang belum diisi dan akan melewati batas isi mundur dalam
     * HARI_PERINGATAN_KUNCI hari ke depan (bisa melintasi dua periode).
     *
     * @return Collection<int, CarbonImmutable>
     */
    private function hampirTerkunci(PesertaMagang $peserta, CarbonImmutable $hariIni): Collection
    {
        $batas = PengaturanMagang::ambil()->batas_isi_mundur_hari;

        // Tanggal t terkunci saat hari ini > t + batas.
        $rentang = [
            'mulai' => $hariIni->subDays($batas),
            'selesai' => $hariIni->subDays(max($batas - self::HARI_PERINGATAN_KUNCI, 0)),
        ];

        return collect($this->kalender->hari($peserta, $rentang))
            ->filter(fn (array $baris) => $baris['akses'] === KalenderLogbook::BISA
                && ! $baris['dibuka']
                && ! $baris['libur']
                && ! $baris['logbook']
                && $baris['tanggal']->lt($hariIni))
            ->map(fn (array $baris) => $baris['tanggal'])
            ->values();
    }

    /**
     * Sisa hari kalender sampai magang selesai (0 bila sudah lewat), atau null.
     */
    private function sisaHari(PesertaMagang $peserta, CarbonImmutable $hariIni): ?int
    {
        if (! $peserta->periode_selesai) {
            return null;
        }

        return max(0, (int) $hariIni->diffInDays(CarbonImmutable::instance($peserta->periode_selesai)->startOfDay(), false));
    }
}
