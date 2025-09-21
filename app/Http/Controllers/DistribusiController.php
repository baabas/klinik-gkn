<?php

namespace App\Http\Controllers;

use App\Models\LokasiKlinik;
use App\Models\PermintaanBarang;
use App\Models\PermintaanBarangDetail;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistribusiController extends Controller
{
    public function index()
    {
        $klinikSiapKirim = LokasiKlinik::whereHas('permintaanBarang', function ($query) {
            $query->where('status', PermintaanBarang::STATUS_DISETUJUI);
        })
        ->withCount(['permintaanBarang' => function ($query) {
            $query->where('status', PermintaanBarang::STATUS_DISETUJUI);
        }])
        ->where('nama_lokasi', '!=', 'Gudang Pusat') // Abaikan gudang pusat
        ->get();

        return view('distribusi.index', compact('klinikSiapKirim'));
    }

    /**
     * Menampilkan form untuk membuat pengiriman ke klinik tertentu.
     */
    public function create($id_lokasi_tujuan)
    {
        $klinik = LokasiKlinik::findOrFail($id_lokasi_tujuan);
        $gudangPusat = LokasiKlinik::where('nama_lokasi', 'Gudang Pusat')->firstOrFail();

        // Ambil semua item dari permintaan yg statusnya APPROVED untuk klinik ini,
        // lalu kelompokkan berdasarkan barang yang sama dan jumlahkan totalnya.
        $barangUntukDikirim = PermintaanBarangDetail::select('barang_id', DB::raw('SUM(total_unit) as total_unit'))
            ->whereNotNull('barang_id')
            ->whereHas('permintaan', function ($query) use ($id_lokasi_tujuan) {
                $query->where('status', PermintaanBarang::STATUS_DISETUJUI)
                    ->where('lokasi_id', $id_lokasi_tujuan);
            })
            ->groupBy('barang_id')
            ->with('barangMedis') // Ambil info detail barang
            ->get();

        // Cek stok setiap barang di gudang pusat
        foreach ($barangUntukDikirim as $item) {
            $stok = StokBarang::where('id_lokasi', $gudangPusat->id)->where('id_barang', $item->barang_id)->first();
            $item->stok_gudang = $stok ? $stok->jumlah : 0;
        }

        return view('distribusi.create', compact('klinik', 'gudangPusat', 'barangUntukDikirim'));
    }

    /**
     * Menyimpan data pengiriman dan mengupdate stok.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_lokasi_sumber' => 'required|exists:lokasi_klinik,id',
            'id_lokasi_tujuan' => 'required|exists:lokasi_klinik,id',
            'tanggal_distribusi' => 'required|date',
            'barang' => 'required|array|min:1',
            'barang.*.id_barang' => 'required|exists:barang_medis,id_obat',
            'barang.*.jumlah_dikirim' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Stok Barang
            foreach ($request->barang as $item) {
                $jumlahDikirim = (int) $item['jumlah_dikirim'];

                if ($jumlahDikirim > 0) {
                    // Kurangi stok dari Gudang Pusat
                    StokBarang::where('id_lokasi', $request->id_lokasi_sumber)
                        ->where('id_barang', $item['id_barang'])
                        ->decrement('jumlah', $jumlahDikirim);

                    // Tambah/Update stok di Klinik Tujuan
                    StokBarang::updateOrCreate(
                        ['id_lokasi' => $request->id_lokasi_tujuan, 'id_barang' => $item['id_barang']],
                        ['jumlah' => DB::raw("jumlah + {$jumlahDikirim}")]
                    );
                }
            }

            // 2. Update Status Semua Permintaan Terkait menjadi "COMPLETED"
            PermintaanBarang::where('lokasi_id', $request->id_lokasi_tujuan)
                ->where('status', PermintaanBarang::STATUS_DISETUJUI)
                ->update(['status' => PermintaanBarang::STATUS_DIPENUHI]);

            DB::commit();
            return redirect()->route('distribusi.index')->with('success', 'Distribusi barang berhasil diproses dan stok telah diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
