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
            'tampilkan_peta' => 'nullable|boolean',
            'peta_embed' => 'nullable|string|max:3000',
        ], [
            'nama.required' => 'Nama perusahaan wajib diisi.',
            'alamat.required' => 'Alamat perusahaan wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // Peta: kode <iframe> atau URL sematan Google Maps; kosong = peta dari alamat.
        $petaEmbed = Perusahaan::normalisasiPetaEmbed($validated['peta_embed'] ?? null);

        if ($petaEmbed === false) {
            return back()
                ->withInput()
                ->withErrors(['peta_embed' => 'Tautan peta tidak dikenali. Tempel kode "Sematkan peta" dari Google Maps (diawali <iframe src="https://www.google.com/maps/embed?...), atau kosongkan agar peta dibuat dari alamat.']);
        }

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

            'tampilkan_peta' => $request->boolean('tampilkan_peta'),
            'peta_embed_url' => $petaEmbed,
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
