<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Obat;
use App\Models\DaftarPenyakit;
use App\Models\RekamMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RekamMedisController extends Controller
{
    /**
     * Menampilkan form untuk membuat rekam medis baru.
     */
    public function create(User $user): View
    {
        $daftar_penyakit = DaftarPenyakit::orderBy('nama_penyakit')->get();
        $daftar_obat = Obat::orderBy('nama_obat')->get();

        return view('rekam-medis.create', compact('user', 'daftar_penyakit', 'daftar_obat'));
    }

    /**
     * Menyimpan rekam medis baru ke database.
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'riwayat_sakit' => ['nullable', 'string'],
            'pengobatan' => ['nullable', 'string'],
            'diagnosa_kode' => ['nullable', 'array'],
            'diagnosa_kode.*' => ['exists:daftar_penyakit,kode_penyakit'],
            'obat_id' => ['nullable', 'array'],
            'obat_id.*' => ['exists:obat,id_obat'],
            'kuantitas' => ['nullable', 'array'],
            'kuantitas.*' => ['required_with:obat_id.*', 'integer', 'min:1'],
            'nama_sa' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin_sa' => ['nullable', 'string'],
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $rekamMedis = RekamMedis::create([
                    'id_pasien' => $user->id,
                    'nip' => Auth::user()->nip,
                    'tanggal_kunjungan' => now(),
                    'riwayat_sakit' => $request->riwayat_sakit,
                    'pengobatan' => $request->pengobatan,
                    'nama_sa' => $request->nama_sa,
                    'jenis_kelamin_sa' => $request->jenis_kelamin_sa,
                ]);

                if ($request->has('diagnosa_kode')) {
                    foreach ($request->diagnosa_kode as $kode_penyakit) {
                        $rekamMedis->detailDiagnosa()->create([
                            'kode_penyakit' => $kode_penyakit,
                        ]);
                    }
                }

                if ($request->has('obat_id')) {
                    foreach ($request->obat_id as $index => $id_obat) {
                        $kuantitas = (int)$request->kuantitas[$index];
                        $obat = Obat::find($id_obat);

                        if ($obat && $obat->stok_saat_ini >= $kuantitas) {
                            $rekamMedis->resepObat()->create([
                                'id_obat' => $id_obat,
                                'kuantitas' => $kuantitas,
                            ]);
                            $obat->decrement('stok_saat_ini', $kuantitas);
                        } else {
                            throw new \Exception("Stok untuk obat {$obat->nama_obat} tidak mencukupi.");
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan rekam medis: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('pasien.show', $user->nip)->with('success', 'Rekam medis berhasil disimpan.');
    }
}
