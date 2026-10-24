<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Daftar divisi untuk penempatan peserta. Divisi yang sudah dipakai
 * tidak bisa dihapus, cukup dinonaktifkan.
 */
class DivisiController extends Controller
{
    public function index(): View
    {
        return view('superadmin.divisi.index', [
            'divisis' => Divisi::withCount('penempatans')->orderByDesc('aktif')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('baru', [
            'nama' => ['required', 'string', 'max:100', 'unique:divisis,nama'],
        ], [], ['nama' => 'nama divisi']);

        $divisi = Divisi::create(['nama' => $validated['nama'], 'aktif' => true]);

        LogAktivitas::catat('simpan_divisi', $divisi, "Divisi {$divisi->nama} ditambahkan");

        return redirect()
            ->route('superadmin.divisi.index')
            ->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function update(Request $request, Divisi $divisi): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('divisis', 'nama')->ignore($divisi->id)],
            'aktif' => ['required', 'boolean'],
        ], [], ['nama' => 'nama divisi']);

        $divisi->fill($validated);
        $berubah = $divisi->getDirty();
        $divisi->save();

        if ($berubah) {
            LogAktivitas::catat('simpan_divisi', $divisi, "Divisi {$divisi->nama} diperbarui", $berubah);
        }

        return redirect()
            ->route('superadmin.divisi.index')
            ->with('success', 'Divisi berhasil disimpan.');
    }

    public function destroy(Divisi $divisi): RedirectResponse
    {
        if ($divisi->penempatans()->exists()) {
            return redirect()
                ->route('superadmin.divisi.index')
                ->with('error', "Divisi {$divisi->nama} sudah dipakai untuk penempatan peserta. Nonaktifkan saja.");
        }

        LogAktivitas::catat('hapus_divisi', $divisi, "Divisi {$divisi->nama} dihapus");

        $divisi->delete();

        return redirect()
            ->route('superadmin.divisi.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }
}
