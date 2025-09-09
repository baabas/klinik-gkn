<?php

namespace App\Http\Controllers;

use App\Models\RekamMedis;
use App\Models\User;
use App\Models\DaftarPenyakit;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\BarangMedis;

class RekamMedisController extends Controller
{
    public function create(User $user): View
    {
        $penyakit = DaftarPenyakit::orderBy('nama_penyakit')->get();
        $obat = BarangMedis::where('tipe', 'OBAT')
                            ->orderBy('nama_obat')
                            ->get();

        return view('rekam-medis.create', compact('user', 'penyakit', 'obat'));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        // 1. Validasi semua input dari form
        $validated = $request->validate([
            'tanggal_kunjungan' => ['required', 'date'],
            'riwayat_sakit' => ['nullable', 'string'],
            'pengobatan' => ['nullable', 'string'],
            'diagnosa' => ['required', 'array', 'min:1'],
            'diagnosa.*' => ['exists:daftar_penyakit,kode_penyakit'],
            'obat' => ['nullable', 'array'],
            'obat.*' => ['exists:barang_medis,id_obat'],
            'kuantitas' => ['nullable', 'array'],
            'kuantitas.*' => ['required_with:obat.*', 'integer', 'min:1'],
            'nama_sa' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin_sa' => ['nullable', 'string', 'max:20'],
        ]);

        DB::beginTransaction();

        try {
            // 2. Simpan data rekam medis, TERMASUK 'nama_sa' dan 'jenis_kelamin_sa'
            $rekamMedis = RekamMedis::create([
                'id_pasien' => $user->id,
                'nip' => auth()->user()->nip,
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'riwayat_sakit' => $validated['riwayat_sakit'],
                'pengobatan' => $validated['pengobatan'],
                // ================== BAGIAN PENTING ADA DI SINI ==================
                'nama_sa' => $validated['nama_sa'],
                'jenis_kelamin_sa' => $validated['jenis_kelamin_sa'],
                // ===============================================================
            ]);

            // 3. Simpan detail diagnosa
            if (!empty($validated['diagnosa'])) {
                foreach ($validated['diagnosa'] as $kode_penyakit) {
                    $rekamMedis->detailDiagnosa()->create([
                        'kode_penyakit' => $kode_penyakit,
                    ]);
                }
            }
            
            // 4. Simpan resep obat dan kurangi stok
            if (!empty($validated['obat'])) {
                $idLokasiDokter = auth()->user()->id_lokasi;

                foreach ($validated['obat'] as $index => $id_obat) {
                    $kuantitas = $validated['kuantitas'][$index];

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
                        'kuantitas' => $kuantitas,
                    ]);

                    $stok->decrement('jumlah', $kuantitas);
                }
            }

            DB::commit();

            return redirect()->route('pasien.show', $user->nip)->with('success', 'Rekam medis berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}