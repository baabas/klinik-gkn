<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'DOKTER']);
        Role::create(['name' => 'PASIEN']);
        Role::create(['name' => 'PENGADAAN']);
    }
}
