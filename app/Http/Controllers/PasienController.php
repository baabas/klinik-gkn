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

    public function show($identifier)
    {
        $pasien = User::whereHas('roles', fn($q) => $q->where('name', 'PASIEN'))
            ->where(function ($query) use ($identifier) {
                $query->where('nip', $identifier)
                      ->orWhere('nik', $identifier);
            })
            ->first();

        if (!$pasien) {
            abort(404, 'Pasien tidak ditemukan.');
        }

        // PERBAIKAN UTAMA: Hapus 'rekamMedis' dan 'checkups' dari sini.
        // Accessor di Model akan menanganinya secara otomatis.
        $pasien->load('karyawan', 'nonKaryawan');

        return view('pasien.show', ['pasien' => $pasien]);
    }

    public function myCard()
    {
        $user = Auth::user();
        return view('pasien.my-card', compact('user'));
    }
}