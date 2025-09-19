<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\StokBarangSeeder;

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
            RoleSeeder::class,
            KaryawanSeeder::class,
            DaftarPenyakitSeeder::class,
            StokBarangSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
