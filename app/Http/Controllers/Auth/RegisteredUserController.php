<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\MasterKantor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman registrasi.
     */
    public function create(): View
    {
        $fallbackKantors = collect([
            'KPP Gayam Sari',
            'KPP Semarang Selatan',
            'KPP Semarang Tengah 1',
            'KPP Semarang Barat',
            'Pihak Ketiga',
            'Kanwil DJPB Jawa Tengah',
            'KPTIK BMN Semarang',
            'KPP Madya Dua Semarang',
            'Kanwil DJP Jateng 1',
            'Kanwil DJKN Jateng dan DIY',
            'KPKNL Semarang',
            'Tamu',
        ])->map(fn ($namaKantor) => (object) ['nama_kantor' => $namaKantor]);

        // Ambil data kantor dari master_kantor yang aktif
        $kantors = Schema::hasTable('master_kantor')
            ? MasterKantor::where('is_active', true)
                ->orderBy('nama_kantor')
                ->get()
                ->whenEmpty(fn () => $fallbackKantors)
            : $fallbackKantors;

        return view('auth.register', compact('kantors'));
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
                'nip' => ['required', 'digits:18', 'unique:users,nip', 'unique:karyawan,nip'],
                'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:karyawan,email'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'kantor' => ['required', 'string', 'max:100'],
                'tanggal_lahir' => ['required', 'date'],
                'jenis_kelamin' => ['required', 'in:L,P'],
                'alamat' => ['nullable', 'string'],
                'no_hp' => ['nullable', 'string', 'max:20', 'unique:karyawan,no_hp', 'unique:non_karyawan,no_hp'],
                'alergi' => ['nullable', 'string'],
                ], [
                'nip.digits' => 'NIP harus terdiri dari 18 digit.',
                'name.regex' => 'Nama hanya boleh berisi huruf, spasi, dan titik.',
                'no_hp.unique' => 'Nomor HP sudah terdaftar dalam sistem.',
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
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                'alergi' => $request->alergi,
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

    /**
     * Check for duplicate NIP and Email
     */
    public function checkDuplicate(Request $request)
    {
        $nip = $request->input('nip');
        $email = $request->input('email');
        $noHp = $request->input('no_hp');

        $nipExists = false;
        $emailExists = false;
        $noHpExists = false;

        if ($nip) {
            $nipExists = User::where('nip', $nip)->exists() || 
                         Karyawan::where('nip', $nip)->exists();
        }

        if ($email) {
            $emailExists = User::where('email', $email)->exists() || 
                          Karyawan::where('email', $email)->exists();
        }

        if ($noHp) {
            $noHpExists = Karyawan::where('no_hp', $noHp)->exists() || 
                         \App\Models\NonKaryawan::where('no_hp', $noHp)->exists();
        }

        return response()->json([
            'nip_exists' => $nipExists,
            'email_exists' => $emailExists,
            'no_hp_exists' => $noHpExists
        ]);
    }
}
