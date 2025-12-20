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
            ['nama' => 'Kanwil DJBC Jateng dan DIY', 'kode' => 'BC-KANWIL'],
            ['nama' => 'Kanwil DJKN Jateng dan DIY', 'kode' => 'KANWIL-DJKN-JT-DIY'],
            ['nama' => 'Kanwil DJP Jateng I', 'kode' => 'KANWIL-DJP-JT1'],
            ['nama' => 'Kanwil DJPb Prov. Jawa Tengah', 'kode' => 'KANWIL-DJPB-JT'],
            ['nama' => 'KPKNL Semarang', 'kode' => 'KPKNL-SMG'],
            ['nama' => 'KPP Madya Dua Semarang', 'kode' => 'KPP-MD2-SMG'],
            ['nama' => 'KPP Madya Semarang', 'kode' => 'KPP-MDY'],
            ['nama' => 'KPP Pratama Semarang Barat', 'kode' => 'KPP-SMGB'],
            ['nama' => 'KPP Pratama Semarang Candisari', 'kode' => 'KPP-CDS'],
            ['nama' => 'KPP Pratama Semarang Gayamsari', 'kode' => 'KPP-GS'],
            ['nama' => 'KPP Pratama Semarang Selatan', 'kode' => 'KPP-SMGS'],
            ['nama' => 'KPP Pratama Semarang Tengah', 'kode' => 'KPP-SMGT1'],
            ['nama' => 'KPP Pratama Semarang Timur', 'kode' => 'KPP-SMT'],
            ['nama' => 'KPPBC TMP A Semarang', 'kode' => 'KPPBC-SMG'],
            ['nama' => 'KPPBC TMP Tanjung Emas', 'kode' => 'KPPBC-TJE'],
            ['nama' => 'KPPN Semarang I', 'kode' => 'KPPN-SMG1'],
            ['nama' => 'KPPN Semarang II', 'kode' => 'KPPN-SMG2'],
            ['nama' => 'KPTIK BMN Semarang', 'kode' => 'KPTIK-BMN-SMG'],
            ['nama' => 'Lainnya', 'kode' => 'Lain'],
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
