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
use App\Http\Controllers\SuperAdmin\PesertaMagangController;
use App\Http\Controllers\SuperAdmin\UserController;


/*
|--------------------------------------------------------------------------
| WEBSITE PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    $banners = Banner::aktif()->get();

    $perusahaan = Perusahaan::first();

    $pengaturan = PengaturanWebsite::first();

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

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| AREA ADMIN
|--------------------------------------------------------------------------
|
| Khusus Admin.
|
*/

Route::middleware(['auth', 'admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | PENGAJUAN MAGANG
    |--------------------------------------------------------------------------
    |
    | Route lama tetap dipertahankan.
    |
    */

    Route::get(
        '/pengajuan',
        [PengajuanMagangController::class, 'index']
    )->name('admin.pengajuan.index');

    Route::get(
        '/pengajuan/create',
        [PengajuanMagangController::class, 'create']
    )->name('admin.pengajuan.create');

    Route::post(
        '/pengajuan',
        [PengajuanMagangController::class, 'store']
    )->name('admin.pengajuan.store');

    Route::get(
        '/pengajuan/{pengajuanMagang}',
        [PengajuanMagangController::class, 'show']
    )->name('admin.pengajuan.show');

    Route::put(
        '/pengajuan/{pengajuanMagang}/status',
        [PengajuanMagangController::class, 'updateStatus']
    )->name('admin.pengajuan.status');

    Route::delete(
        '/pengajuan/{pengajuanMagang}',
        [PengajuanMagangController::class, 'destroy']
    )->name('admin.pengajuan.destroy');

});


/*
|--------------------------------------------------------------------------
| FITUR BERSAMA ADMIN + SUPER ADMIN
|--------------------------------------------------------------------------
|
| Admin dan Super Admin dapat mengakses:
|
| - Profil Perusahaan
| - Kelola Banner
| - Kelola Konten
| - Data Peserta Magang
| - Absensi Google Sheets
|
*/

Route::middleware(['auth', 'admin.superadmin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFIL PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/superadmin/perusahaan',
        [PerusahaanController::class, 'index']
    )->name('superadmin.perusahaan.index');

    Route::put(
        '/superadmin/perusahaan',
        [PerusahaanController::class, 'update']
    )->name('superadmin.perusahaan.update');


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
    | KELOLA KONTEN WEBSITE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/superadmin/pengaturan',
        [PengaturanWebsiteController::class, 'index']
    )->name('superadmin.pengaturan.index');

    Route::put(
        '/superadmin/pengaturan',
        [PengaturanWebsiteController::class, 'update']
    )->name('superadmin.pengaturan.update');


    /*
    |--------------------------------------------------------------------------
    | DATA PESERTA MAGANG
    |--------------------------------------------------------------------------
    |
    | Admin dan Super Admin dapat:
    |
    | - Melihat peserta
    | - Menambah peserta
    | - Melihat detail peserta
    | - Mengedit peserta
    | - Menghapus peserta
    |
    */

    Route::resource(
        '/superadmin/peserta-magang',
        PesertaMagangController::class
    )
        ->parameters([
            'peserta-magang' => 'pesertaMagang',
        ])
        ->names('superadmin.peserta-magang');


    /*
    |--------------------------------------------------------------------------
    | UPDATE ABSENSI GOOGLE SHEETS
    |--------------------------------------------------------------------------
    |
    | Admin dan Super Admin dapat mengubah:
    |
    | - Pagi Masuk
    | - Pagi Keluar
    | - Siang Masuk
    | - Siang Keluar
    |
    | Perubahan nantinya dikirim oleh MAGENTA
    | langsung ke Google Sheets peserta.
    |
    */

    Route::put(
        '/superadmin/peserta-magang/{pesertaMagang}/absensi',
        [PesertaMagangController::class, 'updateAbsensi']
    )->name('superadmin.peserta-magang.absensi.update');

});


/*
|--------------------------------------------------------------------------
| AREA KHUSUS SUPER ADMIN
|--------------------------------------------------------------------------
|
| Hanya Super Admin.
|
*/

Route::middleware(['auth', 'superadmin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/superadmin/dashboard',
        function () {
            return view('superadmin.dashboard.index');
        }
    )->name('superadmin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | KELOLA PENGGUNA
    |--------------------------------------------------------------------------
    */

    Route::resource(
        '/superadmin/pengguna',
        UserController::class
    )
        ->parameters([
            'pengguna' => 'user',
        ])
        ->names('superadmin.pengguna');

});