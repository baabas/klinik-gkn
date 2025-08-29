<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;


class PasienController extends Controller
{
    /**
     * Menampilkan daftar semua pasien (untuk Admin/Dokter).
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $pasien = User::whereHas('roles', function ($query) {
                $query->where('name', 'PASIEN');
            })
            ->when($search, function ($query, $search) {
                return $query->where('nama_karyawan', 'like', "%{$search}%")
                             ->orWhere('nip', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('pasien.index', compact('pasien'));
    }

    /**
     * Menampilkan kartu detail pasien (untuk Admin/Dokter).
     */
    public function show(User $user): View
    {
        $karyawan = Karyawan::find($user->nip);

        $user->load(['rekamMedis' => function ($query) {
            $query->with(['detailDiagnosa.penyakit', 'resepObat.obat'])
                  ->orderBy('tanggal_kunjungan', 'desc');
        }]);

        return view('pasien.show', compact('user', 'karyawan'));
    }

    /**
     * Menampilkan kartu pasien untuk pengguna yang sedang login.
     */
    public function myCard(): View
    {
        $user = Auth::user();
        $karyawan = Karyawan::find($user->nip);

        $user->load(['rekamMedis' => function ($query) {
            $query->with(['detailDiagnosa.penyakit', 'resepObat.obat'])
                  ->orderBy('tanggal_kunjungan', 'desc');
        }]);

        return view('pasien.show', compact('user', 'karyawan'));
    }
}
