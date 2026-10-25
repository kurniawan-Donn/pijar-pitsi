<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PesertaMagangRequest;
use App\Models\Divisi;
use App\Models\LogAktivitas;
use App\Models\PesertaMagang;
use App\Services\PeriodeMagang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PesertaMagangController extends Controller
{
    /**
     * Menampilkan daftar peserta magang.
     */
    public function index(): View
    {
        $pesertaMagangs = PesertaMagang::with(['penilaian', 'user'])
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
     * Menampilkan detail peserta: data, akun, penempatan, dan tautan fitur.
     */
    public function show(PesertaMagang $pesertaMagang, PeriodeMagang $periode): View
    {
        $pesertaMagang->load(['penilaian', 'user', 'pengajuanMagang.mahasiswa', 'penempatans.divisi', 'biodata', 'feedback', 'suratKeterangan']);

        return view('superadmin.peserta-magang.show', [
            'pesertaMagang' => $pesertaMagang,
            'periodeMagang' => $periode->untukPeserta($pesertaMagang),
            'pilihanDivisi' => Divisi::query()
                ->where('aktif', true)
                ->orWhereIn('id', $pesertaMagang->penempatans->pluck('divisi_id'))
                ->orderBy('nama')
                ->get(),
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
     * Menghapus peserta beserta data turunannya (cascadeOnDelete):
     * biodata, logbook, izin, penempatan, penilaian, feedback, surat.
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
