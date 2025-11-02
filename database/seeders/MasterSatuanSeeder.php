<?php

namespace Database\Seeders;

use App\Models\MasterSatuan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Tablet', 'singkatan' => 'Tab'],
            ['nama' => 'Botol', 'singkatan' => 'Btl'],
            ['nama' => 'Pcs', 'singkatan' => 'Pcs'],
            ['nama' => 'Vial', 'singkatan' => 'Vial'],
            ['nama' => 'Tube', 'singkatan' => 'Tub'],
            ['nama' => 'Troches', 'singkatan' => 'Trc'],
            ['nama' => 'Kapsul', 'singkatan' => 'Kaps'],
            ['nama' => 'Sirup', 'singkatan' => 'Srp'],
        ];

        foreach ($data as $item) {
            MasterSatuan::create([
                'nama_satuan' => $item['nama'],
                'singkatan' => $item['singkatan'],
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Master Satuan berhasil di-seed (8 data)');
    }
}
