<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BiodataPeserta;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use App\Models\SuratKeterangan;
use App\Services\MonitoringPeserta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Halaman daftar per fitur (Biodata, Logbook & Absensi, Penilaian, Surat
 * Keterangan): semua peserta beserta statusnya, dengan tautan ke halaman
 * per peserta.
 */
class DaftarPesertaController extends Controller
{
    /**
     * Pilihan filter "keadaan" per halaman.
     *
     * @var array<string, array<string, string>>
     */
    public const KEADAAN = [
        'biodata' => [
            'diajukan' => 'Menunggu tinjauan',
            'perlu_revisi' => 'Perlu revisi',
            'belum' => 'Belum dikirim',
            'disetujui' => 'Disetujui',
        ],
        'logbook' => [
            'menunggu' => 'Ada yang menunggu verifikasi',
            'belum_hari_ini' => 'Belum isi hari ini',
        ],
        'penilaian' => [
            'belum' => 'Belum dinilai',
            'sudah' => 'Sudah dinilai',
        ],
        'surat' => [
            'siap' => 'Siap diterbitkan',
            'belum_siap' => 'Syarat belum lengkap',
            'terbit' => 'Sudah terbit',
        ],
    ];

    public function biodata(Request $request): View
    {
        $filter = $this->filter($request, 'biodata', 'aktif');

        $baris = $this->peserta($filter, ['biodata', 'pengajuanMagang.mahasiswa'])
            ->map(function (PesertaMagang $peserta) {
                $biodata = BiodataPeserta::untuk($peserta);

                return [
                    'peserta' => $peserta,
                    'biodata' => $biodata,
                    'keadaan' => match (true) {
                        ! $biodata->exists, $biodata->status === BiodataPeserta::STATUS_DRAFT => 'belum',
                        default => $biodata->status,
                    },
                ];
            })
            // Yang perlu ditindaklanjuti admin tampil paling atas.
            ->sortBy(fn (array $item) => array_search($item['keadaan'], array_keys(self::KEADAAN['biodata']), true))
            ->values();

        return $this->tampil('biodata', 'Biodata Peserta', 'Status pengisian dan tinjauan biodata', $filter, $baris);
    }

    public function logbook(Request $request, MonitoringPeserta $monitoring): View
    {
        $filter = $this->filter($request, 'logbook', 'aktif');

        $baris = $monitoring->tabel([
            'status' => $filter['status'],
            'cari' => $filter['cari'] ?? null,
            'belum_hari_ini' => ($filter['keadaan'] ?? null) === 'belum_hari_ini',
            'urut' => 'menunggu',
        ]);

        $izinMenunggu = IzinPeserta::whereIn('peserta_magang_id', $baris->pluck('peserta.id'))
            ->where('status', LogbookHarian::MENUNGGU)
            ->selectRaw('peserta_magang_id, count(*) as jumlah')
            ->groupBy('peserta_magang_id')
            ->pluck('jumlah', 'peserta_magang_id');

        $baris = $baris
            ->map(fn (array $item) => $item + ['izin_menunggu' => (int) ($izinMenunggu[$item['peserta']->id] ?? 0)])
            ->when(
                ($filter['keadaan'] ?? null) === 'menunggu',
                fn (Collection $daftar) => $daftar->filter(fn (array $item) => $item['rekap']['menunggu'] > 0 || $item['izin_menunggu'] > 0)
            )
            ->values();

        return $this->tampil('logbook', 'Logbook & Absensi', 'Logbook periode berjalan, verifikasi, dan uang saku', $filter, $baris);
    }

    public function penilaian(Request $request): View
    {
        $filter = $this->filter($request, 'penilaian', 'semua');

        $baris = $this->peserta($filter, ['penilaian.penilai'])
            ->map(fn (PesertaMagang $peserta) => [
                'peserta' => $peserta,
                'penilaian' => $peserta->penilaian,
                'keadaan' => $peserta->penilaian ? 'sudah' : 'belum',
            ])
            ->sortBy(fn (array $item) => $item['keadaan'] === 'belum' ? 0 : 1)
            ->values();

        return $this->tampil('penilaian', 'Penilaian Akhir', 'Nilai akhir dan predikat setiap peserta', $filter, $baris);
    }

    public function surat(Request $request): View
    {
        $filter = $this->filter($request, 'surat', 'selesai');

        $baris = $this->peserta($filter, ['suratKeterangan', 'biodata', 'penilaian', 'feedback'])
            ->map(function (PesertaMagang $peserta) {
                $surat = $peserta->suratKeterangan;
                $syarat = SuratKeterangan::syaratBelumTerpenuhi($peserta);

                return [
                    'peserta' => $peserta,
                    'surat' => $surat,
                    'syarat' => $syarat,
                    'keadaan' => match (true) {
                        (bool) $surat?->sudahTerbit() => 'terbit',
                        $syarat === [] => 'siap',
                        default => 'belum_siap',
                    },
                ];
            })
            ->sortBy(fn (array $item) => array_search($item['keadaan'], array_keys(self::KEADAAN['surat']), true))
            ->values();

        return $this->tampil('surat', 'Surat Keterangan', 'Syarat dan status surat keterangan magang', $filter, $baris);
    }

    /**
     * @return array{status: string, cari?: ?string, keadaan?: ?string}
     */
    private function filter(Request $request, string $jenis, string $statusBawaan): array
    {
        $filter = $request->validate([
            'status' => ['nullable', Rule::in(['aktif', 'selesai', 'semua'])],
            'cari' => ['nullable', 'string', 'max:100'],
            'keadaan' => ['nullable', Rule::in(array_keys(self::KEADAAN[$jenis]))],
        ]);

        $filter['status'] ??= $statusBawaan;

        return $filter;
    }

    /**
     * Peserta sesuai filter status & pencarian (keadaan disaring setelahnya).
     *
     * @param  array<int, string>  $relasi
     * @return Collection<int, PesertaMagang>
     */
    private function peserta(array $filter, array $relasi): Collection
    {
        return PesertaMagang::with($relasi)
            ->when($filter['status'] !== 'semua', fn (Builder $query) => $query->where('status', $filter['status']))
            ->when($filter['cari'] ?? null, fn (Builder $query, string $cari) => $query->where(fn (Builder $sub) => $sub
                ->where('nama', 'like', "%{$cari}%")
                ->orWhere('nim', 'like', "%{$cari}%")
                ->orWhere('nomor_peserta', 'like', "%{$cari}%")))
            ->orderBy('nama')
            ->get();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $baris
     */
    private function tampil(string $jenis, string $judul, string $subjudul, array $filter, Collection $baris): View
    {
        // Filter keadaan untuk logbook sudah diterapkan saat menyusun baris.
        if ($jenis !== 'logbook' && ($filter['keadaan'] ?? null)) {
            $baris = $baris->where('keadaan', $filter['keadaan'])->values();
        }

        return view('superadmin.daftar-peserta.index', [
            'jenis' => $jenis,
            'judul' => $judul,
            'subjudul' => $subjudul,
            'filter' => $filter,
            'baris' => $baris,
            'pilihanKeadaan' => self::KEADAAN[$jenis],
        ]);
    }
}
