<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Pastikan Auth di-import

class BarangMedisController extends Controller
{
    /**
     * Menampilkan daftar semua barang medis beserta total stok dan fitur pencarian.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $barang = BarangMedis::withSum('stok', 'jumlah')
            ->when($search, function ($query, $search) {
                return $query->where('nama_obat', 'like', "%{$search}%")
                             ->orWhere('kode_obat', 'like', "%{$search}%");
            })
            ->orderBy('nama_obat', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('barang-medis.index', compact('barang'));
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     */
    public function create()
    {
        // Hanya Pengadaan yang boleh mengakses halaman ini
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        return view('barang-medis.create');
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $validated = $request->validate([
            'kode_obat' => 'required|string|max:50|unique:barang_medis,kode_obat',
            'nama_obat' => 'required|string|max:255',
            'tipe' => ['required', Rule::in(['OBAT', 'ALKES'])],
            'satuan' => 'required|string|max:100',
            'kemasan' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $barangBaru = BarangMedis::create($validated);

            // Inisialisasi stok di semua lokasi dengan jumlah 0
            $lokasi = LokasiKlinik::all();
            foreach ($lokasi as $loc) {
                $barangBaru->stok()->create([
                    'id_lokasi' => $loc->id,
                    'jumlah' => 0
                ]);
            }

            DB::commit();
            return redirect()->route('barang-medis.index')->with('success', 'Barang baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan barang baru: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail satu barang.
     */
    public function show(BarangMedis $barangMedi)
    {
        $barangMedi->load('stok.lokasi');
        // return view('barang-medis.show', compact('barangMedi'));
        return "Halaman detail untuk: " . $barangMedi->nama_obat . ". (View belum dibuat)";
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(BarangMedis $barangMedi)
    {
        // return view('barang-medis.edit', compact('barangMedi'));
        return "Halaman edit untuk: " . $barangMedi->nama_obat . ". (View belum dibuat)";
    }

    /**
     * Mengupdate data barang di database.
     */
    public function update(Request $request, BarangMedis $barangMedi)
    {
        $validated = $request->validate([
            'kode_obat' => ['required', 'string', 'max:50', Rule::unique('barang_medis')->ignore($barangMedi->id_obat, 'id_obat')],
            'nama_obat' => 'required|string|max:255',
            'tipe' => ['required', Rule::in(['OBAT', 'ALKES'])],
            'satuan' => 'required|string|max:100',
            'kemasan' => 'nullable|string|max:100',
        ]);

        $barangMedi->update($validated);
        return redirect()->route('barang-medis.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Menghapus barang dari database.
     */
    public function destroy(BarangMedis $barangMedi)
    {
        try {
            $barangMedi->delete();
            return redirect()->route('barang-medis.index')->with('success', 'Barang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('barang-medis.index')->with('error', 'Gagal menghapus barang karena masih digunakan di data lain.');
        }
    }
}
