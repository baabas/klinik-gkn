<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(): View
    {
        return view('laporan.index');
    }

    public function cetakLaporanObat(Request $request)
    {
        $filter = $this->getFilterBulanTahun($request);
        $lokasiId = Auth::user()->id_lokasi;

        $daftar_obat = DB::table('resep_obat as ro')
            ->join('barang_medis as o', 'ro.id_obat', '=', 'o.id_obat')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('stok_barang as sb', 'o.id_obat', '=', 'sb.id_barang')
            ->where('sb.id_lokasi', $lokasiId)
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])
            ->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('o.nama_obat')
            ->distinct()->orderBy('o.nama_obat')->get();

        $stok_obat = DB::table('barang_medis as bm')
            ->leftJoin('stok_barang as sb', function ($join) use ($lokasiId) {
                $join->on('bm.id_obat', '=', 'sb.id_barang')
                    ->where('sb.id_lokasi', $lokasiId);
            })
            ->select('bm.nama_obat', DB::raw('SUM(sb.jumlah) as stok_saat_ini'))
            ->groupBy('bm.nama_obat', 'sb.id_lokasi')
            ->get()->keyBy('nama_obat');

        $data_pemakaian_mingguan = DB::table('resep_obat as ro')
            ->join('barang_medis as o', 'ro.id_obat', '=', 'o.id_obat')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('stok_barang as sb', 'o.id_obat', '=', 'sb.id_barang')
            ->where('sb.id_lokasi', $lokasiId)
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])
            ->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('o.nama_obat', DB::raw("CASE WHEN EXTRACT(DAY FROM rm.tanggal_kunjungan) BETWEEN 1 AND 7 THEN 1 WHEN EXTRACT(DAY FROM rm.tanggal_kunjungan) BETWEEN 8 AND 14 THEN 2 WHEN EXTRACT(DAY FROM rm.tanggal_kunjungan) BETWEEN 15 AND 21 THEN 3 WHEN EXTRACT(DAY FROM rm.tanggal_kunjungan) BETWEEN 22 AND 28 THEN 4 ELSE 5 END as minggu_ke"), DB::raw('SUM(ro.jumlah) as jumlah'))
            ->groupBy('o.nama_obat', 'minggu_ke', 'sb.id_lokasi')->get()->groupBy('nama_obat');

        $data_pemakaian_harian = DB::table('resep_obat as ro')
            ->join('barang_medis as o', 'ro.id_obat', '=', 'o.id_obat')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('stok_barang as sb', 'o.id_obat', '=', 'sb.id_barang')
            ->where('sb.id_lokasi', $lokasiId)
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])
            ->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('o.nama_obat', DB::raw('EXTRACT(DAY FROM rm.tanggal_kunjungan) as hari'), DB::raw('SUM(ro.jumlah) as jumlah'))
            ->groupBy('o.nama_obat', 'hari')
            ->get()
            ->groupBy('nama_obat');

        $pdf = Pdf::loadView('laporan.pdf_obat', [
            'daftar_obat' => $daftar_obat,
            'data_pemakaian_mingguan' => $data_pemakaian_mingguan,
            'data_pemakaian_harian' => $data_pemakaian_harian,
            'stok_obat' => $stok_obat,
            'filter' => $filter,
            'lokasiId' => $lokasiId
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pemakaian-obat-'.$filter['string'].'.pdf');
    }

    public function cetakLaporanPenyakitKunjungan(Request $request)
    {
        $filter = $this->getFilterBulanTahun($request);
        $user = Auth::user();
        $idLokasi = $user->id_lokasi; // Filter berdasarkan lokasi dokter

        $daftar_penyakit = DB::table('detail_diagnosa as dd')
            ->join('daftar_penyakit as dp', 'dd.ICD10', '=', 'dp.ICD10')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('users as u', 'rm.id_dokter', '=', 'u.id') // Join dengan tabel users untuk filter lokasi
            ->when($idLokasi, function ($query, $idLokasi) {
                return $query->where('u.id_lokasi', $idLokasi);
            })
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('dp.ICD10', 'dp.nama_penyakit')->distinct()->orderBy('dp.nama_penyakit')->get();

        $data_kasus = DB::table('detail_diagnosa as dd')
            ->join('daftar_penyakit as dp', 'dd.ICD10', '=', 'dp.ICD10')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('users as u', 'rm.id_dokter', '=', 'u.id') // Join dengan tabel users untuk filter lokasi
            ->when($idLokasi, function ($query, $idLokasi) {
                return $query->where('u.id_lokasi', $idLokasi);
            })
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('dp.nama_penyakit', DB::raw('EXTRACT(DAY FROM rm.tanggal_kunjungan) as hari'), DB::raw('COUNT(dd.id_detail_diagnosa) as jumlah'))
            ->groupBy('dp.nama_penyakit', 'hari')->get()->groupBy('nama_penyakit');

        // [DIPERBAIKI] Gabungkan daftar kantor dari karyawan dan lokasi gedung dari non-karyawan
        $daftar_kantor_karyawan = DB::table('karyawan')
            ->whereNotNull('kantor')->where('kantor', '!=', '')
            ->select('kantor')->distinct()->pluck('kantor');

        $daftar_kantor_non_karyawan = DB::table('non_karyawan')
            ->whereNotNull('lokasi_gedung')->where('lokasi_gedung', '!=', '')
            ->select('lokasi_gedung as kantor')->distinct()->pluck('kantor');

        $daftar_kantor = $daftar_kantor_karyawan->merge($daftar_kantor_non_karyawan)->unique()->sort()->values();

        // [DIPERBAIKI] Gabungkan data kunjungan dari karyawan dan non-karyawan
        $data_kunjungan_karyawan = DB::table('rekam_medis as rm')
            ->join('users', 'rm.NIP_pasien', '=', 'users.nip')
            ->join('karyawan as k', 'users.nip', '=', 'k.nip')
            ->join('users as dokter', 'rm.id_dokter', '=', 'dokter.id') // Join dengan tabel users untuk filter lokasi dokter
            ->when($idLokasi, function ($query, $idLokasi) {
                return $query->where('dokter.id_lokasi', $idLokasi);
            })
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('k.kantor', DB::raw('EXTRACT(DAY FROM rm.tanggal_kunjungan) as hari'), DB::raw('COUNT(rm.id_rekam_medis) as jumlah'))
            ->groupBy('k.kantor', 'hari')->get();

        $data_kunjungan_non_karyawan = DB::table('rekam_medis as rm')
            ->join('users', 'rm.NIK_pasien', '=', 'users.nik')
            ->join('non_karyawan as nk', 'users.nik', '=', 'nk.nik')
            ->join('users as dokter', 'rm.id_dokter', '=', 'dokter.id') // Join dengan tabel users untuk filter lokasi dokter
            ->when($idLokasi, function ($query, $idLokasi) {
                return $query->where('dokter.id_lokasi', $idLokasi);
            })
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->whereNotNull('nk.lokasi_gedung')->where('nk.lokasi_gedung', '!=', '')
            ->select('nk.lokasi_gedung as kantor', DB::raw('EXTRACT(DAY FROM rm.tanggal_kunjungan) as hari'), DB::raw('COUNT(rm.id_rekam_medis) as jumlah'))
            ->groupBy('nk.lokasi_gedung', 'hari')->get();

        // Gabungkan data kunjungan
        $data_kunjungan_gabungan = $data_kunjungan_karyawan->concat($data_kunjungan_non_karyawan);
        
        // Group by kantor dan aggregasi jumlah
        $data_kunjungan = $data_kunjungan_gabungan->groupBy('kantor')->map(function ($items) {
            return $items->groupBy('hari')->map(function ($hariItems) {
                return (object) [
                    'kantor' => $hariItems->first()->kantor,
                    'hari' => $hariItems->first()->hari,
                    'jumlah' => $hariItems->sum('jumlah')
                ];
            });
        });

        $pdf = Pdf::loadView('laporan.pdf_penyakit_kunjungan', [
            'daftar_penyakit' => $daftar_penyakit,
            'data_kasus' => $data_kasus,
            'daftar_kantor' => $daftar_kantor,
            'data_kunjungan' => $data_kunjungan,
            'filter' => $filter
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-penyakit-kunjungan-'.$filter['string'].'.pdf');
    }

    private function getFilterBulanTahun(Request $request): array
    {
        $filter_bulan_tahun = $request->input('filter_bulan', date('Y-m'));
        return [
            'string' => $filter_bulan_tahun,
            'tahun' => (int)substr($filter_bulan_tahun, 0, 4),
            'bulan' => (int)substr($filter_bulan_tahun, 5, 2),
            'nama_bulan' => date('F Y', strtotime($filter_bulan_tahun)),
            'nama_bulan_upper' => strtoupper(date('F Y', strtotime($filter_bulan_tahun))),
            'jumlah_hari' => (int)date('t', strtotime($filter_bulan_tahun))
        ];
    }
}