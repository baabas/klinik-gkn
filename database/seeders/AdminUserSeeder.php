<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        
        $dokterRole = Role::where('name', 'DOKTER')->first();
        $pengadaanRole = Role::where('name', 'PENGADAAN')->first();

        $users = [
            [
                'nip' => '199205202016072002',
                'nama_karyawan' => 'Ani Lestari',
                'email' => 'ani.lestari@example.com',
                'password' => Hash::make('12345678'),
                'akses' => 'DOKTER',
                'role_id' => $dokterRole->id ?? null,
            ],
            [
                'nip' => '198703152010121001',
                'nama_karyawan' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'password' => Hash::make('12345678'),
                'akses' => 'PENGADAAN',
                'role_id' => $pengadaanRole->id ?? null,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::where('nip', $userData['nip'])->first();

            if (!$user) {
                $user = User::create([
                    'nip' => $userData['nip'],
                    'nama_karyawan' => $userData['nama_karyawan'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'akses' => $userData['akses'],
                ]);
            }
            
            // Cek jika role ditemukan sebelum menyinkronkan
            if ($userData['role_id']) {
                $user->roles()->syncWithoutDetaching([$userData['role_id']]);
            }
        }
    }
}