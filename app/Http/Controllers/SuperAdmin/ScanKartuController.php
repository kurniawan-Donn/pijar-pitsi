<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ScanKartuAbsensi;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Foto kartu absensi berdampingan dengan hasil bacaan dan jam yang tersimpan di logbook.
 */
class ScanKartuController extends Controller
{
    public function show(ScanKartuAbsensi $scanKartuAbsensi): View
    {
        return view('superadmin.scan-kartu.show', [
            'scan' => $scanKartuAbsensi->load('pesertaMagang', 'logbooks'),
            'logbooks' => $scanKartuAbsensi->logbooks->keyBy(fn ($logbook) => $logbook->tanggal->day),
        ]);
    }

    /**
     * Foto kartu (disk privat): pemilik, admin, dan super admin. Dipakai juga oleh peserta.
     */
    public function foto(ScanKartuAbsensi $scanKartuAbsensi): Response
    {
        Gate::authorize('view', $scanKartuAbsensi);

        abort_unless(Storage::disk('local')->exists($scanKartuAbsensi->foto_path), 404);

        return Storage::disk('local')->response($scanKartuAbsensi->foto_path, null, [
            'Cache-Control' => 'private, max-age=600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
