<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\CekStatusPengajuanRequest;
use App\Http\Requests\StorePengajuanMagangRequest;
use App\Models\Mahasiswa;
use App\Models\PengajuanMagang;
use App\Models\Perusahaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends Controller
{
    /**
     * Simpan pendaftaran magang dari form website.
     */
    public function store(StorePengajuanMagangRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Surat pengantar disimpan di disk privat (tidak bisa diakses publik).
        $dokumenPath = $request->file('dokumen')->store('pengajuan', 'local');

        try {
            $pengajuan = DB::transaction(function () use ($validated, $dokumenPath) {
                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['nim' => $validated['nim']],
                    [
                        'nama' => $validated['nama'],
                        'email' => $validated['email'],
                        'no_hp' => $validated['no_hp'],
                        'asal_kampus' => $validated['asal_kampus'],
                        'program_studi' => $validated['program_studi'],
                        'fakultas' => $validated['fakultas'] ?? null,
                    ]
                );

                return $mahasiswa->pengajuanMagangs()->create([
                    // Pendaftar melamar ke perusahaan pemilik website.
                    'perusahaan_id' => Perusahaan::query()->value('id'),
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_selesai' => $validated['tanggal_selesai'],
                    'posisi' => $validated['posisi'],
                    'status' => PengajuanMagang::STATUS_MENUNGGU,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'dokumen_path' => $dokumenPath,
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($dokumenPath);

            throw $e;
        }

        return redirect()
            ->to(route('home').'#daftar')
            ->with('pendaftaran_berhasil', [
                'nomor' => $pengajuan->nomor(),
                'nama' => $validated['nama'],
                'email' => $validated['email'],
            ]);
    }

    /**
     * Cek status pengajuan berdasarkan NIM + email.
     */
    public function cekStatus(CekStatusPengajuanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $pengajuans = PengajuanMagang::query()
            ->whereHas('mahasiswa', fn ($query) => $query
                ->where('nim', $validated['status_nim'])
                ->where('email', $validated['status_email']))
            ->latest()
            ->get();

        $redirect = redirect()->to(route('home').'#status')->withInput();

        if ($pengajuans->isEmpty()) {
            return $redirect->with(
                'status_tidak_ditemukan',
                'Pengajuan tidak ditemukan. Pastikan NIM dan email sama dengan saat mendaftar.'
            );
        }

        return $redirect->with('status_pengajuan', $pengajuans->map(fn (PengajuanMagang $pengajuan) => [
            'nomor' => $pengajuan->nomor(),
            'posisi' => $pengajuan->posisi,
            'status' => $pengajuan->status,
            'status_label' => $pengajuan->statusLabel(),
            'periode' => $pengajuan->tanggal_mulai->translatedFormat('d M Y')
                .' – '.$pengajuan->tanggal_selesai->translatedFormat('d M Y'),
            'diajukan' => $pengajuan->created_at->translatedFormat('d M Y'),
            'catatan' => $pengajuan->catatan_admin,
        ])->all());
    }
}
