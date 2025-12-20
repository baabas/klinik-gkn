<?php

namespace Database\Seeders;

use App\Models\MasterWhatsappValidator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterWhatsappValidatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_validator' => 'Pengawas 1',
                'nomor_wa' => '081234567890',
                'keterangan' => 'Validator utama untuk distribusi obat',
                'is_active' => true,
            ],
            [
                'nama_validator' => 'Pengawas 2',
                'nomor_wa' => '081904841683',
                'keterangan' => 'Validator backup',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            MasterWhatsappValidator::create($item);
        }

        $this->command->info('✅ Master WhatsApp Validator berhasil di-seed (' . count($data) . ' data)');
    }
}
