<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Ambil peran aktif yang tersimpan di sesi.
        $activeRole = session('active_role');

        // Jika tidak ada user yang login ATAU peran aktifnya tidak ada dalam daftar peran yang diizinkan.
        if (!Auth::check() || !in_array($activeRole, $roles)) {
            // Tolak akses.
            abort(403, 'AKSES DITOLAK. Anda tidak memiliki izin untuk halaman ini dalam sesi saat ini.');
        }

        return $next($request);
    }
}