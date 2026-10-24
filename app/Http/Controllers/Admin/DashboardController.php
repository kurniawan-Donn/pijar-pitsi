<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatistikMagang;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Admin dengan statistik magang.
     */
    public function __invoke(StatistikMagang $statistik): View
    {
        return view('admin.dashboard.index', self::dataDashboard($statistik));
    }

    /**
     * Data dashboard yang sama untuk Admin & Super Admin.
     *
     * @return array<string, mixed>
     */
    public static function dataDashboard(StatistikMagang $statistik): array
    {
        return [
            'peserta' => $statistik->peserta(),
            'pengajuanPerStatus' => $statistik->pengajuanPerStatus(),
            'pengajuanPerBulan' => $statistik->pengajuanPerBulan(),
            'berakhirBulanIni' => $statistik->berakhirBulanIni(),
            'pengajuanTerbaru' => $statistik->pengajuanTerbaru(),
            'sebaranPredikat' => $statistik->sebaranPredikat(),
            'absensi' => $statistik->absensiBulanIni(),
        ];
    }
}
