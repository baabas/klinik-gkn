<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Menampilkan halaman login admin.
     */
    public function create(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Menangani permintaan autentikasi admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek apakah user punya peran DOKTER atau PENGADAAN
            if ($user->hasRole('DOKTER') || $user->hasRole('PENGADAAN')) {
                $request->session()->regenerate();

                // Tentukan peran aktif berdasarkan prioritas (Dokter > Pengadaan)
                $activeRole = $user->hasRole('DOKTER') ? 'DOKTER' : 'PENGADAAN';
                $request->session()->put('active_role', $activeRole);

                return redirect()->intended(route('dashboard'));
            }

            // Jika tidak punya peran admin, logout dan tolak
            Auth::logout();
            return back()->withErrors([
                'nip' => 'Anda tidak memiliki hak akses admin.',
            ])->onlyInput('nip');
        }

        return back()->withErrors([
            'nip' => 'NIP atau Password yang Anda masukkan salah.',
        ])->onlyInput('nip');
    }
}