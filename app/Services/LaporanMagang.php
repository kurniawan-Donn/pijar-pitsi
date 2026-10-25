<?php

namespace App\Services;

use App\Models\FeedbackPeserta;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Penyusun laporan magang. Hasilnya dipakai halaman laporan, CSV, dan versi cetak.
 *
 * Format hasil:
 * [
 *   'judul' => string, 'keterangan' => string,
 *   'kolom' => list<string>, 'baris' => list<list<string|int|float|null>>,
 *   'ringkasan' => array<string, string|int>,
 * ]
 */
class LaporanMagang
{
    public const JENIS = [
        'peserta' => 'Rekap Peserta',
        'absensi' => 'Kehadiran & Uang Saku',
        'nilai' => 'Nilai Akhir',
        'feedback' => 'Feedback Magang',
    ];

    public function __construct(
        private readonly KalenderLogbook $kalender,
        private readonly PeriodeMagang $periode,
    ) {}

    /**
     * @param  array<string, mixed>  $filter
     * @return array{judul: string, keterangan: string, kolom: array<int, string>, baris: array<int, array<int, mixed>>, ringkasan: array<string, mixed>}
     */
    public function buat(string $jenis, array $filter): array
    {
        return match ($jenis) {
            'absensi' => $this->absensi($filter),
            'nilai' => $this->nilai($filter),
            'feedback' => $this->feedback($filter),
            default => $this->peserta($filter),
        };
    }

    /**
     * Rekap peserta yang periodenya beririsan dengan rentang tanggal.
     *
     * @param  array{dari?: ?string, sampai?: ?string, status?: ?string}  $filter
     */
    private function peserta(array $filter): array
    {
        $peserta = $this->queryPeriode($filter['dari'] ?? null, $filter['sampai'] ?? null)
            ->when(in_array($filter['status'] ?? null, ['aktif', 'selesai'], true), fn (Builder $q) => $q->where('status', $filter['status']))
            ->with('penilaian')
            ->orderBy('periode_mulai')
            ->orderBy('nama')
            ->get();

        $baris = $peserta->values()->map(fn (PesertaMagang $p, int $i) => [
            $i + 1,
            $p->nama,
            $p->nim ?: '-',
            $p->asal_kampus ?: '-',
            $p->program_studi ?: '-',
            $p->periode_mulai?->format('d/m/Y') ?? '-',
            $p->periode_selesai?->format('d/m/Y') ?? '-',
            ucfirst((string) $p->status),
            $p->pembimbing ?: '-',
            $p->penilaian ? number_format($p->penilaian->nilai_akhir, 2, ',', '') : '-',
            $p->penilaian?->predikat ?? '-',
        ])->all();

        return [
            'judul' => 'Rekap Peserta Magang',
            'keterangan' => $this->labelPeriode($filter['dari'] ?? null, $filter['sampai'] ?? null)
                .(! empty($filter['status']) ? ' · status '.$filter['status'] : ''),
            'kolom' => ['No', 'Nama', 'NIM', 'Asal Kampus', 'Program Studi', 'Mulai', 'Selesai', 'Status', 'Pembimbing', 'Nilai Akhir', 'Predikat'],
            'baris' => $baris,
            'ringkasan' => [
                'Jumlah peserta' => $peserta->count(),
                'Aktif' => $peserta->where('status', 'aktif')->count(),
                'Selesai' => $peserta->where('status', 'selesai')->count(),
                'Sudah dinilai' => $peserta->filter(fn ($p) => $p->penilaian)->count(),
            ],
        ];
    }

    /**
     * Rekap kehadiran & uang saku per peserta untuk satu periode 28–27
     * (dari logbook harian di database).
     *
     * @param  array{periode?: ?string}  $filter
     */
    private function absensi(array $filter): array
    {
        $kunci = $this->kunciPeriode($filter['periode'] ?? null);
        $rentang = $this->periode->rentang($kunci);

        $peserta = $this->queryPeriode($rentang['mulai']->toDateString(), $rentang['selesai']->toDateString())
            ->whereNotNull('periode_mulai')
            ->whereNotNull('periode_selesai')
            ->with(['biodata', 'penempatans.divisi'])
            ->orderBy('nama')
            ->get();

        $total = ['hari_penuh' => 0, 'setengah_hari' => 0, 'uang_saku' => 0, 'menunggu' => 0];

        $baris = $peserta->values()->map(function (PesertaMagang $p, int $i) use ($kunci, &$total) {
            $periode = collect($this->kalender->daftarPeriode($p))->firstWhere('kunci', $kunci);
            $rekap = $this->kalender->rekap($periode ? $this->kalender->hari($p, $periode) : []);
            $biodata = $p->biodata;

            foreach (array_keys($total) as $kolom) {
                $total[$kolom] += $rekap[$kolom];
            }

            return [
                $i + 1,
                $p->nama,
                $p->nim ?: '-',
                $p->penempatanUntuk($kunci)?->divisi?->nama ?? '-',
                $rekap['hari_penuh'],
                $rekap['setengah_hari'],
                $rekap['izin'],
                $rekap['sakit'],
                $rekap['libur'],
                $rekap['belum_diisi'],
                $rekap['menunggu'],
                $rekap['uang_saku'],
                $biodata?->no_rekening ? trim($biodata->nama_bank.' '.$biodata->no_rekening.' a.n. '.$biodata->atas_nama_rekening) : '-',
            ];
        })->all();

        return [
            'judul' => 'Rekap Kehadiran & Uang Saku',
            'keterangan' => 'Periode '.$this->periode->label($kunci).' ('.$rentang['mulai']->translatedFormat('d M').' – '.$rentang['selesai']->translatedFormat('d M Y').')',
            'kolom' => ['No', 'Nama', 'NIM', 'Divisi', 'Hari Penuh', 'Setengah Hari', 'Izin', 'Sakit', 'Libur', 'Belum Diisi', 'Menunggu Verifikasi', 'Uang Saku (Rp)', 'Rekening'],
            'baris' => $baris,
            'ringkasan' => [
                'Jumlah peserta' => $peserta->count(),
                'Total hari penuh' => $total['hari_penuh'].' hari',
                'Total setengah hari' => $total['setengah_hari'].' hari',
                'Total uang saku' => 'Rp '.number_format($total['uang_saku'], 0, ',', '.'),
                'Menunggu verifikasi' => $total['menunggu'].' hari',
            ],
        ];
    }

    /**
     * Daftar nilai akhir beserta nilai per aspek.
     *
     * @param  array{dari?: ?string, sampai?: ?string}  $filter
     */
    private function nilai(array $filter): array
    {
        $penilaian = PenilaianPeserta::with(['pesertaMagang', 'details', 'penilai'])
            ->whereHas('pesertaMagang', fn (Builder $q) => $this->filterPeriode($q, $filter['dari'] ?? null, $filter['sampai'] ?? null))
            ->get()
            ->sortBy(fn (PenilaianPeserta $n) => $n->pesertaMagang->nama)
            ->values();

        // Kolom aspek = gabungan aspek yang pernah dipakai, sesuai urutan kemunculan.
        $aspek = $penilaian->flatMap(fn ($n) => $n->details->pluck('nama_aspek'))->unique()->values()->all();

        $baris = $penilaian->map(function (PenilaianPeserta $n, int $i) use ($aspek) {
            $nilaiAspek = $n->details->pluck('nilai', 'nama_aspek');

            return array_merge(
                [$i + 1, $n->pesertaMagang->nama, $n->pesertaMagang->nim ?: '-', $n->pesertaMagang->asal_kampus ?: '-'],
                array_map(fn (string $nama) => $nilaiAspek[$nama] ?? '-', $aspek),
                [
                    number_format($n->nilai_akhir, 2, ',', ''),
                    $n->predikat,
                    $n->penilai?->name ?? '-',
                    $n->dinilai_pada?->format('d/m/Y') ?? '-',
                ]
            );
        })->all();

        return [
            'judul' => 'Nilai Akhir Peserta Magang',
            'keterangan' => $this->labelPeriode($filter['dari'] ?? null, $filter['sampai'] ?? null),
            'kolom' => array_merge(['No', 'Nama', 'NIM', 'Asal Kampus'], $aspek, ['Nilai Akhir', 'Predikat', 'Dinilai Oleh', 'Tanggal']),
            'baris' => $baris,
            'ringkasan' => [
                'Jumlah dinilai' => $penilaian->count(),
                'Rata-rata' => $penilaian->isEmpty() ? '-' : number_format($penilaian->avg('nilai_akhir'), 2, ',', '.'),
                'Tertinggi' => $penilaian->isEmpty() ? '-' : number_format($penilaian->max('nilai_akhir'), 2, ',', '.'),
            ],
        ];
    }

    /**
     * Skor feedback per peserta (filter tanggal selesai magang).
     *
     * @param  array{dari?: ?string, sampai?: ?string}  $filter
     */
    private function feedback(array $filter): array
    {
        $rekap = app(RekapFeedback::class);
        $daftar = $rekap->daftar($filter['dari'] ?? null, $filter['sampai'] ?? null);
        $hasil = $rekap->hitung($daftar);
        $nomor = array_keys(config('feedback.pertanyaan_skala'));
        $angka = fn (float $nilai) => number_format($nilai, 2, ',', '');

        $baris = $daftar->values()->map(fn (FeedbackPeserta $feedback, int $i) => [
            $i + 1,
            $feedback->pesertaMagang->nama,
            $feedback->pesertaMagang->asal_kampus ?: '-',
            $feedback->pesertaMagang->periode_selesai?->format('d/m/Y') ?? '-',
            ...array_map(fn (int $n) => (int) ($feedback->skor[$n] ?? 0), $nomor),
            $angka($feedback->rataRata()),
            $feedback->labelRekomendasi(),
        ])->all();

        return [
            'judul' => 'Rekap Feedback Magang',
            'keterangan' => 'Selesai magang: '.mb_strtolower($this->labelPeriode($filter['dari'] ?? null, $filter['sampai'] ?? null))
                .' · skor 1–5 untuk pertanyaan P1–P'.count($nomor),
            'kolom' => ['No', 'Nama', 'Asal Kampus', 'Selesai', ...array_map(fn (int $n) => 'P'.$n, $nomor), 'Rata-rata', 'Rekomendasi'],
            'baris' => $baris,
            'ringkasan' => [
                'Jumlah feedback' => $hasil['jumlah'],
                'Rata-rata kepuasan' => $angka($hasil['rata_rata']).' / 5',
                'Bersedia merekomendasikan' => ($hasil['rekomendasi']['sangat_bersedia'] + $hasil['rekomendasi']['bersedia']).' peserta',
            ],
        ];
    }

    /**
     * Peserta yang periodenya beririsan dengan [dari, sampai]. Peserta tanpa
     * periode tetap ikut bila filter kosong.
     */
    private function queryPeriode(?string $dari, ?string $sampai): Builder
    {
        return $this->filterPeriode(PesertaMagang::query(), $dari, $sampai);
    }

    private function filterPeriode(Builder $query, ?string $dari, ?string $sampai): Builder
    {
        // whereDate: kolom tersimpan sebagai "Y-m-d 00:00:00".
        return $query
            ->when($sampai, fn (Builder $q) => $q->where(fn (Builder $w) => $w->whereNull('periode_mulai')->orWhereDate('periode_mulai', '<=', $sampai)))
            ->when($dari, fn (Builder $q) => $q->where(fn (Builder $w) => $w->whereNull('periode_selesai')->orWhereDate('periode_selesai', '>=', $dari)));
    }

    /**
     * Kunci periode (Y-m) dari filter, atau periode yang sedang berjalan.
     */
    private function kunciPeriode(?string $kunci): string
    {
        return $kunci && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $kunci)
            ? $kunci
            : $this->periode->kunciUntuk(CarbonImmutable::today());
    }

    private function labelPeriode(?string $dari, ?string $sampai): string
    {
        if (! $dari && ! $sampai) {
            return 'Semua periode';
        }

        $format = fn (?string $tanggal) => $tanggal ? CarbonImmutable::parse($tanggal)->translatedFormat('d M Y') : '…';

        return 'Periode '.$format($dari).' – '.$format($sampai);
    }
}
