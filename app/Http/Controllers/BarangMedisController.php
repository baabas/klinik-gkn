<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
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
            ->withSum(['stok' => function ($q) use ($idLokasi) {
                if ($idLokasi) {
                    $q->where('id_lokasi', $idLokasi);
                } else {
                    // Jika tidak ada filter lokasi, tampilkan semua
                    return $q;
                }
            }], 'jumlah')
            ->withSum(['stok as stok_gkn1' => function ($q) use ($gkn1Id) {
                $q->where('id_lokasi', $gkn1Id ?? 0);
            }], 'jumlah')
            ->withSum(['stok as stok_gkn2' => function ($q) use ($gkn2Id) {
                $q->where('id_lokasi', $gkn2Id ?? 0);
            }], 'jumlah')
            ->withSum(['stokMasuk as total_kemasan_masuk' => function ($q) use ($idLokasi) {
                if ($idLokasi) {
                    $q->where('id_lokasi', $idLokasi);
                }
            }], 'jumlah_kemasan')
            ->withSum(['stokMasuk as total_unit_masuk' => function ($q) use ($idLokasi) {
                if ($idLokasi) {
                    $q->where('id_lokasi', $idLokasi);
                }
            }], 'perubahan')
            ->withMax(['stokMasuk as tanggal_masuk_terakhir' => function ($q) use ($idLokasi) {
                if ($idLokasi) {
                    $q->where('id_lokasi', $idLokasi);
                }
            }], 'tanggal_transaksi')
            ->withMin(['stokMasuk as expired_terdekat' => function ($q) use ($idLokasi) {
                if ($idLokasi) {
                    $q->where('id_lokasi', $idLokasi);
                }
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

        return view('barang-medis.index', compact('barang', 'search'));
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
                ->withSum(['stok' => function ($q) use ($idLokasi) {
                    if ($idLokasi) {
                        $q->where('id_lokasi', $idLokasi);
                    } else {
                        // Jika tidak ada filter lokasi, tampilkan semua
                        return $q;
                    }
                }], 'jumlah')
                ->withSum(['stok as stok_gkn1' => function ($q) use ($gkn1Id) {
                    $q->where('id_lokasi', $gkn1Id ?? 0);
                }], 'jumlah')
                ->withSum(['stok as stok_gkn2' => function ($q) use ($gkn2Id) {
                    $q->where('id_lokasi', $gkn2Id ?? 0);
                }], 'jumlah')
                ->withSum(['stokMasuk as total_kemasan_masuk' => function ($q) use ($idLokasi) {
                    if ($idLokasi) {
                        $q->where('id_lokasi', $idLokasi);
                    }
                }], 'jumlah_kemasan')
                ->withSum(['stokMasuk as total_unit_masuk' => function ($q) use ($idLokasi) {
                    if ($idLokasi) {
                        $q->where('id_lokasi', $idLokasi);
                    }
                }], 'perubahan')
                ->withMax(['stokMasuk as tanggal_masuk_terakhir' => function ($q) use ($idLokasi) {
                    if ($idLokasi) {
                        $q->where('id_lokasi', $idLokasi);
                    }
                }], 'tanggal_transaksi')
                ->withMin(['stokMasuk as expired_terdekat' => function ($q) use ($idLokasi) {
                    if ($idLokasi) {
                        $q->where('id_lokasi', $idLokasi);
                    }
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

        $validated = $request->validate([
            'kategori_barang' => 'required|string|in:Obat,BMHP,Alkes,APD',
            'nama_obat' => 'required|string|max:255',
            'isi_kemasan_jumlah' => 'required|integer|min:1',
            'isi_kemasan_satuan' => 'required|string|in:strip,kotak,botol,vial,tube,lainnya',
            'isi_kemasan_satuan_custom' => 'required_if:isi_kemasan_satuan,lainnya|nullable|string|max:50',
            'isi_per_satuan' => 'required|integer|min:1',
            'satuan_terkecil' => 'required|string|in:Tablet,Botol,Pcs,Vial,Tube,Troches,Kapsul,Sirup,lainnya',
            'satuan_terkecil_custom' => 'required_if:satuan_terkecil,lainnya|nullable|string|max:50',
        ]);

        // Process custom values
        if ($validated['isi_kemasan_satuan'] === 'lainnya') {
            $validated['isi_kemasan_satuan'] = $validated['isi_kemasan_satuan_custom'];
        }
        
        if ($validated['satuan_terkecil'] === 'lainnya') {
            $validated['satuan_terkecil'] = $validated['satuan_terkecil_custom'];
        }
        
        // Remove custom fields from validated data as they're not needed in database
        unset($validated['isi_kemasan_satuan_custom'], $validated['satuan_terkecil_custom']);

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

        $validated = $request->validate([
            'kategori_barang' => 'required|string|in:Obat,BMHP,Alkes,APD',
            'nama_obat' => 'required|string|max:255',
            'isi_kemasan_jumlah' => 'required|integer|min:1',
            'isi_kemasan_satuan' => 'required|string|in:strip,kotak,botol,vial,tube,lainnya',
            'isi_kemasan_satuan_custom' => 'required_if:isi_kemasan_satuan,lainnya|nullable|string|max:50',
            'isi_per_satuan' => 'required|integer|min:1',
            'satuan_terkecil' => 'required|string|in:Tablet,Botol,Pcs,Vial,Tube,Troches,Kapsul,Sirup,lainnya',
            'satuan_terkecil_custom' => 'required_if:satuan_terkecil,lainnya|nullable|string|max:50',
        ]);

        // Process custom values
        if ($validated['isi_kemasan_satuan'] === 'lainnya') {
            $validated['isi_kemasan_satuan'] = $validated['isi_kemasan_satuan_custom'];
        }
        
        if ($validated['satuan_terkecil'] === 'lainnya') {
            $validated['satuan_terkecil'] = $validated['satuan_terkecil_custom'];
        }
        
        // Remove custom fields from validated data as they're not needed in database
        unset($validated['isi_kemasan_satuan_custom'], $validated['satuan_terkecil_custom']);

        // Set kemasan ke "Box" secara otomatis
        $validated['kemasan'] = 'Box';
        
        // Set satuan sama dengan satuan_terkecil
        $validated['satuan'] = $validated['satuan_terkecil'];

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
            DB::transaction(function () use ($barang, $jumlahDistribusi, $idLokasiAsal, $idLokasiTujuan) {
                // --- PROSES LOKASI ASAL ---
                $stokAsal = StokBarang::where('id_barang', $barang->id_obat)
                    ->where('id_lokasi', $idLokasiAsal)
                    ->lockForUpdate()
                    ->first();

                $stokSebelumAsal = $stokAsal->jumlah ?? 0;

                if ($stokSebelumAsal < $jumlahDistribusi) {
                    throw new \Exception("Stok tidak mencukupi di lokasi asal. Stok tersedia: {$stokSebelumAsal}");
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

    public function printPdf()
    {
        try {
            // Check user role
            if (!Auth::user()->hasRole('PENGADAAN')) {
                abort(403, 'Anda tidak memiliki hak akses.');
            }

            // Get all barang medis with their stock information
            $barangMedis = BarangMedis::with(['stok.lokasiKlinik'])
                ->orderBy('nama_obat')
                ->get();

            // Debug: Check if we have data
            if ($barangMedis->isEmpty()) {
                return response('No data found', 404);
            }

            // Prepare data for PDF
            $data = [
                'barangMedis' => $barangMedis,
                'tanggal_cetak' => now()->format('d/m/Y H:i:s'),
                'nama_user' => Auth::check() ? Auth::user()->name : 'Guest'
            ];

            // Generate PDF
            $pdf = Pdf::loadView('barang-medis.pdf', $data);
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download('Daftar_Obat_Alat_Medis_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error('PDF Generation Stack Trace: ' . $e->getTraceAsString());
            
            return response('Error generating PDF: ' . $e->getMessage(), 500);
        }
    }
}