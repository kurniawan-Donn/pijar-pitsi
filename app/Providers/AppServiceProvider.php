<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
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
                    ->withErrors(['status_nim' => 'Terlalu banyak pengecekan. Silakan coba lagi dalam 1 menit.']));
        });
    }
}
