<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use App\Models\DetailPermintaanBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\StokBarang;
use App\Models\StokHistory;

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
            'barang_baru.*.tipe' => 'required_with:barang_baru|in:OBAT,ALKES',
            'barang_baru.*.satuan' => 'required_with:barang_baru|string|max:100',
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
                            'tipe_barang_baru' => $item['tipe'],
                            'satuan_barang_baru' => $item['satuan'],
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
                'detail.*.tipe_jumlah_disetujui' => 'nullable|in:SATUAN,KEMASAN',
                'action' => 'required|in:APPROVED,REJECTED'
            ]);

            DB::beginTransaction();
            try {
                // Update jumlah disetujui untuk setiap item detail
                if($request->has('detail')) {
                    foreach($request->detail as $itemData) {
                        $detailModel = DetailPermintaanBarang::with('barangMedis')->find($itemData['id'] ?? null);

                        if (!$detailModel) {
                            continue;
                        }

                        $jumlahInput = $itemData['jumlah_disetujui'] ?? null;
                        $jumlahInput = ($jumlahInput === '' || $jumlahInput === null) ? null : (int) $jumlahInput;

                        $tipeInput = $itemData['tipe_jumlah_disetujui'] ?? 'SATUAN';
                        if (!in_array($tipeInput, ['SATUAN', 'KEMASAN'], true)) {
                            $tipeInput = 'SATUAN';
                        }

                        $jumlahSatuan = $jumlahInput;
                        $jumlahKemasan = null;

                        if ($jumlahInput !== null && $tipeInput === 'KEMASAN' && $detailModel->barangMedis) {
                            $isiPerKemasan = max(1, (int) ($detailModel->barangMedis->isi_per_kemasan ?? 1));
                            $jumlahKemasan = $jumlahInput;
                            $jumlahSatuan = $jumlahKemasan * $isiPerKemasan;
                        }

                        $detailModel->update([
                            'jumlah_disetujui' => $jumlahSatuan,
                            'tipe_jumlah_disetujui' => $tipeInput,
                            'jumlah_kemasan_disetujui' => $jumlahKemasan,
                        ]);
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
        // Validasi: hanya permintaan yang disetujui ('APPROVED') yang bisa diproses
        if ($permintaan->status !== 'APPROVED') {
            return redirect()->back()->with('error', 'Hanya permintaan yang berstatus DISETUJUI yang dapat dikonfirmasi.');
        }

        $permintaan->loadMissing('detail.barangMedis');

        try {
            DB::transaction(function () use ($permintaan) {
                $lokasiTujuan = $permintaan->id_lokasi_peminta;

                // Loop melalui setiap item dalam detail permintaan
                foreach ($permintaan->detail as $detail) {
                    // Hanya proses item yang memiliki id_barang dan jumlah disetujui > 0
                    if ($detail->id_barang && $detail->jumlah_disetujui > 0) {
                        $barangId = $detail->id_barang;
                        $jumlahDiterima = (int) $detail->jumlah_disetujui;
                        $keteranganTambahan = '';

                        if ($detail->tipe_jumlah_disetujui === 'KEMASAN') {
                            $jumlahKemasan = $detail->jumlah_kemasan_disetujui ?? $detail->jumlah_disetujui;
                            $barangMedis = $detail->barangMedis;
                            $isiPerKemasan = $barangMedis && $barangMedis->isi_per_kemasan ? (int) $barangMedis->isi_per_kemasan : 1;
                            $isiPerKemasan = max(1, $isiPerKemasan);
                            $jumlahDiterima = (int) $jumlahKemasan * $isiPerKemasan;

                            if ($jumlahKemasan) {
                                $satuanKemasan = $barangMedis->satuan_kemasan ?? 'kemasan';
                                $satuanTerkecil = $barangMedis->satuan_terkecil ?? $barangMedis->satuan;
                                $keteranganTambahan = sprintf(' (%d %s x %d %s)', $jumlahKemasan, $satuanKemasan, $isiPerKemasan, $satuanTerkecil);
                            }
                        }
                        // 1. Tambah stok di lokasi tujuan (lokasi dokter)
                        $stokTujuan = StokBarang::firstOrCreate(
                            ['id_barang' => $barangId, 'id_lokasi' => $lokasiTujuan],
                            ['jumlah' => 0]
                        );

                        $stokSebelum = $stokTujuan->jumlah;
                        $stokTujuan->increment('jumlah', $jumlahDiterima);

                        // 2. Catat riwayat penambahan stok
                        StokHistory::create([
                            'id_barang' => $barangId,
                            'id_lokasi' => $lokasiTujuan,
                            'perubahan' => $jumlahDiterima,
                            'stok_sebelum' => $stokSebelum,
                            'stok_sesudah' => $stokTujuan->jumlah,
                            'keterangan' => 'Penerimaan barang dari permintaan ' . $permintaan->kode_permintaan . $keteranganTambahan,
                            'user_id' => Auth::id(),
                        ]);
                    }
                }

                // 3. Update status permintaan menjadi 'COMPLETED' (DITERIMA/SELESAI)
                $permintaan->update(['status' => 'COMPLETED']);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses penerimaan: ' . $e->getMessage());
        }

        return redirect()->route('permintaan.show', $permintaan->id)->with('success', 'Permintaan barang telah berhasil diselesaikan.');
    }
}
