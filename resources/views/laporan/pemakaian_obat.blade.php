@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Pemakaian Obat</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan Bulan: {{ $filter['nama_bulan'] }}</h5>
            <form action="{{ route('laporan.pemakaian_obat') }}" method="GET">
                <div class="input-group">
                    <input type="month" name="filter_bulan" class="form-control" value="{{ $filter['string'] }}" max="{{ date('Y-m') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="mb-5">
                <h4 class="mb-3">Laporan Harian</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="p-2">Nama Obat</th>
                                @for ($i = 1; $i <= $filter['jumlah_hari']; $i++)
                                    <th class="p-2" style="min-width: 35px;">{{ $i }}</th>
                                @endfor
                                <th class="p-2 bg-secondary text-white">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar_obat as $nama_obat)
                                @php
                                    $pemakaian_obat = $data_pemakaian_harian->get($nama_obat);
                                    $total_bulanan = $pemakaian_obat ? $pemakaian_obat->sum('jumlah') : 0;
                                @endphp
                                <tr>
                                    <td class="p-2 text-start">{{ $nama_obat }}</td>
                                    @for ($hari = 1; $hari <= $filter['jumlah_hari']; $hari++)
                                        @php $jumlah = $pemakaian_obat ? $pemakaian_obat->where('hari', $hari)->sum('jumlah') : 0; @endphp
                                        <td class="p-2">{{ $jumlah > 0 ? $jumlah : '' }}</td>
                                    @endfor
                                    <td class="p-2 bg-light fw-bold">{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $filter['jumlah_hari'] + 2 }}" class="p-4 text-center">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h4 class="mb-3">Rekapitulasi Mingguan</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="p-2">Nama Obat</th>
                                <th class="p-2">Minggu I</th>
                                <th class="p-2">Minggu II</th>
                                <th class="p-2">Minggu III</th>
                                <th class="p-2">Minggu IV</th>
                                <th class="p-2">Minggu V</th>
                                <th class="p-2 bg-secondary text-white">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar_obat as $nama_obat)
                                @php
                                    $pemakaian_mingguan = $data_pemakaian_mingguan->get($nama_obat);
                                    $minggu1 = $pemakaian_mingguan ? $pemakaian_mingguan->where('minggu_ke', 1)->sum('jumlah') : 0;
                                    $minggu2 = $pemakaian_mingguan ? $pemakaian_mingguan->where('minggu_ke', 2)->sum('jumlah') : 0;
                                    $minggu3 = $pemakaian_mingguan ? $pemakaian_mingguan->where('minggu_ke', 3)->sum('jumlah') : 0;
                                    $minggu4 = $pemakaian_mingguan ? $pemakaian_mingguan->where('minggu_ke', 4)->sum('jumlah') : 0;
                                    $minggu5 = $pemakaian_mingguan ? $pemakaian_mingguan->where('minggu_ke', 5)->sum('jumlah') : 0;
                                    $total_bulanan = $minggu1 + $minggu2 + $minggu3 + $minggu4 + $minggu5;
                                @endphp
                                <tr>
                                    <td class="p-2 text-start">{{ $nama_obat }}</td>
                                    <td class="p-2">{{ $minggu1 ?: '' }}</td>
                                    <td class="p-2">{{ $minggu2 ?: '' }}</td>
                                    <td class="p-2">{{ $minggu3 ?: '' }}</td>
                                    <td class="p-2">{{ $minggu4 ?: '' }}</td>
                                    <td class="p-2">{{ $minggu5 ?: '' }}</td>
                                    <td class="p-2 bg-light fw-bold">{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="p-4 text-center">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
