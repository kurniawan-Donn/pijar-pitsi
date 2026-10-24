<?php

namespace App\Services;

use App\Models\BiodataPeserta;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Monitoring peserta untuk admin: progres biodata, kelengkapan logbook
 * periode berjalan, verifikasi yang menunggu, dan riwayat logbook terbaru.
 * Semua data dari database (tanpa Google Sheets).
 */
class MonitoringPeserta
{
    /**
     * Pilihan urutan tabel.
     *
     * @var array<string, string>
     */
    public const URUTAN = [
        'nama' => 'Nama',
        'belum_diisi' => 'Logbook belum diisi terbanyak',
        'menunggu' => 'Menunggu verifikasi terbanyak',
        'biodata' => 'Progres biodata terendah',
    ];

    public function __construct(private KalenderLogbook $kalender) {}

    /**
     * Satu baris per peserta untuk periode berjalan.
     *
     * @param  array{status?: ?string, divisi?: ?int, cari?: ?string, belum_hari_ini?: bool, urut?: ?string}  $filter
     * @return Collection<int, array<string, mixed>>
     */
    public function tabel(array $filter = []): Collection
    {
        $status = $filter['status'] ?? 'aktif';
        $hariIni = CarbonImmutable::today();
        $kunciHariIni = $this->kalender->kunciPeriode($hariIni);

        $peserta = PesertaMagang::with(['user', 'biodata', 'penempatans.divisi'])
            ->when($status !== 'semua', fn ($query) => $query->where('status', $status))
            ->when($filter['cari'] ?? null, fn ($query, $cari) => $query->where(fn ($sub) => $sub
                ->where('nama', 'like', "%{$cari}%")
                ->orWhere('nim', 'like', "%{$cari}%")
                ->orWhere('nomor_peserta', 'like', "%{$cari}%")))
            ->orderBy('nama')
            ->get();

        $terakhir = LogbookHarian::whereIn('peserta_magang_id', $peserta->pluck('id'))
            ->selectRaw('peserta_magang_id, max(tanggal) as tanggal')
            ->groupBy('peserta_magang_id')
            ->pluck('tanggal', 'peserta_magang_id');

        $baris = $peserta->map(function (PesertaMagang $item) use ($hariIni, $kunciHariIni, $terakhir) {
            $periode = $this->kalender->pilihPeriode($item, $kunciHariIni);
            $hari = $periode ? $this->kalender->hari($item, $periode) : [];
            $rekap = $this->kalender->rekap($hari);
            $penempatan = $periode ? $item->penempatanUntuk($periode['kunci']) : null;

            $barisHariIni = collect($hari)->first(fn (array $baris) => $baris['tanggal']->equalTo($hariIni));
            $wajibHariIni = $barisHariIni && $barisHariIni['akses'] !== KalenderLogbook::DI_LUAR && ! $barisHariIni['libur'];

            return [
                'peserta' => $item,
                'periode' => $periode,
                'divisi' => $penempatan?->divisi,
                'mentor' => $penempatan?->mentor_nama,
                'biodata' => BiodataPeserta::untuk($item),
                'rekap' => $rekap,
                'terisi' => collect($hari)->filter(fn (array $baris) => $baris['logbook'] !== null)->count(),
                'terakhir' => isset($terakhir[$item->id]) ? CarbonImmutable::parse($terakhir[$item->id]) : null,
                // null = hari ini bukan hari kerja / di luar masa magang
                'isi_hari_ini' => $wajibHariIni ? $barisHariIni['logbook'] !== null : null,
            ];
        });

        if (! empty($filter['divisi'])) {
            $baris = $baris->filter(fn (array $item) => $item['divisi']?->id === (int) $filter['divisi']);
        }

        if (! empty($filter['belum_hari_ini'])) {
            $baris = $baris->filter(fn (array $item) => $item['isi_hari_ini'] === false);
        }

        $baris = match ($filter['urut'] ?? 'nama') {
            'belum_diisi' => $baris->sortByDesc(fn (array $item) => $item['rekap']['belum_diisi']),
            'menunggu' => $baris->sortByDesc(fn (array $item) => $item['rekap']['menunggu']),
            'biodata' => $baris->sortBy(fn (array $item) => $item['biodata']->progres()['persen']),
            default => $baris,
        };

        return $baris->values();
    }

    /**
     * Angka ringkas peserta aktif (dari tabel()).
     *
     * @param  Collection<int, array<string, mixed>>|null  $tabel
     * @return array<string, int>
     */
    public function ringkasan(?Collection $tabel = null): array
    {
        $tabel ??= $this->tabel(['status' => 'aktif']);
        $statusBiodata = $tabel->map(fn (array $item) => $item['biodata']->exists ? $item['biodata']->status : 'belum');

        return [
            'peserta_aktif' => $tabel->count(),
            'punya_akun' => $tabel->filter(fn (array $item) => $item['peserta']->user?->aktif)->count(),
            'biodata_disetujui' => $statusBiodata->filter(fn ($status) => $status === BiodataPeserta::STATUS_DISETUJUI)->count(),
            'biodata_diajukan' => $statusBiodata->filter(fn ($status) => $status === BiodataPeserta::STATUS_DIAJUKAN)->count(),
            'biodata_revisi' => $statusBiodata->filter(fn ($status) => $status === BiodataPeserta::STATUS_PERLU_REVISI)->count(),
            'biodata_belum' => $statusBiodata->filter(fn ($status) => in_array($status, ['belum', BiodataPeserta::STATUS_DRAFT], true))->count(),
            'wajib_hari_ini' => $tabel->filter(fn (array $item) => $item['isi_hari_ini'] !== null)->count(),
            'sudah_isi_hari_ini' => $tabel->filter(fn (array $item) => $item['isi_hari_ini'] === true)->count(),
            'logbook_menunggu' => LogbookHarian::whereIn('peserta_magang_id', $tabel->pluck('peserta.id'))
                ->where('status_verifikasi', LogbookHarian::MENUNGGU)
                ->whereNull('izin_peserta_id')
                ->count(),
            'izin_menunggu' => IzinPeserta::whereIn('peserta_magang_id', $tabel->pluck('peserta.id'))
                ->where('status', LogbookHarian::MENUNGGU)
                ->count(),
        ];
    }

    /**
     * Logbook yang terakhir diisi / diubah lintas peserta.
     *
     * @return Collection<int, LogbookHarian>
     */
    public function riwayatTerbaru(int $jumlah = 15): Collection
    {
        return LogbookHarian::with('pesertaMagang')
            ->where('sumber', 'aplikasi')
            ->latest('updated_at')
            ->limit($jumlah)
            ->get();
    }
}
