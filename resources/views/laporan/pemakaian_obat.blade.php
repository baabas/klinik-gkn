@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Pemakaian Obat</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan Bulan: {{ $filter['nama_bulan'] }}</h5>
            {{-- Form Filter --}}
            <form action="{{ route('laporan.pemakaian_obat') }}" method="GET">
                <div class="input-group">
                    <input type="month" name="filter_bulan" class="form-control" value="{{ $filter['string'] }}" max="{{ date('Y-m') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="card-body">
            
            {{-- Navigasi Tabulasi --}}
            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="harian-tab" data-bs-toggle="tab" data-bs-target="#harian-tab-pane" type="button" role="tab">Laporan Harian</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="mingguan-tab" data-bs-toggle="tab" data-bs-target="#mingguan-tab-pane" type="button" role="tab">Laporan Mingguan</button>
              </li>
            </ul>

            <div class="tab-content" id="myTabContent">
              {{-- KONTEN TAB HARIAN --}}
              <div class="tab-pane fade show active" id="harian-tab-pane" role="tabpanel">

                {{-- Tabel Tanggal 1-16 --}}
                <h4 class="mb-3 text-center">LAPORAN PEMAKAIAN OBAT TANGGAL 01 S/D 16 {{ strtoupper($filter['nama_bulan']) }}</h4>
                <div class="table-responsive mb-5">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="p-2 text-start">NAMA OBAT</th>
                                @for ($i = 1; $i <= 16; $i++)
                                    <th class="p-2" style="min-width: 35px;">{{ $i }}</th>
                                @endfor
                                <th class="p-2 bg-secondary text-white">JML</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar_obat as $nama_obat)
                                <tr>
                                    <td class="p-2 text-start">{{ $nama_obat }}</td>
                                    @php $sub_total_1 = 0; @endphp
                                    @for ($i = 1; $i <= 16; $i++)
                                        @php
                                            $jumlah = $data_pemakaian_harian->get($nama_obat) ? $data_pemakaian_harian->get($nama_obat)->where('hari', $i)->sum('jumlah') : 0;
                                            $sub_total_1 += $jumlah;
                                        @endphp
                                        <td class="p-2">{{ $jumlah ?: '' }}</td>
                                    @endfor
                                    <td class="p-2 bg-light fw-bold">{{ $sub_total_1 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="18" class="text-center p-4">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Tabel Tanggal 17-31 --}}
                <h4 class="mb-3 text-center">LAPORAN PEMAKAIAN OBAT TANGGAL 17 S/D {{ $filter['jumlah_hari'] }} {{ strtoupper($filter['nama_bulan']) }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="p-2 text-start">NAMA OBAT</th>
                                @for ($i = 17; $i <= $filter['jumlah_hari']; $i++)
                                    <th class="p-2" style="min-width: 35px;">{{ $i }}</th>
                                @endfor
                                <th class="p-2 bg-secondary text-white">JML</th>
                                <th class="p-2 bg-dark text-white">TOTAL BULAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar_obat as $nama_obat)
                                <tr>
                                    <td class="p-2 text-start">{{ $nama_obat }}</td>
                                    @php
                                        // Hitung total periode pertama lagi untuk mendapatkan total bulanan
                                        $total_periode_1 = 0;
                                        for ($i = 1; $i <= 16; $i++) {
                                            $total_periode_1 += $data_pemakaian_harian->get($nama_obat) ? $data_pemakaian_harian->get($nama_obat)->where('hari', $i)->sum('jumlah') : 0;
                                        }
                                        $sub_total_2 = 0;
                                    @endphp
                                    @for ($i = 17; $i <= $filter['jumlah_hari']; $i++)
                                        @php
                                            $jumlah = $data_pemakaian_harian->get($nama_obat) ? $data_pemakaian_harian->get($nama_obat)->where('hari', $i)->sum('jumlah') : 0;
                                            $sub_total_2 += $jumlah;
                                        @endphp
                                        <td class="p-2">{{ $jumlah ?: '' }}</td>
                                    @endfor
                                    <td class="p-2 bg-light fw-bold">{{ $sub_total_2 }}</td>
                                    <td class="p-2 bg-light fw-bold text-primary">{{ $total_periode_1 + $sub_total_2 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($filter['jumlah_hari'] - 16) + 3 }}" class="text-center p-4">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

              </div>

              {{-- KONTEN TAB MINGGUAN --}}
              <div class="tab-pane fade" id="mingguan-tab-pane" role="tabpanel">
                <h4 class="mb-3">Rekapitulasi Mingguan</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="p-2 text-start">Nama Obat</th>
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
    </div>
@endsection