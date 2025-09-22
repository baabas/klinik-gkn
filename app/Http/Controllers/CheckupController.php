<?php

namespace App\Http\Controllers;

use App\Models\Checkup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CheckupController extends Controller
{
    /**
     * PERBAIKAN: Menampilkan form checkup berdasarkan identifier (NIP/NIK).
     *
     * @param string $identifier
     * @return View
     */
    public function create(string $identifier): View
    {
        // Cari pasien secara manual berdasarkan NIP atau NIK
        $pasien = User::where('nip', $identifier)->orWhere('nik', $identifier)->firstOrFail();
        
        // Mengirim variabel 'pasien' ke view
        return view('checkup.create', compact('pasien'));
    }

    /**
     * PERBAIKAN: Menyimpan data checkup berdasarkan identifier (NIP/NIK).
     *
     * @param Request $request
     * @param string $identifier
     * @return RedirectResponse
     */
    public function store(Request $request, string $identifier): RedirectResponse
    {
        // Cari pasien yang akan di-checkup
        $pasien = User::where('nip', $identifier)->orWhere('nik', $identifier)->firstOrFail();

        // Validasi input dari form
        $validated = $request->validate([
            'tanggal_pemeriksaan' => ['required', 'date'],
            'tekanan_darah' => ['nullable', 'string', 'max:20'],
            'gula_darah' => ['nullable', 'string', 'max:20'],
            'kolesterol' => ['nullable', 'string', 'max:20'],
            'asam_urat' => ['nullable', 'string', 'max:20'],
            'berat_badan' => ['nullable', 'numeric'],
            'tinggi_badan' => ['nullable', 'numeric'],
            'lingkar_perut' => ['nullable', 'numeric'],
            'nama_sa' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin_sa' => ['nullable', 'string', 'max:20'],
        ]);

        // Hitung Indeks Massa Tubuh (IMT) secara otomatis jika data tersedia
        $imt = null;
        if ($request->filled('berat_badan') && $request->filled('tinggi_badan') && $request->tinggi_badan > 0) {
            $tinggi_meter = $request->tinggi_badan / 100;
            $imt = round($request->berat_badan / ($tinggi_meter * $tinggi_meter), 2);
        }

        // Menyiapkan data untuk disimpan
        $checkupData = $validated;
        $checkupData['indeks_massa_tubuh'] = $imt;
        $checkupData['nip_pasien'] = $pasien->nip; // Akan berisi NIP jika pasien karyawan, atau null
        $checkupData['nik_pasien'] = $pasien->nik; // Akan berisi NIK jika pasien non-karyawan, atau null
        $checkupData['id_dokter'] = Auth::id();

        // Simpan data ke database
        Checkup::create($checkupData);

        // Redirect kembali ke halaman detail pasien yang benar dengan identifier yang sama
        return redirect()->route('pasien.show', $identifier)->with('success', 'Data check-up berhasil disimpan.');
    }
}