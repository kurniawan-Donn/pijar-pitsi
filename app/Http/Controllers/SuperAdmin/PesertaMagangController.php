<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\MemilihBulanAbsensi;
use App\Http\Controllers\Controller;
use App\Http\Requests\PesertaMagangRequest;
use App\Models\LogAktivitas;
use App\Models\PesertaMagang;
use App\Services\AbsensiSheetService;
use App\Services\Logbook;
use App\Services\RekapAbsensi;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PesertaMagangController extends Controller
{
    use MemilihBulanAbsensi;

    /**
     * Menampilkan daftar peserta magang.
     */
    public function index(): View
    {
        $pesertaMagangs = PesertaMagang::with('penilaian')
            ->orderBy('nama', 'asc')
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
        $pesertaMagang->load(['penilaian', 'user', 'pengajuanMagang.mahasiswa']);

        $bulan = $this->bulanDariRequest($request);
        $sheet = $this->bacaSheetBulan($pesertaMagang, $bulan, $absensi);
        $sheetRows = $sheet['rows'];

        $rekap = RekapAbsensi::hitung($sheetRows);

        return view('superadmin.peserta-magang.show', [
            'pesertaMagang' => $pesertaMagang,
            'sheetRows' => $sheetRows,
            'sheetError' => $sheet['error'],
            'rekap' => $rekap,
            'uangSaku' => RekapAbsensi::uangSaku(
                $rekap,
                config('absensi.uang_saku.hari_penuh'),
                config('absensi.uang_saku.setengah_hari')
            ),
            'bulanDipilih' => $bulan,
            'pilihanBulan' => $this->pilihanBulan($pesertaMagang, $bulan),
            'ringkasanLogbook' => Logbook::ringkasan(Logbook::baris($sheetRows)),
            'tabAktif' => $sheet['tab'],
            'diambilPada' => $sheet['diambil_pada'],
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

            LogAktivitas::catat(
                'ubah_absensi',
                $pesertaMagang,
                "Absensi {$pesertaMagang->nama} ({$validated['bulan']}, baris {$validated['sheet_row']}) diperbarui",
                array_intersect_key($validated, array_flip(['bulan', 'sheet_row', 'pagi_masuk', 'pagi_keluar', 'siang_masuk', 'siang_keluar']))
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
        LogAktivitas::catat(
            'hapus_peserta',
            $pesertaMagang,
            "Peserta {$pesertaMagang->nama} dihapus",
            ['nim' => $pesertaMagang->nim, 'status' => $pesertaMagang->status]
        );

        // Akun login peserta ikut dihapus supaya tidak tertinggal tanpa data.
        DB::transaction(function () use ($pesertaMagang) {
            $akun = $pesertaMagang->user;

            $pesertaMagang->delete();
            $akun?->delete();
        });

        return redirect()
            ->route('superadmin.peserta-magang.index')
            ->with('success', 'Data peserta magang berhasil dihapus.');
    }
}
