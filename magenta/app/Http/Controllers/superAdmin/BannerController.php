<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Menampilkan daftar banner.
     */
    public function index()
    {
        $banners = Banner::orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'superadmin.banners.index',
            compact('banners')
        );
    }

    /**
     * Menampilkan halaman Kelola Banner.
     */
    public function create()
    {
        $banner = Banner::where('status', 'aktif')
            ->orderBy('urutan', 'asc')
            ->first();

        return view(
            'superadmin.banners.create',
            compact('banner')
        );
    }

    /**
     * Menyimpan atau memperbarui banner utama.
     *
     * Kalau sudah ada banner aktif:
     * → update banner tersebut.
     *
     * Kalau belum ada:
     * → buat banner baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',

            'deskripsi' => 'nullable|string',

            'gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'tombol_teks' => 'nullable|string|max:100',

            'tombol_link' => 'nullable|string|max:255',

            'status' => 'required|in:aktif,nonaktif',

            'urutan' => 'required|integer|min:0',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CARI BANNER AKTIF
        |--------------------------------------------------------------------------
        */

        $banner = Banner::where('status', 'aktif')
            ->orderBy('urutan', 'asc')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | JIKA SUDAH ADA BANNER AKTIF → UPDATE
        |--------------------------------------------------------------------------
        */

        if ($banner) {

            /*
             * Jika upload gambar baru,
             * hapus gambar lama.
             */
            if ($request->hasFile('gambar')) {

                if ($banner->gambar) {

                    Storage::disk('public')
                        ->delete($banner->gambar);
                }

                $validated['gambar'] = $request
                    ->file('gambar')
                    ->store('banners', 'public');
            }

            /*
             * Banner ini tetap menjadi banner utama.
             */
            $validated['status'] = 'aktif';

            /*
             * Nonaktifkan banner aktif lainnya.
             */
            Banner::where('id', '!=', $banner->id)
                ->where('status', 'aktif')
                ->update([
                    'status' => 'nonaktif',
                ]);

            /*
             * Update banner utama.
             */
            $banner->update($validated);

        } else {

            /*
             |--------------------------------------------------------------------------
             | JIKA BELUM ADA BANNER AKTIF → BUAT BARU
             |--------------------------------------------------------------------------
             */

            if ($request->hasFile('gambar')) {

                $validated['gambar'] = $request
                    ->file('gambar')
                    ->store('banners', 'public');
            }

            /*
             * Nonaktifkan semua banner lama.
             */
            Banner::where('status', 'aktif')
                ->update([
                    'status' => 'nonaktif',
                ]);

            /*
             * Buat banner utama.
             */
            $validated['status'] = 'aktif';

            Banner::create($validated);
        }


        return redirect()
            ->route('superadmin.banners.create')
            ->with(
                'success',
                'Banner berhasil diperbarui.'
            );
    }


    /**
     * Menampilkan detail banner.
     */
    public function show(Banner $banner)
    {
        return view(
            'superadmin.banners.show',
            compact('banner')
        );
    }


    /**
     * Form edit banner.
     */
    public function edit(Banner $banner)
    {
        return view(
            'superadmin.banners.edit',
            compact('banner')
        );
    }


    /**
     * Memperbarui banner.
     */
    public function update(
        Request $request,
        Banner $banner
    ) {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',

            'deskripsi' => 'nullable|string',

            'gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'tombol_teks' => 'nullable|string|max:100',

            'tombol_link' => 'nullable|string|max:255',

            'status' => 'required|in:aktif,nonaktif',

            'urutan' => 'required|integer|min:0',
        ]);


        /*
         * Ganti gambar jika ada upload baru.
         */
        if ($request->hasFile('gambar')) {

            if ($banner->gambar) {

                Storage::disk('public')
                    ->delete($banner->gambar);
            }

            $validated['gambar'] = $request
                ->file('gambar')
                ->store('banners', 'public');
        }


        /*
         * Kalau banner menjadi aktif,
         * nonaktifkan banner lain.
         */
        if ($validated['status'] === 'aktif') {

            Banner::where('id', '!=', $banner->id)
                ->where('status', 'aktif')
                ->update([
                    'status' => 'nonaktif',
                ]);
        }


        $banner->update($validated);


        return redirect()
            ->route('superadmin.banners.create')
            ->with(
                'success',
                'Banner berhasil diperbarui.'
            );
    }


    /**
     * Menghapus banner.
     */
    public function destroy(Banner $banner)
    {
        if ($banner->gambar) {

            Storage::disk('public')
                ->delete($banner->gambar);
        }

        $banner->delete();

        return redirect()
            ->route('superadmin.banners.create')
            ->with(
                'success',
                'Banner berhasil dihapus.'
            );
    }
}