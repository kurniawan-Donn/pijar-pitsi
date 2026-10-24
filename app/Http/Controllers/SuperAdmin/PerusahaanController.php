<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    /**
     * Menampilkan halaman Profil Perusahaan.
     */
    public function index()
    {
        $perusahaan = Perusahaan::first();

        return view(
            'superadmin.perusahaan.index',
            compact('perusahaan')
        );
    }

    /**
     * Menyimpan atau memperbarui Profil Perusahaan.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama perusahaan wajib diisi.',
            'alamat.required' => 'Alamat perusahaan wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'nama_perusahaan' => $validated['nama'],

            'alamat' => $validated['alamat'],
            'kota' => $validated['kota'] ?? '',

            'telepon' => $validated['telepon'] ?? '',
            'no_telepon' => $validated['telepon'] ?? '',

            'email' => $validated['email'] ?? '',
            'website' => $validated['website'] ?? '',
            'deskripsi' => $validated['deskripsi'] ?? null,
        ];

        $perusahaan = Perusahaan::first();

        if ($perusahaan) {
            $perusahaan->update($data);
        } else {
            Perusahaan::create($data);
        }

        return redirect()
            ->route('superadmin.perusahaan.index')
            ->with(
                'success',
                'Profil perusahaan berhasil disimpan.'
            );
    }
}