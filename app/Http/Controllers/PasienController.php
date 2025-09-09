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

        $pasien = User::whereHas('roles', function ($query) {
            $query->where('name', 'PASIEN');
        })
        ->when($search, function ($query, $search) {
            return $query->where('nama_karyawan', 'like', "%{$search}%")
                         ->orWhere('nip', 'like', "%{$search}%");
        })
        ->with('karyawan')
        ->latest()
        ->paginate(10);

        return view('pasien.index', compact('pasien'));
    }

    // --- PERBAIKAN UTAMA DI SINI ---
    public function show(User $user)
    {
        // 1. Ambil data detail karyawan melalui relasi yang ada di model User
        $karyawan = $user->karyawan;
        
        // 2. Kirim kedua variabel ('user' dan 'karyawan') ke view
        return view('pasien.show', compact('user', 'karyawan'));
    }
    // --------------------------------

    public function myCard()
    {
        $user = Auth::user();
        return view('pasien.my-card', compact('user'));
    }
}