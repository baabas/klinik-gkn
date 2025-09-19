<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Models\BarangMedis;
use App\Models\StokBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenerimaanBarangController extends Controller
{
    public function create()
    {
        $barangList = BarangMedis::orderBy('nama_obat')->get();
        return view('penerimaan-barang.create', compact('barangList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_penerimaan' => 'required|date|before_or_equal:today',
            'nomor_faktur' => 'required|string|max:50',
            'supplier' => 'required|string|max:255',
            'id_lokasi' => 'required|exists:lokasi_klinik,id',
            'keterangan' => 'nullable|string',
            
            // Validasi detail items dalam bentuk array
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang_medis,id_obat',
            'items.*.batch' => 'required|array|min:1',
            'items.*.batch.*.nomor' => 'required|string|max:50',
            'items.*.batch.*.exp_date' => 'required|date|after:today',
            'items.*.batch.*.jumlah_kemasan' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            // Buat header penerimaan
            $penerimaan = PenerimaanBarang::create([
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'nomor_faktur' => $validated['nomor_faktur'],
                'supplier' => $validated['supplier'],
                'id_lokasi' => $validated['id_lokasi'],
                'keterangan' => $validated['keterangan'],
                'created_by' => Auth::id(),
            ]);

            // Proses setiap item
            foreach ($validated['items'] as $item) {
                $barang = BarangMedis::find($item['id_barang']);

                // Proses setiap batch dalam item
                foreach ($item['batch'] as $batchData) {
                    // Hitung total unit berdasarkan konversi
                    $totalUnit = $barang->hitungTotalUnit($batchData['jumlah_kemasan']);

                    // Buat record di penerimaan_barang_detail
                    $penerimaan->detailPenerimaan()->create([
                        'id_barang' => $item['id_barang'],
                        'nomor_batch' => $batchData['nomor'],
                        'tanggal_kadaluarsa' => $batchData['exp_date'],
                        'jumlah_kemasan' => $batchData['jumlah_kemasan'],
                        'jumlah_unit' => $totalUnit
                    ]);

                    // Buat atau update stok batch
                    StokBatch::create([
                        'id_barang' => $item['id_barang'],
                        'id_lokasi' => $validated['id_lokasi'],
                        'nomor_batch' => $batchData['nomor'],
                        'tanggal_kadaluarsa' => $batchData['exp_date'],
                        'jumlah_unit' => $totalUnit,
                        'created_by' => Auth::id(),
                        'supplier' => $validated['supplier'],
                        'nomor_faktur' => $validated['nomor_faktur'],
                        'tanggal_penerimaan' => $validated['tanggal_penerimaan']
                    ]);

                    // Update total stok
                    DB::table('stok_barang')
                        ->where('id_barang', $item['id_barang'])
                        ->where('id_lokasi', $validated['id_lokasi'])
                        ->increment('jumlah', $totalUnit);
                }
            }
        });

        return redirect()->route('penerimaan-barang.index')
                        ->with('success', 'Penerimaan barang berhasil disimpan');
    }
}