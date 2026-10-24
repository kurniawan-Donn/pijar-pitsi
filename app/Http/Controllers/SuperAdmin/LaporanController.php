<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\LaporanMagang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Halaman laporan (rekap peserta, absensi, nilai).
     */
    public function index(Request $request, LaporanMagang $laporan): View
    {
        [$jenis, $filter] = $this->filter($request);

        return view('superadmin.laporan.index', [
            'jenis' => $jenis,
            'filter' => $filter,
            'laporan' => $laporan->buat($jenis, $filter),
            'daftarJenis' => LaporanMagang::JENIS,
        ]);
    }

    /**
     * Versi cetak (untuk dicetak atau disimpan sebagai PDF dari browser).
     */
    public function cetak(Request $request, LaporanMagang $laporan): View
    {
        [$jenis, $filter] = $this->filter($request);

        return view('superadmin.laporan.cetak', [
            'laporan' => $laporan->buat($jenis, $filter),
        ]);
    }

    /**
     * Unduh laporan sebagai CSV yang langsung terbaca rapi di Excel.
     */
    public function csv(Request $request, LaporanMagang $laporan): StreamedResponse
    {
        [$jenis, $filter] = $this->filter($request);
        $data = $laporan->buat($jenis, $filter);

        $namaFile = 'laporan-'.$jenis.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($data) {
            $output = fopen('php://output', 'w');

            // BOM agar Excel membaca UTF-8; pemisah ";" sesuai pengaturan regional Indonesia.
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [$data['judul']], ';');
            fputcsv($output, [$data['keterangan']], ';');
            fputcsv($output, [], ';');
            fputcsv($output, $data['kolom'], ';');

            foreach ($data['baris'] as $baris) {
                fputcsv($output, $baris, ';');
            }

            fclose($output);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Ambil & validasi jenis laporan serta filternya.
     *
     * @return array{0: string, 1: array<string, ?string>}
     */
    private function filter(Request $request): array
    {
        $validated = $request->validate([
            'jenis' => ['nullable', Rule::in(array_keys(LaporanMagang::JENIS))],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'status' => ['nullable', Rule::in(['aktif', 'selesai'])],
            'bulan' => ['nullable', 'date_format:Y-m'],
        ], [
            'sampai.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
            'bulan.date_format' => 'Format bulan tidak valid.',
        ]);

        return [
            $validated['jenis'] ?? 'peserta',
            [
                'dari' => $validated['dari'] ?? null,
                'sampai' => $validated['sampai'] ?? null,
                'status' => $validated['status'] ?? null,
                'bulan' => $validated['bulan'] ?? now()->format('Y-m'),
            ],
        ];
    }
}
