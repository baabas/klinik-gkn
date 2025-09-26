<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        // Debug: Log the incoming request data
        Log::info('Registration attempt', $request->all());

        // 1. Validasi input
        try {
            $request->validate([
                'nip' => ['required', 'string', 'max:30', 'unique:users,nip', 'unique:karyawan,nip'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:karyawan,email'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'kantor' => ['required', 'string', 'max:100'],
                'alamat' => ['required', 'string'],
                'agama' => ['required', 'string', 'max:50'],
                'tanggal_lahir' => ['required', 'date'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', $e->errors());
            throw $e;
        }

        try {
            // 2. Simpan data ke tabel karyawan terlebih dahulu
            Log::info('Creating karyawan record');
            $karyawan = Karyawan::create([
                'nip' => $request->nip,
                'nama_karyawan' => $request->name,
                'kantor' => $request->kantor,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'agama' => $request->agama,
                'tanggal_lahir' => $request->tanggal_lahir,
            ]);
            Log::info('Karyawan created successfully', ['nip' => $karyawan->nip]);

            // 3. Buat user baru di tabel users
            Log::info('Creating user record');
            $user = User::create([
                'nip' => $request->nip,
                'nama_karyawan' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'akses' => 'PASIEN', // Mengatur kolom 'akses' menjadi 'PASIEN'
            ]);
            Log::info('User created successfully', ['id' => $user->id]);

            // 4. Berikan peran "PASIEN" kepada user baru
            $pasienRole = Role::where('name', 'PASIEN')->first();
            if ($pasienRole) {
                $user->roles()->attach($pasienRole);
                Log::info('Role attached successfully');
            }

            event(new Registered($user));

            Log::info('Registration completed successfully');
            return redirect()->route('login')
                ->with('status', 'Anda berhasil registrasi. Silakan login kembali.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.'
            ])->withInput();
        }
    }
}
