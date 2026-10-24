<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackPeserta;
use App\Models\PesertaMagang;
use App\Services\RekapFeedback;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Hasil feedback peserta: rekap seluruh peserta dan detail per peserta.
 */
class FeedbackController extends Controller
{
    public function index(Request $request, RekapFeedback $rekap): View
    {
        $filter = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ], [
            'sampai.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
        ]);

        $daftar = $rekap->daftar($filter['dari'] ?? null, $filter['sampai'] ?? null);

        return view('superadmin.feedback.index', [
            'filter' => $filter,
            'daftar' => $daftar,
            'hasil' => $rekap->hitung($daftar),
        ]);
    }

    public function show(PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load('feedback');

        return view('superadmin.feedback.show', [
            'pesertaMagang' => $pesertaMagang,
            'feedback' => $pesertaMagang->feedback,
            'dibukaPada' => FeedbackPeserta::dibukaPada($pesertaMagang),
        ]);
    }
}
