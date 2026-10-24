<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PengajuanMagangController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfilController;
use App\Http\Controllers\Peserta\BiodataController as PesertaBiodataController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboardController;
use App\Http\Controllers\Peserta\FeedbackController as PesertaFeedbackController;
use App\Http\Controllers\Peserta\IzinController as PesertaIzinController;
use App\Http\Controllers\Peserta\LogbookController as PesertaLogbookController;
use App\Http\Controllers\SuperAdmin\AkunPesertaController;
use App\Http\Controllers\SuperAdmin\AspekPenilaianController;
use App\Http\Controllers\SuperAdmin\BannerController;
use App\Http\Controllers\SuperAdmin\BiodataPesertaController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\DivisiController;
use App\Http\Controllers\SuperAdmin\FeedbackController;
use App\Http\Controllers\SuperAdmin\ImporLogbookController;
use App\Http\Controllers\SuperAdmin\LaporanController;
use App\Http\Controllers\SuperAdmin\LogAktivitasController;
use App\Http\Controllers\SuperAdmin\LogbookController;
use App\Http\Controllers\SuperAdmin\LogbookHarianController;
use App\Http\Controllers\SuperAdmin\MonitoringController;
use App\Http\Controllers\SuperAdmin\PenempatanPesertaController;
use App\Http\Controllers\SuperAdmin\PengaturanMagangController;
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

        Route::get('/biodata', [PesertaBiodataController::class, 'edit'])->name('biodata.edit');
        Route::put('/biodata', [PesertaBiodataController::class, 'update'])->name('biodata.update');

        // Logbook harian (tanggal Y-m-d)
        Route::controller(PesertaLogbookController::class)
            ->prefix('/logbook')
            ->name('logbook.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cetak', 'cetak')->name('cetak');
                Route::get('/bukti/{logbookBukti}', 'bukti')->name('bukti');
                Route::get('/{tanggal}', 'edit')->where('tanggal', '\d{4}-\d{2}-\d{2}')->name('edit');
                Route::put('/{tanggal}', 'update')->where('tanggal', '\d{4}-\d{2}-\d{2}')->name('update');
                Route::delete('/{tanggal}', 'destroy')->where('tanggal', '\d{4}-\d{2}-\d{2}')->name('destroy');
            });

        // Feedback program magang (dibuka sebulan sebelum selesai)
        Route::get('/feedback', [PesertaFeedbackController::class, 'edit'])->name('feedback.edit');
        Route::put('/feedback', [PesertaFeedbackController::class, 'update'])->name('feedback.update');

        // Izin / sakit beberapa hari
        Route::controller(PesertaIzinController::class)
            ->prefix('/izin')
            ->name('izin.')
            ->group(function () {
                Route::get('/ajukan', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::delete('/{izinPeserta}', 'destroy')->name('destroy');
                Route::get('/{izinPeserta}/bukti', 'bukti')->name('bukti');
            });
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

        // Pengaturan magang: aturan umum, tarif uang saku, hari libur
        Route::controller(PengaturanMagangController::class)
            ->prefix('/pengaturan-magang')
            ->name('pengaturan-magang.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::put('/', 'update')->name('update');
                Route::post('/tarif', 'simpanTarif')->name('tarif.store');
                Route::delete('/tarif/{tarifUangSaku}', 'hapusTarif')->name('tarif.destroy');
                Route::post('/libur', 'simpanLibur')->name('libur.store');
                Route::delete('/libur/{hariLibur}', 'hapusLibur')->name('libur.destroy');
            });

        // Master divisi
        Route::resource('divisi', DivisiController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Divisi & mentor peserta per periode
        Route::put(
            '/peserta-magang/{pesertaMagang}/penempatan',
            [PenempatanPesertaController::class, 'update']
        )->name('peserta-magang.penempatan.update');

        // Biodata peserta: detail program & tinjau
        Route::controller(BiodataPesertaController::class)
            ->prefix('/peserta-magang/{pesertaMagang}/biodata')
            ->group(function () {
                Route::get('/', 'show')->name('peserta-magang.biodata');
                Route::put('/program', 'updateProgram')->name('peserta-magang.biodata.program');
                Route::put('/tinjau', 'tinjau')->name('peserta-magang.biodata.tinjau');
            });

        // Logbook harian (database): verifikasi, izin/sakit, buka kunci, cetak
        Route::controller(LogbookHarianController::class)->group(function () {
            Route::get('/peserta-magang/{pesertaMagang}/logbook-harian', 'index')->name('peserta-magang.logbook-harian');
            Route::put('/peserta-magang/{pesertaMagang}/logbook-harian/verifikasi', 'verifikasi')->name('peserta-magang.logbook-harian.verifikasi');
            Route::post('/peserta-magang/{pesertaMagang}/logbook-harian/buka-kunci', 'bukaKunci')->name('peserta-magang.logbook-harian.buka-kunci');
            Route::get('/peserta-magang/{pesertaMagang}/logbook-harian/cetak', 'cetak')->name('peserta-magang.logbook-harian.cetak');
            Route::put('/peserta-magang/{pesertaMagang}/izin/{izinPeserta}', 'verifikasiIzin')->name('peserta-magang.izin.verifikasi');
            Route::get('/logbook-bukti/{logbookBukti}', 'bukti')->name('logbook-bukti.show');
            Route::get('/izin-bukti/{izinPeserta}', 'buktiIzin')->name('izin-bukti.show');
        });

        // Impor riwayat logbook dari buku kerja lama
        Route::controller(ImporLogbookController::class)
            ->prefix('/peserta-magang/{pesertaMagang}/impor-logbook')
            ->name('peserta-magang.impor-logbook.')
            ->group(function () {
                Route::post('/sheets', 'dariGoogleSheets')->name('sheets');
                Route::post('/xlsx', 'dariXlsx')->name('xlsx');
                Route::delete('/', 'hapus')->name('hapus');
            });

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

        // Feedback peserta: rekap & detail
        Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/peserta-magang/{pesertaMagang}/feedback', [FeedbackController::class, 'show'])->name('peserta-magang.feedback');

        // Monitoring peserta (biodata, logbook, verifikasi)
        Route::get('/monitoring', MonitoringController::class)->name('monitoring.index');

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

        Route::get(
            '/peserta-magang/{pesertaMagang}/penilaian/cetak',
            [PenilaianController::class, 'cetak']
        )->name('peserta-magang.penilaian.cetak');

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
        Route::put('/aspek-penilaian/predikat', [AspekPenilaianController::class, 'updatePredikat'])
            ->can('kelola-aspek-penilaian')
            ->name('aspek-penilaian.predikat');

        // Log aktivitas
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])
            ->can('lihat-log-aktivitas')
            ->name('log-aktivitas.index');

    });
