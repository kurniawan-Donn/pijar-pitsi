<?php

namespace App\Http\Controllers\Concerns;

use App\Models\PesertaMagang;
use App\Services\AbsensiSheetService;
use App\Services\CredentialTidakDitemukanException;
use App\Services\TabBulanTidakDitemukanException;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/**
 * Pemilihan bulan untuk halaman yang membaca tab bulanan Google Sheets
 * (absensi & logbook).
 */
trait MemilihBulanAbsensi
{
    /**
     * Bulan dari parameter ?bulan=YYYY-MM, default bulan berjalan.
     */
    protected function bulanDariRequest(Request $request): CarbonImmutable
    {
        $bulan = (string) $request->input('bulan');

        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)) {
            return CarbonImmutable::createFromFormat('!Y-m', $bulan);
        }

        return CarbonImmutable::now()->startOfMonth();
    }

    /**
     * Baca tab bulan peserta dan ubah kegagalan menjadi pesan untuk admin.
     *
     * @return array{rows: array<int, array<string, mixed>>, tab: ?string, diambil_pada: ?CarbonImmutable, error: ?string}
     */
    protected function bacaSheetBulan(PesertaMagang $peserta, CarbonImmutable $bulan, AbsensiSheetService $absensi): array
    {
        $hasil = ['rows' => [], 'tab' => null, 'diambil_pada' => null, 'error' => null];

        if (! $peserta->google_sheet_url) {
            return ['error' => 'Peserta ini belum memiliki link Google Sheets.'] + $hasil;
        }

        try {
            $data = $absensi->ambil($peserta->google_sheet_url, $bulan);

            return [
                'rows' => $data['rows'],
                'tab' => $data['tab'],
                'diambil_pada' => CarbonImmutable::parse($data['diambil_pada']),
                'error' => null,
            ];
        } catch (TabBulanTidakDitemukanException $e) {
            return ['error' => 'Tab bulan '.$bulan->translatedFormat('F Y').' belum ada di Google Sheets peserta. '
                .'Tab yang tersedia: '.($e->tersedia ? implode(', ', $e->tersedia) : '(tidak ada)').'.'] + $hasil;
        } catch (CredentialTidakDitemukanException $e) {
            report($e);

            return ['error' => 'Koneksi Google Sheets belum diatur: file credential service account belum dipasang di server.'] + $hasil;
        } catch (\Throwable $e) {
            report($e);

            return ['error' => 'Data Google Sheets belum dapat dibaca. '
                .'Periksa koneksi, URL spreadsheet, dan izin Service Account.'] + $hasil;
        }
    }

    /**
     * Pilihan bulan: sepanjang periode magang (maks. 24 bulan),
     * atau 6 bulan terakhir bila periode belum diisi.
     *
     * @return array<string, string> [YYYY-MM => "Oktober 2026"]
     */
    protected function pilihanBulan(PesertaMagang $peserta, CarbonImmutable $dipilih): array
    {
        $sekarang = CarbonImmutable::now()->startOfMonth();

        $mulai = $peserta->periode_mulai
            ? CarbonImmutable::parse($peserta->periode_mulai)->startOfMonth()
            : $sekarang->subMonths(5);

        $selesai = $peserta->periode_selesai
            ? CarbonImmutable::parse($peserta->periode_selesai)->startOfMonth()
            : $sekarang;

        if ($selesai->lessThan($mulai)) {
            $selesai = $mulai;
        }

        $pilihan = [];

        for ($bulan = $mulai; $bulan->lessThanOrEqualTo($selesai) && count($pilihan) < 24; $bulan = $bulan->addMonth()) {
            $pilihan[$bulan->format('Y-m')] = $bulan->translatedFormat('F Y');
        }

        $pilihan[$dipilih->format('Y-m')] ??= $dipilih->translatedFormat('F Y');
        ksort($pilihan);

        return $pilihan;
    }
}
