<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WajibGantiPassword
{
    /**
     * Route yang tetap boleh dibuka sebelum password sementara diganti.
     */
    private const DIIZINKAN = ['profil.edit', 'profil.password', 'logout'];

    /**
     * Akun dengan password sementara (dibuat/di-reset admin) wajib
     * mengganti password sebelum membuka halaman lain.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->wajib_ganti_password && ! $request->routeIs(...self::DIIZINKAN)) {
            return redirect()
                ->route('profil.edit')
                ->with('warning', 'Silakan ganti password sementara Anda sebelum melanjutkan.');
        }

        return $next($request);
    }
}
