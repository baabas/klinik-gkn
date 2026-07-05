<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\PermintaanBarang;
use App\Models\PendingStokMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\StokBarang;
use App\Models\StokHistory;
use Barryvdh\DomPDF\Facade\Pdf;

class PermintaanBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        // Memuat relasi 'lokasiPeminta' dan 'userPeminta' untuk efisiensi query
        $query = PermintaanBarang::with('lokasiPeminta', 'userPeminta')->latest();

        // Jika user memiliki role DOKTER, filter permintaan berdasarkan lokasi
        if ($user->hasRole('DOKTER')) {
            $query->where('id_lokasi_peminta', $user->id_lokasi);
        }

        $permintaan = $query->paginate(10);

        return view('permintaan.index', compact('permintaan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mengambil semua data barang untuk ditampilkan di dropdown form
        $barangMedis = BarangMedis::orderBy('nama_obat')->get();

        return view('permintaan.create', compact('barangMedis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'tanggal_permintaan' => 'required|date',
            'catatan' => 'nullable|string',
            // Validasi untuk barang yang sudah ada di database
            'barang' => 'nullable|array',
            'barang.*.id' => 'required_with:barang|exists:barang_medis,id_obat',
            'barang.*.jumlah' => 'required_with:barang|integer|min:1',
            // Validasi untuk request barang baru yang belum ada di database
            'barang_baru' => 'nullable|array',
            'barang_baru.*.nama' => 'required_with:barang_baru|string|max:255',
            'barang_baru.*.jumlah' => 'required_with:barang_baru|integer|min:1',
        ]);

        // Pastikan setidaknya ada satu item yang diminta
        if (empty($request->barang) && empty($request->barang_baru)) {
            return redirect()->back()->with('error', 'Permintaan tidak boleh kosong. Silakan tambahkan minimal satu barang.')->withInput();
        }

        // 2. Gunakan DB Transaction untuk memastikan integritas data
        DB::beginTransaction();
        try {
            // 3. Buat data "header" permintaan
            $permintaan = PermintaanBarang::create([
                'kode_permintaan' => 'REQ-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'tanggal_permintaan' => $request->tanggal_permintaan,
                'catatan' => $request->catatan,
                'status' => 'PENDING',
                'id_user_peminta' => Auth::id(),
                'id_lokasi_peminta' => Auth::user()->id_lokasi,
            ]);

            // 4. Simpan detail untuk barang yang sudah terdaftar
            if ($request->has('barang')) {
                foreach ($request->barang as $item) {
                    if(!empty($item['id']) && !empty($item['jumlah'])) {
                        $permintaan->detail()->create([
                            'id_barang' => $item['id'],
                            'jumlah_diminta' => $item['jumlah'],
                            'kemasan_diminta' => 'Box', // Fixed kemasan as Box
                            'catatan' => null, // Catatan dikosongkan untuk barang terdaftar
                        ]);
                    }
                }
            }

            // 5. Simpan detail untuk request barang baru
            if ($request->has('barang_baru')) {
                foreach ($request->barang_baru as $item) {
                     if(!empty($item['nama']) && !empty($item['jumlah'])) {
                        $permintaan->detail()->create([
                            'id_barang' => null, // ID barang dikosongkan karena barang baru
                            'jumlah_diminta' => $item['jumlah'],
                            'nama_barang_baru' => $item['nama'],
                            'tipe_barang_baru' => null,
                            'kemasan_barang_baru' => 'Box', // Fixed kemasan as Box
                            'catatan_barang_baru' => null, // Catatan dikosongkan
                        ]);
                    }
                }
            }

            DB::commit(); // Jika semua proses berhasil, simpan data secara permanen

            return redirect()->route('permintaan.index')->with('success', 'Permintaan barang berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack(); // Jika terjadi error, batalkan semua query yang sudah dijalankan
            return redirect()->back()->with('error', 'Terjadi kesalahan fatal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
        public function show(PermintaanBarang $permintaan)
        {
            $permintaan->load('detail.barangMedis', 'userPeminta', 'lokasiPeminta');

            // Kirim data ke view 'permintaan.show'
            return view('permintaan.show', compact('permintaan'));
        }

    /**
     * Show the form for editing the specified resource.
     */
        public function edit(PermintaanBarang $permintaan)
        {
            // Pastikan hanya PENGADAAN yang bisa mengakses halaman ini
            if (!Auth::user()->hasRole('PENGADAAN')) {
                abort(403, 'Anda tidak memiliki hak akses untuk memproses permintaan.');
            }
            // Pastikan hanya permintaan PENDING yang bisa diproses
            if ($permintaan->status !== 'PENDING') {
                return redirect()->route('permintaan.show', $permintaan->id)->with('warning', 'Permintaan ini sudah diproses.');
            }

            $permintaan->load('detail.barangMedis', 'userPeminta', 'lokasiPeminta');

            return view('permintaan.edit', compact('permintaan'));
        }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, PermintaanBarang $permintaan)
        {
            if (!Auth::user()->hasRole('PENGADAAN')) {
                abort(403, 'Anda tidak memiliki hak akses.');
            }

            $request->validate([
                'detail.*.jumlah_disetujui' => 'nullable|integer|min:0',
                'action' => 'required|in:APPROVED,REJECTED'
            ]);

            DB::beginTransaction();
            try {
                // Update jumlah disetujui untuk setiap item detail
                if($request->has('detail')) {
                    foreach($request->detail as $itemData) {
                        DB::table('detail_permintaan_barang')
                            ->where('id', $itemData['id'])
                            ->update(['jumlah_disetujui' => $itemData['jumlah_disetujui'] ?? null]);
                    }
                }

                // Update status permintaan utama
                $permintaan->status = $request->action;
                $permintaan->save();

                DB::commit();

                return redirect()->route('permintaan.index')->with('success', 'Permintaan berhasil diproses dan status telah diubah menjadi ' . $request->action);

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PermintaanBarang $permintaanBarang)
    {
        // Untuk menghapus data permintaan
        // (Akan dibuat nanti)
    }

    /**
     * [BARU] Untuk Dokter mengonfirmasi penerimaan barang.
     */
    public function konfirmasiPenerimaan(PermintaanBarang $permintaan)
    {
        // Validasi: hanya permintaan yang sedang diproses ('PROCESSING') yang bisa dikonfirmasi
        if ($permintaan->status !== 'PROCESSING') {
            return redirect()->back()->with('error', 'Hanya permintaan yang berstatus SEDANG DIPROSES yang dapat dikonfirmasi.');
        }

        try {
            DB::transaction(function () use ($permintaan) {
                $lokasiTujuan = $permintaan->id_lokasi_peminta;

                // Ambil semua pending stok masuk untuk permintaan ini
                $pendingStoks = PendingStokMasuk::where('id_permintaan', $permintaan->id)->get();

                if ($pendingStoks->isEmpty()) {
                    throw new \Exception('Tidak ada data barang masuk yang dapat dikonfirmasi.');
                }

                // Loop melalui setiap pending stok masuk
                foreach ($pendingStoks as $pending) {
                    // 1. Tambah stok di lokasi tujuan (lokasi dokter)
                    $stokTujuan = StokBarang::firstOrCreate(
                        ['id_barang' => $pending->id_barang, 'id_lokasi' => $lokasiTujuan],
                        ['jumlah' => 0]
                    );

                    $stokSebelum = $stokTujuan->jumlah;
                    $jumlahSatuanTerkecil = $pending->total_satuan_terkecil;
                    $stokTujuan->increment('jumlah', $jumlahSatuanTerkecil);

                    // 2. Catat riwayat penambahan stok
                    StokHistory::create([
                        'id_barang' => $pending->id_barang,
                        'id_lokasi' => $lokasiTujuan,
                        'perubahan' => $jumlahSatuanTerkecil,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stokTujuan->jumlah,
                        'keterangan' => 'Penerimaan barang dari permintaan ' . $permintaan->kode_permintaan,
                        'tanggal_transaksi' => $pending->tanggal_masuk,
                        'expired_at' => $pending->expired_at,
                        'jumlah_kemasan' => $pending->jumlah_kemasan,
                        'isi_per_kemasan' => $pending->isi_per_kemasan,
                        'satuan_kemasan' => $pending->satuan_kemasan,
                        'user_id' => Auth::id(),
                    ]);

                    // 3. Hapus data pending setelah diproses
                    $pending->delete();
                }

                // 4. Update status permintaan menjadi 'COMPLETED' (DITERIMA/SELESAI)
                $permintaan->update(['status' => 'COMPLETED']);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses penerimaan: ' . $e->getMessage());
        }

        return redirect()->route('permintaan.show', $permintaan->id)->with('success', 'Permintaan barang telah berhasil diselesaikan.');
    }


    /**
     * Dokter mengajukan retur untuk barang yang sudah didistribusikan pengadaan.
     */
    public function ajukanRetur(Request $request, PermintaanBarang $permintaan)
    {
        if (!Auth::user()->hasRole('DOKTER')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengajukan retur.');
        }

        if ($permintaan->id_lokasi_peminta !== Auth::user()->id_lokasi) {
            abort(403, 'Anda hanya dapat mengajukan retur untuk permintaan di lokasi Anda.');
        }

        if ($permintaan->status !== 'PROCESSING') {
            return redirect()->back()->with('error', 'Retur hanya dapat diajukan untuk permintaan yang sudah didistribusikan dan belum dikonfirmasi diterima.');
        }

        $request->validate([
            'alasan_retur' => 'required|string|max:1000',
        ], [
            'alasan_retur.required' => 'Alasan retur wajib diisi.',
        ]);

        $catatanRetur = trim((string) $request->alasan_retur);
        $catatanLama = trim((string) ($permintaan->catatan ?? ''));
        $urutanRetur = substr_count($catatanLama, '[RETUR ') + 1;
        $catatanBaru = trim($catatanLama . "\n\n[RETUR #{$urutanRetur} " . now()->format('d/m/Y H:i') . '] ' . $catatanRetur);

        $permintaan->update([
            'status' => 'RETUR',
            'catatan' => $catatanBaru,
        ]);

        return redirect()->route('permintaan.show', $permintaan->id)->with('success', 'Pengajuan retur berhasil dikirim ke pengadaan. Jika barang pengganti masih belum sesuai, retur dapat diajukan kembali sampai permintaan benar-benar sesuai.');
    }

    /**
     * Pengadaan memproses permintaan retur agar barang dapat diinput ulang.
     */
    public function prosesRetur(PermintaanBarang $permintaan)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'Anda tidak memiliki hak akses untuk memproses retur.');
        }

        if ($permintaan->status !== 'RETUR') {
            return redirect()->back()->with('error', 'Hanya permintaan berstatus RETUR yang dapat diproses ulang.');
        }

        DB::transaction(function () use ($permintaan) {
            // Hapus pending stok dari pengiriman yang diretur. Jika dokter retur lagi
            // setelah input ulang berikutnya, siklus ini bisa diulang dari status RETUR.
            PendingStokMasuk::where('id_permintaan', $permintaan->id)->delete();

            $catatanLama = trim((string) ($permintaan->catatan ?? ''));
            $urutanProsesRetur = substr_count($catatanLama, '[PROSES RETUR ') + 1;
            $catatanBaru = trim($catatanLama . "\n\n[PROSES RETUR #{$urutanProsesRetur} " . now()->format('d/m/Y H:i') . '] Pengadaan membuka ulang permintaan untuk input barang masuk pengganti.');

            $permintaan->update([
                'status' => 'APPROVED',
                'catatan' => $catatanBaru,
            ]);
        });

        return redirect()
            ->route('barang-masuk.create', ['request_id' => $permintaan->id])
            ->with('success', 'Retur diproses. Silakan input ulang barang masuk pengganti. Setelah semua item diinput, dokter akan konfirmasi kembali; jika masih belum sesuai, dokter dapat mengajukan retur lagi.');
    }

    /**
     * Generate PDF untuk rincian obat yang diminta
     */
    public function printPdf(PermintaanBarang $permintaan)
    {
        if (!Auth::user()->hasRole('PENGADAAN')) {
            abort(403, 'PDF permintaan hanya dapat dicetak oleh staf pengadaan.');
        }

        // PDF dicetak saat barang sudah diinput pengadaan dan sedang menunggu konfirmasi dokter.
        if ($permintaan->status !== 'PROCESSING') {
            return redirect()->back()->with('error', 'PDF hanya dapat dicetak saat permintaan menunggu konfirmasi dokter.');
        }

        // Load pending stok untuk tanggal masuk dan expired barang yang baru diinput pengadaan.
        $permintaan->load([
            'detail.barangMedis',
            'detail.pendingStokMasuks' => function($query) {
                $query->orderBy('tanggal_masuk', 'desc');
            },
            'userPeminta',
            'lokasiPeminta'
        ]);

        $pdf = Pdf::loadView('permintaan.pdf-rincian', compact('permintaan'));
        
        $filename = 'Rincian_Permintaan_' . $permintaan->kode_permintaan . '.pdf';
        
        return $pdf->download($filename);
    }
}
