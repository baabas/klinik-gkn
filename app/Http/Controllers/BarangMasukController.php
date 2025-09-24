<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\StokBarang;
use App\Models\StokHistory;
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
        $entries = StokHistory::query()
            ->with(['barang', 'lokasi', 'user'])
            // Tampilkan semua transaksi termasuk koreksi pengurangan
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

        $barang = BarangMedis::orderBy('nama_obat')->get(['id_obat', 'nama_obat']);

        return view('barang-medis.masuk.index', compact('entries', 'barang'));
    }

    /**
     * Form input barang masuk.
     */
    public function create()
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $barang = BarangMedis::orderBy('nama_obat')->get();
        $lokasi = LokasiKlinik::orderBy('nama_lokasi')->get();

        return view('barang-medis.masuk.create', compact('barang', 'lokasi'));
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
            'id_lokasi' => 'required|exists:lokasi_klinik,id',
            'tanggal_masuk' => 'required|date',
            'keterangan_umum' => 'nullable|string|max:500',
            'batches' => 'required|array|min:1',
            'batches.*.jumlah_kemasan' => 'required|integer|min:1',
            'batches.*.expired_at' => 'nullable|date|after_or_equal:today',
            'batches.*.keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            // Ambil data barang untuk mendapatkan informasi kemasan
            $barang = BarangMedis::findOrFail($validated['id_barang']);
            
            $totalSatuanMasuk = 0;

            // Proses setiap batch
            foreach ($validated['batches'] as $batchIndex => $batch) {
                // Hitung total satuan terkecil untuk batch ini
                // Jumlah kemasan * isi kemasan * isi per satuan
                $satuanBatch = $batch['jumlah_kemasan'] * 
                              ($barang->isi_kemasan_jumlah ?? 1) * 
                              ($barang->isi_per_satuan ?? 1);

                $totalSatuanMasuk += $satuanBatch;

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
                    'isi_per_kemasan' => ($barang->isi_kemasan_jumlah ?? 1) * ($barang->isi_per_satuan ?? 1),
                    'satuan_kemasan' => $barang->kemasan ?? 'Box',
                    'keterangan' => $keteranganBatch,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', "Data barang masuk berhasil disimpan dengan total " . count($validated['batches']) . " batch.");
    }
}