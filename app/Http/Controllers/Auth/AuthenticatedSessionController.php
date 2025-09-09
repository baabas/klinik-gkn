<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Menangani permintaan autentikasi yang masuk.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // Atur peran aktif untuk sesi ini sebagai PASIEN.
        $request->session()->put('active_role', 'PASIEN');

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Hancurkan sesi yang terautentikasi.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ================== PERUBAHAN PRESISI DI SINI ==================

        // 1. Ambil peran AKTIF dari sesi SEBELUM sesi dihancurkan.
        $activeRole = $request->session()->get('active_role');

        // 2. Lakukan proses logout seperti biasa.
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3. Arahkan pengguna berdasarkan peran AKTIF yang sudah kita simpan.
        if ($activeRole === 'DOKTER' || $activeRole === 'PENGADAAN') {
            // Jika sesi yang logout adalah DOKTER atau PENGADAAN,
            // arahkan kembali ke halaman login admin.
            return redirect()->route('admin.login');
        }

        // Jika bukan (berarti sesi yang logout adalah PASIEN),
        // arahkan ke halaman login biasa.
        return redirect()->route('login');
        
        // ===============================================================
    }
}