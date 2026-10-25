<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\BiodataPeserta;
use App\Services\KalenderLogbook;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Rekap kehadiran & uang saku peserta: ringkasan semua periode magang
 * dan rincian satu periode yang dipilih.
 */
class RekapController extends Controller
{
    public function __invoke(Request $request, KalenderLogbook $kalender): View
    {
        $peserta = $request->user()->pesertaMagang;
        $periode = $kalender->pilihPeriode($peserta, $request->query('periode'));

        // Periode yang belum dimulai tidak dihitung (semua harinya belum tiba).
        $ringkasan = collect($kalender->daftarPeriode($peserta))
            ->filter(fn (array $item) => $item['mulai']->lte(today()))
            ->map(fn (array $item) => $item + ['rekap' => $kalender->rekap($kalender->hari($peserta, $item))])
            ->values();

        $jumlah = fn (string $kolom) => (int) $ringkasan->sum(fn (array $item) => $item['rekap'][$kolom]);

        return view('peserta.rekap', [
            'peserta' => $peserta,
            'periode' => $periode,
            'daftarPeriode' => $kalender->daftarPeriode($peserta),
            'ringkasan' => $ringkasan,
            'total' => collect(['hari_penuh', 'setengah_hari', 'izin', 'sakit', 'belum_diisi', 'menunggu', 'uang_saku'])
                ->mapWithKeys(fn (string $kolom) => [$kolom => $jumlah($kolom)])
                ->all(),
            'rekap' => $periode ? $kalender->rekap($kalender->hari($peserta, $periode)) : null,
            'tarif' => $periode ? $kalender->tarifPeriode($periode) : null,
            'biodata' => BiodataPeserta::untuk($peserta),
        ]);
    }
}
