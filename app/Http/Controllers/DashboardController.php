<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard yang sesuai dengan role user.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Cek jika role user adalah PENGADAAN
        if ($user->hasRole('PENGADAAN')) {
            return $this->dashboardPengadaan();
        }

        // Jika bukan, tampilkan dashboard untuk DOKTER (default)
        return $this->dashboardDokter();
    }

    /**
     * Menyiapkan data dan menampilkan dashboard untuk role PENGADAAN.
     */
    private function dashboardPengadaan(): View
    {
        // 1. Data untuk card statistik
        $permintaanPending = PermintaanBarang::where('status', 'PENDING')->count();
        $stokMenipis = BarangMedis::withSum('stok', 'jumlah')
            ->get()
            ->where('stok_sum_jumlah', '<', 50) // Stok dianggap menipis jika < 50
            ->count();
        $totalMasterBarang = BarangMedis::count();

        // 2. Data untuk tabel "Permintaan Terbaru"
        $permintaanTerbaru = PermintaanBarang::with('lokasiPeminta')
            ->where('status', 'PENDING')
            ->latest()
            ->limit(5)
            ->get();

        // 3. Data untuk tabel "Stok Terendah"
        $stokTerendah = BarangMedis::withSum('stok', 'jumlah')
            ->orderBy('stok_sum_jumlah', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard-pengadaan', compact(
            'permintaanPending',
            'stokMenipis',
            'totalMasterBarang',
            'permintaanTerbaru',
            'stokTerendah'
        ));
    }

    /**
     * Menyiapkan data dan menampilkan dashboard untuk role DOKTER.
     */
    private function dashboardDokter(): View
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $data_penyakit = DB::table('detail_diagnosa as dd')
            ->join('daftar_penyakit as dp', 'dd.kode_penyakit', '=', 'dp.kode_penyakit')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->select('dp.nama_penyakit', DB::raw('COUNT(dd.kode_penyakit) as jumlah'))
            ->groupBy('dp.nama_penyakit')->orderBy('jumlah', 'desc')->limit(5)->get();

        $total_kasus_penyakit = DB::table('detail_diagnosa as dd')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)->count();

        $data_obat = DB::table('resep_obat as ro')
            ->join('barang_medis as bm', 'ro.id_obat', '=', 'bm.id_obat')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->select('bm.nama_obat', DB::raw('SUM(ro.kuantitas) as jumlah'))
            ->groupBy('bm.nama_obat')->orderBy('jumlah', 'desc')->limit(5)->get();

        $total_pemakaian_obat = DB::table('resep_obat as ro')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)->sum('ro.kuantitas');

        $kasus_hari_ini = DB::table('rekam_medis')->whereDate('tanggal_kunjungan', today())->count();

        return view('dashboard', compact(
            'data_penyakit',
            'total_kasus_penyakit',
            'data_obat',
            'total_pemakaian_obat',
            'kasus_hari_ini'
        ));
    }
}
