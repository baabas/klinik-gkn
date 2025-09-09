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
     * Menampilkan form untuk membuat data check-up baru.
     */
    public function create(User $user): View
    {
        return view('checkup.create', compact('user'));
    }

    /**
     * Menyimpan data check-up baru ke database.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
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

        $validated['user_id'] = $user->id;

        Checkup::create($validated);

        return redirect()->route('pasien.show', $user->nip)->with('success', 'Data check-up berhasil disimpan.');
    }
}