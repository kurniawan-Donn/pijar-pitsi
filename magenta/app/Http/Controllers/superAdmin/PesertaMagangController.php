<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PesertaMagang;
use App\Services\GoogleSheetsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesertaMagangController extends Controller
{
    protected GoogleSheetsService $googleSheets;

    /**
     * Menyiapkan service Google Sheets.
     */
    public function __construct(
        GoogleSheetsService $googleSheets
    ) {
        $this->googleSheets = $googleSheets;
    }

    /**
     * Menampilkan daftar peserta magang.
     */
    public function index(): View
    {
        $pesertaMagangs = PesertaMagang::orderBy(
            'nama',
            'asc'
        )->paginate(10);

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
        return view(
            'superadmin.peserta-magang.create'
        );
    }

    /**
     * Menyimpan peserta baru.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'nim' => [
                'nullable',
                'string',
                'max:50',
            ],

            'asal_kampus' => [
                'nullable',
                'string',
                'max:255',
            ],

            'program_studi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'periode_mulai' => [
                'nullable',
                'date',
            ],

            'periode_selesai' => [
                'nullable',
                'date',
                'after_or_equal:periode_mulai',
            ],

            'pembimbing' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'in:aktif,selesai',
            ],

            'google_sheet_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
        ], [
            'nama.required' =>
                'Nama peserta wajib diisi.',

            'periode_selesai.after_or_equal' =>
                'Tanggal selesai harus sama atau setelah tanggal mulai.',

            'status.in' =>
                'Status peserta tidak valid.',

            'google_sheet_url.url' =>
                'Link Google Sheets tidak valid.',
        ]);

        PesertaMagang::create(
            $validated
        );

        return redirect()
            ->route(
                'superadmin.peserta-magang.index'
            )
            ->with(
                'success',
                'Data peserta magang berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail peserta
     * sekaligus membaca data dari Google Sheets.
     */
    public function show(
        PesertaMagang $pesertaMagang
    ): View {
        /*
        |--------------------------------------------------------------------------
        | ABSENSI LOKAL
        |--------------------------------------------------------------------------
        |
        | Tetap dimuat untuk menjaga data lokal
        | yang mungkin sudah ada.
        |
        */

        $pesertaMagang->load([
            'absensis' => function ($query) {
                $query->orderBy(
                    'tanggal',
                    'desc'
                );
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | GOOGLE SHEETS
        |--------------------------------------------------------------------------
        */

        $sheetRows = [];

        $sheetError = null;

        /*
        |--------------------------------------------------------------------------
        | NAMA TAB BULAN
        |--------------------------------------------------------------------------
        |
        | Contoh:
        | SEPTEMBER 2026
        |
        */

        $currentMonthTitle = strtoupper(
            now()->format('F Y')
        );

        /*
        |--------------------------------------------------------------------------
        | CEK LINK GOOGLE SHEETS
        |--------------------------------------------------------------------------
        */

        if (
            !$pesertaMagang->google_sheet_url
        ) {
            $sheetError =
                'Peserta ini belum memiliki link Google Sheets.';
        } else {
            try {
                /*
                |--------------------------------------------------------------------------
                | RANGE GOOGLE SHEETS
                |--------------------------------------------------------------------------
                |
                | B11 = No
                | C11 = Tanggal
                | D11 = Pagi Masuk
                | E11 = Pagi Keluar
                | F11 = Siang Masuk
                | G11 = Siang Keluar
                | H11 = Activity
                | I11 = Output
                | J11 = Lokasi
                | K11 = Action
                |
                | Baris 11 = header
                | Baris 12 = sub header
                | Baris 13 = data pertama
                |
                */

                $range =
                    "'{$currentMonthTitle}'!B11:K100";

                /*
                |--------------------------------------------------------------------------
                | BACA GOOGLE SHEETS
                |--------------------------------------------------------------------------
                */

                $values =
                    $this->googleSheets
                        ->getValuesFromUrl(
                            $pesertaMagang->google_sheet_url,
                            $range
                        );

                /*
                |--------------------------------------------------------------------------
                | PROSES DATA
                |--------------------------------------------------------------------------
                */

                foreach (
                    $values as $index => $row
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | Lewati dua baris header.
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $index < 2
                    ) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Pastikan selalu terdapat 10 kolom.
                    |--------------------------------------------------------------------------
                    */

                    $row = array_pad(
                        $row,
                        10,
                        ''
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Lewati baris kosong.
                    |--------------------------------------------------------------------------
                    */

                    $isEmpty = true;

                    foreach (
                        $row as $value
                    ) {
                        if (
                            trim(
                                (string) $value
                            ) !== ''
                        ) {
                            $isEmpty = false;

                            break;
                        }
                    }

                    if ($isEmpty) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Nomor baris asli Google Sheets.
                    |--------------------------------------------------------------------------
                    |
                    | Range dimulai dari baris 11.
                    |
                    | index 0 = row 11
                    | index 1 = row 12
                    | index 2 = row 13
                    |
                    */

                    $sheetRow =
                        $index + 11;

                    /*
                    |--------------------------------------------------------------------------
                    | Simpan data.
                    |--------------------------------------------------------------------------
                    */

                    $sheetRows[] = [
                        'sheet_row' =>
                            $sheetRow,

                        'no' =>
                            $row[0] ?? '',

                        'tanggal' =>
                            $row[1] ?? '',

                        'pagi_masuk' =>
                            $row[2] ?? '',

                        'pagi_keluar' =>
                            $row[3] ?? '',

                        'siang_masuk' =>
                            $row[4] ?? '',

                        'siang_keluar' =>
                            $row[5] ?? '',

                        'activity' =>
                            $row[6] ?? '',

                        'output' =>
                            $row[7] ?? '',

                        'lokasi' =>
                            $row[8] ?? '',

                        'action' =>
                            $row[9] ?? '',
                    ];
                }
            } catch (
                \Throwable $e
            ) {
                $sheetError =
                    'Data Google Sheets belum dapat dibaca. '
                    . 'Periksa koneksi, URL spreadsheet, '
                    . 'dan izin Service Account.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

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
    public function edit(
        PesertaMagang $pesertaMagang
    ): View {
        return view(
            'superadmin.peserta-magang.edit',
            compact('pesertaMagang')
        );
    }

    /**
     * Memperbarui data peserta.
     */
    public function update(
        Request $request,
        PesertaMagang $pesertaMagang
    ): RedirectResponse {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'nim' => [
                'nullable',
                'string',
                'max:50',
            ],

            'asal_kampus' => [
                'nullable',
                'string',
                'max:255',
            ],

            'program_studi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'periode_mulai' => [
                'nullable',
                'date',
            ],

            'periode_selesai' => [
                'nullable',
                'date',
                'after_or_equal:periode_mulai',
            ],

            'pembimbing' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'in:aktif,selesai',
            ],

            'google_sheet_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
        ], [
            'nama.required' =>
                'Nama peserta wajib diisi.',

            'periode_selesai.after_or_equal' =>
                'Tanggal selesai harus sama atau setelah tanggal mulai.',

            'status.in' =>
                'Status peserta tidak valid.',

            'google_sheet_url.url' =>
                'Link Google Sheets tidak valid.',
        ]);

        $pesertaMagang->update(
            $validated
        );

        return redirect()
            ->route(
                'superadmin.peserta-magang.index'
            )
            ->with(
                'success',
                'Data peserta magang berhasil diperbarui.'
            );
    }

    /**
     * Memperbarui absensi peserta langsung
     * ke Google Sheets.
     */
    public function updateAbsensi(
        Request $request,
        PesertaMagang $pesertaMagang
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'sheet_row' => [
                'required',
                'integer',
                'min:13',
            ],

            'pagi_masuk' => [
                'nullable',
                'date_format:H:i',
            ],

            'pagi_keluar' => [
                'nullable',
                'date_format:H:i',
            ],

            'siang_masuk' => [
                'nullable',
                'date_format:H:i',
            ],

            'siang_keluar' => [
                'nullable',
                'date_format:H:i',
            ],
        ], [
            'sheet_row.required' =>
                'Baris Google Sheets tidak ditemukan.',

            'sheet_row.integer' =>
                'Baris Google Sheets tidak valid.',

            'sheet_row.min' =>
                'Baris Google Sheets tidak valid.',

            'pagi_masuk.date_format' =>
                'Format Pagi Masuk harus HH:MM.',

            'pagi_keluar.date_format' =>
                'Format Pagi Keluar harus HH:MM.',

            'siang_masuk.date_format' =>
                'Format Siang Masuk harus HH:MM.',

            'siang_keluar.date_format' =>
                'Format Siang Keluar harus HH:MM.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CEK GOOGLE SHEETS
        |--------------------------------------------------------------------------
        */

        if (
            !$pesertaMagang->google_sheet_url
        ) {
            return redirect()
                ->route(
                    'superadmin.peserta-magang.show',
                    $pesertaMagang
                )
                ->with(
                    'error',
                    'Peserta ini belum memiliki Google Sheets.'
                );
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | NAMA TAB
            |--------------------------------------------------------------------------
            */

            $sheetTitle = strtoupper(
                now()->format('F Y')
            );

            /*
            |--------------------------------------------------------------------------
            | NOMOR BARIS GOOGLE SHEETS
            |--------------------------------------------------------------------------
            */

            $sheetRow = (int) $validated['sheet_row'];

            /*
            |--------------------------------------------------------------------------
            | RANGE ABSENSI
            |--------------------------------------------------------------------------
            |
            | D = Pagi Masuk
            | E = Pagi Keluar
            | F = Siang Masuk
            | G = Siang Keluar
            |
            */

            $range =
                "'{$sheetTitle}'!"
                . "D{$sheetRow}:G{$sheetRow}";

            /*
            |--------------------------------------------------------------------------
            | DATA YANG AKAN DISIMPAN
            |--------------------------------------------------------------------------
            */

            $values = [
                [
                    $validated['pagi_masuk'] ?? '',
                    $validated['pagi_keluar'] ?? '',
                    $validated['siang_masuk'] ?? '',
                    $validated['siang_keluar'] ?? '',
                ],
            ];

            /*
            |--------------------------------------------------------------------------
            | UPDATE GOOGLE SHEETS
            |--------------------------------------------------------------------------
            */

            $this->googleSheets
                ->updateValuesFromUrl(
                    $pesertaMagang->google_sheet_url,
                    $range,
                    $values
                );

            /*
            |--------------------------------------------------------------------------
            | BERHASIL
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'superadmin.peserta-magang.show',
                    $pesertaMagang
                )
                ->with(
                    'success',
                    'Data absensi berhasil diperbarui di Google Sheets.'
                );

        } catch (
            \Throwable $e
        ) {

            /*
            |--------------------------------------------------------------------------
            | GAGAL
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'superadmin.peserta-magang.show',
                    $pesertaMagang
                )
                ->with(
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
    public function destroy(
        PesertaMagang $pesertaMagang
    ): RedirectResponse {
        $pesertaMagang->delete();

        return redirect()
            ->route(
                'superadmin.peserta-magang.index'
            )
            ->with(
                'success',
                'Data peserta magang berhasil dihapus.'
            );
    }
}