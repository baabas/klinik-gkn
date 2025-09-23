<?php

namespace Database\Seeders;

use App\Models\BarangKemasan;
use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\StokBarang;
use App\Models\StokHistory;
use Illuminate\Database\Seeder;

class ContohBarangSeeder extends Seeder
{
    public function run(): void
    {
        $lokasi = LokasiKlinik::firstOrCreate(['nama_lokasi' => 'Gudang Pusat']);

        $barang = BarangMedis::updateOrCreate(
            ['kode_obat' => 'OBT-DEMO-0001'],
            [
                'nama_obat' => 'Paracetamol 500mg',
                'tipe' => 'OBAT',
                'satuan_dasar' => 'tablet',
                'stok' => 0,
                'created_by' => null,
            ]
        );

        $barang->kemasanBarang()->delete();

        $kemasan = [
            [
                'nama_kemasan' => 'Box',
                'isi_per_kemasan' => 200,
                'is_default' => false,
            ],
            [
                'nama_kemasan' => 'Strip',
                'isi_per_kemasan' => 10,
                'is_default' => true,
            ],
        ];

        $barang->kemasanBarang()->createMany($kemasan);

        $stokLokasi = StokBarang::firstOrCreate(
            [
                'id_barang' => $barang->id_obat,
                'id_lokasi' => $lokasi->id,
            ],
            [
                'jumlah' => 0,
            ]
        );

        $barang->stokLokasi()
            ->where('id_lokasi', '!=', $lokasi->id)
            ->update(['jumlah' => 0]);

        $barang->update(['stok' => 0]);

        $jumlahBox = 2;
        $isiBox = 200;
        $totalUnit = $jumlahBox * $isiBox;

        $stokSebelum = (int) $stokLokasi->jumlah;
        $stokLokasi->jumlah = $stokSebelum + $totalUnit;
        $stokLokasi->save();

        $barang->update([
            'stok' => StokBarang::where('id_barang', $barang->id_obat)->sum('jumlah'),
        ]);

        $boxKemasan = $barang->kemasanBarang()->where('nama_kemasan', 'Box')->first();

        StokHistory::create([
            'id_barang' => $barang->id_obat,
            'id_lokasi' => $lokasi->id,
            'perubahan' => $totalUnit,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokLokasi->jumlah,
            'keterangan' => 'Seeder barang masuk contoh',
            'tanggal_transaksi' => now()->toDateString(),
            'jumlah_kemasan' => $jumlahBox,
            'isi_per_kemasan' => $isiBox,
            'satuan_kemasan' => 'Box',
            'kemasan_id' => $boxKemasan instanceof BarangKemasan ? $boxKemasan->id : null,
            'base_unit' => $barang->satuan_dasar,
        ]);
    }
}
