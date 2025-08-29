<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObatController extends Controller
{
    /**
     * Menampilkan daftar semua obat.
     */
    public function index(): View
    {
        $obat = Obat::orderBy('nama_obat', 'asc')->paginate(10);
        return view('obat.index', compact('obat'));
    }

    /**
     * Menampilkan form untuk menambah obat baru.
     */
    public function create(): View
    {
        return view('obat.create');
    }

    /**
     * Menyimpan obat baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:255|unique:obat',
            'kode_obat' => 'nullable|string|max:50|unique:obat',
            'satuan' => 'nullable|string|max:50',
            'kemasan' => 'nullable|string|max:50',
            'stok_saat_ini' => 'required|integer|min:0',
        ]);

        Obat::create($request->all());

        return redirect()->route('obat.index')->with('success', 'Obat baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data obat.
     */
    public function edit(Obat $obat): View
    {
        return view('obat.edit', compact('obat'));
    }

    /**
     * Mengupdate data obat di database.
     */
    public function update(Request $request, Obat $obat)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:255|unique:obat,nama_obat,' . $obat->id_obat . ',id_obat',
            'kode_obat' => 'nullable|string|max:50|unique:obat,kode_obat,' . $obat->id_obat . ',id_obat',
            'satuan' => 'nullable|string|max:50',
            'kemasan' => 'nullable|string|max:50',
            'stok_saat_ini' => 'required|integer|min:0',
        ]);

        $obat->update($request->all());

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui.');
    }

    /**
     * Menghapus data obat dari database.
     */
    public function destroy(Obat $obat)
    {
        $obat->delete();
        return redirect()->route('obat.index')->with('success', 'Obat berhasil dihapus.');
    }
}
