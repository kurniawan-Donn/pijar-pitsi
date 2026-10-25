<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\PengaturanMagang;
use App\Models\Perusahaan;
use App\Models\PesertaMagang;
use App\Models\SuratKeterangan;
use App\Services\CetakSuratKeterangan;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Surat keterangan magang: tinjau draft, terbitkan (nomor otomatis), unduh PDF.
 */
class SuratKeteranganController extends Controller
{
    public function show(PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load(['suratKeterangan.penerbit', 'biodata', 'penilaian', 'feedback', 'penempatans.divisi']);
        $surat = $pesertaMagang->suratKeterangan;

        return view('superadmin.peserta-magang.surat', [
            'pesertaMagang' => $pesertaMagang,
            'surat' => $surat,
            'syarat' => SuratKeterangan::syaratBelumTerpenuhi($pesertaMagang),
            'pratinjau' => SuratKeterangan::snapshot($pesertaMagang, CarbonImmutable::today(), $surat?->tampilkan_nilai ?? false),
            'profilPerusahaanKosong' => ! Perusahaan::query()->where(fn ($query) => $query->whereNotNull('nama_perusahaan')->orWhereNotNull('nama'))->exists(),
            'contohNomor' => SuratKeterangan::formatNomor(
                PengaturanMagang::ambil()->format_nomor_surat,
                SuratKeterangan::urutBerikutnya((int) now()->format('Y')),
                now()
            ),
        ]);
    }

    /**
     * Buat draft untuk peserta yang sudah selesai sebelum fitur ini ada.
     */
    public function buatDraft(PesertaMagang $pesertaMagang): RedirectResponse
    {
        if ($pesertaMagang->status !== 'selesai') {
            return $this->kembali($pesertaMagang)->with('error', 'Draft surat dibuat saat status peserta menjadi selesai.');
        }

        SuratKeterangan::firstOrCreate(['peserta_magang_id' => $pesertaMagang->id]);

        return $this->kembali($pesertaMagang)->with('success', 'Draft surat keterangan dibuat.');
    }

    /**
     * Terbitkan (atau terbitkan ulang dengan nomor yang sama).
     */
    public function terbitkan(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_surat' => ['required', 'date'],
            'tampilkan_nilai' => ['nullable', 'boolean'],
        ], [], ['tanggal_surat' => 'tanggal surat']);

        $surat = $pesertaMagang->suratKeterangan;

        if (! $surat) {
            return $this->kembali($pesertaMagang)->with('error', 'Draft surat belum ada.');
        }

        if ($syarat = SuratKeterangan::syaratBelumTerpenuhi($pesertaMagang)) {
            return $this->kembali($pesertaMagang)->with('error', 'Surat belum bisa diterbitkan: '.implode(' ', $syarat));
        }

        $tanggal = CarbonImmutable::parse($validated['tanggal_surat'])->startOfDay();
        $tampilkanNilai = (bool) ($validated['tampilkan_nilai'] ?? false);
        $ulang = $surat->sudahTerbit();

        DB::transaction(function () use ($surat, $pesertaMagang, $tanggal, $tampilkanNilai, $request) {
            // Nomor hanya diberikan sekali; terbit ulang memakai nomor yang sama.
            if (! $surat->nomor_surat) {
                $urut = SuratKeterangan::urutBerikutnya($tanggal->year);

                $surat->fill([
                    'tahun' => $tanggal->year,
                    'urut' => $urut,
                    'nomor_surat' => SuratKeterangan::formatNomor(PengaturanMagang::ambil()->format_nomor_surat, $urut, $tanggal),
                ]);
            }

            $surat->pastikanKodeVerifikasi();

            $surat->fill([
                'status' => SuratKeterangan::TERBIT,
                'tanggal_surat' => $tanggal->toDateString(),
                'tampilkan_nilai' => $tampilkanNilai,
                'data' => SuratKeterangan::snapshot($pesertaMagang, $tanggal, $tampilkanNilai),
                'diterbitkan_oleh' => $request->user()->id,
                'diterbitkan_pada' => now(),
            ])->save();
        });

        LogAktivitas::catat(
            $ulang ? 'terbitkan_ulang_surat' : 'terbitkan_surat',
            $pesertaMagang,
            "Surat keterangan {$surat->nomor_surat} untuk {$pesertaMagang->nama} ".($ulang ? 'diterbitkan ulang' : 'diterbitkan')
        );

        return $this->kembali($pesertaMagang)->with('success', $ulang
            ? "Surat {$surat->nomor_surat} diterbitkan ulang dengan data terbaru."
            : "Surat {$surat->nomor_surat} diterbitkan. Peserta sudah bisa mengunduhnya.");
    }

    public function pdf(PesertaMagang $pesertaMagang, CetakSuratKeterangan $cetak): Response|RedirectResponse
    {
        $surat = $pesertaMagang->suratKeterangan;

        if (! $surat) {
            return $this->kembali($pesertaMagang)->with('error', 'Draft surat belum ada.');
        }

        return $cetak->pdf($surat);
    }

    private function kembali(PesertaMagang $pesertaMagang): RedirectResponse
    {
        return redirect()->route('superadmin.peserta-magang.surat', $pesertaMagang);
    }
}
