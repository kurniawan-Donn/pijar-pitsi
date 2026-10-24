<?php

namespace App\Services;

use App\Models\BiodataPeserta;
use App\Models\PengaturanMagang;
use App\Models\Perusahaan;
use App\Models\PesertaMagang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rekap logbook satu periode dalam format sheet bulanan (PDF):
 * kepala (nama, divisi, kampus, nomor), tabel harian, rekap uang saku,
 * rekening, dan blok tanda tangan.
 */
class CetakLogbook
{
    public function __construct(private KalenderLogbook $kalender) {}

    /**
     * @param  array{kunci: string, label: string, mulai: CarbonImmutable, selesai: CarbonImmutable}  $periode
     */
    public function pdf(PesertaMagang $peserta, array $periode): Response
    {
        $peserta->loadMissing(['biodata', 'penempatans.divisi', 'pengajuanMagang.mahasiswa', 'user']);
        $hari = $this->kalender->hari($peserta, $periode);

        $data = [
            'peserta' => $peserta,
            'biodata' => BiodataPeserta::untuk($peserta),
            'periode' => $periode,
            'hari' => $hari,
            'rekap' => $this->kalender->rekap($hari),
            'tarif' => $this->kalender->tarifPeriode($periode),
            'penempatan' => $peserta->penempatanUntuk($periode['kunci']),
            'pengaturan' => PengaturanMagang::ambil(),
            'perusahaan' => Perusahaan::query()->first(),
        ];

        $nama = 'logbook-'.Str::slug($peserta->nama).'-'.$periode['kunci'].'.pdf';

        return Pdf::loadView('logbook.cetak', $data)
            ->setPaper('a4', 'portrait')
            ->download($nama);
    }
}
