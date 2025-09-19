<?php

namespace App\Http\Controllers;

use App\Models\PermintaanBarangBaru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermintaanBarangBaruController extends Controller
{
    public function create()
    {
        return view('permintaan-barang.create-new');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'tipe' => 'required|in:OBAT,ALKES',
            'satuan' => 'required|string|max:50',
            'kemasan' => 'required|string|max:50',
            'jumlah_satuan_perkemasan' => 'required|integer|min:1',
            'jumlah_unit_persatuan' => 'required|integer|min:1',
            'satuan_terkecil' => 'required|string|max:50',
            'spesifikasi' => 'required|string',
            'jumlah_permintaan' => 'required|integer|min:1',
            'alasan_permintaan' => 'required|string'
        ]);

        $permintaan = PermintaanBarangBaru::create([
            'id_pemohon' => Auth::id(),
            'id_lokasi' => Auth::user()->id_lokasi,
            'status' => 'PENDING',
            'data_barang' => json_encode([
                'nama_barang' => $validated['nama_barang'],
                'tipe' => $validated['tipe'],
                'satuan' => $validated['satuan'],
                'kemasan' => $validated['kemasan'],
                'jumlah_satuan_perkemasan' => $validated['jumlah_satuan_perkemasan'],
                'jumlah_unit_persatuan' => $validated['jumlah_unit_persatuan'],
                'satuan_terkecil' => $validated['satuan_terkecil'],
                'spesifikasi' => $validated['spesifikasi']
            ]),
            'jumlah_permintaan' => $validated['jumlah_permintaan'],
            'alasan_permintaan' => $validated['alasan_permintaan']
        ]);

        return redirect()->route('permintaan-barang.index')
                        ->with('success', 'Permintaan barang baru berhasil diajukan.');
    }
}