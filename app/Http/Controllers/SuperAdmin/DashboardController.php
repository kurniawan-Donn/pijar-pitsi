<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Controller;
use App\Services\StatistikMagang;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Super Admin dengan statistik magang.
     */
    public function __invoke(StatistikMagang $statistik): View
    {
        return view('superadmin.dashboard.index', AdminDashboardController::dataDashboard($statistik));
    }
}
