<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BiodataPeserta;
use App\Models\LogAktivitas;
use App\Models\PesertaMagang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Admin menyiapkan detail program, lalu meninjau biodata yang dikirim peserta.
 */
class BiodataPesertaController extends Controller
{
    public function show(PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load(['biodata.peninjau', 'pengajuanMagang.mahasiswa', 'user']);

        return view('superadmin.peserta-magang.biodata', [
            'pesertaMagang' => $pesertaMagang,
            'biodata' => BiodataPeserta::untuk($pesertaMagang),
        ]);
    }

    /**
     * Simpan detail program magang (terkunci untuk peserta).
     */
    public function updateProgram(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validateWithBag('program', [
            'posisi_dilamar' => ['nullable', 'string', 'max:255'],
            'jenis_program' => ['nullable', Rule::in(array_keys(BiodataPeserta::JENIS_PROGRAM))],
            'jenis_program_lainnya' => ['nullable', 'required_if:jenis_program,lainnya', 'string', 'max:255'],
            'durasi_bulan' => ['nullable', 'integer', 'min:1', 'max:24'],
            'mentor_magang' => ['nullable', 'string', 'max:255'],
        ], [
            'jenis_program_lainnya.required_if' => 'Isi keterangan jenis program lainnya.',
        ], [
            'posisi_dilamar' => 'posisi yang dilamar',
            'jenis_program' => 'jenis program',
            'durasi_bulan' => 'durasi',
            'mentor_magang' => 'mentor magang',
        ]);

        if (($validated['jenis_program'] ?? null) !== 'lainnya') {
            $validated['jenis_program_lainnya'] = null;
        }

        $biodata = BiodataPeserta::untuk($pesertaMagang->load(['biodata', 'pengajuanMagang.mahasiswa', 'user']));
        $biodata->fill($validated);
        $berubah = $biodata->getDirty();
        $biodata->save();

        LogAktivitas::catat('ubah_program_biodata', $pesertaMagang, "Detail program biodata {$pesertaMagang->nama} diperbarui", collect($berubah)->only(BiodataPeserta::FIELD_PROGRAM)->all());

        return redirect()
            ->to(route('superadmin.peserta-magang.biodata', $pesertaMagang).'#program')
            ->with('success', 'Detail program berhasil disimpan.');
    }

    /**
     * Setujui biodata, atau minta revisi dengan catatan.
     * Revisi juga bisa diminta untuk biodata yang sudah disetujui (membuka kunci).
     */
    public function tinjau(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validateWithBag('tinjau', [
            'keputusan' => ['required', Rule::in(['setujui', 'revisi'])],
            'catatan_revisi' => ['nullable', 'required_if:keputusan,revisi', 'string', 'max:1000'],
        ], [
            'catatan_revisi.required_if' => 'Tulis catatan yang perlu diperbaiki peserta.',
        ]);

        $biodata = $pesertaMagang->biodata;
        $kembali = redirect()->to(route('superadmin.peserta-magang.biodata', $pesertaMagang).'#tinjau');

        $boleh = $validated['keputusan'] === 'setujui'
            ? $biodata?->status === BiodataPeserta::STATUS_DIAJUKAN
            : in_array($biodata?->status, [BiodataPeserta::STATUS_DIAJUKAN, BiodataPeserta::STATUS_DISETUJUI], true);

        if (! $boleh) {
            return $kembali->with('error', 'Biodata belum dikirim peserta atau statusnya tidak sesuai untuk tindakan ini.');
        }

        DB::transaction(function () use ($validated, $biodata, $pesertaMagang, $request) {
            $setujui = $validated['keputusan'] === 'setujui';

            $biodata->update([
                'status' => $setujui ? BiodataPeserta::STATUS_DISETUJUI : BiodataPeserta::STATUS_PERLU_REVISI,
                'catatan_revisi' => $setujui ? null : $validated['catatan_revisi'],
                'ditinjau_oleh' => $request->user()->id,
                'ditinjau_pada' => now(),
            ]);

            // Data identitas yang disetujui menjadi acuan data peserta & laporan.
            if ($setujui) {
                $pesertaMagang->update([
                    'nama' => $biodata->nama_lengkap,
                    'nim' => $biodata->nim,
                    'asal_kampus' => $biodata->perguruan_tinggi,
                ]);
            }
        });

        LogAktivitas::catat(
            'tinjau_biodata',
            $pesertaMagang,
            $validated['keputusan'] === 'setujui'
                ? "Biodata {$pesertaMagang->nama} disetujui"
                : "Biodata {$pesertaMagang->nama} diminta revisi",
            array_filter(['catatan_revisi' => $validated['catatan_revisi'] ?? null])
        );

        return $kembali->with('success', $validated['keputusan'] === 'setujui'
            ? 'Biodata disetujui. Nama, NIM, dan asal kampus peserta diperbarui sesuai biodata.'
            : 'Permintaan revisi dikirim ke peserta.');
    }
}
