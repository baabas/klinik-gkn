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

        $pasien = User::where('akses', 'PASIEN')
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

    public function searchPasien(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $pasien = User::where('akses', 'PASIEN')
            ->where(function($q) use ($query) {
                $q->where('nama_karyawan', 'like', "%{$query}%")
                  ->orWhere('nip', 'like', "%{$query}%")
                  ->orWhere('nik', 'like', "%{$query}%");
            })
            ->with('karyawan', 'nonKaryawan')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->nip ?? $item->nik,
                    'nama' => $item->nama_karyawan,
                    'identifier' => $item->nip ?? $item->nik,
                    'type' => $item->karyawan ? 'karyawan' : 'non-karyawan',
                    'tanggal_lahir' => $item->karyawan?->tanggal_lahir ?? $item->nonKaryawan?->tanggal_lahir,
                    'url' => $item->karyawan 
                        ? route('pasien.show', $item->nip) 
                        : route('pasien.show_non_karyawan', $item->nik)
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $pasien
        ]);
    }

    /**
     * Menampilkan detail pasien karyawan dengan riwayat medis.
     */
    public function show(User $pasien)
    {
        // Load data yang diperlukan untuk karyawan
        $pasien->load([
            'karyawan',
            'rekamMedisKaryawan.detailDiagnosa.penyakit',
            'rekamMedisKaryawan.resepObat.obat',
            'checkupKaryawan'
        ]);

        // Kirim dengan nama 'user' untuk kompatibilitas view
        return view('pasien.show', ['user' => $pasien]);
    }

    /**
     * Menampilkan detail pasien non-karyawan dengan riwayat medis.
     */
    public function showNonKaryawan(User $pasien)
    {
        // Load data yang diperlukan untuk non-karyawan
        $pasien->load([
            'nonKaryawan',
            'rekamMedisNonKaryawan.detailDiagnosa.penyakit',
            'rekamMedisNonKaryawan.resepObat.obat',
            'checkupNonKaryawan'
        ]);

        return view('pasien.show-non-karyawan', compact('pasien'));
    }

    public function myCard()
    {
        $user = Auth::user();
        return view('pasien.my-card', compact('user'));
    }
}