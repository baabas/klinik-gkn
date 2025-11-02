<?php

namespace App\Http\Controllers;

use App\Models\MasterKantor;
use Illuminate\Http\Request;

class MasterKantorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kantors = MasterKantor::orderBy('nama_kantor')->paginate(15);
        return view('master-kantor.index', compact('kantors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-kantor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kantor' => 'required|string|max:200|unique:master_kantor,nama_kantor',
            'kode_kantor' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        MasterKantor::create($validated);

        return redirect()->route('master-kantor.index')
            ->with('success', 'Kantor berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $kantor = MasterKantor::findOrFail($id);
        return view('master-kantor.edit', compact('kantor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kantor = MasterKantor::findOrFail($id);

        $validated = $request->validate([
            'nama_kantor' => 'required|string|max:200|unique:master_kantor,nama_kantor,' . $id . ',id_kantor',
            'kode_kantor' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $kantor->update($validated);

        return redirect()->route('master-kantor.index')
            ->with('success', 'Kantor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kantor = MasterKantor::findOrFail($id);
        $kantor->delete();

        return redirect()->route('master-kantor.index')
            ->with('success', 'Kantor berhasil dihapus.');
    }
}
