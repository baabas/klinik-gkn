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
     * [DIUBAH] Menggunakan $pasien sebagai nama parameter.
     */
    public function create(User $pasien): View
    {
        $user = $pasien;
        return view('checkup.create', compact('user'));
    }

    /**
     * [DIUBAH] Menggunakan $pasien sebagai nama parameter.
     */
    public function store(Request $request, User $pasien): RedirectResponse
    {
        $user = $pasien;
        $validated = $request->validate([
            'tanggal_pemeriksaan' => ['required', 'date'],
            'tekanan_darah' => ['nullable', 'string', 'max:20'],
            'gula_darah' => ['nullable', 'string', 'max:20'],
            'kolesterol' => ['nullable', 'string', 'max:20'],
            'asam_urat' => ['nullable', 'string', 'max:20'],
            'berat_badan' => ['nullable', 'string', 'max:20'],
            'tinggi_badan' => ['nullable', 'string', 'max:20'],
            'indeks_massa_tubuh' => ['nullable', 'string', 'max:20'],
            'lingkar_perut' => ['nullable', 'string', 'max:20'],
            'nama_sa' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin_sa' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['nip_pasien'] = $user->nip;
        $validated['nik_pasien'] = $user->nik;
        $validated['id_dokter'] = Auth::id();

        Checkup::create($validated);

        $redirectRoute = $user->nip ? route('pasien.show', $user->nip) : route('pasien.show_non_karyawan', $user->nik);
        return redirect($redirectRoute)->with('success', 'Data check-up berhasil disimpan.');
    }
}