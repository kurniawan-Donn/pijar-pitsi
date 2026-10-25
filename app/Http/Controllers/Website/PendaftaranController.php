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
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PendaftaranController extends Controller
{
    /**
     * Masa berlaku tautan dokumen di hasil cek status (menit).
     */
    public const MASA_BERLAKU_DOKUMEN = 30;

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
     * Cek status pengajuan berdasarkan kode pengajuan + email.
     *
     * Kode MG berurutan dan mudah ditebak, jadi email wajib cocok. Pesan gagal
     * dibuat sama agar tidak membocorkan kode mana yang ada.
     */
    public function cekStatus(CekStatusPengajuanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $pengajuan = PengajuanMagang::query()
            ->whereKey(PengajuanMagang::idDariNomor($validated['status_kode']))
            ->whereHas('mahasiswa', fn ($query) => $query->where('email', $validated['status_email']))
            ->first();

        $redirect = redirect()->to(route('home').'#status')->withInput();

        if (! $pengajuan) {
            return $redirect->with(
                'status_tidak_ditemukan',
                'Pengajuan tidak ditemukan. Pastikan kode pengajuan dan email sama dengan saat mendaftar.'
            );
        }

        return $redirect->with('status_pengajuan', [[
            'nomor' => $pengajuan->nomor(),
            'posisi' => $pengajuan->posisi,
            'status' => $pengajuan->status,
            'status_label' => $pengajuan->statusLabel(),
            'periode' => $pengajuan->tanggal_mulai->translatedFormat('d M Y')
                .' – '.$pengajuan->tanggal_selesai->translatedFormat('d M Y'),
            'diajukan' => $pengajuan->created_at->translatedFormat('d M Y'),
            'catatan' => $pengajuan->catatan_admin,
            'dokumen_url' => $pengajuan->dokumen_path
                ? URL::temporarySignedRoute('pengajuan.dokumen', now()->addMinutes(self::MASA_BERLAKU_DOKUMEN), $pengajuan)
                : null,
        ]]);
    }

    /**
     * Surat pengantar milik pendaftar. Route dijaga middleware signed, jadi
     * mengganti ID di URL membuat signature tidak cocok (403).
     */
    public function dokumen(PengajuanMagang $pengajuanMagang): StreamedResponse
    {
        return $pengajuanMagang->responDokumen();
    }
}
