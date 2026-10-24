<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Services\MonitoringPeserta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Monitoring peserta: biodata, logbook periode berjalan, dan verifikasi.
 */
class MonitoringController extends Controller
{
    public function __invoke(Request $request, MonitoringPeserta $monitoring): View
    {
        $filter = $request->validate([
            'status' => ['nullable', Rule::in(['aktif', 'selesai', 'semua'])],
            'divisi' => ['nullable', 'integer'],
            'cari' => ['nullable', 'string', 'max:100'],
            'belum_hari_ini' => ['nullable', 'boolean'],
            'urut' => ['nullable', Rule::in(array_keys(MonitoringPeserta::URUTAN))],
        ]);

        $filter['status'] ??= 'aktif';

        return view('superadmin.monitoring.index', [
            'filter' => $filter,
            'tabel' => $monitoring->tabel($filter),
            'ringkasan' => $monitoring->ringkasan(),
            'riwayat' => $monitoring->riwayatTerbaru(),
            'divisis' => Divisi::orderBy('nama')->get(),
        ]);
    }
}
