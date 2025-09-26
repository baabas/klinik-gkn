<?php

namespace App\Http\Controllers;

use App\Models\DaftarPenyakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaftarPenyakitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DaftarPenyakit::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ICD10', 'LIKE', '%' . $search . '%')
                  ->orWhere('nama_penyakit', 'LIKE', '%' . $search . '%');
            });
        }

        $penyakits = $query->orderBy('ICD10')->paginate(20);

        // If this is an AJAX request, return the full page content for parsing
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('daftar-penyakit.index', compact('penyakits'));
        }

        return view('daftar-penyakit.index', compact('penyakits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daftar-penyakit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ICD10' => 'required|string|max:20|unique:daftar_penyakit,ICD10',
            'nama_penyakit' => 'required|string|max:255'
        ], [
            'ICD10.required' => 'Kode ICD10 harus diisi',
            'ICD10.unique' => 'Kode ICD10 sudah ada dalam database',
            'ICD10.max' => 'Kode ICD10 maksimal 20 karakter',
            'nama_penyakit.required' => 'Nama penyakit harus diisi',
            'nama_penyakit.max' => 'Nama penyakit maksimal 255 karakter'
        ]);

        try {
            DaftarPenyakit::create([
                'ICD10' => strtoupper($request->ICD10),
                'nama_penyakit' => $request->nama_penyakit
            ]);

            return redirect()
                ->route('daftar-penyakit.index')
                ->with('success', 'Data penyakit berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data penyakit: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penyakit = DaftarPenyakit::with('detailDiagnosa.rekamMedis.karyawan')
            ->findOrFail($id);

        return view('daftar-penyakit.show', compact('penyakit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penyakit = DaftarPenyakit::findOrFail($id);
        return view('daftar-penyakit.edit', compact('penyakit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $penyakit = DaftarPenyakit::findOrFail($id);

        $request->validate([
            'ICD10' => 'required|string|max:20|unique:daftar_penyakit,ICD10,' . $id . ',ICD10',
            'nama_penyakit' => 'required|string|max:255'
        ], [
            'ICD10.required' => 'Kode ICD10 harus diisi',
            'ICD10.unique' => 'Kode ICD10 sudah ada dalam database',
            'ICD10.max' => 'Kode ICD10 maksimal 20 karakter',
            'nama_penyakit.required' => 'Nama penyakit harus diisi',
            'nama_penyakit.max' => 'Nama penyakit maksimal 255 karakter'
        ]);

        try {
            $penyakit->update([
                'ICD10' => strtoupper($request->ICD10),
                'nama_penyakit' => $request->nama_penyakit
            ]);

            return redirect()
                ->route('daftar-penyakit.index')
                ->with('success', 'Data penyakit berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data penyakit: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $penyakit = DaftarPenyakit::findOrFail($id);
            
            // Check if penyakit is being used in diagnosa
            $detailDiagnosaCount = $penyakit->detailDiagnosa()->count();
            
            if ($detailDiagnosaCount > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak dapat menghapus penyakit ini karena sudah digunakan dalam ' . $detailDiagnosaCount . ' diagnosa');
            }

            $penyakit->delete();

            return redirect()
                ->route('daftar-penyakit.index')
                ->with('success', 'Data penyakit berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data penyakit: ' . $e->getMessage());
        }
    }

    /**
     * Search penyakit for autocomplete
     */
    public function search(Request $request)
    {
        $term = $request->get('term');
        
        $penyakits = DaftarPenyakit::where('ICD10', 'LIKE', '%' . $term . '%')
            ->orWhere('nama_penyakit', 'LIKE', '%' . $term . '%')
            ->orderBy('ICD10')
            ->limit(10)
            ->get();

        $results = $penyakits->map(function ($penyakit) {
            return [
                'id' => $penyakit->ICD10,
                'value' => $penyakit->ICD10 . ' - ' . $penyakit->nama_penyakit,
                'label' => $penyakit->ICD10 . ' - ' . $penyakit->nama_penyakit,
                'ICD10' => $penyakit->ICD10,
                'nama_penyakit' => $penyakit->nama_penyakit
            ];
        });

        return response()->json($results);
    }
}