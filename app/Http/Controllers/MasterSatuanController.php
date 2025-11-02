<?php

namespace App\Http\Controllers;

use App\Models\MasterSatuan;
use Illuminate\Http\Request;

class MasterSatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = MasterSatuan::orderBy('nama_satuan')->paginate(15);
        return view('master-satuan.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-satuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:100|unique:master_satuan,nama_satuan',
            'singkatan' => 'nullable|string|max:20',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        MasterSatuan::create($validated);

        return redirect()->route('master-satuan.index')
            ->with('success', 'Satuan berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = MasterSatuan::findOrFail($id);
        return view('master-satuan.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = MasterSatuan::findOrFail($id);

        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:100|unique:master_satuan,nama_satuan,' . $id . ',id_satuan',
            'singkatan' => 'nullable|string|max:20',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $item->update($validated);

        return redirect()->route('master-satuan.index')
            ->with('success', 'Satuan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = MasterSatuan::findOrFail($id);
        $item->delete();

        return redirect()->route('master-satuan.index')
            ->with('success', 'Satuan berhasil dihapus!');
    }
}
