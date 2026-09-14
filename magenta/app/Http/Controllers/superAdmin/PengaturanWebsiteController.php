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

        ]);


        /*
        |--------------------------------------------------------------------------
        | Ambil data pengaturan
        |--------------------------------------------------------------------------
        */

        $pengaturan = PengaturanWebsite::first();


        /*
        |--------------------------------------------------------------------------
        | Jika belum ada data, buat baru
        |--------------------------------------------------------------------------
        */

        if (!$pengaturan) {

            $pengaturan = PengaturanWebsite::create(
                $validated
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Jika sudah ada, update data
        |--------------------------------------------------------------------------
        */

        else {

            $pengaturan->update(
                $validated
            );

        }


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