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

class RekamMedisController extends Controller
{
    /**
     * Menampilkan form untuk membuat rekam medis baru.
     */
    public function create(User $user): View
    {
    $lokasiId = Auth::user()->id_lokasi;
        $obat = BarangMedis::whereHas('stok', fn($q) =>
                    $q->where('id_lokasi', $lokasiId)
                      ->where('jumlah', '>', 0))
                ->with(['stok' => fn($q) => $q->where('id_lokasi', $lokasiId)])
                ->orderBy('nama_obat')
                ->get();

        return view('rekam-medis.create', compact('user', 'obat'));
    }

    /**
     * Menyimpan data rekam medis baru ke database.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        // ... (kode store Anda tidak perlu diubah)
        $validated = $request->validate([
            'tanggal_kunjungan' => ['required', 'date'],
            'anamnesa' => ['nullable', 'string'],
            'terapi' => ['nullable', 'string'],
            'diagnosa' => ['nullable', 'array'],
            'diagnosa.*.kode_penyakit' => ['required_with:diagnosa', 'string', 'exists:daftar_penyakit,ICD10'],
            'obat' => ['nullable', 'array'],
            'obat.*.id_obat' => ['required_with:obat', 'integer', 'exists:barang_medis,id_obat'],
            'obat.*.kuantitas' => ['required_with:obat', 'integer', 'min:1'],
            'nama_sa' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin_sa' => ['nullable', 'string', 'max:20'],
        ]);

        DB::beginTransaction();

        try {
            $rekamMedis = RekamMedis::create([
                'nip_pasien' => $user->nip,
                'id_dokter'      => Auth::id(),
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'anamnesa' => $validated['anamnesa'],
                'terapi' => $validated['terapi'],
                'nama_sa' => $validated['nama_sa'],
                'jenis_kelamin_sa' => $validated['jenis_kelamin_sa'],
            ]);

            if (!empty($validated['diagnosa'])) {
                foreach ($validated['diagnosa'] as $diag) {
                    if (!empty($diag['kode_penyakit'])) {
                        $rekamMedis->detailDiagnosa()->create([
                            'ICD10' => $diag['kode_penyakit'],
                        ]);
                    }
                }
            }

            if (!empty($validated['obat'])) {
                $idLokasiDokter = Auth::user()->id_lokasi;

                foreach ($validated['obat'] as $resep) {
                    if (!empty($resep['id_obat'])) {
                        $id_obat = $resep['id_obat'];
                        $kuantitas = $resep['kuantitas'];

                        $stok = StokBarang::where('id_barang', $id_obat)
                                          ->where('id_lokasi', $idLokasiDokter)
                                          ->first();

                        if (!$stok || $stok->jumlah < $kuantitas) {
                            DB::rollBack();
                            $namaObat = BarangMedis::find($id_obat)->nama_obat ?? 'Obat';
                            return redirect()->back()->withInput()->with('error', "Stok untuk {$namaObat} tidak mencukupi. Stok tersedia: " . ($stok->jumlah ?? 0));
                        }

                        $rekamMedis->resepObat()->create([
                            'id_obat' => $id_obat,
                            'jumlah' => $kuantitas,
                            'aturan_pakai' => $resep['aturan_pakai'] ?? 'Aturan pakai belum diisi',
                        ]);

                        $stok->decrement('jumlah', $kuantitas);
                    }
                }
            }

            DB::commit();

            return redirect()->route('pasien.show', $user->nip)->with('success', 'Rekam medis berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Mencari nama penyakit berdasarkan kode ICD-10 untuk API autocomplete.
     */
    public function findPenyakit($icd10)
    {
        $penyakit = DaftarPenyakit::where('ICD10', 'LIKE', "%{$icd10}%")->first();

        if ($penyakit) {
            return response()->json([
                'success' => true,
                'nama_penyakit' => $penyakit->nama_penyakit,
                'kode_penyakit' => $penyakit->ICD10
            ]);
        }

        return response()->json([
            'success' => false,
            'nama_penyakit' => 'Kode ICD-10 tidak ditemukan.'
        ], 404);
    }
}
