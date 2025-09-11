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
                'id_lokasi' => 1, // <-- id_lokasi diisi untuk DOKTER
                'role_id' => $dokterRole->id ?? null,
            ],
            [
                'nip' => '199901202022032001',
                'nama_karyawan' => 'Putri Amelia',
                'email' => 'putri.a@example.com',
                'password' => Hash::make('12345678'),
                'akses' => 'DOKTER',
                'id_lokasi' => 2, 
                'role_id' => $dokterRole->id ?? null,
            ],
            [
                'nip' => '198703152010121001',
                'nama_karyawan' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'password' => Hash::make('12345678'),
                'akses' => 'PENGADAAN',
                'id_lokasi' => null, // <-- id_lokasi diatur menjadi null untuk PENGADAAN
                'role_id' => $pengadaanRole->id ?? null,
            ],
        ];

        foreach ($users as $userData) {
            // Menggunakan updateOrCreate untuk efisiensi, akan membuat user jika belum ada, atau update jika sudah ada
            $user = User::updateOrCreate(
                ['nip' => $userData['nip']], // Kunci untuk mencari pengguna
                [
                    'nama_karyawan' => $userData['nama_karyawan'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'akses' => $userData['akses'],
                    'id_lokasi' => $userData['id_lokasi'], // Menyimpan id_lokasi (bisa null)
                ]
            );
            
            // Cek jika role ditemukan sebelum menyinkronkan
            if ($userData['role_id']) {
                $user->roles()->syncWithoutDetaching([$userData['role_id']]);
            }
        }
    }
}