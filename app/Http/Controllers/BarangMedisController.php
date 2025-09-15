<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\StokHistory;
use App\Models\StokBarang;

class BarangMedisController extends Controller
{
    /**
     * Menampilkan daftar semua barang medis beserta total stok dan fitur pencarian.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $gkn1Id = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 1%')->value('id');
        $gkn2Id = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 2%')->value('id');

        $barang = BarangMedis::query()
            ->withSum('stok', 'jumlah')
            ->withSum(['stok as stok_gkn1' => function ($q) use ($gkn1Id) {
                $q->where('id_lokasi', $gkn1Id ?? 0);
            }], 'jumlah')
            ->withSum(['stok as stok_gkn2' => function ($q) use ($gkn2Id) {
                $q->where('id_lokasi', $gkn2Id ?? 0);
            }], 'jumlah')
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
        return "Halaman detail untuk: " . $barangMedi->nama_obat . ". (View belum dibuat)";
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(BarangMedis $barangMedi)
    {
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


    /**
     * Menampilkan riwayat mutasi stok untuk suatu barang.
     */
    public function history(BarangMedis $barangMedi)
    {
        $histories = StokHistory::where('id_barang', $barangMedi->id_obat)
            ->with('lokasi', 'user')
            ->orderByDesc('created_at')
            ->get();

        return view('barang-medis.history', [
            'barangMedi' => $barangMedi,
            'histories' => $histories,
        ]);
    }

    /**
     * Memproses distribusi stok antar lokasi.
     */
    public function distribusi(Request $request, BarangMedis $barang)
    {
        $validated = $request->validate([
            'lokasi_asal' => 'required|exists:lokasi_klinik,id',
            'lokasi_tujuan' => 'required|exists:lokasi_klinik,id|different:lokasi_asal',
            'jumlah' => 'required|integer|min:1',
        ], [
            'lokasi_tujuan.different' => 'Lokasi tujuan tidak boleh sama dengan lokasi asal.'
        ]);

        $jumlahDistribusi = $validated['jumlah'];
        $idLokasiAsal = $validated['lokasi_asal'];
        $idLokasiTujuan = $validated['lokasi_tujuan'];

        try {
            DB::transaction(function () use ($barang, $jumlahDistribusi, $idLokasiAsal, $idLokasiTujuan) {
                // --- PROSES LOKASI ASAL ---
                $stokAsal = StokBarang::where('id_barang', $barang->id_obat)
                    ->where('id_lokasi', $idLokasiAsal)
                    ->lockForUpdate()
                    ->first();

                $stokSebelumAsal = $stokAsal->jumlah ?? 0;

                if ($stokSebelumAsal < $jumlahDistribusi) {
                    throw new \Exception('Stok di lokasi asal tidak mencukupi untuk distribusi.');
                }

                $stokAsal->decrement('jumlah', $jumlahDistribusi);

                StokHistory::create([
                    'id_barang' => $barang->id_obat,
                    'id_lokasi' => $idLokasiAsal,
                    'perubahan' => -$jumlahDistribusi, // [FIX] Ganti nama kolom & beri nilai negatif
                    'stok_sebelum' => $stokSebelumAsal, // [FIX] Tambahkan stok sebelum
                    'stok_sesudah' => $stokAsal->jumlah, // [FIX] Tambahkan stok sesudah
                    'keterangan' => 'Distribusi ke Lokasi ID ' . $idLokasiTujuan,
                    'user_id' => auth()->id(),
                ]);

                // --- PROSES LOKASI TUJUAN ---
                $stokTujuan = StokBarang::firstOrCreate(
                    ['id_barang' => $barang->id_obat, 'id_lokasi' => $idLokasiTujuan],
                    ['jumlah' => 0] // Buat dengan stok 0 jika belum ada
                );

                $stokSebelumTujuan = $stokTujuan->jumlah;
                $stokTujuan->increment('jumlah', $jumlahDistribusi);

                StokHistory::create([
                    'id_barang' => $barang->id_obat,
                    'id_lokasi' => $idLokasiTujuan,
                    'perubahan' => $jumlahDistribusi, // [FIX] Ganti nama kolom
                    'stok_sebelum' => $stokSebelumTujuan, // [FIX] Tambahkan stok sebelum
                    'stok_sesudah' => $stokTujuan->jumlah, // [FIX] Tambahkan stok sesudah
                    'keterangan' => 'Distribusi dari Lokasi ID ' . $idLokasiAsal,
                    'user_id' => auth()->id(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('barang-medis.index')->with('success', 'Distribusi stok berhasil dilakukan.');
    }
}