<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use App\Models\PengajuanMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanMagangController extends Controller
{
    /**
     * Daftar pengajuan.
     */
    public function index()
    {
        $pengajuans = PengajuanMagang::with([
            'mahasiswa',
            'perusahaan'
        ])->latest()->get();

        return view('admin.pengajuan.index', compact('pengajuans'));
    }

    /**
     * Form pengajuan.
     */
    public function create()
    {
        return redirect('/#pengajuan');
    }

    /**
     * Simpan pengajuan dari website utama.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'posisi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nama.required' => 'Nama mahasiswa wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'posisi.required' => 'Posisi magang wajib diisi.',
        ]);

        DB::transaction(function () use ($validated) {

            // Cari mahasiswa berdasarkan NIM.
            $mahasiswa = Mahasiswa::where('nim', $validated['nim'])->first();

            if ($mahasiswa) {
                // Perbarui data mahasiswa jika sudah ada.
                $mahasiswa->nim = $validated['nim'];
                $mahasiswa->nama = $validated['nama'];
                $mahasiswa->email = $validated['email'];
                $mahasiswa->save();
            } else {
                // Buat mahasiswa baru.
                $mahasiswa = new Mahasiswa();
                $mahasiswa->nim = $validated['nim'];
                $mahasiswa->nama = $validated['nama'];
                $mahasiswa->email = $validated['email'];
                $mahasiswa->no_hp = '-';
                $mahasiswa->program_studi = '-';
                $mahasiswa->fakultas = '-';
                $mahasiswa->alamat = null;
                $mahasiswa->save();
            }

            // Cari perusahaan berdasarkan nama.
            $perusahaan = Perusahaan::where(
                'nama_perusahaan',
                $validated['nama_perusahaan']
            )->first();

            if (!$perusahaan) {
                // Buat perusahaan baru.
                $perusahaan = new Perusahaan();
                $perusahaan->nama_perusahaan = $validated['nama_perusahaan'];
                $perusahaan->alamat = '-';
                $perusahaan->kota = '-';
                $perusahaan->no_telepon = '-';
                $perusahaan->email = null;
                $perusahaan->nama_pembimbing = null;
                $perusahaan->jabatan_pembimbing = null;
                $perusahaan->save();
            }

            // Simpan pengajuan magang.
            $pengajuan = new PengajuanMagang();
            $pengajuan->mahasiswa_id = $mahasiswa->id;
            $pengajuan->perusahaan_id = $perusahaan->id;
            $pengajuan->tanggal_mulai = $validated['tanggal_mulai'];
            $pengajuan->tanggal_selesai = $validated['tanggal_selesai'];
            $pengajuan->posisi = $validated['posisi'];
            $pengajuan->status = 'menunggu';
            $pengajuan->keterangan = $validated['keterangan'] ?? null;
            $pengajuan->save();
        });

        return redirect('/#pengajuan')
            ->with('success', 'Pengajuan magang berhasil dikirim. Silakan cek status pengajuan secara berkala.');
    }

    /**
     * Detail pengajuan.
     */
    public function show(PengajuanMagang $pengajuanMagang)
    {
        $pengajuanMagang->load([
            'mahasiswa',
            'perusahaan'
        ]);

        return view(
            'admin.pengajuan.show',
            compact('pengajuanMagang')
        );
    }

    /**
     * Update status pengajuan.
     */
    public function updateStatus(Request $request, PengajuanMagang $pengajuanMagang)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,disetujui,ditolak',
            'keterangan' => 'nullable|string',
        ]);

        $pengajuanMagang->status = $validated['status'];
        $pengajuanMagang->keterangan = $validated['keterangan'] ?? $pengajuanMagang->keterangan;
        $pengajuanMagang->save();

        return back()->with(
            'success',
            'Status pengajuan berhasil diperbarui.'
        );
    }

    /**
     * Hapus pengajuan.
     */
    public function destroy(PengajuanMagang $pengajuanMagang)
    {
        $pengajuanMagang->delete();

        return back()->with(
            'success',
            'Pengajuan magang berhasil dihapus.'
        );
    }
}