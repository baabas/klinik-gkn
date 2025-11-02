<?php

namespace Database\Seeders;

use App\Models\MasterIsiKemasan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterIsiKemasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Strip', 'singkatan' => 'Strp'],
            ['nama' => 'Kotak', 'singkatan' => 'Ktk'],
            ['nama' => 'Botol', 'singkatan' => 'Btl'],
            ['nama' => 'Vial', 'singkatan' => 'Vial'],
            ['nama' => 'Tube', 'singkatan' => 'Tub'],
        ];

        foreach ($data as $item) {
            MasterIsiKemasan::create([
                'nama_isi_kemasan' => $item['nama'],
                'singkatan' => $item['singkatan'],
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Master Isi Kemasan berhasil di-seed (5 data)');
    }
}
