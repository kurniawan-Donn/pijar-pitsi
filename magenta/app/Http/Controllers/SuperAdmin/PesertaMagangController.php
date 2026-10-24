<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PesertaMagangRequest;
use App\Models\PesertaMagang;
use App\Services\GoogleSheetsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesertaMagangController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LAYOUT GOOGLE SHEETS ABSENSI
    |--------------------------------------------------------------------------
    |
    | B11 = No            G11 = Siang Keluar
    | C11 = Tanggal       H11 = Activity
    | D11 = Pagi Masuk    I11 = Output
    | E11 = Pagi Keluar   J11 = Lokasi
    | F11 = Siang Masuk   K11 = Action
    |
    | Baris 11 = header, baris 12 = sub header, baris 13 = data pertama.
    |
    */

    private const SHEET_HEADER_ROW = 11;

    private const SHEET_FIRST_DATA_ROW = 13;

    private const SHEET_COLUMNS = [
        'no',
        'tanggal',
        'pagi_masuk',
        'pagi_keluar',
        'siang_masuk',
        'siang_keluar',
        'activity',
        'output',
        'lokasi',
        'action',
    ];

    /**
     * Menampilkan daftar peserta magang.
     */
    public function index(): View
    {
        $pesertaMagangs = PesertaMagang::orderBy('nama', 'asc')
            ->paginate(10);

        return view(
            'superadmin.peserta-magang.index',
            compact('pesertaMagangs')
        );
    }

    /**
     * Menampilkan form tambah peserta.
     */
    public function create(): View
    {
        return view('superadmin.peserta-magang.create');
    }

    /**
     * Menyimpan peserta baru.
     */
    public function store(PesertaMagangRequest $request): RedirectResponse
    {
        PesertaMagang::create($request->validated());

        return redirect()
            ->route('superadmin.peserta-magang.index')
            ->with('success', 'Data peserta magang berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail peserta
     * sekaligus membaca data dari Google Sheets.
     */
    public function show(PesertaMagang $pesertaMagang): View
    {
        // Absensi lokal tetap dimuat untuk menjaga data lama.
        $pesertaMagang->load([
            'absensis' => fn ($query) => $query->orderBy('tanggal', 'desc'),
        ]);

        $sheetRows = [];
        $sheetError = null;

        // Nama tab bulan, contoh: SEPTEMBER 2026
        $currentMonthTitle = $this->currentSheetTitle();

        if (!$pesertaMagang->google_sheet_url) {
            $sheetError = 'Peserta ini belum memiliki link Google Sheets.';
        } else {
            try {
                $range = "'{$currentMonthTitle}'!B" . self::SHEET_HEADER_ROW . ':K100';

                $values = app(GoogleSheetsService::class)->getValuesFromUrl(
                    $pesertaMagang->google_sheet_url,
                    $range
                );

                $sheetRows = $this->mapSheetRows($values);
            } catch (\Throwable $e) {
                report($e);

                $sheetError = 'Data Google Sheets belum dapat dibaca. '
                    . 'Periksa koneksi, URL spreadsheet, '
                    . 'dan izin Service Account.';
            }
        }

        return view(
            'superadmin.peserta-magang.show',
            compact(
                'pesertaMagang',
                'sheetRows',
                'currentMonthTitle',
                'sheetError'
            )
        );
    }

    /**
     * Menampilkan form edit peserta.
     */
    public function edit(PesertaMagang $pesertaMagang): View
    {
        return view(
            'superadmin.peserta-magang.edit',
            compact('pesertaMagang')
        );
    }

    /**
     * Memperbarui data peserta.
     */
    public function update(
        PesertaMagangRequest $request,
        PesertaMagang $pesertaMagang
    ): RedirectResponse {
        $pesertaMagang->update($request->validated());

        return redirect()
            ->route('superadmin.peserta-magang.index')
            ->with('success', 'Data peserta magang berhasil diperbarui.');
    }

    /**
     * Memperbarui absensi peserta langsung
     * ke Google Sheets.
     */
    public function updateAbsensi(
        Request $request,
        PesertaMagang $pesertaMagang
    ): RedirectResponse {
        $validated = $request->validate([
            'sheet_row' => ['required', 'integer', 'min:' . self::SHEET_FIRST_DATA_ROW],
            'pagi_masuk' => ['nullable', 'date_format:H:i'],
            'pagi_keluar' => ['nullable', 'date_format:H:i'],
            'siang_masuk' => ['nullable', 'date_format:H:i'],
            'siang_keluar' => ['nullable', 'date_format:H:i'],
        ], [
            'sheet_row.required' => 'Baris Google Sheets tidak ditemukan.',
            'sheet_row.integer' => 'Baris Google Sheets tidak valid.',
            'sheet_row.min' => 'Baris Google Sheets tidak valid.',
            'pagi_masuk.date_format' => 'Format Pagi Masuk harus HH:MM.',
            'pagi_keluar.date_format' => 'Format Pagi Keluar harus HH:MM.',
            'siang_masuk.date_format' => 'Format Siang Masuk harus HH:MM.',
            'siang_keluar.date_format' => 'Format Siang Keluar harus HH:MM.',
        ]);

        $redirect = redirect()->route(
            'superadmin.peserta-magang.show',
            $pesertaMagang
        );

        if (!$pesertaMagang->google_sheet_url) {
            return $redirect->with(
                'error',
                'Peserta ini belum memiliki Google Sheets.'
            );
        }

        try {
            $sheetRow = (int) $validated['sheet_row'];

            // D = Pagi Masuk, E = Pagi Keluar, F = Siang Masuk, G = Siang Keluar
            $range = "'{$this->currentSheetTitle()}'!D{$sheetRow}:G{$sheetRow}";

            $values = [[
                $validated['pagi_masuk'] ?? '',
                $validated['pagi_keluar'] ?? '',
                $validated['siang_masuk'] ?? '',
                $validated['siang_keluar'] ?? '',
            ]];

            app(GoogleSheetsService::class)->updateValuesFromUrl(
                $pesertaMagang->google_sheet_url,
                $range,
                $values
            );

            return $redirect->with(
                'success',
                'Data absensi berhasil diperbarui di Google Sheets.'
            );
        } catch (\Throwable $e) {
            report($e);

            return $redirect->with(
                'error',
                'Data absensi gagal diperbarui. '
                . 'Periksa koneksi dan izin Google Sheets.'
            );
        }
    }

    /**
     * Menghapus peserta.
     *
     * Absensi lokal peserta juga akan ikut terhapus
     * karena menggunakan cascadeOnDelete().
     */
    public function destroy(PesertaMagang $pesertaMagang): RedirectResponse
    {
        $pesertaMagang->delete();

        return redirect()
            ->route('superadmin.peserta-magang.index')
            ->with('success', 'Data peserta magang berhasil dihapus.');
    }

    /**
     * Nama tab Google Sheets untuk bulan berjalan.
     */
    private function currentSheetTitle(): string
    {
        return strtoupper(now()->format('F Y'));
    }

    /**
     * Mengubah data mentah Google Sheets menjadi baris absensi.
     */
    private function mapSheetRows(array $values): array
    {
        $rows = [];
        $columnCount = count(self::SHEET_COLUMNS);
        $headerRows = self::SHEET_FIRST_DATA_ROW - self::SHEET_HEADER_ROW;

        foreach ($values as $index => $row) {
            // Lewati baris header & sub header.
            if ($index < $headerRows) {
                continue;
            }

            $row = array_pad($row, $columnCount, '');

            // Lewati baris kosong.
            if (collect($row)->every(fn ($value) => trim((string) $value) === '')) {
                continue;
            }

            $rows[] = [
                // Nomor baris asli di Google Sheets.
                'sheet_row' => $index + self::SHEET_HEADER_ROW,
            ] + array_combine(
                self::SHEET_COLUMNS,
                array_slice($row, 0, $columnCount)
            );
        }

        return $rows;
    }
}
