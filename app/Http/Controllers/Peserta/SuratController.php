<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Services\CetakSuratKeterangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Surat keterangan magang milik peserta (hanya yang sudah terbit).
 */
class SuratController extends Controller
{
    public function show(Request $request): View
    {
        return view('peserta.surat.show', [
            'peserta' => $request->user()->pesertaMagang,
            'surat' => $request->user()->pesertaMagang->suratKeterangan,
        ]);
    }

    public function unduh(Request $request, CetakSuratKeterangan $cetak): Response|RedirectResponse
    {
        $surat = $request->user()->pesertaMagang->suratKeterangan;

        if (! $surat?->sudahTerbit()) {
            return redirect()->route('peserta.surat.show')->with('error', 'Surat keterangan belum diterbitkan.');
        }

        return $cetak->pdf($surat);
    }
}
