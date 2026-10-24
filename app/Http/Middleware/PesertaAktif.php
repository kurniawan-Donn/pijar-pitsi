<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PesertaAktif
{
    /**
     * Area peserta hanya untuk akun aktif yang terhubung ke data peserta.
     * Peserta yang magangnya sudah selesai hanya boleh membaca (GET).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Anda sedang dinonaktifkan. Hubungi admin magang.']);
        }

        $peserta = $user->pesertaMagang;

        if (! $peserta) {
            abort(403, 'Akun ini belum terhubung dengan data peserta magang.');
        }

        if ($peserta->status === 'selesai' && ! $request->isMethodSafe()) {
            return back()->with('error', 'Masa magang Anda sudah selesai. Data hanya dapat dilihat.');
        }

        return $next($request);
    }
}
