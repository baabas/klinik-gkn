<?php

namespace App\Http\Controllers;

use App\Models\MasterIsiKemasan;
use Illuminate\Http\Request;

class MasterIsiKemasanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = MasterIsiKemasan::orderBy('nama_isi_kemasan')->paginate(15);
        return view('master-isi-kemasan.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-isi-kemasan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_isi_kemasan' => 'required|string|max:100|unique:master_isi_kemasan,nama_isi_kemasan',
            'singkatan' => 'nullable|string|max:20',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        MasterIsiKemasan::create($validated);

        return redirect()->route('master-isi-kemasan.index')
            ->with('success', 'Isi kemasan berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = MasterIsiKemasan::findOrFail($id);
        return view('master-isi-kemasan.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = MasterIsiKemasan::findOrFail($id);

        $validated = $request->validate([
            'nama_isi_kemasan' => 'required|string|max:100|unique:master_isi_kemasan,nama_isi_kemasan,' . $id . ',id_isi_kemasan',
            'singkatan' => 'nullable|string|max:20',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $item->update($validated);

        return redirect()->route('master-isi-kemasan.index')
            ->with('success', 'Isi kemasan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = MasterIsiKemasan::findOrFail($id);
        $item->delete();

        return redirect()->route('master-isi-kemasan.index')
            ->with('success', 'Isi kemasan berhasil dihapus!');
    }
}
