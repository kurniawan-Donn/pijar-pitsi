<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PesertaMagang;
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
    public function store(Request $request): RedirectResponse
    {
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

        PesertaMagang::create($validated);

        return redirect()
            ->route('superadmin.peserta-magang.index')
            ->with(
                'success',
                'Data peserta magang berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail peserta.
     */
    public function show(PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load([
            'absensis' => function ($query) {
                $query->orderBy('tanggal', 'desc');
            },
        ]);

        return view(
            'superadmin.peserta-magang.show',
            compact('pesertaMagang')
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

        $pesertaMagang->update($validated);

        return redirect()
            ->route('superadmin.peserta-magang.index')
            ->with(
                'success',
                'Data peserta magang berhasil diperbarui.'
            );
    }

    /**
     * Menghapus peserta.
     *
     * Absensi peserta juga akan ikut terhapus
     * karena relasi database menggunakan cascadeOnDelete().
     */
    public function destroy(
        PesertaMagang $pesertaMagang
    ): RedirectResponse {
        $pesertaMagang->delete();

        return redirect()
            ->route('superadmin.peserta-magang.index')
            ->with(
                'success',
                'Data peserta magang berhasil dihapus.'
            );
    }
}