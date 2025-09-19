<?php

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use App\Models\PermintaanBarangBaru;
use App\Models\BarangMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengadaanController extends Controller
{
    public function index()
    {
        $permintaanRegular = PermintaanBarang::with(['pemohon', 'detailPermintaan.barang'])
            ->where('status', 'PENDING')
            ->latest()
            ->get();

        $permintaanBaru = PermintaanBarangBaru::with('pemohon')
            ->where('status', 'PENDING')
            ->latest()
            ->get();

        return view('pengadaan.index', compact('permintaanRegular', 'permintaanBaru'));
    }

    public function prosesPermintaanRegular(Request $request, PermintaanBarang $permintaan)
    {
        $validated = $request->validate([
            'status' => 'required|in:DISETUJUI,DITOLAK',
            'items' => 'required_if:status,DISETUJUI|array',
            'items.*.id' => 'required_if:status,DISETUJUI|exists:detail_permintaan_barang,id',
            'items.*.jumlah_disetujui' => 'required_if:status,DISETUJUI|integer|min:0',
            'catatan' => 'required_if:status,DITOLAK|nullable|string'
        ]);

        if ($validated['status'] === 'DISETUJUI') {
            foreach ($validated['items'] as $item) {
                $detail = $permintaan->detailPermintaan()->find($item['id']);
                if ($detail) {
                    $detail->update([
                        'jumlah_disetujui' => $item['jumlah_disetujui'],
                        'status' => 'DISETUJUI'
                    ]);

                    // Update stok
                    if ($item['jumlah_disetujui'] > 0) {
                        $detail->barang->stok()
                            ->where('id_lokasi', $permintaan->id_lokasi)
                            ->increment('jumlah', $item['jumlah_disetujui']);
                    }
                }
            }
        }

        $permintaan->update([
            'status' => $validated['status'],
            'catatan_pengadaan' => $validated['catatan'] ?? null,
            'id_pengadaan' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Permintaan berhasil diproses');
    }

    public function prosesPermintaanBaru(Request $request, PermintaanBarangBaru $permintaan)
    {
        $validated = $request->validate([
            'status' => 'required|in:DISETUJUI,DITOLAK',
            'jumlah_disetujui' => 'required_if:status,DISETUJUI|integer|min:0',
            'catatan' => 'required_if:status,DITOLAK|nullable|string'
        ]);

        if ($validated['status'] === 'DISETUJUI') {
            // Buat barang baru
            $dataBarang = $permintaan->data_barang;
            $barangBaru = BarangMedis::create([
                'kode_obat' => 'NEW-' . time(), // Temporary kode
                'nama_obat' => $dataBarang['nama_barang'],
                'tipe' => $dataBarang['tipe'],
                'satuan' => $dataBarang['satuan'],
                'kemasan' => $dataBarang['kemasan'],
                'jumlah_satuan_perkemasan' => $dataBarang['jumlah_satuan_perkemasan'],
                'jumlah_unit_persatuan' => $dataBarang['jumlah_unit_persatuan'],
                'satuan_terkecil' => $dataBarang['satuan_terkecil']
            ]);

            // Buat stok awal
            $barangBaru->stok()->create([
                'id_lokasi' => $permintaan->id_lokasi,
                'jumlah' => $validated['jumlah_disetujui'] * 
                           $dataBarang['jumlah_satuan_perkemasan'] * 
                           $dataBarang['jumlah_unit_persatuan']
            ]);
        }

        $permintaan->update([
            'status' => $validated['status'],
            'catatan_pengadaan' => $validated['catatan'] ?? null,
            'id_pengadaan' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Permintaan barang baru berhasil diproses');
    }
}