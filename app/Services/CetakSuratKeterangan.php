<?php

namespace App\Services;

use App\Models\SuratKeterangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * PDF surat keterangan magang. Surat terbit memakai salinan data saat
 * diterbitkan; draft memakai data terkini dan diberi tanda DRAFT.
 */
class CetakSuratKeterangan
{
    public function pdf(SuratKeterangan $surat): Response
    {
        $surat->loadMissing('pesertaMagang');
        $draft = ! $surat->sudahTerbit();

        $data = $draft
            ? SuratKeterangan::snapshot($surat->pesertaMagang, CarbonImmutable::today(), $surat->tampilkan_nilai)
            : $surat->data;

        $nama = 'surat-keterangan-magang-'.Str::slug($data['nama'] ?? 'peserta').($draft ? '-draft' : '').'.pdf';

        return Pdf::loadView('surat.cetak', [
            'data' => $data,
            'nomor' => $surat->nomor_surat,
            'draft' => $draft,
        ])->setPaper('a4', 'portrait')->download($nama);
    }
}
