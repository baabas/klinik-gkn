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

    public function show(User $user)
    {
        return view('pasien.show', compact('user'));
    }

    // --- PERUBAHAN UTAMA DI SINI ---
    /**
     * Menampilkan kartu pasien untuk user yang sedang login.
     */
    public function myCard()
    {
        $user = Auth::user();

        // Mengirim data user ke view yang sama dengan 'show',
        // tetapi kita akan menggunakan layout yang berbeda di dalam view tersebut.
        return view('pasien.my-card', compact('user'));
    }
}
