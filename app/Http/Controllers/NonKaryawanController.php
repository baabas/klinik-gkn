<?php

namespace App\Http\Controllers;

use App\Models\NonKaryawan;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NonKaryawanController extends Controller
{
    /**
     * Menampilkan form untuk membuat pasien non-karyawan baru.
     */
    public function create()
    {
        return view('pasien_non_karyawan.create');
    }

    /**
     * Menyimpan data pasien non-karyawan baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nik' => 'required|string|digits:16|unique:users,nik',
            'nama' => 'required|string|max:255',
            'lokasi_gedung' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
        ], [
            // [DITAMBAHKAN] Pesan validasi kustom
            'nik.unique' => 'NIK ini sudah terdaftar sebagai pasien.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            'lokasi_gedung.required' => 'Lokasi gedung wajib dipilih.',
        ]);

        // Gunakan transaction untuk memastikan kedua tabel berhasil diisi
        DB::beginTransaction();
        try {
            // 1. Buat record di tabel 'users'
            $user = User::create([
                'nik' => $validatedData['nik'],
                'nama_karyawan' => $validatedData['nama'],
                'akses' => 'PASIEN',
                'email' => null, // Email dikosongkan
                'password' => null, // Password dikosongkan
            ]);

            // 2. Beri peran 'PASIEN'
            $user->roles()->attach(Role::where('name', 'PASIEN')->first());

            // 3. Buat record di tabel 'non_karyawan' sebagai profil
            NonKaryawan::create([
                'nik' => $validatedData['nik'],
                'lokasi_gedung' => $validatedData['lokasi_gedung'],
                'tanggal_lahir' => $validatedData['tanggal_lahir'],
            ]);

            DB::commit(); // Simpan perubahan jika semua berhasil

            return redirect()->route('pasien.index')->with('success', 'Pasien non-karyawan berhasil didaftarkan.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error
            return redirect()->back()->with('error', 'Gagal mendaftarkan pasien. ' . $e->getMessage())->withInput();
        }
    }
}