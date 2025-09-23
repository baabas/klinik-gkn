<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            // Seeder untuk membuat role (PASIEN, DOKTER, PENGADAAN)
            RoleSeeder::class,

            // Seeder untuk mengisi data master karyawan
            KaryawanSeeder::class,

            // Seeder untuk mengisi tabel daftar_penyakit dengan data ICD-10 dari file CSV
            DaftarPenyakitSeeder::class,

            // Seeder lain yang mungkin Anda butuhkan
            PengadaanSeeder::class,
            ContohBarangSeeder::class,
            
            // Seeder untuk membuat akun user (Dokter, Pengadaan, dll.)
            AdminUserSeeder::class,
            PermintaanBarangSeeder::class,
        ]);
    }
}
