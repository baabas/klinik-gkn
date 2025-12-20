<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
        // Rate Limiting - Cegah brute force attack
        $key = 'login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // 1. Validasi input dari form menggunakan NIP atau Email (dengan Max Length Validation)
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'login.required' => 'NIP atau Email wajib diisi.',
            'login.max' => 'NIP atau Email terlalu panjang (maksimal 255 karakter).',
            'password.required' => 'Password wajib diisi.',
            'password.max' => 'Password terlalu panjang (maksimal 255 karakter).',
        ]);

        // Validasi tambahan: cek apakah login adalah NIP (18 digit) atau Email valid
        $loginValue = $credentials['login'];
        $isEmail = filter_var($loginValue, FILTER_VALIDATE_EMAIL);
        $isNIP = preg_match('/^\d{18}$/', $loginValue);
        
        if (!$isEmail && !$isNIP) {
            return back()->withErrors([
                'login' => 'Masukkan NIP (18 digit angka) atau Email yang valid.',
            ])->onlyInput('login');
        }

        $field = $isEmail ? 'email' : 'nip';

        // 2. Mencoba untuk melakukan otentikasi
        if (Auth::attempt([
            $field => $credentials['login'],
            'password' => $credentials['password'],
        ])) {
            // Reset rate limiting counter jika login berhasil
            RateLimiter::clear($key);
            
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

        // Increment rate limiting counter jika login gagal
        RateLimiter::hit($key, 60);

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
