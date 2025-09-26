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
        // Statistik permintaan berdasarkan status
        $permintaanPending = PermintaanBarang::where('status', 'PENDING')->count();
        $permintaanApproved = PermintaanBarang::where('status', 'APPROVED')->count();
        $permintaanCompleted = PermintaanBarang::where('status', 'COMPLETED')->count();
        $permintaanRejected = PermintaanBarang::where('status', 'REJECTED')->count();
        
        // Statistik stok
        $stokMenipis = BarangMedis::withSum('stok as stok_sum_jumlah', 'jumlah')
            ->get()->where('stok_sum_jumlah', '<', 50)->count();
        $totalMasterBarang = BarangMedis::count();
        
        // Permintaan terbaru yang masih pending
        $permintaanTerbaru = PermintaanBarang::with(['lokasiPeminta', 'userPeminta'])
            ->where('status', 'PENDING')
            ->latest('tanggal_permintaan')
            ->limit(5)
            ->get();
            
        // Stok terendah dengan informasi kemasan
        $stokTerendah = BarangMedis::withSum('stok as stok_sum_jumlah', 'jumlah')
            ->get()
            ->sortBy('stok_sum_jumlah')
            ->take(5);

        // Trending barang yang paling sering diminta (bulan ini)
        $trendingBarang = DB::table('detail_permintaan_barang as dpb')
            ->join('permintaan_barang as pb', 'dpb.id_permintaan', '=', 'pb.id')
            ->join('barang_medis as bm', 'dpb.id_barang', '=', 'bm.id_obat')
            ->whereMonth('pb.tanggal_permintaan', now()->month)
            ->whereYear('pb.tanggal_permintaan', now()->year)
            ->whereNotNull('dpb.id_barang')
            ->select('bm.nama_obat', 'bm.kemasan', DB::raw('SUM(dpb.jumlah_diminta) as total_diminta'))
            ->groupBy('bm.id_obat', 'bm.nama_obat', 'bm.kemasan')
            ->orderBy('total_diminta', 'desc')
            ->limit(5)
            ->get();

        // Distribusi permintaan per lokasi
        $distribusiLokasi = DB::table('permintaan_barang as pb')
            ->join('lokasi_klinik as lk', 'pb.id_lokasi_peminta', '=', 'lk.id')
            ->select('lk.nama_lokasi', DB::raw('COUNT(pb.id) as jumlah_permintaan'))
            ->whereMonth('pb.tanggal_permintaan', now()->month)
            ->whereYear('pb.tanggal_permintaan', now()->year)
            ->groupBy('lk.id', 'lk.nama_lokasi')
            ->orderBy('jumlah_permintaan', 'desc')
            ->get();

        // Statistik permintaan barang baru vs terdaftar
        $barangTerdaftar = DB::table('detail_permintaan_barang as dpb')
            ->join('permintaan_barang as pb', 'dpb.id_permintaan', '=', 'pb.id')
            ->whereNotNull('dpb.id_barang')
            ->whereMonth('pb.tanggal_permintaan', now()->month)
            ->whereYear('pb.tanggal_permintaan', now()->year)
            ->count();
            
        $barangBaru = DB::table('detail_permintaan_barang as dpb')
            ->join('permintaan_barang as pb', 'dpb.id_permintaan', '=', 'pb.id')
            ->whereNull('dpb.id_barang')
            ->whereNotNull('dpb.nama_barang_baru')
            ->whereMonth('pb.tanggal_permintaan', now()->month)
            ->whereYear('pb.tanggal_permintaan', now()->year)
            ->count();

        return view('dashboard-pengadaan', compact(
            'permintaanPending', 'permintaanApproved', 'permintaanCompleted', 'permintaanRejected',
            'stokMenipis', 'totalMasterBarang', 'permintaanTerbaru', 'stokTerendah',
            'trendingBarang', 'distribusiLokasi', 'barangTerdaftar', 'barangBaru'
        ));
    }

    /**
     * Menyiapkan data dan menampilkan dashboard untuk role DOKTER.
     */
    private function dashboardDokter(): \Illuminate\View\View
    {
        $user = Auth::user();
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $idLokasi = $user->id_lokasi; // Filter berdasarkan lokasi dokter

        // [DIPERBAIKI] Kueri diubah untuk menggunakan kolom ICD10 dan filter lokasi melalui dokter
        $data_penyakit = DB::table('detail_diagnosa as dd')
            ->join('daftar_penyakit as dp', 'dd.ICD10', '=', 'dp.ICD10') // Join menggunakan ICD10
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('users as u', 'rm.id_dokter', '=', 'u.id') // Join dengan tabel users untuk filter lokasi
            ->when($idLokasi, function ($query) use ($idLokasi) {
                return $query->where('u.id_lokasi', $idLokasi);
            })
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->select('dp.nama_penyakit', DB::raw('COUNT(dd.ICD10) as jumlah')) // Count menggunakan ICD10
            ->groupBy('dp.nama_penyakit')->orderBy('jumlah', 'desc')->limit(5)->get();

        $total_kasus_penyakit = DB::table('detail_diagnosa as dd')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('users as u', 'rm.id_dokter', '=', 'u.id') // Join dengan tabel users untuk filter lokasi
            ->when($idLokasi, function ($query) use ($idLokasi) {
                return $query->where('u.id_lokasi', $idLokasi);
            })
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)->whereYear('rm.tanggal_kunjungan', $tahunIni)->count();

        $data_obat = DB::table('resep_obat as ro')
            ->join('barang_medis as bm', 'ro.id_obat', '=', 'bm.id_obat')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('users as u', 'rm.id_dokter', '=', 'u.id') // Join dengan tabel users untuk filter lokasi
            ->when($idLokasi, function ($query) use ($idLokasi) {
                return $query->where('u.id_lokasi', $idLokasi);
            })
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->select('bm.nama_obat', DB::raw('SUM(ro.jumlah) as jumlah')) // Menggunakan `jumlah` sesuai migrasi resep_obat
            ->groupBy('bm.nama_obat')->orderBy('jumlah', 'desc')->limit(5)->get();

        $total_pemakaian_obat = DB::table('resep_obat as ro')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('users as u', 'rm.id_dokter', '=', 'u.id') // Join dengan tabel users untuk filter lokasi
            ->when($idLokasi, function ($query) use ($idLokasi) {
                return $query->where('u.id_lokasi', $idLokasi);
            })
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)->whereYear('rm.tanggal_kunjungan', $tahunIni)->sum('ro.jumlah'); // Menggunakan `jumlah`

        $kasus_hari_ini = DB::table('rekam_medis as rm')
            ->join('users as u', 'rm.id_dokter', '=', 'u.id') // Join dengan tabel users untuk filter lokasi
            ->when($idLokasi, function ($query) use ($idLokasi) {
                return $query->where('u.id_lokasi', $idLokasi);
            })
            ->whereDate('tanggal_kunjungan', today())->count();

        return view('dashboard', compact(
            'data_penyakit', 'total_kasus_penyakit', 'data_obat', 'total_pemakaian_obat', 'kasus_hari_ini'
        ));
    }
}
