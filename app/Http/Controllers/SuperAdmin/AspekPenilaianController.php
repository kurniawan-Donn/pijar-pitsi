<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanAspekPenilaianRequest;
use App\Models\AspekPenilaian;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Pengaturan aspek & bobot penilaian (khusus Super Admin).
 *
 * Aspek tidak dihapus, cukup dinonaktifkan, supaya riwayat nilai tetap utuh.
 */
class AspekPenilaianController extends Controller
{
    public function index(): View
    {
        return view('superadmin.aspek-penilaian.index', [
            'aspeks' => AspekPenilaian::orderBy('urutan')->orderBy('id')->get(),
        ]);
    }

    public function update(SimpanAspekPenilaianRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            foreach ($validated['aspek'] ?? [] as $id => $data) {
                AspekPenilaian::whereKey($id)->update([
                    'nama' => $data['nama'],
                    'bobot' => $data['bobot'],
                    'aktif' => (bool) ($data['aktif'] ?? false),
                ]);
            }

            if (! empty($validated['baru']['nama'])) {
                AspekPenilaian::create([
                    'nama' => $validated['baru']['nama'],
                    'bobot' => $validated['baru']['bobot'],
                    'urutan' => (int) AspekPenilaian::max('urutan') + 1,
                    'aktif' => true,
                ]);
            }
        });

        LogAktivitas::catat(
            'ubah_aspek_penilaian',
            null,
            'Aspek & bobot penilaian diperbarui',
            ['aktif' => AspekPenilaian::aktif()->pluck('bobot', 'nama')->all()]
        );

        return redirect()
            ->route('superadmin.aspek-penilaian.index')
            ->with('success', 'Aspek penilaian berhasil disimpan. Perubahan berlaku untuk penilaian berikutnya.');
    }
}
