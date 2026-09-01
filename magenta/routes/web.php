<?php

use Illuminate\Support\Facades\Route;

use App\Models\Banner;
use App\Models\Perusahaan;
use App\Models\PengaturanWebsite;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengajuanMagangController;

use App\Http\Controllers\SuperAdmin\BannerController;
use App\Http\Controllers\SuperAdmin\PerusahaanController;
use App\Http\Controllers\SuperAdmin\PengaturanWebsiteController;


/*
|--------------------------------------------------------------------------
| WEBSITE PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    /*
    |--------------------------------------------------------------------------
    | Ambil banner aktif
    |--------------------------------------------------------------------------
    */

    $banners = Banner::where('status', 'aktif')
        ->orderBy('urutan', 'asc')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Ambil profil perusahaan
    |--------------------------------------------------------------------------
    */

    $perusahaan = Perusahaan::first();


    /*
    |--------------------------------------------------------------------------
    | Ambil pengaturan website
    |--------------------------------------------------------------------------
    */

    $pengaturan = PengaturanWebsite::first();


    /*
    |--------------------------------------------------------------------------
    | Kirim data ke website utama
    |--------------------------------------------------------------------------
    */

    return view('website.index', [
        'banners' => $banners,
        'perusahaan' => $perusahaan,
        'pengaturan' => $pengaturan,
    ]);

})->name('home');


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

// Menampilkan halaman login
Route::get(
    '/login',
    [AuthController::class, 'showLogin']
)->name('login');


// Proses login
Route::post(
    '/login',
    [AuthController::class, 'login']
)->name('login.process');


// Logout
Route::post(
    '/logout',
    [AuthController::class, 'logout']
)->name('logout');


/*
|--------------------------------------------------------------------------
| PENGAJUAN MAGANG
|--------------------------------------------------------------------------
*/

// Menampilkan daftar pengajuan
Route::get(
    '/pengajuan',
    [PengajuanMagangController::class, 'index']
)->name('admin.pengajuan.index');


// Menampilkan form pengajuan
Route::get(
    '/pengajuan/create',
    [PengajuanMagangController::class, 'create']
)->name('admin.pengajuan.create');


// Menyimpan pengajuan
Route::post(
    '/pengajuan',
    [PengajuanMagangController::class, 'store']
)->name('admin.pengajuan.store');


// Menampilkan detail pengajuan
Route::get(
    '/pengajuan/{pengajuanMagang}',
    [PengajuanMagangController::class, 'show']
)->name('admin.pengajuan.show');


// Update status pengajuan
Route::put(
    '/pengajuan/{pengajuanMagang}/status',
    [PengajuanMagangController::class, 'updateStatus']
)->name('admin.pengajuan.status');


// Menghapus pengajuan
Route::delete(
    '/pengajuan/{pengajuanMagang}',
    [PengajuanMagangController::class, 'destroy']
)->name('admin.pengajuan.destroy');


/*
|--------------------------------------------------------------------------
| DASHBOARD ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/admin/dashboard',
        function () {

            return view(
                'admin.dashboard.index'
            );

        }
    )->name('admin.dashboard');

});


/*
|--------------------------------------------------------------------------
| SUPER ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/superadmin/dashboard',
        function () {

            return view(
                'superadmin.dashboard.index'
            );

        }
    )->name('superadmin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | KELOLA BANNER
    |--------------------------------------------------------------------------
    */

    Route::resource(
        '/superadmin/banners',
        BannerController::class
    )->names('superadmin.banners');


    /*
    |--------------------------------------------------------------------------
    | PROFIL PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    // Menampilkan halaman profil perusahaan
    Route::get(
        '/superadmin/perusahaan',
        [PerusahaanController::class, 'index']
    )->name('superadmin.perusahaan.index');


    // Menyimpan / memperbarui profil perusahaan
    Route::put(
        '/superadmin/perusahaan',
        [PerusahaanController::class, 'update']
    )->name('superadmin.perusahaan.update');


    /*
    |--------------------------------------------------------------------------
    | PENGATURAN WEBSITE
    |--------------------------------------------------------------------------
    */

    // Menampilkan halaman pengaturan website
    Route::get(
        '/superadmin/pengaturan',
        [PengaturanWebsiteController::class, 'index']
    )->name('superadmin.pengaturan.index');


    // Menyimpan / memperbarui pengaturan website
    Route::put(
        '/superadmin/pengaturan',
        [PengaturanWebsiteController::class, 'update']
    )->name('superadmin.pengaturan.update');

});