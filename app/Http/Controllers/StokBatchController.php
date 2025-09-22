<?php

namespace App\Http\Controllers;

use App\Models\StokBatch;
use App\Models\BarangMedis;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokBatchController extends Controller
{
    public function index()
    {
        $batches = StokBatch::with(['barang', 'lokasi'])
            ->orderBy('tanggal_kadaluarsa')
            ->get();

        return view('stok-batch.index', compact('batches'));
    }

    public function create()
    {
        $barangList = BarangMedis::orderBy('nama_obat')->get();
        return view('stok-batch.create', compact('barangList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => 'required|exists:barang_medis,id_obat',
            'id_lokasi' => 'required|exists:lokasi_klinik,id',
            'nomor_batch' => 'required|string|max:50',
            'tanggal_kadaluarsa' => 'required|date|after:today',
            'jumlah_unit' => 'required|integer|min:1',
            'supplier' => 'required|string|max:255',
            'nomor_faktur' => 'required|string|max:50',
            'tanggal_penerimaan' => 'required|date|before_or_equal:today'
        ]);

        DB::transaction(function () use ($validated) {
            // Buat record batch baru
            StokBatch::create([
                ...$validated,
                'created_by' => Auth::id()
            ]);

            // Update total stok
            $stok = StokBarang::firstOrCreate(
                [
                    'id_barang' => $validated['id_barang'],
                    'id_lokasi' => $validated['id_lokasi']
                ],
                ['jumlah' => 0]
            );

            $stok->increment('jumlah', $validated['jumlah_unit']);

            // Update status exp
            $this->updateStokStatus($validated['id_barang'], $validated['id_lokasi']);
        });

        return redirect()->route('stok-batch.index')
                        ->with('success', 'Batch stok berhasil ditambahkan');
    }

    private function updateStokStatus($idBarang, $idLokasi)
    {
        $stok = StokBarang::where('id_barang', $idBarang)
                         ->where('id_lokasi', $idLokasi)
                         ->first();

        if (!$stok) return;

        // Cek batch dengan tanggal kadaluarsa terdekat yang masih memiliki stok
        $nearestBatch = StokBatch::where('id_barang', $idBarang)
                                ->where('id_lokasi', $idLokasi)
                                ->where('jumlah_unit', '>', 0)
                                ->orderBy('tanggal_kadaluarsa')
                                ->first();

        if ($nearestBatch) {
            $stok->status_exp = $nearestBatch->status_exp;
            $stok->save();
        }
    }

    public function monitor()
    {
        $expiringSoon = StokBatch::with(['barang', 'lokasi'])
            ->whereDate('tanggal_kadaluarsa', '<=', now()->addMonths(3))
            ->whereDate('tanggal_kadaluarsa', '>=', now())
            ->where('jumlah_unit', '>', 0)
            ->orderBy('tanggal_kadaluarsa')
            ->get();

        $expired = StokBatch::with(['barang', 'lokasi'])
            ->whereDate('tanggal_kadaluarsa', '<', now())
            ->where('jumlah_unit', '>', 0)
            ->orderBy('tanggal_kadaluarsa')
            ->get();

        return view('stok-batch.monitor', compact('expiringSoon', 'expired'));
    }
}