<?php

namespace App\Services;

use App\Models\PengajuanMagang;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\PredikatPenilaian;
use App\Models\TarifUangSaku;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Angka-angka untuk dashboard Admin & Super Admin.
 */
class StatistikMagang
{
    public function __construct(
        private readonly AbsensiSheetService $absensi,
    ) {}

    /**
     * @return array{aktif: int, selesai: int, total: int, belum_dinilai: int}
     */
    public function peserta(): array
    {
        $perStatus = PesertaMagang::query()
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        return [
            'aktif' => (int) ($perStatus['aktif'] ?? 0),
            'selesai' => (int) ($perStatus['selesai'] ?? 0),
            'total' => (int) $perStatus->sum(),
            'belum_dinilai' => PesertaMagang::where('status', 'selesai')->doesntHave('penilaian')->count(),
        ];
    }

    /**
     * Jumlah pengajuan per status (semua status selalu ada).
     *
     * @return array<string, int>
     */
    public function pengajuanPerStatus(): array
    {
        $jumlah = PengajuanMagang::query()
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        return collect(PengajuanMagang::STATUS_LABELS)
            ->mapWithKeys(fn ($label, $status) => [$status => (int) ($jumlah[$status] ?? 0)])
            ->all();
    }

    /**
     * Jumlah pengajuan masuk per bulan, N bulan terakhir (termasuk bulan ini).
     *
     * @return array<int, array{bulan: string, label: string, jumlah: int}>
     */
    public function pengajuanPerBulan(int $jumlahBulan = 6): array
    {
        $awal = CarbonImmutable::now()->startOfMonth()->subMonths($jumlahBulan - 1);

        $perBulan = PengajuanMagang::query()
            ->where('created_at', '>=', $awal)
            ->get(['created_at'])
            ->countBy(fn (PengajuanMagang $pengajuan) => $pengajuan->created_at->format('Y-m'));

        return collect(range(0, $jumlahBulan - 1))
            ->map(function (int $i) use ($awal, $perBulan) {
                $bulan = $awal->addMonths($i);

                return [
                    'bulan' => $bulan->format('Y-m'),
                    'label' => $bulan->translatedFormat('M Y'),
                    'jumlah' => (int) ($perBulan[$bulan->format('Y-m')] ?? 0),
                ];
            })
            ->all();
    }

    /**
     * Peserta aktif yang periode magangnya berakhir bulan ini.
     *
     * @return Collection<int, PesertaMagang>
     */
    public function berakhirBulanIni(): Collection
    {
        $bulanIni = CarbonImmutable::now();

        return PesertaMagang::with('penilaian')
            ->where('status', 'aktif')
            // whereDate: kolom tersimpan sebagai "Y-m-d 00:00:00".
            ->whereDate('periode_selesai', '>=', $bulanIni->startOfMonth()->toDateString())
            ->whereDate('periode_selesai', '<=', $bulanIni->endOfMonth()->toDateString())
            ->orderBy('periode_selesai')
            ->get();
    }

    /**
     * Pengajuan terbaru.
     *
     * @return Collection<int, PengajuanMagang>
     */
    public function pengajuanTerbaru(int $jumlah = 5): Collection
    {
        return PengajuanMagang::with('mahasiswa')->latest()->limit($jumlah)->get();
    }

    /**
     * Sebaran predikat nilai akhir, urut dari predikat tertinggi. Predikat
     * yang tidak ada lagi di tabel (mis. huruf A–E lama) ditampilkan di akhir.
     *
     * @return array<string, int>
     */
    public function sebaranPredikat(): array
    {
        $jumlah = PenilaianPeserta::query()
            ->selectRaw('predikat, count(*) as jumlah')
            ->groupBy('predikat')
            ->pluck('jumlah', 'predikat');

        $urutan = PredikatPenilaian::orderByDesc('batas_bawah')->pluck('nama');

        return $urutan
            ->merge($jumlah->keys()->diff($urutan))
            ->mapWithKeys(fn (string $predikat) => [$predikat => (int) ($jumlah[$predikat] ?? 0)])
            ->all();
    }

    /**
     * Ringkasan absensi bulan ini dari data yang sudah tersimpan di cache
     * (tidak memanggil Google, supaya dashboard tetap cepat).
     *
     * @return array{peserta_tersedia: int, peserta_aktif: int, hadir: int, izin: int, sakit: int, uang_saku: int}
     */
    public function absensiBulanIni(): array
    {
        $bulan = CarbonImmutable::now()->startOfMonth();

        $peserta = PesertaMagang::where('status', 'aktif')
            ->whereNotNull('google_sheet_url')
            ->get(['id', 'google_sheet_url']);

        $tarif = TarifUangSaku::berlakuPada($bulan);

        $hasil = ['peserta_tersedia' => 0, 'peserta_aktif' => $peserta->count(), 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'uang_saku' => 0];

        foreach ($peserta as $item) {
            $data = $this->absensi->dariCache($item->google_sheet_url, $bulan);

            if (! $data) {
                continue;
            }

            $rekap = RekapAbsensi::hitung($data['rows']);

            $hasil['peserta_tersedia']++;
            $hasil['hadir'] += $rekap['hadir'];
            $hasil['izin'] += $rekap['izin'];
            $hasil['sakit'] += $rekap['sakit'];
            $hasil['uang_saku'] += RekapAbsensi::uangSaku(
                $rekap,
                $tarif->tarif_hari_penuh,
                $tarif->tarif_setengah_hari
            );
        }

        return $hasil;
    }
}
