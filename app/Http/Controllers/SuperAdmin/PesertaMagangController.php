<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PesertaMagangRequest;
use App\Models\PesertaMagang;
use App\Services\AbsensiSheetService;
use App\Services\CredentialTidakDitemukanException;
use App\Services\RekapAbsensi;
use App\Services\TabBulanTidakDitemukanException;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesertaMagangController extends Controller
{
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
     * Menampilkan detail peserta sekaligus membaca absensi
     * bulan terpilih dari Google Sheets.
     */
    public function show(Request $request, PesertaMagang $pesertaMagang, AbsensiSheetService $absensi): View
    {
        $bulan = $this->bulanDariRequest($request);

        $sheetRows = [];
        $sheetError = null;
        $tabAktif = null;
        $diambilPada = null;

        if (! $pesertaMagang->google_sheet_url) {
            $sheetError = 'Peserta ini belum memiliki link Google Sheets.';
        } else {
            try {
                $hasil = $absensi->ambil($pesertaMagang->google_sheet_url, $bulan);

                $sheetRows = $hasil['rows'];
                $tabAktif = $hasil['tab'];
                $diambilPada = CarbonImmutable::parse($hasil['diambil_pada']);
            } catch (TabBulanTidakDitemukanException $e) {
                $sheetError = 'Tab bulan '.$bulan->translatedFormat('F Y').' belum ada di Google Sheets peserta. '
                    .'Tab yang tersedia: '.($e->tersedia ? implode(', ', $e->tersedia) : '(tidak ada)').'.';
            } catch (CredentialTidakDitemukanException $e) {
                report($e);
                $sheetError = 'Koneksi Google Sheets belum diatur: file credential service account belum dipasang di server.';
            } catch (\Throwable $e) {
                report($e);
                $sheetError = 'Data Google Sheets belum dapat dibaca. '
                    .'Periksa koneksi, URL spreadsheet, dan izin Service Account.';
            }
        }

        return view('superadmin.peserta-magang.show', [
            'pesertaMagang' => $pesertaMagang,
            'sheetRows' => $sheetRows,
            'sheetError' => $sheetError,
            'rekap' => RekapAbsensi::hitung($sheetRows),
            'bulanDipilih' => $bulan,
            'pilihanBulan' => $this->pilihanBulan($pesertaMagang, $bulan),
            'tabAktif' => $tabAktif,
            'diambilPada' => $diambilPada,
        ]);
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
     * Memperbarui absensi peserta langsung ke Google Sheets.
     */
    public function updateAbsensi(
        Request $request,
        PesertaMagang $pesertaMagang,
        AbsensiSheetService $absensi
    ): RedirectResponse {
        $validated = $request->validate([
            'bulan' => ['required', 'date_format:Y-m'],
            'sheet_row' => ['required', 'integer', 'min:'.config('absensi.first_data_row'), 'max:'.config('absensi.last_row')],
            'pagi_masuk' => ['nullable', 'date_format:H:i'],
            'pagi_keluar' => ['nullable', 'date_format:H:i'],
            'siang_masuk' => ['nullable', 'date_format:H:i'],
            'siang_keluar' => ['nullable', 'date_format:H:i'],
        ], [
            'bulan.required' => 'Bulan absensi tidak ditemukan.',
            'bulan.date_format' => 'Bulan absensi tidak valid.',
            'sheet_row.required' => 'Baris Google Sheets tidak ditemukan.',
            'sheet_row.integer' => 'Baris Google Sheets tidak valid.',
            'sheet_row.min' => 'Baris Google Sheets tidak valid.',
            'sheet_row.max' => 'Baris Google Sheets tidak valid.',
            'pagi_masuk.date_format' => 'Format Pagi Masuk harus HH:MM.',
            'pagi_keluar.date_format' => 'Format Pagi Keluar harus HH:MM.',
            'siang_masuk.date_format' => 'Format Siang Masuk harus HH:MM.',
            'siang_keluar.date_format' => 'Format Siang Keluar harus HH:MM.',
        ]);

        $bulan = CarbonImmutable::createFromFormat('!Y-m', $validated['bulan']);

        $redirect = redirect()->route('superadmin.peserta-magang.show', [
            'pesertaMagang' => $pesertaMagang,
            'bulan' => $validated['bulan'],
        ]);

        if (! $pesertaMagang->google_sheet_url) {
            return $redirect->with('error', 'Peserta ini belum memiliki Google Sheets.');
        }

        try {
            $absensi->perbaruiWaktu(
                $pesertaMagang->google_sheet_url,
                $bulan,
                (int) $validated['sheet_row'],
                $validated
            );

            return $redirect->with('success', 'Data absensi berhasil diperbarui di Google Sheets.');
        } catch (\Throwable $e) {
            report($e);

            return $redirect->with(
                'error',
                'Data absensi gagal diperbarui. Periksa koneksi dan izin Google Sheets.'
            );
        }
    }

    /**
     * Hapus cache dan baca ulang absensi dari Google Sheets.
     */
    public function muatUlangAbsensi(
        Request $request,
        PesertaMagang $pesertaMagang,
        AbsensiSheetService $absensi
    ): RedirectResponse {
        $bulan = $this->bulanDariRequest($request);

        if ($pesertaMagang->google_sheet_url) {
            $absensi->lupakan($pesertaMagang->google_sheet_url, $bulan);
        }

        return redirect()
            ->route('superadmin.peserta-magang.show', [
                'pesertaMagang' => $pesertaMagang,
                'bulan' => $bulan->format('Y-m'),
            ])
            ->with('success', 'Data absensi dimuat ulang dari Google Sheets.');
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
     * Bulan dari parameter ?bulan=YYYY-MM, default bulan berjalan.
     */
    private function bulanDariRequest(Request $request): CarbonImmutable
    {
        $bulan = (string) $request->input('bulan');

        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)) {
            return CarbonImmutable::createFromFormat('!Y-m', $bulan);
        }

        return CarbonImmutable::now()->startOfMonth();
    }

    /**
     * Pilihan bulan: sepanjang periode magang (maks. 24 bulan),
     * atau 6 bulan terakhir bila periode belum diisi.
     *
     * @return array<string, string> [YYYY-MM => "Oktober 2026"]
     */
    private function pilihanBulan(PesertaMagang $peserta, CarbonImmutable $dipilih): array
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
