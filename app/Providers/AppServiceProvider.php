<?php

namespace App\Providers;

use App\Models\PengaturanMagang;
use App\Models\User;
use App\Services\PeriodeMagang;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Tanggal awal periode diatur admin di halaman Pengaturan Magang.
        $this->app->bind(PeriodeMagang::class, fn () => new PeriodeMagang(
            PengaturanMagang::ambil()->tanggal_awal_periode
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureGates();
    }

    /**
     * Hak akses aksi khusus Super Admin, dikumpulkan di satu tempat.
     * Dipakai di route (->can()) dan sidebar (@can), sebagai lapisan
     * kedua di samping middleware role.
     */
    private function configureGates(): void
    {
        $superAdmin = fn (User $user): bool => $user->role === 'super_admin';

        Gate::define('kelola-pengguna', $superAdmin);
        Gate::define('kelola-aspek-penilaian', $superAdmin);
        Gate::define('lihat-log-aktivitas', $superAdmin);
    }

    /**
     * Batas pengiriman form publik supaya tidak disalahgunakan (spam).
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('pendaftaran', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(fn () => redirect()
                    ->to(route('home').'#daftar')
                    ->withInput($request->except('dokumen'))
                    ->withErrors(['nim' => 'Terlalu banyak pengiriman pendaftaran. Silakan coba lagi dalam 1 menit.']));
        });

        RateLimiter::for('cek-status', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(fn () => redirect()
                    ->to(route('home').'#status')
                    ->withInput()
                    ->withErrors(['status_kode' => 'Terlalu banyak pengecekan. Silakan coba lagi dalam 1 menit.']));
        });
    }
}
