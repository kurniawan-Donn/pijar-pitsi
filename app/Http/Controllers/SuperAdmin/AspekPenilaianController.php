<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanAspekPenilaianRequest;
use App\Models\AspekPenilaian;
use App\Models\KelompokPenilaian;
use App\Models\LogAktivitas;
use App\Models\PredikatPenilaian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Pengaturan penilaian (khusus Super Admin), mengikuti sheet PENILAIAN MHS
 * MAGANG: kelompok berbobot, aspek per kelompok, dan tabel predikat.
 *
 * Aspek tidak dihapus, cukup dinonaktifkan, supaya riwayat nilai tetap utuh.
 */
class AspekPenilaianController extends Controller
{
    public function index(): View
    {
        $predikats = PredikatPenilaian::orderBy('batas_bawah')->get();

        return view('superadmin.aspek-penilaian.index', [
            'kelompoks' => KelompokPenilaian::with('aspeks')->orderBy('urutan')->orderBy('id')->get(),
            'predikats' => $predikats,
        ]);
    }

    public function update(SimpanAspekPenilaianRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $kelompokIds = KelompokPenilaian::pluck('id')->all();

        DB::transaction(function () use ($validated, $kelompokIds) {
            foreach ($validated['kelompok'] as $id => $data) {
                KelompokPenilaian::whereKey($id)->update(['nama' => $data['nama'], 'bobot' => $data['bobot']]);
            }

            foreach ($validated['aspek'] ?? [] as $id => $data) {
                AspekPenilaian::whereKey($id)->update([
                    'nama' => $data['nama'],
                    'aktif' => (bool) ($data['aktif'] ?? false),
                ]);
            }

            $urutan = (int) AspekPenilaian::max('urutan');

            foreach ($validated['baru'] ?? [] as $kelompokId => $nama) {
                if (filled($nama) && in_array((int) $kelompokId, $kelompokIds, true)) {
                    AspekPenilaian::create([
                        'nama' => $nama,
                        'bobot' => 0,
                        'urutan' => ++$urutan,
                        'aktif' => true,
                        'kelompok_penilaian_id' => (int) $kelompokId,
                    ]);
                }
            }

            if (! empty($validated['kelompok_baru']['nama'])) {
                KelompokPenilaian::create([
                    'nama' => $validated['kelompok_baru']['nama'],
                    'bobot' => $validated['kelompok_baru']['bobot'],
                    'urutan' => (int) KelompokPenilaian::max('urutan') + 1,
                ]);
            }
        });

        LogAktivitas::catat('ubah_aspek_penilaian', null, 'Kelompok & aspek penilaian diperbarui', [
            'kelompok' => KelompokPenilaian::pluck('bobot', 'nama')->all(),
        ]);

        return redirect()
            ->route('superadmin.aspek-penilaian.index')
            ->with('success', 'Kelompok dan aspek penilaian berhasil disimpan. Perubahan berlaku untuk penilaian berikutnya.');
    }

    /**
     * Simpan tabel predikat (batas bawah, nama, keterangan).
     */
    public function updatePredikat(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('predikat', [
            'predikat' => ['nullable', 'array'],
            'predikat.*.batas_bawah' => ['required', 'numeric', 'min:0', 'max:100'],
            'predikat.*.nama' => ['required', 'string', 'max:50'],
            'predikat.*.keterangan' => ['nullable', 'string', 'max:500'],
            'predikat.*.hapus' => ['nullable', 'boolean'],
            'baru.batas_bawah' => ['nullable', 'required_with:baru.nama', 'numeric', 'min:0', 'max:100'],
            'baru.nama' => ['nullable', 'required_with:baru.batas_bawah', 'string', 'max:50'],
            'baru.keterangan' => ['nullable', 'string', 'max:500'],
        ], [], [
            'predikat.*.batas_bawah' => 'batas bawah',
            'predikat.*.nama' => 'nama predikat',
            'baru.batas_bawah' => 'batas bawah predikat baru',
            'baru.nama' => 'nama predikat baru',
        ]);

        $tersisa = collect($validated['predikat'] ?? [])
            ->reject(fn (array $data) => (bool) ($data['hapus'] ?? false))
            ->map(fn (array $data) => round((float) $data['batas_bawah'], 2));

        if (! empty($validated['baru']['nama'])) {
            $tersisa->push(round((float) $validated['baru']['batas_bawah'], 2));
        }

        $pesan = match (true) {
            ! $tersisa->contains(0.0) => 'Harus ada predikat dengan batas bawah 0 supaya semua nilai punya predikat.',
            $tersisa->duplicates()->isNotEmpty() => 'Batas bawah predikat tidak boleh sama.',
            default => null,
        };

        if ($pesan) {
            return back()->withInput()->withErrors(['predikat' => $pesan], 'predikat');
        }

        DB::transaction(function () use ($validated) {
            // Batas sementara negatif supaya pertukaran batas tidak bentrok dengan indeks unik.
            PredikatPenilaian::whereKey(array_keys($validated['predikat'] ?? []))->update(['batas_bawah' => DB::raw('-id')]);

            foreach ($validated['predikat'] ?? [] as $id => $data) {
                $predikat = PredikatPenilaian::find($id);

                if (! $predikat) {
                    continue;
                }

                ($data['hapus'] ?? false)
                    ? $predikat->delete()
                    : $predikat->update([
                        'batas_bawah' => round((float) $data['batas_bawah'], 2),
                        'nama' => $data['nama'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]);
            }

            if (! empty($validated['baru']['nama'])) {
                PredikatPenilaian::create([
                    'batas_bawah' => round((float) $validated['baru']['batas_bawah'], 2),
                    'nama' => $validated['baru']['nama'],
                    'keterangan' => $validated['baru']['keterangan'] ?? null,
                ]);
            }
        });

        LogAktivitas::catat('ubah_aspek_penilaian', null, 'Tabel predikat penilaian diperbarui', [
            'predikat' => PredikatPenilaian::orderBy('batas_bawah')->pluck('nama', 'batas_bawah')->all(),
        ]);

        return redirect()
            ->to(route('superadmin.aspek-penilaian.index').'#predikat')
            ->with('success', 'Tabel predikat berhasil disimpan.');
    }
}
