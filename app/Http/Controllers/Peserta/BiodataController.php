<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanBiodataRequest;
use App\Models\BiodataPeserta;
use App\Models\LogAktivitas;
use App\Models\PesertaMagang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Biodata milik peserta yang sedang login.
 */
class BiodataController extends Controller
{
    public function edit(Request $request): View
    {
        $peserta = $this->peserta($request);

        return view('peserta.biodata.edit', [
            'peserta' => $peserta,
            'biodata' => BiodataPeserta::untuk($peserta),
        ]);
    }

    /**
     * Simpan draft, atau kirim untuk ditinjau admin.
     */
    public function update(SimpanBiodataRequest $request): RedirectResponse
    {
        $peserta = $this->peserta($request);
        $biodata = BiodataPeserta::untuk($peserta);

        if ($biodata->exists && ! $biodata->bisaDiubahPeserta()) {
            return redirect()
                ->route('peserta.biodata.edit')
                ->with('error', 'Biodata sedang ditinjau atau sudah disetujui, sehingga tidak dapat diubah.');
        }

        $biodata->fill($request->dataBiodata());

        if ($request->dikirim()) {
            $biodata->fill([
                'status' => BiodataPeserta::STATUS_DIAJUKAN,
                'diajukan_pada' => now(),
                'pernyataan_tanggal' => now()->toDateString(),
            ]);
        }

        $biodata->save();

        if ($request->dikirim()) {
            LogAktivitas::catat('ajukan_biodata', $peserta, "Biodata {$peserta->nama} dikirim untuk ditinjau");

            return redirect()
                ->route('peserta.biodata.edit')
                ->with('success', 'Biodata berhasil dikirim. Admin akan meninjau data Anda.');
        }

        return redirect()
            ->route('peserta.biodata.edit')
            ->with('success', 'Draft biodata berhasil disimpan.');
    }

    private function peserta(Request $request): PesertaMagang
    {
        return $request->user()->pesertaMagang->load(['biodata', 'pengajuanMagang.mahasiswa', 'user']);
    }
}
