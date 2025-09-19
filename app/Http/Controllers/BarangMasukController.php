<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\StokBarang;
use App\Models\StokHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    /**
     * Tampilkan daftar riwayat barang masuk.
     */
    public function index(Request $request)
    {
        $entries = StokHistory::query()
            ->with(['barang.creator', 'lokasi', 'user.karyawan'])
            ->where('perubahan', '>', 0)
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
            'jumlah_kemasan' => 'required|integer|min:1',
            'satuan_kemasan' => 'nullable|string|max:50',
            'isi_per_kemasan' => 'required|integer|min:1',
            'expired_at' => 'nullable|date|after_or_equal:tanggal_masuk',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $totalSatuan = $validated['jumlah_kemasan'] * $validated['isi_per_kemasan'];

            $stok = StokBarang::firstOrCreate(
                [
                    'id_barang' => $validated['id_barang'],
                    'id_lokasi' => $validated['id_lokasi'],
                ],
                ['jumlah' => 0]
            );

            $stokSebelum = $stok->jumlah;
            $stok->increment('jumlah', $totalSatuan);

            StokHistory::create([
                'id_barang' => $validated['id_barang'],
                'id_lokasi' => $validated['id_lokasi'],
                'perubahan' => $totalSatuan,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stok->jumlah,
                'tanggal_transaksi' => $validated['tanggal_masuk'],
                'jumlah_kemasan' => $validated['jumlah_kemasan'],
                'isi_per_kemasan' => $validated['isi_per_kemasan'],
                'satuan_kemasan' => $validated['satuan_kemasan'] ?? null,
                'expired_at' => $validated['expired_at'] ?? null,
                'keterangan' => $validated['keterangan'] ?? 'Barang masuk',
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Data barang masuk berhasil disimpan dan stok diperbarui.');
    }
}
