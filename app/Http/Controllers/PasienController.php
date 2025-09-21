<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pasien = User::whereHas('roles', fn($q) => $q->where('name', 'PASIEN'))
            ->when($search, function ($query, $search) {
                return $query->where('nama_karyawan', 'like', "%{$search}%")
                             ->orWhere('nip', 'like', "%{$search}%")
                             ->orWhere('nik', 'like', "%{$search}%");
            })
            ->with('karyawan', 'nonKaryawan')
            ->latest()
            ->paginate(10);

        return view('pasien.index', compact('pasien'));
    }

    /**
     * [DIUBAH] Menggunakan $pasien sebagai nama parameter agar konsisten.
     */
    public function show(User $pasien)
    {
        $pasien->load('karyawan', 'rekamMedis', 'checkups');
        // Variabel yang dikirim ke view tetap 'user' agar view lama tidak rusak.
        return view('pasien.show', ['user' => $pasien]);
    }

    public function showNonKaryawan(User $pasien)
    {
        $pasien->load('nonKaryawan', 'rekamMedis', 'checkups');
        return view('pasien.show-non-karyawan', compact('pasien'));
    }

    public function myCard()
    {
        $user = Auth::user();
        return view('pasien.my-card', compact('user'));
    }
}