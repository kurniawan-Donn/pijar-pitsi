<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\MemilihBulanAbsensi;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifikasiLogbookRequest;
use App\Models\PesertaMagang;
use App\Services\AbsensiSheetService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Logbook kegiatan harian peserta.
 *
 * Isi logbook (Activity, Output, Lokasi) dibaca dari Google Sheets peserta,
 * dan verifikasi pembimbing ditulis ke kolom Action (Diterima / Ditolak / Review).
 */
class LogbookController extends Controller
{
    use MemilihBulanAbsensi;

    /**
     * Halaman logbook satu bulan.
     */
    public function show(Request $request, PesertaMagang $pesertaMagang, AbsensiSheetService $absensi): View
    {
        $bulan = $this->bulanDariRequest($request);
        $sheet = $this->bacaSheetBulan($pesertaMagang, $bulan, $absensi);

        // Logbook hanya berisi hari yang punya catatan kegiatan / output.
        $logbook = array_values(array_filter(
            $sheet['rows'],
            fn (array $row) => trim($row['activity'].$row['output'].$row['lokasi']) !== ''
        ));

        return view('superadmin.peserta-magang.logbook', [
            'pesertaMagang' => $pesertaMagang,
            'logbook' => $logbook,
            'ringkasan' => self::ringkasan($logbook),
            'sheetError' => $sheet['error'],
            'tabAktif' => $sheet['tab'],
            'bulanDipilih' => $bulan,
            'pilihanBulan' => $this->pilihanBulan($pesertaMagang, $bulan),
        ]);
    }

    /**
     * Setel status verifikasi (kolom Action) untuk hari-hari terpilih.
     */
    public function update(
        VerifikasiLogbookRequest $request,
        PesertaMagang $pesertaMagang,
        AbsensiSheetService $absensi
    ): RedirectResponse {
        $validated = $request->validated();

        $redirect = redirect()->route('superadmin.peserta-magang.logbook', [
            'pesertaMagang' => $pesertaMagang,
            'bulan' => $validated['bulan'],
        ]);

        if (! $pesertaMagang->google_sheet_url) {
            return $redirect->with('error', 'Peserta ini belum memiliki Google Sheets.');
        }

        try {
            $absensi->perbaruiAction(
                $pesertaMagang->google_sheet_url,
                CarbonImmutable::createFromFormat('!Y-m', $validated['bulan']),
                array_map('intval', $validated['baris']),
                $validated['action']
            );
        } catch (\Throwable $e) {
            report($e);

            return $redirect->with('error', 'Verifikasi logbook gagal disimpan. Periksa koneksi dan izin Google Sheets.');
        }

        return $redirect->with(
            'success',
            count($validated['baris']).' hari logbook ditandai "'.$validated['action'].'".'
        );
    }

    /**
     * Jumlah hari per status verifikasi.
     *
     * @param  array<int, array<string, mixed>>  $logbook
     * @return array{total: int, Diterima: int, Ditolak: int, Review: int, belum: int}
     */
    public static function ringkasan(array $logbook): array
    {
        $ringkasan = ['total' => count($logbook), 'belum' => 0]
            + array_fill_keys(config('absensi.action_options'), 0);

        foreach ($logbook as $row) {
            $action = self::actionBaku($row['action'] ?? '');
            $action === null ? $ringkasan['belum']++ : $ringkasan[$action]++;
        }

        return $ringkasan;
    }

    /**
     * Cocokkan isi kolom Action dengan pilihan baku (tanpa membedakan huruf besar/kecil).
     */
    public static function actionBaku(?string $nilai): ?string
    {
        foreach (config('absensi.action_options') as $pilihan) {
            if (strcasecmp(trim((string) $nilai), $pilihan) === 0) {
                return $pilihan;
            }
        }

        return null;
    }
}
