<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Riwayat aksi penting (khusus Super Admin).
 */
class LogAktivitasController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'aksi' => ['nullable', 'string', 'in:'.implode(',', array_keys(LogAktivitas::AKSI))],
            'user_id' => ['nullable', 'integer'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        $logs = LogAktivitas::query()
            ->when($filter['aksi'] ?? null, fn ($q, $aksi) => $q->where('aksi', $aksi))
            ->when($filter['user_id'] ?? null, fn ($q, $id) => $q->where('user_id', $id))
            ->when($filter['dari'] ?? null, fn ($q, $dari) => $q->whereDate('created_at', '>=', $dari))
            ->when($filter['sampai'] ?? null, fn ($q, $sampai) => $q->whereDate('created_at', '<=', $sampai))
            ->latest('created_at')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('superadmin.log-aktivitas.index', [
            'logs' => $logs,
            'filter' => $filter,
            'pengguna' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
