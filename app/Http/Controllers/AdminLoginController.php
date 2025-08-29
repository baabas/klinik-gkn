<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminLoginController extends Controller
{
    /**
     * Menampilkan form login admin.
     */
    public function create(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Menangani permintaan login dari admin.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 2. Coba lakukan autentikasi
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // 3. Cek apakah user memiliki peran 'DOKTER'
            if ($user->roles()->where('name', 'DOKTER')->exists()) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            } else {
                // Jika user bukan DOKTER, logout dan kembalikan ke login admin
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'nip' => 'Akun ini tidak memiliki hak akses sebagai admin.',
                ])->onlyInput('nip');
            }
        }

        // Jika NIP atau password salah
        return back()->withErrors([
            'nip' => 'NIP atau Password yang Anda masukkan salah.',
        ])->onlyInput('nip');
    }
}
