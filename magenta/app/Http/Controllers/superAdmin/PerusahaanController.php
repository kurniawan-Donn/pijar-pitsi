<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanWebsite;
use Illuminate\Http\Request;

class PengaturanWebsiteController extends Controller
{
    /**
     * Menampilkan halaman Pengaturan Website.
     */
    public function index()
    {
        $pengaturan = PengaturanWebsite::first();

        return view(
            'superadmin.pengaturan.index',
            compact('pengaturan')
        );
    }

    /**
     * Menyimpan atau memperbarui pengaturan website.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'tentang_judul' => 'required|string|max:255',
            'tentang_deskripsi' => 'nullable|string',

            'tentang_card1_judul' => 'required|string|max:255',
            'tentang_card1_deskripsi' => 'nullable|string',

            'tentang_card2_judul' => 'required|string|max:255',
            'tentang_card2_deskripsi' => 'nullable|string',

            'tentang_card3_judul' => 'required|string|max:255',
            'tentang_card3_deskripsi' => 'nullable|string',
        ]);

        $pengaturan = PengaturanWebsite::first();

        if ($pengaturan) {
            $pengaturan->update($validated);
        } else {
            PengaturanWebsite::create($validated);
        }

        return redirect()
            ->route('superadmin.pengaturan.index')
            ->with(
                'success',
                'Pengaturan website berhasil disimpan.'
            );
    }
}