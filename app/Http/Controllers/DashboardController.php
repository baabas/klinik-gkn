<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// use Illuminate\View\View; // Tidak perlu karena sudah dihapus dari return type

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard yang sesuai dengan peran aktif di sesi.
     */
    public function index()
    {
        // [DEBUG] Tambahkan debugging
        $user = Auth::user();
        $activeRole = session('active_role');
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // [DEBUG] Jika tidak ada active_role, set default
        if (!$activeRole && $user->roles->isNotEmpty()) {
            $firstRole = $user->roles->first()->name;
            session(['active_role' => $firstRole]);
            $activeRole = $firstRole;
        }
        
        // [DEBUG] Log untuk debugging
        Log::info('Dashboard access', [
            'user_id' => $user->id,
            'active_role' => $activeRole,
            'user_roles' => $user->roles->pluck('name')
        ]);

        switch ($activeRole) {
            case 'PASIEN':
                return $this->dashboardPasien();
            case 'PENGADAAN':
                return $this->dashboardPengadaan();
            case 'DOKTER':
                return $this->dashboardDokter();
            default:
                return response()->view('errors.dashboard-error', [
                    'message' => 'Role tidak valid: ' . $activeRole,
                    'user_id' => $user->id,
                    'available_roles' => $user->roles->pluck('name')
                ], 500);
        }
    }

    /**
     * Menyiapkan data dan menampilkan dashboard untuk role PASIEN.
     */
    private function dashboardPasien(): \Illuminate\View\View
    {
        $user = Auth::user();
        $totalKunjungan = $user->rekamMedis()->count();
        $totalCheckup = $user->checkups()->count();
        $kunjunganTerakhir = $user->rekamMedis()->latest('tanggal_kunjungan')->first();
        $checkupTerakhir = $user->checkups()->latest('tanggal_pemeriksaan')->first();

        return view('dashboard-pasien', compact(
            'user', 'totalKunjungan', 'totalCheckup', 'kunjunganTerakhir', 'checkupTerakhir'
        ));
    }

    /**
     * Menyiapkan data dan menampilkan dashboard untuk role PENGADAAN.
     */
    private function dashboardPengadaan(): \Illuminate\View\View
    {
        $permintaanPending = PermintaanBarang::where('status', 'DIAJUKAN')->count();
        $stokMenipis = BarangMedis::withSum('stok as stok_sum_jumlah', 'jumlah')
            ->get()->where('stok_sum_jumlah', '<', 50)->count();
        $totalMasterBarang = BarangMedis::count();
        $permintaanTerbaru = PermintaanBarang::with(['lokasiPeminta'])
            ->where('status', 'DIAJUKAN')
            ->latest('tanggal_permintaan')
            ->limit(5)
            ->get();
       $stokTerendah = BarangMedis::withSum('stok as stok_sum_jumlah', 'jumlah')
            ->get()
            ->sortBy('stok_sum_jumlah')
            ->take(5);

        return view('dashboard-pengadaan', compact(
            'permintaanPending', 'stokMenipis', 'totalMasterBarang', 'permintaanTerbaru', 'stokTerendah'
        ));
    }

    /**
     * Menyiapkan data dan menampilkan dashboard untuk role DOKTER.
     */
    private function dashboardDokter(): \Illuminate\View\View
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        // [DIPERBAIKI] Kueri diubah untuk menggunakan kolom ICD10
        $data_penyakit = DB::table('detail_diagnosa as dd')
            ->join('daftar_penyakit as dp', 'dd.ICD10', '=', 'dp.ICD10') // Join menggunakan ICD10
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->select('dp.nama_penyakit', DB::raw('COUNT(dd.ICD10) as jumlah')) // Count menggunakan ICD10
            ->groupBy('dp.nama_penyakit')->orderBy('jumlah', 'desc')->limit(5)->get();

        $total_kasus_penyakit = DB::table('detail_diagnosa as dd')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)->whereYear('rm.tanggal_kunjungan', $tahunIni)->count();

        $data_obat = DB::table('resep_obat as ro')
            ->join('barang_medis as bm', 'ro.id_obat', '=', 'bm.id_obat')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->select('bm.nama_obat', DB::raw('SUM(ro.jumlah) as jumlah')) // Menggunakan `jumlah` sesuai migrasi resep_obat
            ->groupBy('bm.nama_obat')->orderBy('jumlah', 'desc')->limit(5)->get();

        $total_pemakaian_obat = DB::table('resep_obat as ro')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)->whereYear('rm.tanggal_kunjungan', $tahunIni)->sum('ro.jumlah'); // Menggunakan `jumlah`

        $kasus_hari_ini = DB::table('rekam_medis')->whereDate('tanggal_kunjungan', today())->count();

        return view('dashboard', compact(
            'data_penyakit', 'total_kasus_penyakit', 'data_obat', 'total_pemakaian_obat', 'kasus_hari_ini'
        ));
    }
}
