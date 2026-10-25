<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\LaporanMagang;
use App\Services\PeriodeMagang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
     * Unduh laporan sebagai file Excel (.xlsx).
     */
    public function xlsx(Request $request, LaporanMagang $laporan): BinaryFileResponse
    {
        [$jenis, $filter] = $this->filter($request);
        $data = $laporan->buat($jenis, $filter);

        $tebal = (new Style)->withFontBold(true);
        $judulKolom = (new Style)->withFontBold(true)->withFontColor(Color::WHITE)->withBackgroundColor('12345B');

        $berkas = tempnam(sys_get_temp_dir(), 'laporan-').'.xlsx';

        $writer = new XlsxWriter;
        $writer->openToFile($berkas);
        $writer->getCurrentSheet()->setName(mb_substr(LaporanMagang::JENIS[$jenis], 0, 31));
        $writer->getOptions()->setColumnWidth(6, 1);
        $writer->getOptions()->setColumnWidthForRange(18, 2, max(2, count($data['kolom'])));

        $writer->addRow(Row::fromValuesWithStyle([$data['judul']], $tebal));
        $writer->addRow(Row::fromValues([$data['keterangan']]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValuesWithStyle($data['kolom'], $judulKolom));

        foreach ($data['baris'] as $baris) {
            $writer->addRow(Row::fromValues($baris));
        }

        $writer->addRow(Row::fromValues([]));

        foreach ($data['ringkasan'] as $label => $nilai) {
            $writer->addRow(Row::fromValues([$label, $nilai]));
        }

        $writer->close();

        return response()
            ->download($berkas, 'laporan-'.$jenis.'-'.now()->format('Ymd-His').'.xlsx')
            ->deleteFileAfterSend();
    }

    /**
     * Unduh laporan sebagai file PDF (A4 landscape).
     */
    public function pdf(Request $request, LaporanMagang $laporan): Response
    {
        [$jenis, $filter] = $this->filter($request);

        return Pdf::loadView('superadmin.laporan.pdf', ['laporan' => $laporan->buat($jenis, $filter)])
            ->setPaper('a4', 'landscape')
            ->download('laporan-'.$jenis.'-'.now()->format('Ymd-His').'.pdf');
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
            'periode' => ['nullable', 'date_format:Y-m'],
        ], [
            'sampai.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
            'periode.date_format' => 'Format periode tidak valid.',
        ]);

        return [
            $validated['jenis'] ?? 'peserta',
            [
                'dari' => $validated['dari'] ?? null,
                'sampai' => $validated['sampai'] ?? null,
                'status' => $validated['status'] ?? null,
                'periode' => $validated['periode'] ?? app(PeriodeMagang::class)->kunciUntuk(now()),
            ],
        ];
    }
}
