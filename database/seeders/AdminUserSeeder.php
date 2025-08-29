<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data admin yang ingin dibuat
        $adminNip = '199205202016072002';
        $adminPassword = '12345678';

        // 1. Cari data karyawan berdasarkan NIP
        $karyawan = Karyawan::where('nip', $adminNip)->first();

        // Jika data karyawan ditemukan
        if ($karyawan) {
            // 2. Buat atau perbarui data di tabel users
            $user = User::updateOrCreate(
                ['nip' => $karyawan->nip], // Cari user dengan NIP ini
                [ // Jika tidak ada, buat baru dengan data ini. Jika ada, perbarui.
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'email' => $karyawan->email,
                    'password' => Hash::make($adminPassword),
                ]
            );

            // 3. Cari peran 'DOKTER'
            $dokterRole = Role::where('name', 'DOKTER')->first();

            // 4. Hubungkan user dengan peran 'DOKTER'
            // syncRoles akan memastikan user hanya memiliki peran ini
            if ($dokterRole) {
                $user->roles()->sync([$dokterRole->id]);
            }

            $this->command->info("Akun admin untuk {$karyawan->nama_karyawan} berhasil dibuat/diperbarui.");
        } else {
            $this->command->warn("Karyawan dengan NIP {$adminNip} tidak ditemukan. Akun admin tidak dibuat.");
        }
    }
}
