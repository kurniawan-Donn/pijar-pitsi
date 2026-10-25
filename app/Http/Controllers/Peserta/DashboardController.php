<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Services\RingkasanPeserta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard peserta magang: logbook hari ini, rekap periode berjalan,
     * logbook yang perlu diperbaiki, biodata, penempatan, dan pengingat.
     */
    public function __invoke(Request $request, RingkasanPeserta $ringkasan): View
    {
        $peserta = $request->user()->pesertaMagang;

        return view('peserta.dashboard', [
            'peserta' => $peserta,
            'r' => $ringkasan->untuk($peserta),
        ]);
    }
}
