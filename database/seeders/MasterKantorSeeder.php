<?php

namespace Database\Seeders;

use App\Models\MasterKantor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterKantorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Data kantor yang sesuai dengan form registrasi pasien
            ['nama' => 'Kanwil', 'kode' => 'KANWIL'],
            ['nama' => 'KPP Gayam Sari', 'kode' => 'KPP-GS'],
            ['nama' => 'KPP Madya SMG', 'kode' => 'KPP-MSMG'],
            ['nama' => 'KPP SMG Selatan', 'kode' => 'KPP-SMGS'],
            ['nama' => 'KPP SMG Tengah 1', 'kode' => 'KPP-SMGT1'],
            ['nama' => 'KPTIK', 'kode' => 'KPTIK'],
            ['nama' => 'PT Gumilang', 'kode' => 'PTG'],
            ['nama' => 'Kanwil DJPB', 'kode' => 'KANWIL-DJPB'],
            ['nama' => 'KPTIK BMN Semarang', 'kode' => 'KPTIK-BMN'],
            ['nama' => 'KPP Madya Dua Semarang', 'kode' => 'KPP-MD2'],
            ['nama' => 'Kanwil DJP Jateng 1', 'kode' => 'KANWIL-DJP-JT1'],
            ['nama' => 'Kanwil DJKN', 'kode' => 'KANWIL-DJKN'],
            ['nama' => 'KPKNL Semarang', 'kode' => 'KPKNL-SMG'],
        ];

        foreach ($data as $item) {
            MasterKantor::create([
                'nama_kantor' => $item['nama'],
                'kode_kantor' => $item['kode'],
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Master Kantor berhasil di-seed (' . count($data) . ' data)');
    }
}
