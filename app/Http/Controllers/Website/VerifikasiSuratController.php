<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\SuratKeterangan;
use Illuminate\Http\Response;

/**
 * Halaman publik dari QR surat keterangan. Hanya menampilkan nama, periode,
 * dan nomor surat agar data pribadi lain tidak terbuka.
 */
class VerifikasiSuratController extends Controller
{
    public function show(string $kode): Response
    {
        $surat = SuratKeterangan::query()
            ->where('kode_verifikasi', $kode)
            ->where('status', SuratKeterangan::TERBIT)
            ->first();

        $perusahaan = Perusahaan::query()->first();

        return response()->view('website.verifikasi-surat', [
            'surat' => $surat,
            'namaPerusahaan' => $perusahaan?->nama_perusahaan ?: ($perusahaan?->nama ?: config('app.name')),
        ], $surat ? 200 : 404);
    }
}
