<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\PenempatanPeserta;
use App\Models\PesertaMagang;
use App\Services\PeriodeMagang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Divisi & mentor peserta per periode (rotasi divisi).
 *
 * Periode yang divisinya dikosongkan memakai penempatan periode sebelumnya.
 */
class PenempatanPesertaController extends Controller
{
    public function update(Request $request, PesertaMagang $pesertaMagang, PeriodeMagang $periode): RedirectResponse
    {
        $validated = $request->validateWithBag('penempatan', [
            'penempatan' => ['nullable', 'array'],
            'penempatan.*.divisi_id' => ['nullable', 'integer', 'exists:divisis,id'],
            'penempatan.*.mentor_nama' => ['nullable', 'string', 'max:255'],
            'penempatan.*.mentor_jabatan' => ['nullable', 'string', 'max:255'],
        ], [], [
            'penempatan.*.divisi_id' => 'divisi',
            'penempatan.*.mentor_nama' => 'nama mentor',
            'penempatan.*.mentor_jabatan' => 'jabatan mentor',
        ]);

        // Hanya periode dalam masa magang peserta yang disimpan.
        $kunciValid = array_column($periode->untukPeserta($pesertaMagang), 'kunci');
        $input = array_intersect_key($validated['penempatan'] ?? [], array_flip($kunciValid));

        $perubahan = DB::transaction(function () use ($pesertaMagang, $input) {
            $perubahan = [];

            foreach ($input as $kunci => $data) {
                $lama = $pesertaMagang->penempatans()->where('periode', $kunci)->first();

                if (empty($data['divisi_id'])) {
                    if ($lama) {
                        $lama->delete();
                        $perubahan[$kunci] = 'ikut periode sebelumnya';
                    }

                    continue;
                }

                $baru = [
                    'divisi_id' => (int) $data['divisi_id'],
                    'mentor_nama' => $data['mentor_nama'] ?? null,
                    'mentor_jabatan' => $data['mentor_jabatan'] ?? null,
                ];

                $penempatan = $lama ?? new PenempatanPeserta(['peserta_magang_id' => $pesertaMagang->id, 'periode' => $kunci]);
                $penempatan->fill($baru);

                if ($penempatan->isDirty()) {
                    $penempatan->save();
                    $perubahan[$kunci] = $penempatan->load('divisi')->divisi->nama;
                }
            }

            return $perubahan;
        });

        if ($perubahan) {
            LogAktivitas::catat('ubah_penempatan', $pesertaMagang, "Penempatan divisi peserta {$pesertaMagang->nama} diperbarui", $perubahan);
        }

        return redirect()
            ->to(route('superadmin.peserta-magang.show', $pesertaMagang).'#penempatan')
            ->with('penempatan_status', $perubahan ? 'Penempatan divisi berhasil disimpan.' : 'Tidak ada perubahan penempatan.');
    }
}
