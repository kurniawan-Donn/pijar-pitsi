<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanPenilaianRequest;
use App\Models\AspekPenilaian;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenilaianController extends Controller
{
    /**
     * Form penilaian akhir peserta.
     */
    public function edit(PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load('penilaian.details', 'penilaian.penilai');

        $nilaiTersimpan = $pesertaMagang->penilaian?->details
            ->pluck('nilai', 'aspek_penilaian_id')
            ->all() ?? [];

        return view('superadmin.peserta-magang.penilaian', [
            'pesertaMagang' => $pesertaMagang,
            'penilaian' => $pesertaMagang->penilaian,
            'aspeks' => AspekPenilaian::aktif()->get(),
            'nilaiTersimpan' => $nilaiTersimpan,
            'predikat' => PenilaianPeserta::PREDIKAT,
        ]);
    }

    /**
     * Simpan penilaian akhir. Nama & bobot aspek disalin ke detail.
     */
    public function update(SimpanPenilaianRequest $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validated();
        $aspeks = AspekPenilaian::aktif()->get();

        $details = $aspeks->map(fn (AspekPenilaian $aspek) => [
            'aspek_penilaian_id' => $aspek->id,
            'nama_aspek' => $aspek->nama,
            'bobot' => $aspek->bobot,
            'nilai' => (int) $validated['nilai'][$aspek->id],
        ]);

        $nilaiAkhir = PenilaianPeserta::hitungNilaiAkhir($details);

        DB::transaction(function () use ($pesertaMagang, $validated, $details, $nilaiAkhir) {
            $penilaian = PenilaianPeserta::updateOrCreate(
                ['peserta_magang_id' => $pesertaMagang->id],
                [
                    'nilai_akhir' => $nilaiAkhir,
                    'predikat' => PenilaianPeserta::predikatUntuk($nilaiAkhir),
                    'catatan_pembimbing' => $validated['catatan_pembimbing'] ?? null,
                    'dinilai_oleh' => auth()->id(),
                    'dinilai_pada' => now(),
                ]
            );

            $penilaian->details()->delete();
            $penilaian->details()->createMany($details->all());
        });

        return redirect()
            ->route('superadmin.peserta-magang.penilaian', $pesertaMagang)
            ->with('success', 'Penilaian disimpan. Nilai akhir '.number_format($nilaiAkhir, 2, ',', '.')
                .' ('.PenilaianPeserta::predikatUntuk($nilaiAkhir).').');
    }
}
