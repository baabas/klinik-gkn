@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Harian Klinik</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan Bulan: {{ $filter['nama_bulan'] }}</h5>
            <form action="{{ route('laporan.harian') }}" method="GET">
                <div class="input-group">
                    <input type="month" name="filter_bulan" class="form-control" value="{{ $filter['string'] }}" max="{{ date('Y-m') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="mb-5">
                <h4 class="mb-3">Laporan Kasus Penyakit</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="p-2">Penyakit</th>
                                <th class="p-2">Kode</th>
                                @for ($i = 1; $i <= $filter['jumlah_hari']; $i++) <th class="p-2" style="min-width: 35px;">{{ $i }}</th> @endfor
                                <th class="p-2 bg-secondary text-white">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar_penyakit as $penyakit)
                                @php
                                    $kasus_penyakit = $data_kasus->get($penyakit->nama_penyakit);
                                    $total_bulanan = $kasus_penyakit ? $kasus_penyakit->sum('jumlah') : 0;
                                @endphp
                                <tr>
                                    <td class="p-2 text-start">{{ $penyakit->nama_penyakit }}</td>
                                    <td class="p-2 text-start">{{ $penyakit->kode_penyakit }}</td>
                                    @for ($hari = 1; $hari <= $filter['jumlah_hari']; $hari++)
                                        @php $jumlah = $kasus_penyakit ? $kasus_penyakit->where('hari', $hari)->sum('jumlah') : 0; @endphp
                                        <td class="p-2">{{ $jumlah > 0 ? $jumlah : '' }}</td>
                                    @endfor
                                    <td class="p-2 bg-light fw-bold">{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $filter['jumlah_hari'] + 3 }}" class="p-4 text-center">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h4 class="mb-3">Laporan Kunjungan Pasien per Kantor</h4>
                 <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="p-2">Kantor</th>
                                @for ($i = 1; $i <= $filter['jumlah_hari']; $i++) <th class="p-2" style="min-width: 35px;">{{ $i }}</th> @endfor
                                <th class="p-2 bg-secondary text-white">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar_kantor as $kantor)
                                @php
                                    $kunjungan_kantor = $data_kunjungan->get($kantor);
                                    $total_bulanan = $kunjungan_kantor ? $kunjungan_kantor->sum('jumlah') : 0;
                                @endphp
                                <tr>
                                    <td class="p-2 text-start">{{ $kantor }}</td>
                                    @for ($hari = 1; $hari <= $filter['jumlah_hari']; $hari++)
                                        @php $jumlah = $kunjungan_kantor ? $kunjungan_kantor->where('hari', $hari)->sum('jumlah') : 0; @endphp
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
        </div>
    </div>
@endsection
