<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PengajuanMagangController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfilController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboardController;
use App\Http\Controllers\SuperAdmin\AkunPesertaController;
use App\Http\Controllers\SuperAdmin\AspekPenilaianController;
use App\Http\Controllers\SuperAdmin\BannerController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\LaporanController;
use App\Http\Controllers\SuperAdmin\LogAktivitasController;
use App\Http\Controllers\SuperAdmin\LogbookController;
use App\Http\Controllers\SuperAdmin\PengaturanWebsiteController;
use App\Http\Controllers\SuperAdmin\PenilaianController;
use App\Http\Controllers\SuperAdmin\PerusahaanController;
use App\Http\Controllers\SuperAdmin\PesertaMagangController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\PendaftaranController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEBSITE PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Pendaftaran magang dari form website (publik).
Route::post('/pengajuan', [PendaftaranController::class, 'store'])
    ->middleware('throttle:pendaftaran')
    ->name('pengajuan.store');

Route::post('/pengajuan/status', [PendaftaranController::class, 'cekStatus'])
    ->middleware('throttle:cek-status')
    ->name('pengajuan.status');

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

// Profil & password pengguna yang sedang login
Route::middleware(['auth', 'role:admin,super_admin,peserta'])
    ->controller(ProfilController::class)
    ->prefix('profil')
    ->name('profil.')
    ->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::put('/password', 'updatePassword')->name('password');
    });

/*
|--------------------------------------------------------------------------
| AREA PESERTA MAGANG
|--------------------------------------------------------------------------
|
| Akun peserta dibuat admin. Peserta berstatus selesai hanya bisa membaca
| (dijaga middleware peserta.aktif).
|
*/

Route::middleware(['auth', 'role:peserta', 'peserta.aktif'])
    ->prefix('peserta')
    ->name('peserta.')
    ->group(function () {
        Route::get('/dashboard', PesertaDashboardController::class)->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| AREA ADMIN
|--------------------------------------------------------------------------
|
| Khusus Admin.
|
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->name('admin.dashboard');

});

/*
|--------------------------------------------------------------------------
| PENGAJUAN MAGANG (ADMIN + SUPER ADMIN)
|--------------------------------------------------------------------------
|
| Nama route admin.pengajuan.* dipertahankan. Penyimpanan pengajuan dari
| website dipindah ke route publik pengajuan.store (lihat di atas).
|
*/

Route::middleware(['auth', 'role:admin,super_admin'])
    ->controller(PengajuanMagangController::class)
    ->prefix('pengajuan')
    ->name('admin.pengajuan.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{pengajuanMagang}', 'show')->name('show');
        Route::get('/{pengajuanMagang}/dokumen', 'dokumen')->name('dokumen');
        Route::put('/{pengajuanMagang}/status', 'updateStatus')->name('status');
        Route::post('/{pengajuanMagang}/peserta', 'buatPeserta')->name('peserta');
        Route::delete('/{pengajuanMagang}', 'destroy')->name('destroy');
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

        // Akun login peserta
        Route::controller(AkunPesertaController::class)
            ->prefix('/peserta-magang/{pesertaMagang}/akun')
            ->name('peserta-magang.akun.')
            ->group(function () {
                Route::post('/', 'store')->name('store');
                Route::put('/password', 'resetPassword')->name('reset');
                Route::put('/status', 'updateStatus')->name('status');
                Route::delete('/', 'destroy')->name('destroy');
            });

        // Update absensi langsung ke Google Sheets peserta
        Route::put(
            '/peserta-magang/{pesertaMagang}/absensi',
            [PesertaMagangController::class, 'updateAbsensi']
        )->name('peserta-magang.absensi.update');

        // Hapus cache & baca ulang absensi dari Google Sheets
        Route::post(
            '/peserta-magang/{pesertaMagang}/absensi/muat-ulang',
            [PesertaMagangController::class, 'muatUlangAbsensi']
        )->name('peserta-magang.absensi.refresh');

        // Logbook kegiatan (dibaca dari sheet, verifikasi ke kolom Action)
        Route::get(
            '/peserta-magang/{pesertaMagang}/logbook',
            [LogbookController::class, 'show']
        )->name('peserta-magang.logbook');

        Route::put(
            '/peserta-magang/{pesertaMagang}/logbook',
            [LogbookController::class, 'update']
        )->name('peserta-magang.logbook.update');

        // Laporan & export
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
        Route::get('/laporan/csv', [LaporanController::class, 'csv'])->name('laporan.csv');
        Route::get('/laporan/xlsx', [LaporanController::class, 'xlsx'])->name('laporan.xlsx');
        Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');

        // Penilaian akhir peserta
        Route::get(
            '/peserta-magang/{pesertaMagang}/penilaian',
            [PenilaianController::class, 'edit']
        )->name('peserta-magang.penilaian');

        Route::put(
            '/peserta-magang/{pesertaMagang}/penilaian',
            [PenilaianController::class, 'update']
        )->name('peserta-magang.penilaian.update');

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

        Route::get('/dashboard', SuperAdminDashboardController::class)
            ->name('dashboard');

        // Kelola pengguna
        Route::resource('pengguna', UserController::class)
            ->parameters(['pengguna' => 'user'])
            ->except('show')
            ->middleware('can:kelola-pengguna');

        // Aspek & bobot penilaian
        Route::get('/aspek-penilaian', [AspekPenilaianController::class, 'index'])
            ->can('kelola-aspek-penilaian')
            ->name('aspek-penilaian.index');
        Route::put('/aspek-penilaian', [AspekPenilaianController::class, 'update'])
            ->can('kelola-aspek-penilaian')
            ->name('aspek-penilaian.update');

        // Log aktivitas
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])
            ->can('lihat-log-aktivitas')
            ->name('log-aktivitas.index');

    });
