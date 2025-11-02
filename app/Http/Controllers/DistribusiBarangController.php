<?php

namespace App\Http\Controllers;

use App\Models\DistribusiBarang;
use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistribusiBarangController extends Controller
{
    /**
     * Menampilkan log distribusi barang (PENGADAAN Only - Read Only Audit Trail)
     * Semua distribusi otomatis approved, ini hanya untuk tracking/audit
     */
    public function index(Request $request)
    {
        // Hanya PENGADAAN yang bisa akses (sudah di-handle di route middleware)
        // Query semua distribusi tanpa filter role
        $query = DistribusiBarang::with([
            'barang',
            'lokasiAsal',
            'lokasiTujuan',
            'user'
        ]);

        // Filter berdasarkan request
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('lokasi_asal')) {
            $query->where('id_lokasi_asal', $request->lokasi_asal);
        }

        if ($request->filled('lokasi_tujuan')) {
            $query->where('id_lokasi_tujuan', $request->lokasi_tujuan);
        }

        // Ambil data dengan pagination
        $distribusi = $query->orderBy('created_at', 'desc')->paginate(20);

        // Data untuk filter
        $lokasi = LokasiKlinik::orderBy('nama_lokasi')->get();

        return view('distribusi-barang.index', compact('distribusi', 'lokasi'));
    }

    /**
     * Menampilkan detail distribusi (PENGADAAN Only - Read Only)
```
    */
    public function show($id)
    {
        $distribusi = DistribusiBarang::with([
            'barang',
            'lokasiAsal',
            'lokasiTujuan',
            'user'
        ])->findOrFail($id);

        // Hanya PENGADAAN yang bisa akses (sudah di-handle di route middleware)
        return view('distribusi-barang.show', compact('distribusi'));
    }
}
