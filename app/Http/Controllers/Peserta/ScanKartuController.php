<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScanKartuAbsensiRequest;
use App\Models\PesertaMagang;
use App\Models\ScanKartuAbsensi;
use App\Services\BacaKartuAbsensi;
use App\Services\GagalBacaKartuException;
use App\Services\JendelaScanKartu;
use App\Services\KalenderLogbook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Isi jam logbook dari foto kartu absensi: unggah → pratinjau → simpan (konfirmasi peserta).
 */
class ScanKartuController extends Controller
{
    public function __construct(
        private JendelaScanKartu $jendela,
        private BacaKartuAbsensi $baca,
    ) {}

    public function create(Request $request): View
    {
        $peserta = $this->peserta($request);

        return view('peserta.scan-kartu.create', [
            'aktif' => $this->jendela->aktif(),
            'pilihan' => $this->jendela->pilihan($peserta),
            'sisa' => $this->jendela->sisaHariIni($peserta),
            'bukaBerikutnya' => $this->jendela->bukaBerikutnya(),
            'keterangan' => $this->jendela->keterangan(),
            'riwayat' => ScanKartuAbsensi::where('peserta_magang_id', $peserta->id)->latest()->limit(5)->get(),
        ]);
    }

    public function store(ScanKartuAbsensiRequest $request): RedirectResponse
    {
        $peserta = $this->peserta($request);
        $kartu = $request->kartu();

        if (! $this->jendela->bolehScan($peserta, $kartu['sisi'], $kartu['tahun'], $kartu['bulan'])) {
            return back()->withErrors(['kartu' => 'Kartu ini tidak bisa di-scan sekarang. '.$this->jendela->keterangan()]);
        }

        if ($this->jendela->sisaHariIni($peserta) < 1) {
            return back()->withErrors(['foto' => 'Batas scan hari ini sudah habis. Coba lagi besok atau isi jam secara manual.']);
        }

        try {
            $scan = $this->baca->scan($peserta, $kartu['sisi'], $kartu['tahun'], $kartu['bulan'], $request->file('foto'));
        } catch (GagalBacaKartuException $e) {
            return back()->withInput()->withErrors(['foto' => $e->getMessage()]);
        }

        return redirect()->route('peserta.scan-kartu.show', $scan);
    }

    public function show(ScanKartuAbsensi $scanKartuAbsensi): View|RedirectResponse
    {
        Gate::authorize('view', $scanKartuAbsensi);

        if ($scanKartuAbsensi->status === ScanKartuAbsensi::GAGAL) {
            return redirect()->route('peserta.scan-kartu.create')->with('error', 'Scan ini gagal dibaca. Silakan scan ulang.');
        }

        return view('peserta.scan-kartu.show', [
            'scan' => $scanKartuAbsensi,
            'baris' => $this->baca->pratinjau($scanKartuAbsensi->load('pesertaMagang')),
        ]);
    }

    public function simpan(Request $request, ScanKartuAbsensi $scanKartuAbsensi, KalenderLogbook $kalender): RedirectResponse
    {
        Gate::authorize('update', $scanKartuAbsensi);

        $jumlah = $this->baca->simpan($scanKartuAbsensi->load('pesertaMagang'), (array) $request->input('baris', []));
        [$awal] = $scanKartuAbsensi->rentang();

        return redirect()
            ->route('peserta.logbook.index', ['periode' => $kalender->kunciPeriode($scanKartuAbsensi->tanggal($awal))])
            ->with('success', "Jam {$jumlah} tanggal dari kartu absensi disimpan dan menunggu verifikasi admin. Lengkapi activity di setiap tanggal tersebut.");
    }

    private function peserta(Request $request): PesertaMagang
    {
        return $request->user()->pesertaMagang;
    }
}
