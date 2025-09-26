<?php

namespace App\Http\Controllers;

use App\Models\RekamMedis;
use App\Models\User;
use App\Models\DaftarPenyakit;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\BarangMedis;
use Carbon\Carbon;

class RekamMedisController extends Controller
{
    public function create(User $pasien): View
    {
        $user = $pasien;
        $lokasiId = Auth::user()->id_lokasi;
        $obat = BarangMedis::whereHas('stok', fn($q) => $q->where('id_lokasi', $lokasiId)->where('jumlah', '>', 0))
                ->with(['stok' => fn($q) => $q->where('id_lokasi', $lokasiId)])
                ->orderBy('nama_obat')
                ->get();

        return view('rekam-medis.create', compact('user', 'obat'));
    }

    public function store(Request $request, User $pasien): RedirectResponse
    {
        $user = $pasien;
        $validated = $request->validate([
            'tanggal_kunjungan' => ['required', 'date'],
            'anamnesa' => ['nullable', 'string'],
            'terapi' => ['nullable', 'string'],
            'diagnosa' => ['nullable', 'array'],
            'diagnosa.*.kode_penyakit' => ['required_with:diagnosa', 'string', 'exists:daftar_penyakit,ICD10'],
            'obat' => ['nullable', 'array'],
            'obat.*.id_obat' => ['required_with:obat', 'integer', 'exists:barang_medis,id_obat'],
            'obat.*.jumlah' => ['required_with:obat', 'integer', 'min:1'],
            // [DIHAPUS] Validasi untuk aturan_pakai dihapus
            // 'obat.*.aturan_pakai' => ['nullable', 'string'], 
            'nama_sa' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin_sa' => ['nullable', 'string', 'max:20'],
        ]);

        DB::beginTransaction();
        try {
            $tanggalKunjungan = Carbon::parse($validated['tanggal_kunjungan'], config('app.timezone'));
            $rekamMedis = RekamMedis::create([
                'nip_pasien' => $user->nip,
                'nik_pasien' => $user->nik,
                'id_dokter' => Auth::id(),
                'tanggal_kunjungan' => $tanggalKunjungan,
                'anamnesa' => $validated['anamnesa'] ?? null,
                'terapi' => $validated['terapi'] ?? null,
                'nama_sa' => $validated['nama_sa'] ?? null,
                'jenis_kelamin_sa' => $validated['jenis_kelamin_sa'] ?? null,
            ]);

            if (!empty($validated['diagnosa'])) {
                foreach ($validated['diagnosa'] as $diag) {
                    if (!empty($diag['kode_penyakit'])) {
                        $rekamMedis->detailDiagnosa()->create(['ICD10' => $diag['kode_penyakit']]);
                    }
                }
            }

            if (!empty($validated['obat'])) {
                $idLokasiDokter = Auth::user()->id_lokasi;
                foreach ($validated['obat'] as $resep) {
                    if (!empty($resep['id_obat']) && !empty($resep['jumlah'])) {
                        $stok = StokBarang::where('id_barang', $resep['id_obat'])->where('id_lokasi', $idLokasiDokter)->first();
                        if (!$stok || $stok->jumlah < $resep['jumlah']) {
                            DB::rollBack();
                            $namaObat = BarangMedis::find($resep['id_obat'])->nama_obat ?? 'Obat';
                            return redirect()->back()->withInput()->with('error', "Stok untuk {$namaObat} tidak mencukupi. Stok tersedia: " . ($stok->jumlah ?? 0));
                        }
                        $rekamMedis->resepObat()->create([
                            'id_obat' => $resep['id_obat'],
                            'jumlah' => $resep['jumlah'],
                        ]);
                        $stok->decrement('jumlah', $resep['jumlah']);
                    }
                }
            }

            DB::commit();
            $redirectRoute = $user->nip ? route('pasien.show', $user->nip) : route('pasien.show_non_karyawan', $user->nik);
            return redirect($redirectRoute)->with('success', 'Rekam medis berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function findPenyakit($icd10)
    {
        $penyakit = DaftarPenyakit::where('ICD10', 'LIKE', "%{$icd10}%")->first();
        if ($penyakit) {
            return response()->json(['success' => true, 'nama_penyakit' => $penyakit->nama_penyakit, 'kode_penyakit' => $penyakit->ICD10]);
        }
        return response()->json(['success' => false, 'nama_penyakit' => 'Kode ICD-10 tidak ditemukan.'], 404);
    }

    public function searchPenyakit(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json(['success' => false, 'data' => []]);
        }

        // Search by name or ICD10 code, limit to 3 results
        $penyakit = DaftarPenyakit::where(function($q) use ($query) {
                $q->where('nama_penyakit', 'LIKE', "%{$query}%")
                  ->orWhere('ICD10', 'LIKE', "%{$query}%");
            })
            ->limit(3)
            ->get(['ICD10 as kode_penyakit', 'nama_penyakit']);

        return response()->json([
            'success' => true,
            'data' => $penyakit
        ]);
    }

    public function searchObat(Request $request)
    {
        $query = $request->get('q');
        $lokasiId = Auth::user()->id_lokasi;
        
        if (strlen($query) < 2) {
            return response()->json(['success' => false, 'data' => []]);
        }

        // Search medicines by name or code, limit to 3 results, only with stock
        $obat = BarangMedis::whereHas('stok', function($q) use ($lokasiId) {
                $q->where('id_lokasi', $lokasiId)->where('jumlah', '>', 0);
            })
            ->where(function($q) use ($query) {
                $q->where('nama_obat', 'LIKE', "%{$query}%")
                  ->orWhere('kode_obat', 'LIKE', "%{$query}%");
            })
            ->with(['stok' => function($q) use ($lokasiId) {
                $q->where('id_lokasi', $lokasiId);
            }])
            ->limit(3)
            ->get(['id_obat', 'nama_obat', 'kode_obat'])
            ->map(function($item) {
                return [
                    'id_obat' => $item->id_obat,
                    'nama_obat' => $item->nama_obat,
                    'kode_obat' => $item->kode_obat,
                    'stok' => $item->stok->first()->jumlah ?? 0
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $obat
        ]);
    }
}