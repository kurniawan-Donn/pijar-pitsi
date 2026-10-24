<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\PengajuanMagangController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SuperAdmin\BannerController;
use App\Http\Controllers\SuperAdmin\PengaturanWebsiteController;
use App\Http\Controllers\SuperAdmin\PerusahaanController;
use App\Http\Controllers\SuperAdmin\PesertaMagangController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Website\HomeController;


/*
|--------------------------------------------------------------------------
| WEBSITE PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

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

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::view('/admin/dashboard', 'admin.dashboard.index')
        ->name('admin.dashboard');

    // Pengajuan magang (URL lama tetap dipertahankan).
    Route::controller(PengajuanMagangController::class)
        ->prefix('pengajuan')
        ->name('admin.pengajuan.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{pengajuanMagang}', 'show')->name('show');
            Route::put('/{pengajuanMagang}/status', 'updateStatus')->name('status');
            Route::delete('/{pengajuanMagang}', 'destroy')->name('destroy');
        });

});


/*
|--------------------------------------------------------------------------
| FITUR BERSAMA ADMIN + SUPER ADMIN
|--------------------------------------------------------------------------
|
| - Profil Perusahaan
| - Kelola Banner
| - Kelola Konten
| - Data Peserta Magang
| - Absensi Google Sheets
|
*/

Route::middleware(['auth', 'role:admin,super_admin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {

        // Profil perusahaan
        Route::get('/perusahaan', [PerusahaanController::class, 'index'])
            ->name('perusahaan.index');
        Route::put('/perusahaan', [PerusahaanController::class, 'update'])
            ->name('perusahaan.update');

        // Kelola banner
        Route::resource('banners', BannerController::class);

        // Kelola konten website
        Route::get('/pengaturan', [PengaturanWebsiteController::class, 'index'])
            ->name('pengaturan.index');
        Route::put('/pengaturan', [PengaturanWebsiteController::class, 'update'])
            ->name('pengaturan.update');

        // Data peserta magang
        Route::resource('peserta-magang', PesertaMagangController::class)
            ->parameters(['peserta-magang' => 'pesertaMagang']);

        // Update absensi langsung ke Google Sheets peserta
        Route::put(
            '/peserta-magang/{pesertaMagang}/absensi',
            [PesertaMagangController::class, 'updateAbsensi']
        )->name('peserta-magang.absensi.update');

    });


/*
|--------------------------------------------------------------------------
| AREA KHUSUS SUPER ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {

        Route::view('/dashboard', 'superadmin.dashboard.index')
            ->name('dashboard');

        // Kelola pengguna
        Route::resource('pengguna', UserController::class)
            ->parameters(['pengguna' => 'user']);

    });
