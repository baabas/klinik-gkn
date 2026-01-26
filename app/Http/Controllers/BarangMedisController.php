<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\DistribusiBarang;
use App\Models\SuratDistribusi;
use App\Models\DetailSuratDistribusi;
use App\Models\MasterWhatsappValidator;
use App\Models\MasterSatuan;
use App\Models\MasterIsiKemasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\StokHistory;
use App\Models\StokBarang;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangMedisController extends Controller
{
    /**
     * Menampilkan daftar semua barang medis beserta total stok dan fitur pencarian.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $idLokasi = $user->id_lokasi; // Filter berdasarkan lokasi user

        $gkn1Id = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 1%')->value('id');
        $gkn2Id = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 2%')->value('id');

        $barang = BarangMedis::query()
            // Filter hanya barang yang memiliki stok di lokasi user
            ->whereHas('stok', function ($query) use ($idLokasi) {
                if ($idLokasi) {
                    $query->where('id_lokasi', $idLokasi);
                }
            })
            // Total stok dari semua lokasi (untuk kolom Total Stok)
            ->withSum('stok', 'jumlah')
            // Stok per lokasi spesifik
            ->withSum(['stok as stok_gkn1' => function ($q) use ($gkn1Id) {
                $q->where('id_lokasi', $gkn1Id ?? 0);
            }], 'jumlah')
            ->withSum(['stok as stok_gkn2' => function ($q) use ($gkn2Id) {
                $q->where('id_lokasi', $gkn2Id ?? 0);
            }], 'jumlah')
            // Total kemasan masuk (dari semua lokasi untuk info global)
            ->withSum('stokMasuk', 'jumlah_kemasan')
            // Total unit masuk (dari semua lokasi untuk info global)
            ->withSum(['stokMasuk as total_unit_masuk'], 'perubahan')
            // Tanggal masuk terakhir dari semua lokasi
            ->withMax(['stokMasuk as tanggal_masuk_terakhir'], 'tanggal_transaksi')
            // Expired terdekat dari semua lokasi (yang belum expired)
            ->withMin(['stokMasuk as expired_terdekat' => function ($q) {
                $q->where('expired_at', '>=', now());
            }], 'expired_at')
            ->with(['stokMasukTerakhir' => function ($query) use ($idLokasi) {
                if ($idLokasi) {
                    $query->where('id_lokasi', $idLokasi);
                }
            }])
            ->with(['stokMasukBulanIni' => function ($query) use ($idLokasi) {
                $query->whereYear('tanggal_transaksi', now()->year)
                      ->whereMonth('tanggal_transaksi', now()->month)
                      ->where('perubahan', '>', 0);
                if ($idLokasi) {
                    $query->where('id_lokasi', $idLokasi);
                }
                $query->orderBy('tanggal_transaksi', 'asc');
            }])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_obat', 'like', "%{$search}%")
                      ->orWhere('kode_obat', 'like', "%{$search}%")
                      ->orWhere('kategori_barang', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_obat')
            ->paginate(15)
            ->withQueryString();

        // Ambil data WhatsApp Validator yang aktif
        $validators = MasterWhatsappValidator::active()->orderBy('nama_validator')->get();

        return view('barang-medis.index', compact('barang', 'search', 'validators'));
    }

    /**
     * API endpoint untuk live search barang medis dengan debounce
     */
    public function search(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $user = Auth::user();
            $idLokasi = $user->id_lokasi; // Filter berdasarkan lokasi user

            $gkn1Id = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 1%')->value('id');
            $gkn2Id = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 2%')->value('id');

            $barang = BarangMedis::query()
                // Filter hanya barang yang memiliki stok di lokasi user
                ->whereHas('stok', function ($query) use ($idLokasi) {
                    if ($idLokasi) {
                        $query->where('id_lokasi', $idLokasi);
                    }
                })
                // Total stok dari semua lokasi (untuk kolom Total Stok)
                ->withSum('stok', 'jumlah')
                // Stok per lokasi spesifik
                ->withSum(['stok as stok_gkn1' => function ($q) use ($gkn1Id) {
                    $q->where('id_lokasi', $gkn1Id ?? 0);
                }], 'jumlah')
                ->withSum(['stok as stok_gkn2' => function ($q) use ($gkn2Id) {
                    $q->where('id_lokasi', $gkn2Id ?? 0);
                }], 'jumlah')
                // Total kemasan masuk (dari semua lokasi untuk info global)
                ->withSum('stokMasuk', 'jumlah_kemasan')
                // Total unit masuk (dari semua lokasi untuk info global)
                ->withSum(['stokMasuk as total_unit_masuk'], 'perubahan')
                // Tanggal masuk terakhir dari semua lokasi
                ->withMax(['stokMasuk as tanggal_masuk_terakhir'], 'tanggal_transaksi')
                // Expired terdekat dari semua lokasi (yang belum expired)
                ->withMin(['stokMasuk as expired_terdekat' => function ($q) {
                    $q->where('expired_at', '>=', now());
                }], 'expired_at')
                ->with(['stokMasukTerakhir' => function ($query) use ($idLokasi) {
                    if ($idLokasi) {
                        $query->where('id_lokasi', $idLokasi);
                    }
                }])
                ->with(['stokMasukBulanIni' => function ($query) use ($idLokasi) {
                    $query->whereYear('tanggal_transaksi', now()->year)
                          ->whereMonth('tanggal_transaksi', now()->month)
                          ->where('perubahan', '>', 0);
                    if ($idLokasi) {
                        $query->where('id_lokasi', $idLokasi);
                    }
                    $query->orderBy('tanggal_transaksi', 'asc');
                }])
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('nama_obat', 'like', "%{$search}%")
                          ->orWhere('kode_obat', 'like', "%{$search}%")
                          ->orWhere('kategori_barang', 'like', "%{$search}%");
                    });
                })
                ->orderBy('nama_obat')
                ->paginate(15);

            // Return JSON for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'table_body' => view('barang-medis.partials.table-body', compact('barang'))->render(),
                    'pagination' => (string) $barang->appends($request->query())->links(),
                    'total' => $barang->total(),
                    'current_page' => $barang->currentPage(),
                    'last_page' => $barang->lastPage()
                ]);
            }

            return view('barang-medis.index', compact('barang', 'search'));

        } catch (\Exception $e) {
            Log::error('Error in BarangMedisController@search: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mencari data: ' . $e->getMessage(),
                    'table_body' => '<tr><td colspan="14" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle mb-2" style="font-size: 2rem;"></i><div>Terjadi kesalahan saat mencari data. Silakan refresh halaman.</div></td></tr>',
                    'pagination' => ''
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat mencari data.');
        }
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     */
    public function create()
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        // Ambil daftar barang baru yang sudah disetujui tapi belum ada di master
        $approvedNewItems = DB::table('detail_permintaan_barang as dpb')
            ->join('permintaan_barang as pb', 'dpb.id_permintaan', '=', 'pb.id')
            ->whereNull('dpb.id_barang') // Barang baru (tidak ada di master)
            ->whereNotNull('dpb.nama_barang_baru')
            ->whereNotNull('dpb.jumlah_disetujui')
            ->where('dpb.jumlah_disetujui', '>', 0)
            ->where('pb.status', 'APPROVED')
            ->select(
                'dpb.nama_barang_baru',
                'dpb.kemasan_barang_baru',
                'dpb.jumlah_disetujui',
                'pb.kode_permintaan',
                'pb.tanggal_permintaan'
            )
            ->orderBy('pb.tanggal_permintaan', 'desc')
            ->get()
            ->unique('nama_barang_baru'); // Hindari duplikasi nama barang yang sama

        return view('barang-medis.create', compact('approvedNewItems'));
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        // Get active satuan from MasterSatuan table
        $activeSatuanList = MasterSatuan::where('is_active', true)
            ->pluck('nama_satuan')
            ->toArray();
        $activeSatuanList[] = 'lainnya'; // Add 'lainnya' option
        $satuanValidation = 'required|string|in:' . implode(',', $activeSatuanList);

        // Get active isi kemasan from MasterIsiKemasan table (convert to lowercase for validation)
        $activeIsiKemasanList = MasterIsiKemasan::where('is_active', true)
            ->pluck('nama_isi_kemasan')
            ->map(fn($item) => strtolower($item))
            ->toArray();
        $activeIsiKemasanList[] = 'lainnya'; // Add 'lainnya' option
        $isiKemasanValidation = 'required|string|in:' . implode(',', $activeIsiKemasanList);

        $validated = $request->validate([
            'kategori_barang' => 'required|string|in:Obat,BMHP,Alkes,APD',
            'nama_obat' => 'required|string|max:255',
            'isi_kemasan_jumlah' => 'required|integer|min:1',
            'isi_kemasan_satuan' => $isiKemasanValidation,
            'isi_kemasan_satuan_custom' => 'required_if:isi_kemasan_satuan,lainnya|nullable|string|max:50',
            'isi_per_satuan' => 'required|integer|min:1',
            'satuan_terkecil' => $satuanValidation,
            'satuan_terkecil_custom' => 'required_if:satuan_terkecil,lainnya|nullable|string|max:50',
            'stok_minimal' => 'nullable|integer|min:0',
        ]);

        // Process custom values
        if ($validated['isi_kemasan_satuan'] === 'lainnya') {
            $validated['isi_kemasan_satuan'] = $validated['isi_kemasan_satuan_custom'];
        } else {
            // Capitalize first letter for database storage
            $validated['isi_kemasan_satuan'] = ucfirst($validated['isi_kemasan_satuan']);
        }

        if ($validated['satuan_terkecil'] === 'lainnya') {
            $validated['satuan_terkecil'] = $validated['satuan_terkecil_custom'];
        }

        // Remove custom fields from validated data as they're not needed in database
        unset($validated['isi_kemasan_satuan_custom'], $validated['satuan_terkecil_custom']);

        // Cek duplikasi berdasarkan nama, isi kemasan satuan, dan satuan terkecil
        $existingBarang = BarangMedis::where('nama_obat', $validated['nama_obat'])
            ->where('isi_kemasan_satuan', $validated['isi_kemasan_satuan'])
            ->where('satuan_terkecil', $validated['satuan_terkecil'])
            ->first();

        if ($existingBarang) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Obat/Alat Medis dengan nama "' . $validated['nama_obat'] . '", kemasan "' . $validated['isi_kemasan_satuan'] . '", dan satuan terkecil "' . $validated['satuan_terkecil'] . '" sudah terdaftar dalam sistem. Jika Anda ingin menambahkan varian berbeda, gunakan kemasan atau satuan terkecil yang berbeda.');
        }

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

            // Update detail_permintaan_barang yang memiliki nama_barang_baru yang sama
            // dengan menambahkan id_barang yang baru dibuat
            DB::table('detail_permintaan_barang as dpb')
                ->join('permintaan_barang as pb', 'dpb.id_permintaan', '=', 'pb.id')
                ->whereNull('dpb.id_barang')
                ->where('dpb.nama_barang_baru', $validated['nama_obat'])
                ->where('pb.status', 'APPROVED')
                ->update(['dpb.id_barang' => $barangBaru->id_obat]);

            DB::commit();
            return redirect()->route('barang-medis.create')->with('success', 'Barang baru berhasil ditambahkan dengan kode: ' . $kodeObat . '. Item ini telah dihubungkan dengan permintaan yang relevan.');
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
        $prefixMap = [
            'Obat' => 'OBT',
            'BMHP' => 'BHP',
            'Alkes' => 'ALS',
            'APD' => 'APD'
        ];

        $prefix = $prefixMap[$kategori] ?? 'OBT';

        // Ambil nomor urut terakhir untuk kategori ini
        $lastCode = BarangMedis::where('kode_obat', 'like', $prefix . '%')
                               ->orderBy('kode_obat', 'desc')
                               ->first();

        if ($lastCode) {
            // Extract nomor dari kode terakhir (misal: OBT001 -> 001)
            $lastNumber = (int) substr($lastCode->kode_obat, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Format dengan padding 3 digit
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     */
    public function show(BarangMedis $barangMedi)
    {
        $barangMedi->load(['stok.lokasi', 'stokHistories' => function ($query) {
            $query->orderBy('tanggal_transaksi', 'desc')->limit(10);
        }]);

        $totalStok = $barangMedi->stok->sum('jumlah');
        $riwayatTerakhir = $barangMedi->stokHistories;

        return view('barang-medis.show', compact('barangMedi', 'totalStok', 'riwayatTerakhir'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BarangMedis $barangMedi)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        return view('barang-medis.edit', compact('barangMedi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarangMedis $barangMedi)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        // Get active satuan from MasterSatuan table
        $activeSatuanList = MasterSatuan::where('is_active', true)
            ->pluck('nama_satuan')
            ->toArray();
        $activeSatuanList[] = 'lainnya'; // Add 'lainnya' option
        $satuanValidation = 'required|string|in:' . implode(',', $activeSatuanList);

        // Get active isi kemasan from MasterIsiKemasan table (convert to lowercase for validation)
        $activeIsiKemasanList = MasterIsiKemasan::where('is_active', true)
            ->pluck('nama_isi_kemasan')
            ->map(fn($item) => strtolower($item))
            ->toArray();
        $activeIsiKemasanList[] = 'lainnya'; // Add 'lainnya' option
        $isiKemasanValidation = 'required|string|in:' . implode(',', $activeIsiKemasanList);

        $validated = $request->validate([
            'kategori_barang' => 'required|string|in:Obat,BMHP,Alkes,APD',
            'nama_obat' => 'required|string|max:255',
            'isi_kemasan_jumlah' => 'required|integer|min:1',
            'isi_kemasan_satuan' => $isiKemasanValidation,
            'isi_kemasan_satuan_custom' => 'required_if:isi_kemasan_satuan,lainnya|nullable|string|max:50',
            'isi_per_satuan' => 'required|integer|min:1',
            'satuan_terkecil' => $satuanValidation,
            'satuan_terkecil_custom' => 'required_if:satuan_terkecil,lainnya|nullable|string|max:50',
            'stok_minimal' => 'nullable|integer|min:0',
        ]);

        // Process custom values
        if ($validated['isi_kemasan_satuan'] === 'lainnya') {
            $validated['isi_kemasan_satuan'] = $validated['isi_kemasan_satuan_custom'];
        } else {
            // Capitalize first letter for database storage
            $validated['isi_kemasan_satuan'] = ucfirst($validated['isi_kemasan_satuan']);
        }

        if ($validated['satuan_terkecil'] === 'lainnya') {
            $validated['satuan_terkecil'] = $validated['satuan_terkecil_custom'];
        }

        // Remove custom fields from validated data as they're not needed in database
        unset($validated['isi_kemasan_satuan_custom'], $validated['satuan_terkecil_custom']);

        // Cek duplikasi berdasarkan nama, isi kemasan satuan, dan satuan terkecil (kecuali untuk barang yang sedang diedit)
        $existingBarang = BarangMedis::where('nama_obat', $validated['nama_obat'])
            ->where('isi_kemasan_satuan', $validated['isi_kemasan_satuan'])
            ->where('satuan_terkecil', $validated['satuan_terkecil'])
            ->where('id_obat', '!=', $barangMedi->id_obat)
            ->first();

        if ($existingBarang) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Obat/Alat Medis dengan nama "' . $validated['nama_obat'] . '", kemasan "' . $validated['isi_kemasan_satuan'] . '", dan satuan terkecil "' . $validated['satuan_terkecil'] . '" sudah terdaftar dalam sistem.');
        }

        // Set kemasan ke "Box" secara otomatis
        $validated['kemasan'] = 'Box';

        // Set satuan sama dengan satuan_terkecil
        $validated['satuan'] = $validated['satuan_terkecil'];

        // Proses koreksi stok jika ada
        if ($request->has('koreksi')) {
            DB::beginTransaction();
            try {
                foreach ($request->input('koreksi', []) as $idLokasi => $koreksi) {
                    // Skip jika tidak ada tipe koreksi
                    if (empty($koreksi['type'])) {
                        continue;
                    }

                    $jumlahKemasan = (int) ($koreksi['kemasan'] ?? 0);
                    $jumlahIsiKemasan = (int) ($koreksi['isi_kemasan'] ?? 0);
                    $jumlahSatuan = (int) ($koreksi['satuan'] ?? 0);
                    
                    // Hitung total dalam satuan terkecil
                    // Formula: (kemasan × isi_kemasan_jumlah × isi_per_satuan) + (isi_kemasan × isi_per_satuan) + satuan
                    $totalSatuan = ($jumlahKemasan * $barangMedi->isi_kemasan_jumlah * $barangMedi->isi_per_satuan) 
                                 + ($jumlahIsiKemasan * $barangMedi->isi_per_satuan) 
                                 + $jumlahSatuan;

                    // Skip jika total 0
                    if ($totalSatuan == 0) {
                        continue;
                    }

                    // Cari atau buat stok untuk lokasi ini
                    $stok = StokBarang::firstOrCreate(
                        [
                            'id_barang' => $barangMedi->id_obat,
                            'id_lokasi' => $idLokasi
                        ],
                        ['jumlah' => 0]
                    );

                    // Tentukan perubahan berdasarkan tipe
                    $perubahan = $koreksi['type'] === 'tambah' ? $totalSatuan : -$totalSatuan;

                    // Validasi stok tidak boleh negatif jika mengurangi
                    if ($koreksi['type'] === 'kurang' && $stok->jumlah < $totalSatuan) {
                        DB::rollBack();
                        $lokasiNama = LokasiKlinik::find($idLokasi)->nama_lokasi ?? 'Lokasi';
                        return redirect()->back()
                            ->with('error', "Stok di {$lokasiNama} tidak mencukupi. Stok saat ini: {$stok->jumlah} {$barangMedi->satuan_terkecil}")
                            ->withInput();
                    }

                    // Validasi stok minimal jika mengurangi
                    if ($koreksi['type'] === 'kurang') {
                        $stokSetelahKoreksi = $stok->jumlah - $totalSatuan;
                        $stokMinimal = $barangMedi->stok_minimal ?? 0;
                        
                        if ($stokSetelahKoreksi < $stokMinimal) {
                            DB::rollBack();
                            $lokasiNama = LokasiKlinik::find($idLokasi)->nama_lokasi ?? 'Lokasi';
                            return redirect()->back()
                                ->with('error', "Koreksi tidak dapat dilakukan. Stok di {$lokasiNama} akan berada di bawah batas minimal ({$stokMinimal}). Stok saat ini: {$stok->jumlah}, setelah koreksi: {$stokSetelahKoreksi}")
                                ->withInput();
                        }
                    }

                    // Update stok
                    $stok->jumlah += $perubahan;
                    $stok->save();

                    // Catat di stok history
                    StokHistory::create([
                        'id_barang' => $barangMedi->id_obat,
                        'id_lokasi' => $idLokasi,
                        'id_user' => Auth::id(),
                        'perubahan' => $perubahan,
                        'stok_sebelum' => $stok->jumlah - $perubahan,
                        'stok_sesudah' => $stok->jumlah,
                        'jumlah_kemasan' => $jumlahKemasan,
                        'tanggal_transaksi' => now(),
                        'expired_at' => !empty($koreksi['expired_at']) ? $koreksi['expired_at'] : null,
                        'keterangan' => 'Koreksi Stok: ' . ($koreksi['keterangan'] ?? ($koreksi['type'] === 'tambah' ? 'Penambahan' : 'Pengurangan') . " {$totalSatuan} {$barangMedi->satuan_terkecil}")
                    ]);
                }

                $barangMedi->update($validated);
                DB::commit();

                return redirect()->route('barang-medis.index')->with('success', 'Data barang dan koreksi stok berhasil diperbarui.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating barang with stock correction: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                    ->withInput();
            }
        }

        $barangMedi->update($validated);

        return redirect()->route('barang-medis.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangMedis $barangMedi)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $namaBarang = $barangMedi->nama_obat;

        try {
            DB::transaction(function () use ($barangMedi) {
                // Hapus referensi di detail_permintaan_barang (set null atau hapus)
                DB::table('detail_permintaan_barang')
                    ->where('id_barang', $barangMedi->id_obat)
                    ->update(['id_barang' => null]);

                // Hapus semua data stok terkait
                $barangMedi->stok()->delete();

                // Hapus semua riwayat stok
                $barangMedi->stokHistories()->delete();

                // Hapus barang itu sendiri
                $barangMedi->delete();
            });

            return redirect()->route('barang-medis.index')->with('success',
                'Barang "' . $namaBarang . '" berhasil dihapus beserta semua data terkait.'
            );

        } catch (\Exception $e) {
            Log::error('Error deleting barang medis: ' . $e->getMessage(), [
                'barang_id' => $barangMedi->id_obat,
                'barang_nama' => $namaBarang,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error',
                'Gagal menghapus barang "' . $namaBarang . '": ' . $e->getMessage()
            );
        }
    }

    /**
     * Show history of a specific item
     */
    public function history(BarangMedis $barangMedi)
    {
        $user = Auth::user();
        $idLokasi = $user->id_lokasi; // Filter berdasarkan lokasi user

        $histories = $barangMedi->stokHistories()
            ->with(['lokasi', 'user'])
            ->when($idLokasi, function ($query, $idLokasi) {
                return $query->where('id_lokasi', $idLokasi);
            })
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('barang-medis.history', compact('barangMedi', 'histories'));
    }

    /**
     * Distribute stock between locations
     */
    public function distribusi(Request $request, BarangMedis $barang)
    {
        $user = Auth::user();

        // Izinkan PENGADAAN dan DOKTER untuk melakukan distribusi
        if (!$user->hasRole('PENGADAAN') && !$user->hasRole('DOKTER')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $validated = $request->validate([
            'lokasi_asal' => 'required|exists:lokasi_klinik,id',
            'lokasi_tujuan' => 'required|exists:lokasi_klinik,id|different:lokasi_asal',
            'jumlah' => 'required|integer|min:1'
        ], [
            'lokasi_tujuan.different' => 'Lokasi tujuan tidak boleh sama dengan lokasi asal.'
        ]);

        $jumlahDistribusi = $validated['jumlah'];
        $idLokasiAsal = $validated['lokasi_asal'];
        $idLokasiTujuan = $validated['lokasi_tujuan'];

        // Validasi khusus untuk role DOKTER - hanya bisa distribusi dari/ke lokasi mereka
        if ($user->hasRole('DOKTER')) {
            $userLokasiId = $user->id_lokasi;

            if ($idLokasiAsal != $userLokasiId && $idLokasiTujuan != $userLokasiId) {
                abort(403, 'Dokter hanya dapat melakukan distribusi dari atau ke lokasi klinik mereka sendiri.');
            }
        }

        try {
            DB::transaction(function () use ($barang, $jumlahDistribusi, $idLokasiAsal, $idLokasiTujuan, $request) {
                // --- PROSES LOKASI ASAL ---
                $stokAsal = StokBarang::where('id_barang', $barang->id_obat)
                    ->where('id_lokasi', $idLokasiAsal)
                    ->lockForUpdate()
                    ->first();

                $stokSebelumAsal = $stokAsal->jumlah ?? 0;

                if ($stokSebelumAsal < $jumlahDistribusi) {
                    throw new \Exception("Stok tidak mencukupi di lokasi asal. Stok tersedia: {$stokSebelumAsal}");
                }

                // Validasi stok minimal
                $stokSetelahDistribusi = $stokSebelumAsal - $jumlahDistribusi;
                $stokMinimal = $barang->stok_minimal ?? 0;
                
                if ($stokSetelahDistribusi < $stokMinimal) {
                    throw new \Exception("Distribusi tidak dapat dilakukan. Stok akan berada di bawah batas minimal ({$stokMinimal}). Stok tersedia: {$stokSebelumAsal}, setelah distribusi: {$stokSetelahDistribusi}");
                }

                // Kurangi stok di lokasi asal
                $stokAsal->decrement('jumlah', $jumlahDistribusi);

                // Catat riwayat pengurangan stok di lokasi asal
                StokHistory::create([
                    'id_barang' => $barang->id_obat,
                    'id_lokasi' => $idLokasiAsal,
                    'perubahan' => -$jumlahDistribusi,
                    'stok_sebelum' => $stokSebelumAsal,
                    'stok_sesudah' => $stokSebelumAsal - $jumlahDistribusi,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Distribusi ke ' . LokasiKlinik::find($idLokasiTujuan)->nama_lokasi,
                    'user_id' => Auth::id()
                ]);

                // --- PROSES LOKASI TUJUAN ---
                $stokTujuan = StokBarang::firstOrCreate(
                    ['id_barang' => $barang->id_obat, 'id_lokasi' => $idLokasiTujuan],
                    ['jumlah' => 0]
                );

                $stokSebelumTujuan = $stokTujuan->jumlah;
                $stokTujuan->increment('jumlah', $jumlahDistribusi);

                // Catat riwayat penambahan stok di lokasi tujuan
                StokHistory::create([
                    'id_barang' => $barang->id_obat,
                    'id_lokasi' => $idLokasiTujuan,
                    'perubahan' => $jumlahDistribusi,
                    'stok_sebelum' => $stokSebelumTujuan,
                    'stok_sesudah' => $stokSebelumTujuan + $jumlahDistribusi,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Distribusi dari ' . LokasiKlinik::find($idLokasiAsal)->nama_lokasi,
                    'user_id' => Auth::id()
                ]);

                // [BARU] LOG DISTRIBUSI UNTUK AUDIT PENGADAAN
                \App\Models\DistribusiBarang::create([
                    'id_barang' => $barang->id_obat,
                    'id_lokasi_asal' => $idLokasiAsal,
                    'id_lokasi_tujuan' => $idLokasiTujuan,
                    'id_user' => Auth::id(),
                    'jumlah' => $jumlahDistribusi,
                    'keterangan' => $request->keterangan ?? null,
                    'status' => 'approved', // Langsung approved, tidak perlu approval
                    'validated_by' => null,
                    'validated_at' => null,
                ]);
            });

            $lokasiAsal = LokasiKlinik::find($idLokasiAsal)->nama_lokasi;
            $lokasiTujuan = LokasiKlinik::find($idLokasiTujuan)->nama_lokasi;

            return redirect()->back()->with('success',
                "Berhasil mendistribusikan {$jumlahDistribusi} unit {$barang->nama_obat} dari {$lokasiAsal} ke {$lokasiTujuan}."
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan distribusi: ' . $e->getMessage());
        }
    }

    /**
     * Distribute multiple items at once between locations
     */
    public function distribusiMulti(Request $request)
    {
        $user = Auth::user();

        // Izinkan PENGADAAN dan DOKTER untuk melakukan distribusi
        if (!$user->hasRole('PENGADAAN') && !$user->hasRole('DOKTER')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $validated = $request->validate([
            'lokasi_asal' => 'required|exists:lokasi_klinik,id',
            'lokasi_tujuan' => 'required|exists:lokasi_klinik,id|different:lokasi_asal',
            'nomor_wa_validator' => 'required|string|min:10|max:15',
            'catatan' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang_medis,id_obat',
            'items.*.jumlah' => 'required|integer|min:1'
        ], [
            'lokasi_tujuan.different' => 'Lokasi tujuan tidak boleh sama dengan lokasi asal.',
            'items.required' => 'Pilih minimal satu obat untuk didistribusikan.',
            'items.min' => 'Pilih minimal satu obat untuk didistribusikan.',
            'nomor_wa_validator.required' => 'Nomor WhatsApp validator wajib diisi.',
            'nomor_wa_validator.min' => 'Nomor WhatsApp minimal 10 digit.',
        ]);

        $idLokasiAsal = $validated['lokasi_asal'];
        $idLokasiTujuan = $validated['lokasi_tujuan'];
        $nomorWaValidator = $validated['nomor_wa_validator'];
        $catatan = $validated['catatan'] ?? null;
        $items = $validated['items'];

        // Validasi khusus untuk role DOKTER - hanya bisa distribusi dari/ke lokasi mereka
        if ($user->hasRole('DOKTER')) {
            $userLokasiId = $user->id_lokasi;

            if ($idLokasiAsal != $userLokasiId && $idLokasiTujuan != $userLokasiId) {
                abort(403, 'Dokter hanya dapat melakukan distribusi dari atau ke lokasi klinik mereka sendiri.');
            }
        }

        $successCount = 0;
        $failedItems = [];
        $successItems = [];
        $suratDistribusi = null;

        try {
            DB::transaction(function () use ($items, $idLokasiAsal, $idLokasiTujuan, $nomorWaValidator, $catatan, &$successCount, &$failedItems, &$successItems, &$suratDistribusi) {
                
                // Buat Surat Distribusi terlebih dahulu
                $suratDistribusi = SuratDistribusi::create([
                    'nomor_surat' => SuratDistribusi::generateNomorSurat(),
                    'kode_validasi' => SuratDistribusi::generateKodeValidasi(),
                    'id_lokasi_asal' => $idLokasiAsal,
                    'id_lokasi_tujuan' => $idLokasiTujuan,
                    'id_user' => Auth::id(),
                    'tanggal_distribusi' => now()->toDateString(),
                    'nomor_wa_validator' => $nomorWaValidator,
                    'catatan' => $catatan,
                    'status' => 'pending',
                ]);

                foreach ($items as $item) {
                    $idBarang = $item['id_barang'];
                    $jumlahDistribusi = $item['jumlah'];

                    $barang = BarangMedis::find($idBarang);
                    if (!$barang) {
                        $failedItems[] = "Barang ID {$idBarang} tidak ditemukan";
                        continue;
                    }

                    // --- PROSES LOKASI ASAL ---
                    $stokAsal = StokBarang::where('id_barang', $idBarang)
                        ->where('id_lokasi', $idLokasiAsal)
                        ->lockForUpdate()
                        ->first();

                    $stokSebelumAsal = $stokAsal->jumlah ?? 0;

                    if ($stokSebelumAsal < $jumlahDistribusi) {
                        $failedItems[] = "{$barang->nama_obat} (stok tidak cukup: {$stokSebelumAsal})";
                        continue;
                    }

                    // Kurangi stok di lokasi asal
                    $stokAsal->decrement('jumlah', $jumlahDistribusi);

                    // Catat riwayat pengurangan stok di lokasi asal
                    StokHistory::create([
                        'id_barang' => $idBarang,
                        'id_lokasi' => $idLokasiAsal,
                        'perubahan' => -$jumlahDistribusi,
                        'stok_sebelum' => $stokSebelumAsal,
                        'stok_sesudah' => $stokSebelumAsal - $jumlahDistribusi,
                        'tanggal_transaksi' => now(),
                        'keterangan' => 'Distribusi ke ' . LokasiKlinik::find($idLokasiTujuan)->nama_lokasi,
                        'user_id' => Auth::id()
                    ]);

                    // --- PROSES LOKASI TUJUAN ---
                    $stokTujuan = StokBarang::firstOrCreate(
                        ['id_barang' => $idBarang, 'id_lokasi' => $idLokasiTujuan],
                        ['jumlah' => 0]
                    );

                    $stokSebelumTujuan = $stokTujuan->jumlah;
                    $stokTujuan->increment('jumlah', $jumlahDistribusi);

                    // Catat riwayat penambahan stok di lokasi tujuan
                    StokHistory::create([
                        'id_barang' => $idBarang,
                        'id_lokasi' => $idLokasiTujuan,
                        'perubahan' => $jumlahDistribusi,
                        'stok_sebelum' => $stokSebelumTujuan,
                        'stok_sesudah' => $stokSebelumTujuan + $jumlahDistribusi,
                        'tanggal_transaksi' => now(),
                        'keterangan' => 'Distribusi dari ' . LokasiKlinik::find($idLokasiAsal)->nama_lokasi,
                        'user_id' => Auth::id()
                    ]);

                    // LOG DISTRIBUSI UNTUK AUDIT PENGADAAN
                    DistribusiBarang::create([
                        'id_barang' => $idBarang,
                        'id_lokasi_asal' => $idLokasiAsal,
                        'id_lokasi_tujuan' => $idLokasiTujuan,
                        'id_user' => Auth::id(),
                        'jumlah' => $jumlahDistribusi,
                        'keterangan' => 'Surat: ' . $suratDistribusi->nomor_surat,
                        'status' => 'approved',
                        'validated_by' => null,
                        'validated_at' => null,
                    ]);

                    // Simpan detail ke surat distribusi
                    DetailSuratDistribusi::create([
                        'id_surat' => $suratDistribusi->id_surat,
                        'id_barang' => $idBarang,
                        'jumlah' => $jumlahDistribusi,
                    ]);

                    $successCount++;
                    $successItems[] = "{$barang->nama_obat} ({$jumlahDistribusi} unit)";
                }

                // Jika tidak ada item yang berhasil, hapus surat
                if ($successCount === 0) {
                    $suratDistribusi->delete();
                    $suratDistribusi = null;
                }
            });

            $lokasiAsal = LokasiKlinik::find($idLokasiAsal)->nama_lokasi;
            $lokasiTujuan = LokasiKlinik::find($idLokasiTujuan)->nama_lokasi;

            $message = "Berhasil mendistribusikan {$successCount} jenis obat dari {$lokasiAsal} ke {$lokasiTujuan}.";
            
            if (count($failedItems) > 0) {
                $message .= " Gagal: " . implode(', ', $failedItems);
            }

            // Jika surat berhasil dibuat, redirect ke halaman cetak surat
            if ($suratDistribusi) {
                return redirect()->route('surat-distribusi.show', $suratDistribusi->id_surat)
                    ->with('success', $message . ' Silakan cetak surat distribusi.');
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan distribusi: ' . $e->getMessage());
        }
    }

    public function printPdf()
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('PENGADAAN')) {
                abort(403, 'Anda tidak memiliki hak akses.');
            }

            $idLokasi = $user->id_lokasi;
            $gkn1Id = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 1%')->value('id');
            $gkn2Id = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 2%')->value('id');

            $barangMedis = BarangMedis::query()
                ->when($idLokasi, function ($query) use ($idLokasi) {
                    $query->whereHas('stok', function ($stokQuery) use ($idLokasi) {
                        $stokQuery->where('id_lokasi', $idLokasi);
                    });
                })
                ->with(['stok' => function ($query) {
                    $query->with('lokasi');
                }])
                ->withSum(['stok' => function ($query) use ($idLokasi) {
                    if ($idLokasi) {
                        $query->where('id_lokasi', $idLokasi);
                    }
                }], 'jumlah')
                ->withSum(['stok as stok_gkn1' => function ($query) use ($gkn1Id) {
                    if ($gkn1Id) {
                        $query->where('id_lokasi', $gkn1Id);
                    }
                }], 'jumlah')
                ->withSum(['stok as stok_gkn2' => function ($query) use ($gkn2Id) {
                    if ($gkn2Id) {
                        $query->where('id_lokasi', $gkn2Id);
                    }
                }], 'jumlah')
                ->withMax(['stokMasuk as tanggal_masuk_terakhir' => function ($query) use ($idLokasi) {
                    if ($idLokasi) {
                        $query->where('id_lokasi', $idLokasi);
                    }
                }], 'tanggal_transaksi')
                ->withMin(['stokMasuk as expired_terdekat' => function ($query) use ($idLokasi) {
                    if ($idLokasi) {
                        $query->where('id_lokasi', $idLokasi);
                    }
                }], 'expired_at')
                ->orderBy('nama_obat')
                ->get();

            $data = [
                'barangMedis' => $barangMedis,
                'tanggal_cetak' => now()->format('d/m/Y H:i:s'),
                'nama_user' => $user->nama_karyawan ?? $user->name ?? 'Pengguna Sistem',
            ];

            $pdf = Pdf::loadView('barang-medis.pdf', $data)->setPaper('A4', 'landscape');

            return $pdf->download('Daftar_Obat_Alat_Medis_' . now()->format('Y-m-d_H-i-s') . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error('PDF Generation Stack Trace: ' . $e->getTraceAsString());

            return response('Error generating PDF: ' . $e->getMessage(), 500);
        }
    }
}
