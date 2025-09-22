<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RekamMedis;
use App\Models\DaftarPenyakit;
use App\Models\BarangMedis;
use App\Models\DetailDiagnosa;
use App\Models\ResepObat;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Carbon\Carbon;

class RekamMedisController extends Controller
{
    /**
     * Menampilkan form untuk membuat rekam medis baru.
     */
    public function create(string $identifier)
    {
        $pasien = User::where('nip', $identifier)->orWhere('nik', $identifier)->firstOrFail();
        
        $lokasiId = Auth::user()->id_lokasi;
        
        // PERBAIKAN: Memastikan query menggunakan nama kolom yang benar dari database
        $obat = BarangMedis::where('tipe', 'OBAT') 
            ->whereHas('stok', function ($query) use ($lokasiId) {
                $query->where('id_lokasi', $lokasiId)->where('jumlah', '>', 0);
            })
            ->with(['stok' => function ($query) use ($lokasiId) {
                $query->where('id_lokasi', $lokasiId);
            }])
            ->orderBy('nama_obat') // Menggunakan 'nama_obat'
            ->get();
        
        return view('rekam-medis.create', compact('pasien', 'obat'));
    }
    
    // ... method 'store' dan 'findPenyakit' tetap sama ...
    
    public function store(Request $request, string $identifier)
    {
        $validated = $request->validate([
            'tanggal_kunjungan' => ['required', 'date'],
            'anamnesa' => ['nullable', 'string'],
            'terapi' => ['nullable', 'string'],
            'diagnosa' => ['nullable', 'array'],
            'diagnosa.*.kode_penyakit' => ['required_with:diagnosa', 'string', 'exists:daftar_penyakit,ICD10'],
            'obat' => ['nullable', 'array'],
            'obat.*.id_obat' => ['required_with:obat', 'integer', 'exists:barang_medis,id_obat'],
            'obat.*.jumlah' => ['required_with:obat', 'integer', 'min:1'],
            'nama_sa' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin_sa' => ['nullable', 'string', 'max:20'],
        ]);

        $pasien = User::where('nip', $identifier)->orWhere('nik', $identifier)->firstOrFail();

        DB::beginTransaction();
        try {
            $rekamMedis = RekamMedis::create([
                'nip_pasien' => $pasien->nip,
                'nik_pasien' => $pasien->nik,
                'id_dokter' => Auth::id(),
                'tanggal_kunjungan' => Carbon::parse($validated['tanggal_kunjungan']),
                'anamnesa' => $validated['anamnesa'] ?? null,
                'terapi' => $validated['terapi'] ?? null,
                'nama_sa' => $validated['nama_sa'] ?? null,
                'jenis_kelamin_sa' => $validated['jenis_kelamin_sa'] ?? null,
            ]);

            if (!empty($validated['diagnosa'])) {
                foreach ($validated['diagnosa'] as $diag) {
                    if (!empty($diag['kode_penyakit'])) {
                        $penyakit = DaftarPenyakit::where('ICD10', $diag['kode_penyakit'])->first();
                        if($penyakit) {
                             $rekamMedis->detailDiagnosa()->create([
                                'id_penyakit' => $penyakit->id,
                                'ICD10' => $penyakit->ICD10
                            ]);
                        }
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
            
            return redirect()->route('pasien.show', $identifier)->with('success', 'Rekam medis berhasil ditambahkan.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function findPenyakit($icd10)
    {
        $penyakit = DaftarPenyakit::where('ICD10', 'LIKE', "%{$icd10}%")
            ->orWhere('nama_penyakit', 'LIKE', "%{$icd10}%")
            ->limit(10)->get();

        if ($penyakit->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $penyakit]);
        }
        return response()->json(['success' => false, 'message' => 'Kode ICD-10 tidak ditemukan.'], 404);
    }
}