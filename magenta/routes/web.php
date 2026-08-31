<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanMagangController;

Route::get('/', function () {
    return view('website.index');
});

Route::get('/pengajuan', [PengajuanMagangController::class, 'index'])
    ->name('admin.pengajuan.index');

Route::get('/pengajuan/create', [PengajuanMagangController::class, 'create'])
    ->name('admin.pengajuan.create');

Route::post('/pengajuan', [PengajuanMagangController::class, 'store'])
    ->name('admin.pengajuan.store');

Route::get('/pengajuan/{pengajuanMagang}', [PengajuanMagangController::class, 'show'])
    ->name('admin.pengajuan.show');

Route::put('/pengajuan/{pengajuanMagang}/status', [PengajuanMagangController::class, 'updateStatus'])
    ->name('admin.pengajuan.status');

Route::delete('/pengajuan/{pengajuanMagang}', [PengajuanMagangController::class, 'destroy'])
    ->name('admin.pengajuan.destroy');