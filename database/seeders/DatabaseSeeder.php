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
           // DaftarPenyakitSeeder::class,

            // Seeder untuk master data 
            MasterKantorSeeder::class,
            MasterIsiKemasanSeeder::class,
            MasterSatuanSeeder::class,
            MasterWhatsappValidatorSeeder::class,

            // Seeder untuk membuat lokasi klinik dan barang medis (PengadaanSeeder membuat lokasi juga)
            PengadaanSeeder::class,

            // Seeder untuk data dummy obat dan stok barang
            //BarangMedisSeeder::class,

            // Seeder untuk membuat akun user (Dokter, Pengadaan, dll.) - SETELAH lokasi dibuat
            AdminUserSeeder::class,

            // Seeder untuk data permintaan (PENDING, APPROVED, COMPLETED) dan stok kritis
            //PermintaanSeeder::class,
        ]);
    }
}
