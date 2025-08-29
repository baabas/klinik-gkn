<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input dasar
        $request->validate([
            'nip' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Cek apakah NIP dan Email ada & cocok di tabel karyawan
        $karyawan = Karyawan::where('nip', $request->nip)
                            ->where('email', $request->email)
                            ->first();

        if (!$karyawan) {
            return back()->withErrors([
                'nip' => 'NIP dan Email tidak cocok dengan data karyawan yang terdaftar.',
            ])->onlyInput('nip', 'email');
        }

        // 3. Cek apakah NIP sudah terdaftar di tabel users
        if (User::where('nip', $request->nip)->exists()) {
            return back()->withErrors([
                'nip' => 'Akun untuk NIP ini sudah terdaftar. Silakan login.',
            ])->onlyInput('nip');
        }

        // 4. Buat user baru
        $user = User::create([
            'nama_karyawan' => $karyawan->nama_karyawan,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 5. Berikan peran "PASIEN" kepada user baru
        $pasienRole = Role::where('name', 'PASIEN')->first();
        if ($pasienRole) {
            $user->roles()->attach($pasienRole);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('pasien.my_card');
    }
}
