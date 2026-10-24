<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\LogAktivitas;
use App\Http\Requests\BannerRequest;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Halaman kelola banner.
     * MAGENTA hanya menggunakan 1 banner utama.
     */
    public function index()
    {
        $banner = Banner::orderBy('id', 'asc')->first();

        return view(
            'superadmin.banners.index',
            compact('banner')
        );
    }

    /**
     * Halaman create tidak digunakan lagi.
     * Kita arahkan langsung ke halaman kelola banner.
     */
    public function create()
    {
        return redirect()->route('superadmin.banners.index');
    }

    /**
     * Simpan banner.
     *
     * Jika banner sudah ada:
     * - update banner tersebut
     *
     * Jika belum ada:
     * - buat banner baru
     */
    public function store(BannerRequest $request)
    {
        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Ambil banner utama
        |--------------------------------------------------------------------------
        */

        $banner = Banner::orderBy('id', 'asc')->first();

        /*
        |--------------------------------------------------------------------------
        | Upload gambar baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('gambar')) {

            /*
             * Kalau sebelumnya sudah ada gambar,
             * hapus file gambar lama.
             */
            if ($banner && $banner->gambar) {
                Storage::disk('public')->delete(
                    $banner->gambar
                );
            }

            /*
             * Simpan gambar baru.
             */
            $validated['gambar'] = $request
                ->file('gambar')
                ->store('banners', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Nilai default
        |--------------------------------------------------------------------------
        */

        $validated['status'] = 'aktif';
        $validated['urutan'] = 0;

        /*
        |--------------------------------------------------------------------------
        | Update atau Create
        |--------------------------------------------------------------------------
        */

        if ($banner) {

            $banner->update($validated);

        } else {

            $banner = Banner::create($validated);
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus banner lain jika ternyata ada data lama
        |--------------------------------------------------------------------------
        |
        | Karena MAGENTA hanya menggunakan 1 banner,
        | kita pastikan database hanya menyimpan banner utama.
        |
        */

        $bannerLain = Banner::where('id', '!=', $banner->id)->get();

        foreach ($bannerLain as $item) {

            if ($item->gambar) {
                Storage::disk('public')->delete(
                    $item->gambar
                );
            }

            $item->delete();
        }

        return redirect()
            ->route('superadmin.banners.index')
            ->with(
                'success',
                'Banner utama berhasil disimpan.'
            );
    }

    /**
     * Tidak digunakan.
     */
    public function show(Banner $banner)
    {
        return redirect()->route(
            'superadmin.banners.index'
        );
    }

    /**
     * Edit diarahkan ke halaman utama.
     */
    public function edit(Banner $banner)
    {
        return redirect()->route(
            'superadmin.banners.index'
        );
    }

    /**
     * Update banner.
     *
     * Tetap disediakan untuk kompatibilitas route resource.
     */
    public function update(
        BannerRequest $request,
        Banner $banner
    ) {
        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Upload gambar baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('gambar')) {

            if ($banner->gambar) {
                Storage::disk('public')->delete(
                    $banner->gambar
                );
            }

            $validated['gambar'] = $request
                ->file('gambar')
                ->store('banners', 'public');
        }

        $validated['status'] = 'aktif';
        $validated['urutan'] = 0;

        $banner->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Hapus banner lain
        |--------------------------------------------------------------------------
        */

        $bannerLain = Banner::where(
            'id',
            '!=',
            $banner->id
        )->get();

        foreach ($bannerLain as $item) {

            if ($item->gambar) {
                Storage::disk('public')->delete(
                    $item->gambar
                );
            }

            $item->delete();
        }

        return redirect()
            ->route('superadmin.banners.index')
            ->with(
                'success',
                'Banner utama berhasil diperbarui.'
            );
    }

    /**
     * Hapus banner utama.
     */
    public function destroy(Banner $banner)
    {
        if ($banner->gambar) {

            Storage::disk('public')->delete(
                $banner->gambar
            );
        }

        LogAktivitas::catat('hapus_banner', $banner, "Banner \"{$banner->judul}\" dihapus");

        $banner->delete();

        return redirect()
            ->route('superadmin.banners.index')
            ->with(
                'success',
                'Banner utama berhasil dihapus.'
            );
    }
}