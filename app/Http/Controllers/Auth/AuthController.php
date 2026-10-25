<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Batas percobaan login gagal sebelum dikunci sementara.
     */
    private const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Lama kunci (detik) setelah batas percobaan tercapai.
     */
    private const LOCKOUT_SECONDS = 60;

    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        // Yang sudah login langsung ke dashboard sesuai role.
        if ($route = auth()->user()?->routeDashboard()) {
            return redirect()->route($route);
        }

        return view('auth.login');
    }

    /**
     * Proses login Admin / Super Admin / Peserta.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Batasi percobaan login per kombinasi email + IP.
        $throttleKey = Str::transliterate(Str::lower($credentials['email'])).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors([
                    'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
                ])
                ->onlyInput('email');
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);

            return back()
                ->withErrors([
                    'email' => 'Email atau password yang kamu masukkan salah.',
                ])
                ->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        $user = Auth::user();
        $routeDashboard = $user->routeDashboard();

        if (! $user->aktif || $routeDashboard === null) {
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => $user->aktif
                        ? 'Akun tidak memiliki hak akses.'
                        : 'Akun Anda sedang dinonaktifkan. Hubungi admin magang.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route($routeDashboard);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
