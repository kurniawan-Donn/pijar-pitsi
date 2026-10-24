<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\PesertaMagang;
use App\Services\CredentialTidakDitemukanException;
use App\Services\ImporRiwayatLogbook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Impor riwayat logbook dari buku kerja lama (Google Sheets atau .xlsx).
 */
class ImporLogbookController extends Controller
{
    public function __construct(private ImporRiwayatLogbook $impor) {}

    public function dariGoogleSheets(PesertaMagang $pesertaMagang): RedirectResponse
    {
        if ($tolak = $this->cekPeserta($pesertaMagang)) {
            return $tolak;
        }

        if (! $pesertaMagang->google_sheet_url) {
            return $this->kembali($pesertaMagang)->with('error', 'Peserta ini belum punya link Google Sheets.');
        }

        try {
            $hasil = $this->impor->dariGoogleSheets($pesertaMagang);
        } catch (CredentialTidakDitemukanException $e) {
            return $this->kembali($pesertaMagang)->with('error', $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->kembali($pesertaMagang)->with('error', 'Google Sheets peserta tidak dapat dibaca. Pastikan sheet dibagikan ke service account aplikasi.');
        }

        return $this->selesai($pesertaMagang, $hasil, 'Google Sheets');
    }

    public function dariXlsx(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $request->validateWithBag('impor', [
            'berkas' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
        ], [
            'berkas.required' => 'Pilih file buku kerja (.xlsx).',
            'berkas.mimes' => 'File harus berformat .xlsx.',
            'berkas.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        if ($tolak = $this->cekPeserta($pesertaMagang)) {
            return $tolak;
        }

        try {
            $hasil = $this->impor->dariXlsx($pesertaMagang, $request->file('berkas')->getRealPath());
        } catch (Throwable $e) {
            report($e);

            return $this->kembali($pesertaMagang)->with('error', 'File tidak dapat dibaca sebagai buku kerja Excel.');
        }

        return $this->selesai($pesertaMagang, $hasil, 'file '.$request->file('berkas')->getClientOriginalName());
    }

    /**
     * Hapus hasil impor (hari tanpa bukti) supaya bisa diimpor ulang.
     */
    public function hapus(PesertaMagang $pesertaMagang): RedirectResponse
    {
        $jumlah = $this->impor->hapus($pesertaMagang);

        LogAktivitas::catat('hapus_impor_logbook', $pesertaMagang, "{$jumlah} hari hasil impor logbook {$pesertaMagang->nama} dihapus");

        return $this->kembali($pesertaMagang)->with('success', "{$jumlah} hari hasil impor dihapus.");
    }

    private function cekPeserta(PesertaMagang $pesertaMagang): ?RedirectResponse
    {
        if (! $pesertaMagang->periode_mulai || ! $pesertaMagang->periode_selesai) {
            return $this->kembali($pesertaMagang)->with('error', 'Isi tanggal mulai dan selesai magang peserta sebelum mengimpor.');
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $hasil
     */
    private function selesai(PesertaMagang $pesertaMagang, array $hasil, string $sumber): RedirectResponse
    {
        LogAktivitas::catat('impor_logbook', $pesertaMagang, "Impor logbook {$pesertaMagang->nama} dari {$sumber}: {$hasil['dibuat']} hari", [
            'dibuat' => $hasil['dibuat'],
            'sudah_ada' => $hasil['sudah_ada'],
            'di_luar' => $hasil['di_luar'],
            'peringatan' => count($hasil['peringatan']),
        ]);

        return $this->kembali($pesertaMagang)
            ->with($hasil['dibuat'] > 0 ? 'success' : 'warning', "Impor dari {$sumber} selesai: {$hasil['dibuat']} hari ditambahkan.")
            ->with('hasil_impor', $hasil);
    }

    private function kembali(PesertaMagang $pesertaMagang): RedirectResponse
    {
        return redirect()->to(route('superadmin.peserta-magang.logbook-harian', $pesertaMagang).'#impor');
    }
}
