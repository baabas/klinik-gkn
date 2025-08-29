<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
public function store(Request $request): RedirectResponse
{
    $credentials = $request->validate([
        'nip' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt(['nip' => $request->nip, 'password' => $request->password], $request->boolean('remember'))) {
        $request->session()->regenerate();
        $user = Auth::user();

        // Cek peran pengguna
        if ($user->roles()->where('name', 'PASIEN')->exists()) {
            // Jika PASIEN, arahkan ke kartu pasien
            return redirect()->intended(route('pasien.my_card'));
        }

        // Untuk peran lain (DOKTER), arahkan ke dashboard
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'nip' => 'NIP atau Password yang Anda masukkan salah.',
    ])->onlyInput('nip');
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
