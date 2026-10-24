<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanWebsite;
use Illuminate\Http\Request;

class PengaturanWebsiteController extends Controller
{
    /**
     * Menampilkan halaman Kelola Konten.
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
     * Menyimpan seluruh perubahan konten website.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | INFORMASI TERBARU
            |--------------------------------------------------------------------------
            */

            'informasi_terbaru_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'informasi_terbaru_deskripsi' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | PENGUMUMAN
            |--------------------------------------------------------------------------
            */

            'pengumuman_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'pengumuman_deskripsi' => [
                'nullable',
                'string',
            ],

            'pengumuman_card1_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'pengumuman_card1_deskripsi' => [
                'nullable',
                'string',
            ],

            'pengumuman_card2_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'pengumuman_card2_deskripsi' => [
                'nullable',
                'string',
            ],

            'pengumuman_card3_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'pengumuman_card3_deskripsi' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | PERSYARATAN MAGANG
            |--------------------------------------------------------------------------
            */

            'persyaratan_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'persyaratan_deskripsi' => [
                'nullable',
                'string',
            ],

            'persyaratan1_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'persyaratan1_deskripsi' => [
                'nullable',
                'string',
            ],

            'persyaratan2_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'persyaratan2_deskripsi' => [
                'nullable',
                'string',
            ],

            'persyaratan3_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'persyaratan3_deskripsi' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | ALUR MAGANG
            |--------------------------------------------------------------------------
            */

            'alur_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'alur_deskripsi' => [
                'nullable',
                'string',
            ],

            'alur1_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'alur1_deskripsi' => [
                'nullable',
                'string',
            ],

            'alur2_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'alur2_deskripsi' => [
                'nullable',
                'string',
            ],

            'alur3_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'alur3_deskripsi' => [
                'nullable',
                'string',
            ],

            'alur4_judul' => [
                'required',
                'string',
                'max:255',
            ],

            'alur4_deskripsi' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | GOOGLE FORM
            |--------------------------------------------------------------------------
            */

            'google_form_url' => [
                'nullable',
                'url:http,https',
                'max:2048',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Ambil atau buat pengaturan website
        |--------------------------------------------------------------------------
        */

        $pengaturan = PengaturanWebsite::first();

        if (! $pengaturan) {

            $pengaturan = new PengaturanWebsite;

        }

        /*
        |--------------------------------------------------------------------------
        | Simpan data
        |--------------------------------------------------------------------------
        */

        $pengaturan->informasi_terbaru_judul =
            $validated['informasi_terbaru_judul'];

        $pengaturan->informasi_terbaru_deskripsi =
            $validated['informasi_terbaru_deskripsi'] ?? null;

        $pengaturan->pengumuman_judul =
            $validated['pengumuman_judul'];

        $pengaturan->pengumuman_deskripsi =
            $validated['pengumuman_deskripsi'] ?? null;

        $pengaturan->pengumuman_card1_judul =
            $validated['pengumuman_card1_judul'];

        $pengaturan->pengumuman_card1_deskripsi =
            $validated['pengumuman_card1_deskripsi'] ?? null;

        $pengaturan->pengumuman_card2_judul =
            $validated['pengumuman_card2_judul'];

        $pengaturan->pengumuman_card2_deskripsi =
            $validated['pengumuman_card2_deskripsi'] ?? null;

        $pengaturan->pengumuman_card3_judul =
            $validated['pengumuman_card3_judul'];

        $pengaturan->pengumuman_card3_deskripsi =
            $validated['pengumuman_card3_deskripsi'] ?? null;

        $pengaturan->persyaratan_judul =
            $validated['persyaratan_judul'];

        $pengaturan->persyaratan_deskripsi =
            $validated['persyaratan_deskripsi'] ?? null;

        $pengaturan->persyaratan1_judul =
            $validated['persyaratan1_judul'];

        $pengaturan->persyaratan1_deskripsi =
            $validated['persyaratan1_deskripsi'] ?? null;

        $pengaturan->persyaratan2_judul =
            $validated['persyaratan2_judul'];

        $pengaturan->persyaratan2_deskripsi =
            $validated['persyaratan2_deskripsi'] ?? null;

        $pengaturan->persyaratan3_judul =
            $validated['persyaratan3_judul'];

        $pengaturan->persyaratan3_deskripsi =
            $validated['persyaratan3_deskripsi'] ?? null;

        $pengaturan->alur_judul =
            $validated['alur_judul'];

        $pengaturan->alur_deskripsi =
            $validated['alur_deskripsi'] ?? null;

        $pengaturan->alur1_judul =
            $validated['alur1_judul'];

        $pengaturan->alur1_deskripsi =
            $validated['alur1_deskripsi'] ?? null;

        $pengaturan->alur2_judul =
            $validated['alur2_judul'];

        $pengaturan->alur2_deskripsi =
            $validated['alur2_deskripsi'] ?? null;

        $pengaturan->alur3_judul =
            $validated['alur3_judul'];

        $pengaturan->alur3_deskripsi =
            $validated['alur3_deskripsi'] ?? null;

        $pengaturan->alur4_judul =
            $validated['alur4_judul'];

        $pengaturan->alur4_deskripsi =
            $validated['alur4_deskripsi'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | GOOGLE FORM
        |--------------------------------------------------------------------------
        */

        $pengaturan->google_form_url =
            $validated['google_form_url'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Simpan ke database
        |--------------------------------------------------------------------------
        */

        $pengaturan->save();

        /*
        |--------------------------------------------------------------------------
        | Kembali ke halaman Kelola Konten
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('superadmin.pengaturan.index')
            ->with(
                'success',
                'Konten website berhasil diperbarui.'
            );
    }
}
