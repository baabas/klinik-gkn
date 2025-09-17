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
            ->select('o.nama_obat', DB::raw("CASE WHEN DAY(rm.tanggal_kunjungan) BETWEEN 1 AND 7 THEN 1 WHEN DAY(rm.tanggal_kunjungan) BETWEEN 8 AND 14 THEN 2 WHEN DAY(rm.tanggal_kunjungan) BETWEEN 15 AND 21 THEN 3 WHEN DAY(rm.tanggal_kunjungan) BETWEEN 22 AND 28 THEN 4 ELSE 5 END as minggu_ke"), DB::raw('SUM(ro.jumlah) as jumlah'))
            ->groupBy('o.nama_obat', 'minggu_ke', 'sb.id_lokasi')->get()->groupBy('nama_obat');

        $data_pemakaian_harian = DB::table('resep_obat as ro')
            ->join('barang_medis as o', 'ro.id_obat', '=', 'o.id_obat')
            ->join('rekam_medis as rm', 'ro.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->join('stok_barang as sb', 'o.id_obat', '=', 'sb.id_barang')
            ->where('sb.id_lokasi', $lokasiId)
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])
            ->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('o.nama_obat', DB::raw('DAY(rm.tanggal_kunjungan) as hari'), DB::raw('SUM(ro.jumlah) as jumlah'))
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

        $daftar_penyakit = DB::table('detail_diagnosa as dd')
            ->join('daftar_penyakit as dp', 'dd.ICD10', '=', 'dp.ICD10')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('dp.ICD10', 'dp.nama_penyakit')->distinct()->orderBy('dp.nama_penyakit')->get();

        $data_kasus = DB::table('detail_diagnosa as dd')
            ->join('daftar_penyakit as dp', 'dd.ICD10', '=', 'dp.ICD10')
            ->join('rekam_medis as rm', 'dd.id_rekam_medis', '=', 'rm.id_rekam_medis')
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('dp.nama_penyakit', DB::raw('DAY(rm.tanggal_kunjungan) as hari'), DB::raw('COUNT(dd.id_detail_diagnosa) as jumlah'))
            ->groupBy('dp.nama_penyakit', 'hari')->get()->groupBy('nama_penyakit');

        $daftar_kantor = DB::table('karyawan')
            ->whereNotNull('kantor')->where('kantor', '!=', '')
            ->select('kantor')->distinct()->orderBy('kantor')->pluck('kantor');

        $data_kunjungan = DB::table('rekam_medis as rm')
            ->join('users', 'rm.NIP_pasien', '=', 'users.nip')
            ->join('karyawan as k', 'users.nip', '=', 'k.nip')
            ->whereYear('rm.tanggal_kunjungan', $filter['tahun'])->whereMonth('rm.tanggal_kunjungan', $filter['bulan'])
            ->select('k.kantor', DB::raw('DAY(rm.tanggal_kunjungan) as hari'), DB::raw('COUNT(rm.id_rekam_medis) as jumlah'))
            ->groupBy('k.kantor', 'hari')->get()->groupBy('kantor');

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