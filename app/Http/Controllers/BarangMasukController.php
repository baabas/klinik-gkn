<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\StokBarang;
use App\Models\StokHistory;
use App\Models\PendingStokMasuk;
use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarangMasukController extends Controller
{
    /**
     * Tampilkan daftar riwayat barang masuk dan koreksi stok.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $idLokasi = $user->id_lokasi; // Filter berdasarkan lokasi user
        
        $entries = StokHistory::query()
            ->with(['barang', 'lokasi', 'user'])
            // Filter berdasarkan lokasi user
            ->when($idLokasi, function ($query) use ($idLokasi) {
                $query->where('id_lokasi', $idLokasi);
            })
            ->when($request->filled('barang'), function ($query) use ($request) {
                $query->where('id_barang', $request->input('barang'));
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate('tanggal_transaksi', $request->input('tanggal'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->input('q');
                $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('nama_obat', 'like', "%{$search}%")
                      ->orWhere('kode_obat', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Filter barang dropdown berdasarkan lokasi user
        $barang = BarangMedis::when($idLokasi, function ($query) use ($idLokasi) {
                $query->whereHas('stok', function ($q) use ($idLokasi) {
                    $q->where('id_lokasi', $idLokasi);
                });
            })
            ->orderBy('nama_obat')
            ->get(['id_obat', 'nama_obat']);

        return view('barang-medis.masuk.index', compact('entries', 'barang'));
    }

    /**
     * Form input barang masuk.
     */
    public function create(request $request)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $barang = BarangMedis::orderBy('nama_obat')->get();
        $lokasi = LokasiKlinik::orderBy('nama_lokasi')->get();

        // Fetch approved requests that need items to be stocked (exclude PROCESSING status)
        $approvedRequests = DB::table('permintaan_barang as pb')
            ->join('detail_permintaan_barang as dpb', 'pb.id', '=', 'dpb.id_permintaan')
            ->leftJoin('barang_medis as bm', 'dpb.id_barang', '=', 'bm.id_obat')
            ->leftJoin('lokasi_klinik as lk', 'pb.id_lokasi_peminta', '=', 'lk.id')
            ->leftJoin('users as u', 'pb.id_user_peminta', '=', 'u.id')
            ->leftJoin('karyawan as k', 'u.nip', '=', 'k.nip')
            ->where('pb.status', 'APPROVED')
            ->where('dpb.jumlah_disetujui', '>', 0)
            ->select(
                'pb.id as id_permintaan',
                'pb.kode_permintaan',
                'pb.tanggal_permintaan',
                'lk.nama_lokasi',
                'k.nama_karyawan',
                'u.nama_karyawan as user_nama',
                'dpb.id as id_detail',
                'dpb.id_barang',
                'dpb.nama_barang_baru',
                'dpb.jumlah_disetujui',
                'dpb.kemasan_diminta',
                'dpb.kemasan_barang_baru',
                'dpb.catatan',
                'dpb.catatan_barang_baru',
                'bm.nama_obat',
                'bm.kode_obat'
            )
            ->orderBy('pb.tanggal_permintaan', 'desc')
            ->get()
            ->groupBy('id_permintaan');

        // Get specific request if ID is provided
        $selectedRequest = null;
        if ($request->has('request_id')) {
            $requestId = $request->input('request_id');
            $selectedRequest = $approvedRequests->get($requestId);
        }

        return view('barang-medis.masuk.create', compact('barang', 'lokasi', 'approvedRequests', 'selectedRequest'));
    }

    /**
     * Simpan data barang masuk baru.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $validated = $request->validate([
            'id_barang' => 'required|exists:barang_medis,id_obat',
            'id_detail' => 'nullable|exists:detail_permintaan_barang,id',
            'id_lokasi' => 'required|exists:lokasi_klinik,id',
            'tanggal_masuk' => 'required|date',
            'keterangan_umum' => 'nullable|string|max:500',
            'batches' => 'required|array|min:1',
            'batches.*.jumlah_kemasan' => 'required|integer|min:1',
            'batches.*.expired_at' => 'required|date|after_or_equal:today',
            'batches.*.keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            // Ambil data barang untuk mendapatkan informasi kemasan
            $barang = BarangMedis::findOrFail($validated['id_barang']);
            
            // Calculate isi per kemasan
            $isiPerKemasan = ($barang->isi_kemasan_jumlah ?? 1) * ($barang->isi_per_satuan ?? 1);

            // Cek apakah ini item dari permintaan atau input manual
            if (!empty($validated['id_detail'])) {
                // Item dari permintaan - simpan ke pending_stok_masuks
                $detailPermintaan = DetailPermintaanBarang::find($validated['id_detail']);
                
                foreach ($validated['batches'] as $batchIndex => $batch) {
                    // Generate keterangan batch
                    $keteranganBatch = $batch['keterangan'] ?: 
                        "Batch " . chr(65 + $batchIndex) . " - {$batch['jumlah_kemasan']} {$barang->kemasan}";
                    
                    if (!empty($validated['keterangan_umum'])) {
                        $keteranganBatch .= " | " . $validated['keterangan_umum'];
                    }

                    // Simpan ke pending_stok_masuks
                    PendingStokMasuk::create([
                        'id_permintaan' => $detailPermintaan->id_permintaan,
                        'id_detail_permintaan' => $validated['id_detail'],
                        'id_barang' => $validated['id_barang'],
                        'id_lokasi' => $validated['id_lokasi'],
                        'jumlah_kemasan' => $batch['jumlah_kemasan'],
                        'isi_per_kemasan' => $isiPerKemasan,
                        'satuan_kemasan' => $barang->kemasan ?? 'Box',
                        'tanggal_masuk' => $validated['tanggal_masuk'],
                        'expired_at' => $batch['expired_at'],
                        'keterangan' => $keteranganBatch,
                        'user_id' => Auth::id(),
                    ]);
                }
                
                // Cek apakah semua item dalam permintaan sudah diproses
                $this->checkAndUpdateRequestStatus($detailPermintaan->id_permintaan);
                
            } else {
                // Input manual (tidak dari permintaan) - langsung update stok seperti sebelumnya
                foreach ($validated['batches'] as $batchIndex => $batch) {
                    // Hitung total satuan terkecil untuk batch ini
                    $satuanBatch = $batch['jumlah_kemasan'] * $isiPerKemasan;

                    // Update atau buat stok barang
                    $stok = StokBarang::firstOrCreate(
                        [
                            'id_barang' => $validated['id_barang'],
                            'id_lokasi' => $validated['id_lokasi'],
                        ],
                        ['jumlah' => 0]
                    );

                    $stokSebelum = $stok->jumlah;
                    $stok->increment('jumlah', $satuanBatch);

                    // Siapkan keterangan batch
                    $keteranganBatch = $batch['keterangan'] ?? 
                        "Batch " . chr(65 + $batchIndex) . " - {$batch['jumlah_kemasan']} {$barang->kemasan}";
                    
                    // Tambahkan keterangan umum jika ada
                    if (!empty($validated['keterangan_umum'])) {
                        $keteranganBatch .= " | " . $validated['keterangan_umum'];
                    }

                    // Catat riwayat stok untuk setiap batch
                    StokHistory::create([
                        'id_barang' => $validated['id_barang'],
                        'id_lokasi' => $validated['id_lokasi'],
                        'perubahan' => $satuanBatch,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stok->jumlah,
                        'tanggal_transaksi' => $validated['tanggal_masuk'],
                        'expired_at' => $batch['expired_at'] ?? null,
                        'jumlah_kemasan' => $batch['jumlah_kemasan'],
                        'isi_per_kemasan' => $isiPerKemasan,
                        'satuan_kemasan' => $barang->kemasan ?? 'Box',
                        'keterangan' => $keteranganBatch,
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        });

        // Tentukan redirect dan pesan berdasarkan context
        if (!empty($validated['id_detail'])) {
            // Item dari permintaan
            $detailPermintaan = DetailPermintaanBarang::find($validated['id_detail']);
            $permintaan = $detailPermintaan->permintaan;
            
            $message = "Item berhasil disimpan dengan " . count($validated['batches']) . " batch. ";
            $message .= $permintaan->status === 'PROCESSING' 
                ? "Semua item telah diproses, menunggu konfirmasi dokter."
                : "Item lainnya masih menunggu diproses.";
            
            return redirect()
                ->route('barang-masuk.create', ['request_id' => $permintaan->id])
                ->with('success', $message);
        } else {
            // Input manual
            return redirect()
                ->route('barang-masuk.index')
                ->with('success', "Data barang masuk berhasil disimpan dengan total " . count($validated['batches']) . " batch.");
        }
    }

    /**
     * Store multiple barang masuk data.
     */
    public function storeMultiple(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|integer|exists:permintaan_barang,id',
            'items' => 'required|array|min:1',
            'items.*.id_detail' => 'required|integer|exists:detail_permintaan_barang,id',
            'items.*.id_barang' => 'required|integer|exists:barang_medis,id_obat',
            'items.*.lokasi_default' => 'required|integer|exists:lokasi_klinik,id',
            'items.*.tanggal_masuk' => 'required|date',
            'items.*.batches' => 'required|array|min:1',
            'items.*.batches.*.jumlah_kemasan' => 'required|integer|min:1',
            'items.*.batches.*.expired_at' => 'required|date|after:today',
            'items.*.batches.*.keterangan' => 'nullable|string|max:255',
        ]);

        $successItems = 0;
        $errors = [];

        DB::transaction(function () use ($validated, &$successItems, &$errors) {
            foreach ($validated['items'] as $itemIndex => $item) {
                try {
                    // Get barang info
                    $barang = BarangMedis::findOrFail($item['id_barang']);
                    
                    // Calculate isi per kemasan
                    $isiPerKemasan = ($barang->isi_kemasan_jumlah ?? 1) * ($barang->isi_per_satuan ?? 1);
                    
                    // Process each batch for this item - SIMPAN KE PENDING, TIDAK LANGSUNG UPDATE STOK
                    foreach ($item['batches'] as $batchIndex => $batch) {
                        // Generate keterangan batch
                        $keteranganBatch = $batch['keterangan'] ?: 
                            "Batch " . chr(65 + $batchIndex) . " - {$batch['jumlah_kemasan']} {$barang->kemasan}";

                        // Simpan ke pending_stok_masuks (BARU) - Tidak langsung update stok
                        PendingStokMasuk::create([
                            'id_permintaan' => $validated['request_id'],
                            'id_detail_permintaan' => $item['id_detail'],
                            'id_barang' => $item['id_barang'],
                            'id_lokasi' => $item['lokasi_default'],
                            'jumlah_kemasan' => $batch['jumlah_kemasan'],
                            'isi_per_kemasan' => $isiPerKemasan,
                            'satuan_kemasan' => $barang->kemasan ?? 'Box',
                            'tanggal_masuk' => $item['tanggal_masuk'],
                            'expired_at' => $batch['expired_at'],
                            'keterangan' => $keteranganBatch,
                            'user_id' => Auth::id(),
                        ]);
                    }

                    $successItems++;

                } catch (\Exception $e) {
                    $barangNama = isset($barang) ? $barang->nama_obat : 'Unknown';
                    $errors[] = "Item " . ($itemIndex + 1) . " ($barangNama): " . $e->getMessage();
                }
            }

            // Cek apakah semua item dalam permintaan sudah diproses
            if ($successItems > 0) {
                $this->checkAndUpdateRequestStatus($validated['request_id']);
            }
        });

        if ($successItems > 0) {
            // Check final status after processing
            $finalPermintaan = PermintaanBarang::find($validated['request_id']);
            $statusMessage = $finalPermintaan->status === 'PROCESSING' 
                ? "Status permintaan diubah menjadi PROCESSING. Semua item telah diproses."
                : "Item berhasil disimpan. Permintaan masih menunggu item lainnya diproses.";
                
            $message = "Berhasil menyimpan {$successItems} barang masuk. {$statusMessage} Barang akan masuk ke stok setelah dikonfirmasi oleh dokter.";
            if (!empty($errors)) {
                $message .= " Namun terdapat " . count($errors) . " item yang gagal disimpan.";
            }
            
            return redirect()
                ->route('barang-masuk.create', ['request_id' => $validated['request_id']])
                ->with('success', $message);
        } else {
            return redirect()
                ->back()
                ->withErrors(['errors' => $errors])
                ->with('error', 'Semua item gagal disimpan.');
        }
    }

    /**
     * Check if all items in a request have been processed and update status accordingly
     */
    private function checkAndUpdateRequestStatus($requestId)
    {
        // Get permintaan with details that have jumlah_disetujui > 0
        $permintaan = PermintaanBarang::with(['detail' => function($query) {
            $query->where('jumlah_disetujui', '>', 0);
        }])->find($requestId);

        if (!$permintaan) {
            return;
        }

        $totalItemsToProcess = $permintaan->detail->count();
        
        // Get detail IDs that have already been processed (have pending stock entries)
        $processedDetailIds = PendingStokMasuk::where('id_permintaan', $requestId)
            ->distinct()
            ->pluck('id_detail_permintaan')
            ->toArray();

        $processedItemsCount = count($processedDetailIds);

        // If all items have been processed, change status to PROCESSING
        if ($processedItemsCount >= $totalItemsToProcess) {
            $permintaan->update(['status' => 'PROCESSING']);
        }
        // Otherwise, keep status as APPROVED for partial processing
    }

    /**
     * Check completion status for items in a request
     */
    public function checkCompletion($requestId)
    {
        // Get detail IDs that have been processed (have pending stock entries)
        $completedDetailIds = PendingStokMasuk::where('id_permintaan', $requestId)
            ->distinct()
            ->pluck('id_detail_permintaan')
            ->toArray();

        return response()->json([
            'success' => true,
            'requestId' => $requestId,
            'completedItems' => $completedDetailIds
        ]);
    }
}