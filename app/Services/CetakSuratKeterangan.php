<?php

namespace App\Services;

use App\Models\PengaturanMagang;
use App\Models\SuratKeterangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * PDF surat keterangan magang. Surat terbit memakai salinan data saat
 * diterbitkan; draft memakai data terkini dan diberi tanda DRAFT.
 * Surat terbit diberi QR menuju halaman verifikasi publik.
 */
class CetakSuratKeterangan
{
    public function __construct(
        private TandaTanganDokumen $tandaTangan,
        private KodeQr $kodeQr,
    ) {}

    public function pdf(SuratKeterangan $surat): Response
    {
        $surat->loadMissing('pesertaMagang');
        $draft = ! $surat->sudahTerbit();

        $data = $draft
            ? SuratKeterangan::snapshot($surat->pesertaMagang, CarbonImmutable::today(), $surat->tampilkan_nilai)
            : $surat->data;

        [$ttdPath, $stempelPath] = $this->gambarPenandaTangan($data['penanda_tangan'] ?? []);
        $urlVerifikasi = $surat->urlVerifikasi();

        $nama = 'surat-keterangan-magang-'.Str::slug($data['nama'] ?? 'peserta').($draft ? '-draft' : '').'.pdf';

        return Pdf::loadView('surat.cetak', [
            'data' => $data,
            'nomor' => $surat->nomor_surat,
            'draft' => $draft,
            'ttd' => $this->tandaTangan->dataUri($ttdPath),
            'stempel' => $this->tandaTangan->dataUri($stempelPath),
            'qr' => $urlVerifikasi ? $this->kodeQr->dataUri($urlVerifikasi) : null,
            'urlVerifikasi' => $urlVerifikasi,
        ])->setPaper('a4', 'portrait')->download($nama);
    }

    /**
     * Path gambar tanda tangan & stempel. Surat terbit sebelum fitur ini belum
     * menyimpannya; untuk surat itu dipakai gambar sekarang hanya bila nama
     * penanda tangannya masih sama.
     *
     * @param  array<string, mixed>  $penandaTangan
     * @return array{?string, ?string}
     */
    private function gambarPenandaTangan(array $penandaTangan): array
    {
        if (array_key_exists('ttd_path', $penandaTangan)) {
            return [$penandaTangan['ttd_path'], $penandaTangan['stempel_path'] ?? null];
        }

        $pengaturan = PengaturanMagang::ambil();

        if (($penandaTangan['nama'] ?? null) && $penandaTangan['nama'] === $pengaturan->menyetujui_nama) {
            return [$pengaturan->menyetujui_ttd_path, $pengaturan->stempel_path];
        }

        return [null, null];
    }
}
