<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\BiodataPeserta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard peserta magang. Ringkasan biodata & logbook menyusul
     * di tahap berikutnya (lihat docs/rencana-fitur.md).
     */
    public function __invoke(Request $request): View
    {
        $peserta = $request->user()->pesertaMagang->load(['biodata', 'pengajuanMagang.mahasiswa', 'user']);

        return view('peserta.dashboard', [
            'peserta' => $peserta,
            'biodata' => BiodataPeserta::untuk($peserta),
        ]);
    }
}
