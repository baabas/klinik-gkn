<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menghapus data lama untuk memastikan data bersih sebelum diisi
        DB::table('obat')->delete();

        DB::table('obat')->insert([
            ['kode_obat' => 'P001', 'nama_obat' => 'Paracetamol 500mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 150],
            ['kode_obat' => 'A001', 'nama_obat' => 'Amoxicillin 500mg', 'satuan' => 'Kapsul', 'kemasan' => 'Strip', 'stok_saat_ini' => 118],
            ['kode_obat' => 'I001', 'nama_obat' => 'Ibuprofen 400mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 85],
            ['kode_obat' => 'M001', 'nama_obat' => 'Mylanta Cair 50ml', 'satuan' => 'Botol', 'kemasan' => 'Fles', 'stok_saat_ini' => 55],
            ['kode_obat' => 'PR01', 'nama_obat' => 'Promag', 'satuan' => 'Tablet', 'kemasan' => 'Box', 'stok_saat_ini' => 197],
            ['kode_obat' => 'B001', 'nama_obat' => 'Bodrex', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 241],
            ['kode_obat' => 'PN01', 'nama_obat' => 'Panadol Biru', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 179],
            ['kode_obat' => 'C001', 'nama_obat' => 'Cefadroxil 500mg', 'satuan' => 'Kapsul', 'kemasan' => 'Strip', 'stok_saat_ini' => 75],
            ['kode_obat' => 'L001', 'nama_obat' => 'Loratadine 10mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 60],
            ['kode_obat' => 'CT01', 'nama_obat' => 'Cetirizine 10mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 90],
            ['kode_obat' => 'AM01', 'nama_obat' => 'Ambroxol 30mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 110],
            ['kode_obat' => 'D001', 'nama_obat' => 'Dexamethasone 0.5mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 130],
            ['kode_obat' => 'OM01', 'nama_obat' => 'Omeprazole 20mg', 'satuan' => 'Kapsul', 'kemasan' => 'Strip', 'stok_saat_ini' => 80],
            ['kode_obat' => 'V001', 'nama_obat' => 'Vitamin C IPI', 'satuan' => 'Tablet', 'kemasan' => 'Fles', 'stok_saat_ini' => 300],
            ['kode_obat' => 'V002', 'nama_obat' => 'Vitamin B Complex', 'satuan' => 'Tablet', 'kemasan' => 'Fles', 'stok_saat_ini' => 270],
            ['kode_obat' => 'S001', 'nama_obat' => 'Sangobion', 'satuan' => 'Kapsul', 'kemasan' => 'Strip', 'stok_saat_ini' => 95],
            ['kode_obat' => 'BT01', 'nama_obat' => 'Betadine 15ml', 'satuan' => 'Botol', 'kemasan' => 'Fles', 'stok_saat_ini' => 30],
            ['kode_obat' => 'DP01', 'nama_obat' => 'Diapet', 'satuan' => 'Kapsul', 'kemasan' => 'Strip', 'stok_saat_ini' => 65],
            ['kode_obat' => 'AS01', 'nama_obat' => 'Asam Mefenamat 500mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 131],
            ['kode_obat' => 'MT01', 'nama_obat' => 'Metformin 500mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 70],
            ['kode_obat' => 'AM02', 'nama_obat' => 'Amlodipine 5mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 98],
            ['kode_obat' => 'SM01', 'nama_obat' => 'Simvastatin 10mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 88],
            ['kode_obat' => 'SL01', 'nama_obat' => 'Salbutamol 2mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 50],
            ['kode_obat' => 'CP01', 'nama_obat' => 'Captopril 25mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 125],
            ['kode_obat' => 'GL01', 'nama_obat' => 'Glibenclamide 5mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 60],
            ['kode_obat' => 'AT01', 'nama_obat' => 'Antangin JRG', 'satuan' => 'Sachet', 'kemasan' => 'Box', 'stok_saat_ini' => 140],
            ['kode_obat' => 'TA01', 'nama_obat' => 'Tolak Angin Cair', 'satuan' => 'Sachet', 'kemasan' => 'Box', 'stok_saat_ini' => 160],
            ['kode_obat' => 'K001', 'nama_obat' => 'Komix OBH', 'satuan' => 'Sachet', 'kemasan' => 'Box', 'stok_saat_ini' => 135],
            ['kode_obat' => 'N001', 'nama_obat' => 'Neurobion Forte', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 80],
            ['kode_obat' => 'R001', 'nama_obat' => 'Ranitidine 150mg', 'satuan' => 'Tablet', 'kemasan' => 'Strip', 'stok_saat_ini' => 89],
        ]);
    }
}
