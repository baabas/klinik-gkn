<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\BarangKemasan;
use App\Models\LokasiKlinik;
use App\Models\StokBarang;
use App\Models\StokHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

        $barang = BarangMedis::with(['kemasanBarang' => function ($query) {
                $query->orderByDesc('is_default')->orderBy('nama_kemasan');
            }])
            ->orderBy('nama_obat')
            ->get();
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
            'kemasan_id' => [
                'required',
                Rule::exists('barang_kemasan', 'id')->where(function ($query) use ($request) {
                    return $query->where('barang_id', $request->input('id_barang'));
                }),
            ],
            'jumlah_kemasan' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'expired_at' => 'nullable|date|after_or_equal:tanggal_transaksi',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $barang = BarangMedis::with('kemasanBarang')
                ->lockForUpdate()
                ->find($validated['id_barang']);

            if (!$barang) {
                throw ValidationException::withMessages([
                    'id_barang' => 'Barang medis tidak ditemukan.',
                ]);
            }

            $kemasan = $barang->kemasanBarang->firstWhere('id', (int) $validated['kemasan_id']);

            if (!$kemasan instanceof BarangKemasan) {
                throw ValidationException::withMessages([
                    'kemasan_id' => 'Jenis kemasan tidak valid untuk barang ini.',
                ]);
            }

            $isiPerKemasan = (int) $kemasan->isi_per_kemasan;
            $jumlahKemasan = (int) $validated['jumlah_kemasan'];
            $totalUnitDasar = $jumlahKemasan * $isiPerKemasan;

            $stokSebelumBarang = (int) $barang->stok;
            $stokSesudahBarang = $stokSebelumBarang + $totalUnitDasar;

            $stokLokasi = StokBarang::where('id_barang', $barang->id_obat)
                ->where('id_lokasi', $validated['id_lokasi'])
                ->lockForUpdate()
                ->first();

            if (!$stokLokasi) {
                $stokLokasi = StokBarang::create([
                    'id_barang' => $barang->id_obat,
                    'id_lokasi' => $validated['id_lokasi'],
                    'jumlah' => 0,
                ]);
            }

            $stokLokasi->increment('jumlah', $totalUnitDasar);

            $barang->update([
                'stok' => $stokSesudahBarang,
            ]);

            StokHistory::create([
                'id_barang' => $barang->id_obat,
                'id_lokasi' => $validated['id_lokasi'],
                'perubahan' => $totalUnitDasar,
                'stok_sebelum' => $stokSebelumBarang,
                'stok_sesudah' => $stokSesudahBarang,
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'jumlah_kemasan' => $jumlahKemasan,
                'isi_per_kemasan' => $isiPerKemasan,
                'satuan_kemasan' => $kemasan->nama_kemasan,
                'kemasan_id' => $kemasan->id,
                'base_unit' => $barang->satuan_dasar,
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
