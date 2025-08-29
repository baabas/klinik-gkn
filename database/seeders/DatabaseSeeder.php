<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            KaryawanSeeder::class,
            DaftarPenyakitSeeder::class,
            ObatSeeder::class,
            AdminUserSeeder::class,
            // Tambahkan seeder lain jika ada
        ]);
    }
}
