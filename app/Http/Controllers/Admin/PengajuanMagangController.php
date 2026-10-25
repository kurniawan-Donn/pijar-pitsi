<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStatusPengajuanRequest;
use App\Models\LogAktivitas;
use App\Models\PengajuanMagang;
use App\Models\PesertaMagang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengajuanMagangController extends Controller
{
    /**
     * Daftar pengajuan dengan filter status & pencarian.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', PengajuanMagang::class);

        $status = $request->query('status');
        $cari = $request->query('cari');

        $pengajuans = PengajuanMagang::with('mahasiswa')
            ->status($status)
            ->cari($cari)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jumlahPerStatus = PengajuanMagang::query()
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        return view('admin.pengajuan.index', [
            'pengajuans' => $pengajuans,
            'jumlahPerStatus' => $jumlahPerStatus,
            'statusAktif' => array_key_exists((string) $status, PengajuanMagang::STATUS_LABELS) ? $status : null,
            'cari' => $cari,
        ]);
    }

    /**
     * Form pengajuan ada di website utama.
     */
    public function create(): RedirectResponse
    {
        return redirect()->to(route('home').'#daftar');
    }

    /**
     * Detail pengajuan.
     */
    public function show(PengajuanMagang $pengajuanMagang): View
    {
        Gate::authorize('view', $pengajuanMagang);

        $pengajuanMagang->load(['mahasiswa', 'perusahaan', 'pesertaMagang']);

        return view('admin.pengajuan.show', compact('pengajuanMagang'));
    }

    /**
     * Unduh / lihat surat pengantar yang diunggah pendaftar.
     */
    public function dokumen(PengajuanMagang $pengajuanMagang): StreamedResponse
    {
        Gate::authorize('view', $pengajuanMagang);

        return $pengajuanMagang->responDokumen();
    }

    /**
     * Update status pengajuan beserta catatan admin.
     */
    public function updateStatus(
        UpdateStatusPengajuanRequest $request,
        PengajuanMagang $pengajuanMagang
    ): RedirectResponse {
        Gate::authorize('update', $pengajuanMagang);

        $statusLama = $pengajuanMagang->status;

        $pengajuanMagang->update($request->validated());

        LogAktivitas::catat(
            'ubah_status_pengajuan',
            $pengajuanMagang,
            "Status {$pengajuanMagang->nomor()} diubah dari {$statusLama} menjadi {$pengajuanMagang->status}",
            ['dari' => $statusLama, 'ke' => $pengajuanMagang->status, 'catatan_admin' => $pengajuanMagang->catatan_admin]
        );

        return redirect()
            ->route('admin.pengajuan.show', $pengajuanMagang)
            ->with('success', 'Status pengajuan diperbarui menjadi "'.$pengajuanMagang->statusLabel().'".');
    }

    /**
     * Buat data peserta magang dari pengajuan yang sudah disetujui.
     */
    public function buatPeserta(PengajuanMagang $pengajuanMagang): RedirectResponse
    {
        Gate::authorize('update', $pengajuanMagang);

        $pengajuanMagang->load(['mahasiswa', 'pesertaMagang']);

        if ($pengajuanMagang->status !== PengajuanMagang::STATUS_DISETUJUI) {
            return back()->with('error', 'Data peserta hanya bisa dibuat dari pengajuan yang sudah disetujui.');
        }

        if ($pengajuanMagang->pesertaMagang) {
            return redirect()
                ->route('superadmin.peserta-magang.show', $pengajuanMagang->pesertaMagang)
                ->with('error', 'Pengajuan ini sudah memiliki data peserta.');
        }

        $mahasiswa = $pengajuanMagang->mahasiswa;

        $peserta = PesertaMagang::create([
            'pengajuan_magang_id' => $pengajuanMagang->id,
            'nama' => $mahasiswa->nama,
            'nim' => $mahasiswa->nim,
            'asal_kampus' => $mahasiswa->asal_kampus,
            'program_studi' => $mahasiswa->program_studi,
            'periode_mulai' => $pengajuanMagang->tanggal_mulai,
            'periode_selesai' => $pengajuanMagang->tanggal_selesai,
            'status' => 'aktif',
        ]);

        LogAktivitas::catat('buat_peserta', $peserta, "Peserta {$peserta->nama} dibuat dari pengajuan {$pengajuanMagang->nomor()}");

        return redirect()
            ->route('superadmin.peserta-magang.edit', $peserta)
            ->with('success', 'Data peserta berhasil dibuat dari pengajuan. Lengkapi pembimbing dan link Google Sheets absensi.');
    }

    /**
     * Hapus pengajuan beserta dokumennya.
     */
    public function destroy(PengajuanMagang $pengajuanMagang): RedirectResponse
    {
        Gate::authorize('delete', $pengajuanMagang);

        if ($pengajuanMagang->dokumen_path) {
            Storage::disk('local')->delete($pengajuanMagang->dokumen_path);
        }

        $pengajuanMagang->load('mahasiswa');

        LogAktivitas::catat(
            'hapus_pengajuan',
            $pengajuanMagang,
            "Pengajuan {$pengajuanMagang->nomor()} ({$pengajuanMagang->mahasiswa?->nama}) dihapus",
            ['status' => $pengajuanMagang->status, 'nim' => $pengajuanMagang->mahasiswa?->nim, 'posisi' => $pengajuanMagang->posisi]
        );

        $pengajuanMagang->delete();

        return redirect()
            ->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan magang berhasil dihapus.');
    }
}
