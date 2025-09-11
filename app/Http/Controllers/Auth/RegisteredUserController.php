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
     * Menampilkan halaman registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Menangani permintaan registrasi yang masuk.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input, termasuk pengecekan NIP dan Email unik di tabel users
        $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:users,nip'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
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

        // 3. Buat user baru
        $user = User::create([
            'nama_karyawan' => $karyawan->nama_karyawan,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'akses' => 'PASIEN', // Mengatur kolom 'akses' menjadi 'PASIEN'
        ]);

        // 4. Berikan peran "PASIEN" kepada user baru
        $pasienRole = Role::where('name', 'PASIEN')->first();
        if ($pasienRole) {
            $user->roles()->attach($pasienRole);
        }

        event(new Registered($user));

        Auth::login($user);

        // 5. Atur peran aktif di sesi untuk memastikan alur aplikasi benar
        $request->session()->put('active_role', 'PASIEN');

        // 6. Arahkan ke halaman kartu pasien setelah berhasil mendaftar
        return redirect()->route('pasien.my_card');
    }
}