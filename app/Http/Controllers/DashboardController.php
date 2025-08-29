<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard.
     */
    public function index(): View
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        // 1. Kasus Penyakit Terbanyak
        $data_penyakit = DB::table('detail_diagnosa as dd')
            ->join('daftar_penyakit as dp', 'dd.kode_penyakit', '=', 'dp.kode_penyakit')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->select('dp.nama_penyakit', DB::raw('COUNT(dd.kode_penyakit) as jumlah'))
            ->groupBy('dp.nama_penyakit')
            ->orderBy('jumlah', 'desc')
            ->limit(5)
            ->get();

        // Total kasus untuk perhitungan persentase
        $total_kasus_penyakit = DB::table('detail_diagnosa as dd')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->count();

        // 2. Pemakaian Obat Teratas
        $data_obat = DB::table('resep_obat as ro')
            ->join('obat as o', 'ro.id_obat', '=', 'o.id_obat')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->select('o.nama_obat', DB::raw('SUM(ro.kuantitas) as jumlah'))
            ->groupBy('o.nama_obat')
            ->orderBy('jumlah', 'desc')
            ->limit(5)
            ->get();

        // Total obat untuk perhitungan persentase
        $total_pemakaian_obat = DB::table('resep_obat as ro')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->sum('ro.kuantitas');

        // 3. Jumlah Kasus Hari Ini
        $kasus_hari_ini = DB::table('rekam_medis')
            ->whereDate('tanggal_kunjungan', today())
            ->count();

        return view('dashboard', compact(
            'data_penyakit',
            'total_kasus_penyakit',
            'data_obat',
            'total_pemakaian_obat',
            'kasus_hari_ini'
        ));
    }
}
