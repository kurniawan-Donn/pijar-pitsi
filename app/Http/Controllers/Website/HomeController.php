<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Faq;
use App\Models\PengaturanWebsite;
use App\Models\Perusahaan;
use App\Models\PesertaMagang;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama website (one-page).
     */
    public function index(): View
    {
        return view('website.index', [
            'banners' => Banner::aktif()->get(),
            'faqs' => Faq::tampil()->berurutan()->get(),
            'perusahaan' => Perusahaan::first(),
            'pengaturan' => PengaturanWebsite::first(),
            'statistik' => [
                'peserta_aktif' => PesertaMagang::where('status', 'aktif')->count(),
                'alumni' => PesertaMagang::where('status', 'selesai')->count(),
            ],
        ]);
    }
}
