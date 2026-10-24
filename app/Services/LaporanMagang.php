<?php

namespace App\Services;

use App\Models\FeedbackPeserta;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\TarifUangSaku;
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
        'absensi' => 'Rekap Absensi',
        'nilai' => 'Nilai Akhir',
        'feedback' => 'Feedback Magang',
    ];

    public function __construct(
        private readonly AbsensiSheetService $absensi,
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
     * Rekap absensi & uang saku per peserta untuk satu bulan (dibaca dari Google Sheets).
     *
     * @param  array{bulan?: ?string}  $filter
     */
    private function absensi(array $filter): array
    {
        $bulan = $this->bulan($filter['bulan'] ?? null);

        $peserta = $this->queryPeriode($bulan->toDateString(), $bulan->endOfMonth()->toDateString())
            ->orderBy('nama')
            ->get();

        // Tarif diatur admin (Pengaturan Magang); dipakai tarif yang berlaku di awal bulan.
        $tarif = TarifUangSaku::berlakuPada($bulan);
        $tarifPenuh = $tarif->tarif_hari_penuh;
        $tarifSetengah = $tarif->tarif_setengah_hari;
        $total = ['hadir' => 0, 'uang_saku' => 0, 'gagal' => 0];

        $baris = $peserta->values()->map(function (PesertaMagang $p, int $i) use ($bulan, $tarifPenuh, $tarifSetengah, &$total) {
            $dasar = [$i + 1, $p->nama, $p->nim ?: '-'];

            if (! $p->google_sheet_url) {
                $total['gagal']++;

                return array_merge($dasar, array_fill(0, 8, '-'), ['Belum ada link Google Sheets']);
            }

            try {
                $rows = $this->absensi->ambil($p->google_sheet_url, $bulan)['rows'];
            } catch (\Throwable $e) {
                report($e);
                $total['gagal']++;

                return array_merge($dasar, array_fill(0, 8, '-'), [$e instanceof TabBulanTidakDitemukanException ? 'Tab bulan tidak ada' : 'Gagal membaca sheet']);
            }

            $rekap = RekapAbsensi::hitung($rows);
            $logbook = Logbook::ringkasan(Logbook::baris($rows));
            $uangSaku = RekapAbsensi::uangSaku($rekap, $tarifPenuh, $tarifSetengah);

            $total['hadir'] += $rekap['hadir'];
            $total['uang_saku'] += $uangSaku;

            return array_merge($dasar, [
                $rekap['tercatat'],
                $rekap['hadir'],
                $rekap['hari_penuh'],
                $rekap['setengah_hari'],
                $rekap['izin'],
                $rekap['sakit'],
                $logbook['Diterima'].'/'.$logbook['total'],
                $uangSaku,
                '',
            ]);
        })->all();

        return [
            'judul' => 'Rekap Absensi & Uang Saku',
            'keterangan' => 'Bulan '.$bulan->translatedFormat('F Y'),
            'kolom' => ['No', 'Nama', 'NIM', 'Hari Tercatat', 'Hadir', 'Hari Penuh', 'Setengah Hari', 'Izin', 'Sakit', 'Logbook Diterima', 'Uang Saku (Rp)', 'Keterangan'],
            'baris' => $baris,
            'ringkasan' => [
                'Jumlah peserta' => $peserta->count(),
                'Total hadir' => $total['hadir'].' hari',
                'Total uang saku' => 'Rp '.number_format($total['uang_saku'], 0, ',', '.'),
                'Tarif hari penuh / setengah hari' => 'Rp '.number_format($tarifPenuh, 0, ',', '.').' / Rp '.number_format($tarifSetengah, 0, ',', '.'),
                'Data tidak terbaca' => $total['gagal'],
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

    private function bulan(?string $bulan): CarbonImmutable
    {
        return $bulan && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)
            ? CarbonImmutable::createFromFormat('!Y-m', $bulan)
            : CarbonImmutable::now()->startOfMonth();
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
