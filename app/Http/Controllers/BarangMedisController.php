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
            ->withSum('stokMasuk as total_kemasan_masuk', 'jumlah_kemasan')
            ->withSum('stokMasuk as total_unit_masuk', 'perubahan')
            ->withMax('stokMasuk as tanggal_masuk_terakhir', 'tanggal_transaksi')
            ->withMin('stokMasuk as expired_terdekat', 'expired_at')
            ->with(['stokMasukTerakhir'])
            ->with(['stokMasukBulanIni' => function ($query) {
                $query->whereYear('tanggal_transaksi', now()->year)
                      ->whereMonth('tanggal_transaksi', now()->month)
                      ->where('perubahan', '>', 0)
                      ->orderBy('tanggal_transaksi', 'asc');
            }])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_obat', 'like', "%{$search}%")
                      ->orWhere('kode_obat', 'like', "%{$search}%");
                });
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
            'kategori_barang' => 'required|string|in:Obat,BMHP,Alkes,APD',
            'nama_obat' => 'required|string|max:255',
            'kemasan' => 'nullable|string|max:100',
            'isi_kemasan_jumlah' => 'required|integer|min:1',
            'isi_kemasan_satuan' => 'required|string|in:strip,kotak,botol,vial,tube',
            'isi_per_satuan' => 'required|integer|min:1',
            'satuan_terkecil' => 'required|string|in:Tablet,Botol,Pcs,Vial,Tube,Troches,Kapsul,Sirup',
        ]);

        // Generate kode otomatis berdasarkan kategori
        $kodeObat = $this->generateKodeBarang($validated['kategori_barang']);
        $validated['kode_obat'] = $kodeObat;
        
        // Set kemasan ke "Box" secara otomatis
        $validated['kemasan'] = 'Box';
        
        // Set satuan sama dengan satuan_terkecil
        $validated['satuan'] = $validated['satuan_terkecil'];

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
            return redirect()->route('barang-medis.index')->with('success', 'Barang baru berhasil ditambahkan dengan kode: ' . $kodeObat);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan barang baru: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Generate kode barang otomatis berdasarkan kategori
     */
    private function generateKodeBarang($kategori)
    {
        // Tentukan prefix berdasarkan kategori
        $prefix = '';
        switch ($kategori) {
            case 'Obat':
                $prefix = 'OBT';
                break;
            case 'BMHP':
                $prefix = 'BMHP';
                break;
            case 'Alkes':
                $prefix = 'ALK';
                break;
            case 'APD':
                $prefix = 'APD';
                break;
            default:
                $prefix = 'GEN'; // Generic untuk yang tidak dikenal
        }

        // Ambil nomor terakhir untuk kategori ini
        $lastBarang = BarangMedis::where('kode_obat', 'LIKE', $prefix . '-%')
            ->orderBy('kode_obat', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastBarang) {
            // Extract nomor dari kode terakhir (contoh: OBT-0001 -> 0001)
            $lastNumber = (int) substr($lastBarang->kode_obat, strlen($prefix) + 1);
            $nextNumber = $lastNumber + 1;
        }

        // Format nomor dengan leading zero (4 digit)
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $formattedNumber;
    }

    /**
     * Menampilkan detail satu barang.
     */
    public function show(BarangMedis $barangMedi)
    {
        // Load relasi yang diperlukan
        $barangMedi->load('stok.lokasi');
        
        // Ambil riwayat transaksi dari stok_history dengan paginasi dan relasi user
        $riwayatTransaksi = StokHistory::where('id_barang', $barangMedi->id_obat)
            ->with(['lokasi', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('barang-medis.show', compact('barangMedi', 'riwayatTransaksi'));
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(BarangMedis $barangMedi)
    {
        // Load relasi stok dengan lokasi
        $barangMedi->load(['stok.lokasi']);
        
        return view('barang-medis.edit', compact('barangMedi'));
    }

    /**
     * Mengupdate data barang di database.
     */
    public function update(Request $request, BarangMedis $barangMedi)
    {
        $validated = $request->validate([
            'kategori_barang' => 'required|string|in:Obat,BMHP,Alkes,APD',
            'nama_obat' => 'required|string|max:255',
            'koreksi' => 'nullable|array',
            'koreksi.*.kemasan' => 'nullable|integer|min:0',
            'koreksi.*.type' => 'nullable|string|in:tambah,kurang',
            'koreksi.*.expired_at' => 'nullable|date|after_or_equal:today',
            'koreksi.*.keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Update data barang
            $barangMedi->update([
                'kategori_barang' => $validated['kategori_barang'],
                'nama_obat' => $validated['nama_obat'],
            ]);

            // Proses koreksi stok jika ada
            if (isset($validated['koreksi'])) {
                foreach ($validated['koreksi'] as $idLokasi => $koreksi) {
                    if (!empty($koreksi['kemasan']) && !empty($koreksi['type'])) {
                        $this->prosesKoreksiStok($barangMedi, $idLokasi, $koreksi);
                    }
                }
            }

            DB::commit();
            return redirect()->route('barang-medis.index')->with('success', 'Data barang berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Proses koreksi stok (tambah/kurang)
     */
    private function prosesKoreksiStok($barangMedi, $idLokasi, $koreksi)
    {
        $stokBarang = StokBarang::where('id_barang', $barangMedi->id_obat)
                                ->where('id_lokasi', $idLokasi)
                                ->first();

        if (!$stokBarang) {
            throw new \Exception("Stok untuk lokasi tidak ditemukan");
        }

        $jumlahKemasan = (int) $koreksi['kemasan'];
        // Hitung total satuan: jumlah kemasan × isi kemasan × isi per satuan
        // Contoh: 1 Box × 10 strip × 10 kapsul = 100 kapsul
        $totalSatuan = $jumlahKemasan * $barangMedi->isi_kemasan_jumlah * $barangMedi->isi_per_satuan;
        
        // Tentukan perubahan berdasarkan type
        $perubahan = $koreksi['type'] === 'tambah' ? $totalSatuan : -$totalSatuan;
        
        // Cek jika pengurangan tidak melebihi stok yang ada
        if ($koreksi['type'] === 'kurang' && ($stokBarang->jumlah + $perubahan) < 0) {
            throw new \Exception("Pengurangan stok melebihi jumlah yang tersedia");
        }

        // Update stok
        $stokLama = $stokBarang->jumlah;
        $stokBarang->jumlah += $perubahan;
        $stokBarang->save();

        // Buat keterangan
        $keterangan = $koreksi['keterangan'] ?? '';
        if ($koreksi['type'] === 'tambah') {
            $keterangan = "Koreksi: Tambah " . $jumlahKemasan . " " . ($barangMedi->kemasan ?? 'Box') . 
                         ($keterangan ? " - " . $keterangan : "");
        } else {
            $keterangan = "Koreksi: Kurang " . $jumlahKemasan . " " . ($barangMedi->kemasan ?? 'Box') . 
                         ($keterangan ? " - " . $keterangan : "");
        }

        // Simpan ke history
        StokHistory::create([
            'id_barang' => $barangMedi->id_obat,
            'id_lokasi' => $idLokasi,
            'user_id' => Auth::id(),
            'jumlah_kemasan' => $jumlahKemasan,
            'isi_per_kemasan' => $barangMedi->isi_kemasan_jumlah * $barangMedi->isi_per_satuan, // Total satuan per kemasan
            'perubahan' => $perubahan,
            'stok_sebelum' => $stokLama,
            'stok_sesudah' => $stokBarang->jumlah,
            'expired_at' => $koreksi['expired_at'] ?? null,
            'keterangan' => $keterangan,
            'tanggal_transaksi' => now(),
        ]);
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
        $histories = $barangMedi->stokHistories()
            ->with('lokasi', 'user')
            ->orderByDesc('tanggal_transaksi')
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
                    'tanggal_transaksi' => now()->toDateString(),
                    'user_id' => auth::id(),
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
                    'tanggal_transaksi' => now()->toDateString(),
                    'user_id' => auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('barang-medis.index')->with('success', 'Distribusi stok berhasil dilakukan.');
    }
}
