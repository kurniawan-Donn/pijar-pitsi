<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanPenilaianRequest;
use App\Models\BiodataPeserta;
use App\Models\KelompokPenilaian;
use App\Models\LogAktivitas;
use App\Models\PengaturanMagang;
use App\Models\PenilaianPeserta;
use App\Models\Perusahaan;
use App\Models\PesertaMagang;
use App\Models\PredikatPenilaian;
use App\Services\PeriodeMagang;
use App\Services\TandaTanganDokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Penilaian akhir peserta mengikuti sheet PENILAIAN MHS MAGANG:
 * aspek per kelompok (Hard/Soft Skill), predikat teks, dan kesimpulan.
 */
class PenilaianController extends Controller
{
    /**
     * Form penilaian akhir peserta.
     */
    public function edit(PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load('penilaian.details', 'penilaian.penilai');

        return view('superadmin.peserta-magang.penilaian', [
            'pesertaMagang' => $pesertaMagang,
            'penilaian' => $pesertaMagang->penilaian,
            'kelompoks' => $this->kelompokAktif(),
            'nilaiTersimpan' => $pesertaMagang->penilaian?->details->pluck('nilai', 'aspek_penilaian_id')->all() ?? [],
            'predikats' => PredikatPenilaian::orderBy('batas_bawah')->get(),
        ]);
    }

    /**
     * Simpan penilaian akhir. Nama aspek, kelompok, dan bobot kelompok disalin ke detail.
     */
    public function update(SimpanPenilaianRequest $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validated();

        $details = $this->kelompokAktif()->flatMap(fn (KelompokPenilaian $kelompok) => $kelompok->aspekAktif->map(fn ($aspek) => [
            'aspek_penilaian_id' => $aspek->id,
            'nama_aspek' => $aspek->nama,
            'kelompok' => $kelompok->nama,
            'bobot_kelompok' => $kelompok->bobot,
            'bobot' => 0,
            'nilai' => (int) $validated['nilai'][$aspek->id],
        ]))->values();

        $nilaiAkhir = PenilaianPeserta::hitungNilaiAkhir($details);
        $predikat = PenilaianPeserta::predikatUntuk($nilaiAkhir);

        DB::transaction(function () use ($pesertaMagang, $validated, $details, $nilaiAkhir, $predikat) {
            $penilaian = PenilaianPeserta::updateOrCreate(
                ['peserta_magang_id' => $pesertaMagang->id],
                [
                    'nilai_akhir' => $nilaiAkhir,
                    'predikat' => $predikat,
                    'kekuatan' => $validated['kekuatan'] ?? null,
                    'aspek_perbaikan' => $validated['aspek_perbaikan'] ?? null,
                    'langkah_perbaikan' => $validated['langkah_perbaikan'] ?? null,
                    'dinilai_oleh' => auth()->id(),
                    'dinilai_pada' => now(),
                ]
            );

            $penilaian->details()->delete();
            $penilaian->details()->createMany($details->all());
        });

        LogAktivitas::catat(
            'simpan_penilaian',
            $pesertaMagang,
            "Penilaian {$pesertaMagang->nama} disimpan: ".number_format($nilaiAkhir, 2, ',', '.')." ({$predikat})",
            ['nilai' => $details->pluck('nilai', 'nama_aspek')->all(), 'nilai_akhir' => $nilaiAkhir]
        );

        return redirect()
            ->route('superadmin.peserta-magang.penilaian', $pesertaMagang)
            ->with('success', 'Penilaian disimpan. Nilai akhir '.number_format($nilaiAkhir, 2, ',', '.')." ({$predikat}).");
    }

    /**
     * Lembar penilaian (PDF) format sheet PENILAIAN MHS MAGANG.
     */
    public function cetak(PesertaMagang $pesertaMagang, PeriodeMagang $periode, TandaTanganDokumen $tandaTangan): Response|RedirectResponse
    {
        $pesertaMagang->load(['penilaian.details', 'penilaian.penilai', 'biodata', 'penempatans.divisi', 'pengajuanMagang.mahasiswa', 'user']);

        if (! $pesertaMagang->penilaian) {
            return redirect()
                ->route('superadmin.peserta-magang.penilaian', $pesertaMagang)
                ->with('error', 'Simpan penilaian terlebih dahulu sebelum mencetak.');
        }

        $pengaturan = PengaturanMagang::ambil();
        $kunciTerakhir = $pesertaMagang->periode_selesai ? $periode->kunciUntuk($pesertaMagang->periode_selesai) : null;

        $pdf = Pdf::loadView('penilaian.cetak', [
            'peserta' => $pesertaMagang,
            'penilaian' => $pesertaMagang->penilaian,
            'biodata' => BiodataPeserta::untuk($pesertaMagang),
            'penempatan' => $kunciTerakhir ? $pesertaMagang->penempatanUntuk($kunciTerakhir) : null,
            'predikats' => PredikatPenilaian::orderBy('batas_bawah')->get(),
            'pengaturan' => $pengaturan,
            'perusahaan' => Perusahaan::query()->first(),
            'ttdMengetahui' => $tandaTangan->dataUri($pengaturan->mengetahui_ttd_path),
            'ttdMenyetujui' => $tandaTangan->dataUri($pengaturan->menyetujui_ttd_path),
            'stempel' => $tandaTangan->dataUri($pengaturan->stempel_path),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('penilaian-'.Str::slug($pesertaMagang->nama).'.pdf');
    }

    /**
     * Kelompok yang punya aspek aktif, beserta aspeknya.
     */
    private function kelompokAktif()
    {
        return KelompokPenilaian::with('aspekAktif')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->filter(fn (KelompokPenilaian $kelompok) => $kelompok->aspekAktif->isNotEmpty())
            ->values();
    }
}
