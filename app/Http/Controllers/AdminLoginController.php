<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Menampilkan form login untuk admin (Dokter & Pengadaan).
     * Method ini dipanggil oleh rute GET /admin/login.
     * (Sebelumnya bernama 'create')
     */
    public function showLoginForm(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Memproses permintaan autentikasi admin menggunakan NIP.
     * Method ini dipanggil oleh rute POST /admin/login.
     * (Sebelumnya bernama 'store')
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validasi input dari form menggunakan NIP
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        // 2. Mencoba untuk melakukan otentikasi
        if (Auth::attempt([
            $field => $credentials['login'],
            'password' => $credentials['password'],
        ])) {
            $user = Auth::user();

            // 3. Cek apakah user punya peran DOKTER atau PENGADAAN (Logika lama Anda dipertahankan)
            if ($user->hasRole('DOKTER') || $user->hasRole('PENGADAAN')) {
                $request->session()->regenerate();

                // Tentukan peran aktif berdasarkan prioritas
                $activeRole = $user->hasRole('DOKTER') ? 'DOKTER' : 'PENGADAAN';
                $request->session()->put('active_role', $activeRole);

                return redirect()->intended(route('dashboard'));
            }

            // 4. Jika tidak punya peran admin, logout dan tolak
            Auth::logout();
            return back()->withErrors([
                'login' => 'Anda tidak memiliki hak akses sebagai admin.',
            ])->onlyInput('login');
        }

        // 5. Jika NIP atau Password salah
        return back()->withErrors([
            'login' => 'NIP/Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    /**
     * Memproses permintaan logout dari admin.
     * Method ini ditambahkan agar sesuai dengan rute POST /admin/logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
