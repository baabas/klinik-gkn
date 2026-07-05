<?php

namespace App\Http\Controllers;

use App\Models\BarangMedis;
use App\Models\FeedbackPasien;
use App\Models\PermintaanBarang;
use App\Models\RekamMedis;
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
        // Statistik kunjungan bulan ini
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $kunjunganBulanIni = RekamMedis::whereMonth('tanggal_kunjungan', $bulanIni)
            ->whereYear('tanggal_kunjungan', $tahunIni)
            ->count();
        
        // Statistik permintaan berdasarkan status
        $permintaanPending = PermintaanBarang::where('status', 'PENDING')->count();
        $permintaanApproved = PermintaanBarang::where('status', 'APPROVED')->count();
        $permintaanCompleted = PermintaanBarang::where('status', 'COMPLETED')->count();
        $permintaanRejected = PermintaanBarang::where('status', 'REJECTED')->count();
        $permintaanRetur = PermintaanBarang::where('status', 'RETUR')->count();
        
        // Statistik stok: hanya hitung barang yang total stoknya sudah di bawah stok minimal.
        $stokMenipis = BarangMedis::withSum('stok as stok_sum_jumlah', 'jumlah')
            ->get()
            ->filter(function ($barang) {
                $totalStok = (int) ($barang->stok_sum_jumlah ?? 0);
                $stokMinimal = (int) ($barang->stok_minimal ?? 0);

                return $stokMinimal > 0 && $totalStok < $stokMinimal;
            })
            ->count();
        $totalMasterBarang = BarangMedis::count();
        
        // Permintaan terbaru yang masih pending
        $permintaanTerbaru = PermintaanBarang::with(['lokasiPeminta', 'userPeminta'])
            ->where('status', 'PENDING')
            ->latest('tanggal_permintaan')
            ->limit(5)
            ->get();
            
        // Stok kritis: tampilkan hanya barang yang total stoknya masih dekat dengan stok minimal.
        // Batas dibuat relatif agar barang yang masih jauh di atas stok minimal tidak ikut tampil.
        $stokTerendah = BarangMedis::withSum('stok as stok_sum_jumlah', 'jumlah')
            ->get()
            ->filter(function ($barang) {
                $totalStok = (int) ($barang->stok_sum_jumlah ?? 0);
                $stokMinimal = (int) ($barang->stok_minimal ?? 0);

                if ($stokMinimal <= 0) {
                    return $totalStok <= 0;
                }

                return $totalStok <= ($stokMinimal * 3);
            })
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

        // ===== DATA FEEDBACK PASIEN (Bulan Ini - Seluruh Lokasi) =====
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        
        $feedbackQuery = DB::table('feedback_pasien as fp')
            ->whereMonth('fp.waktu_feedback', $bulanIni)
            ->whereYear('fp.waktu_feedback', $tahunIni);

        // Total feedback bulan ini
        $totalFeedback = (clone $feedbackQuery)->count();

        // Statistik rating (1=Tidak Puas, 2=Cukup, 3=Puas)
        $feedbackPuas = (clone $feedbackQuery)->where('fp.rating', 3)->count();
        $feedbackCukup = (clone $feedbackQuery)->where('fp.rating', 2)->count();
        $feedbackTidakPuas = (clone $feedbackQuery)->where('fp.rating', 1)->count();

        // Statistik kesesuaian obat
        $obatSesuai = (clone $feedbackQuery)->where('fp.jumlah_obat_sesuai', true)->count();
        $obatTidakSesuai = (clone $feedbackQuery)->where('fp.jumlah_obat_sesuai', false)->count();

        // Hitung persentase
        $feedbackStats = [
            'total' => $totalFeedback,
            'puas' => $feedbackPuas,
            'cukup' => $feedbackCukup,
            'tidak_puas' => $feedbackTidakPuas,
            'obat_sesuai' => $obatSesuai,
            'obat_tidak_sesuai' => $obatTidakSesuai,
            'persentase_puas' => $totalFeedback > 0 ? round(($feedbackPuas / $totalFeedback) * 100, 1) : 0,
            'persentase_cukup' => $totalFeedback > 0 ? round(($feedbackCukup / $totalFeedback) * 100, 1) : 0,
            'persentase_tidak_puas' => $totalFeedback > 0 ? round(($feedbackTidakPuas / $totalFeedback) * 100, 1) : 0,
            'persentase_obat_sesuai' => $totalFeedback > 0 ? round(($obatSesuai / $totalFeedback) * 100, 1) : 0,
            'persentase_obat_tidak_sesuai' => $totalFeedback > 0 ? round(($obatTidakSesuai / $totalFeedback) * 100, 1) : 0,
        ];

        return view('dashboard-pengadaan', compact(
            'permintaanPending', 'permintaanApproved', 'permintaanCompleted', 'permintaanRejected', 'permintaanRetur',
            'stokMenipis', 'totalMasterBarang', 'permintaanTerbaru', 'stokTerendah',
            'trendingBarang', 'distribusiLokasi', 'barangTerdaftar', 'barangBaru', 'feedbackStats'
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
        
        // Jumlah kunjungan bulan ini (filter berdasarkan lokasi dokter)
        $kunjunganBulanIni = DB::table('rekam_medis as rm')
            ->join('users as u', 'rm.id_dokter', '=', 'u.id')
            ->when($idLokasi, function ($query) use ($idLokasi) {
                return $query->where('u.id_lokasi', $idLokasi);
            })
            ->whereMonth('rm.tanggal_kunjungan', $bulanIni)
            ->whereYear('rm.tanggal_kunjungan', $tahunIni)
            ->count();

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
            'kunjunganBulanIni', 'data_penyakit', 'total_kasus_penyakit', 'data_obat', 'total_pemakaian_obat', 'kasus_hari_ini'
        ));
    }
}
