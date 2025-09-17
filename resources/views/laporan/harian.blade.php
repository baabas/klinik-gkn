@extends('layouts.sidebar-layout')

@section('title', 'Laporan Harian')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Harian Gabungan</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('laporan.harian') }}" method="GET" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label for="filter_bulan" class="form-label">Pilih Bulan & Tahun</label>
                    <input type="month" id="filter_bulan" name="filter_bulan" class="form-control" value="{{ $filter['string'] }}" max="{{ date('Y-m') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        {{-- ============================================= --}}
        {{-- BAGIAN LAPORAN PENYAKIT --}}
        {{-- ============================================= --}}
        <h4 class="mb-3">Laporan Penyakit Bulan: <span class="text-primary">{{ $filter['nama_bulan'] }}</span></h4>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-striped text-center text-sm">
                <thead class="table-light">
                    <tr>
                        <th class="align-middle">No.</th>
                        <th class="align-middle">Kode</th>
                        <th class="align-middle text-start">Nama Penyakit</th>
                        @for ($hari = 1; $hari <= $filter['jumlah_hari']; $hari++)
                            <th style="width: 25px;">{{ $hari }}</th>
                        @endfor
                        <th class="align-middle bg-light" style="width: 60px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftar_penyakit as $penyakit)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        {{-- [FIX] Mengubah $penyakit->kode_penyakit menjadi $penyakit->ICD10 --}}
                        <td>{{ $penyakit->ICD10 }}</td>
                        <td class="text-start">{{ $penyakit->nama_penyakit }}</td>
                        @php
                            $total_bulanan = 0;
                            $kasus_penyakit = $data_kasus->get($penyakit->nama_penyakit);
                        @endphp
                        @for ($hari = 1; $hari <= $filter['jumlah_hari']; $hari++)
                            @php
                                $jumlah = $kasus_penyakit ? $kasus_penyakit->where('hari', $hari)->sum('jumlah') : 0;
                                $total_bulanan += $jumlah;
                            @endphp
                            <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                        @endfor
                        <td class="fw-bold bg-light">{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $filter['jumlah_hari'] + 4 }}" class="text-center p-4">Tidak ada data penyakit pada bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- ============================================= --}}
        {{-- BAGIAN LAPORAN KUNJUNGAN --}}
        {{-- ============================================= --}}
        <h4 class="mb-3">Laporan Kunjungan per Kantor Bulan: <span class="text-primary">{{ $filter['nama_bulan'] }}</span></h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center text-sm">
                <thead class="table-light">
                    <tr>
                        <th class="align-middle text-start">Kantor</th>
                        @for ($i = 1; $i <= $filter['jumlah_hari']; $i++)
                            <th style="width: 25px;">{{ $i }}</th>
                        @endfor
                        <th class="align-middle bg-light" style="width: 60px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftar_kantor as $kantor)
                        @php
                            $kunjungan_kantor = $data_kunjungan->get($kantor);
                            $total_bulanan = 0;
                        @endphp
                        <tr>
                            <td class="text-start">{{ $kantor }}</td>
                            @for ($hari = 1; $hari <= $filter['jumlah_hari']; $hari++)
                                @php
                                    $jumlah = $kunjungan_kantor ? $kunjungan_kantor->where('hari', $hari)->sum('jumlah') : 0;
                                    $total_bulanan += $jumlah;
                                @endphp
                                <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                            @endfor
                            <td class="fw-bold bg-light">{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $filter['jumlah_hari'] + 2 }}" class="text-center p-4">Tidak ada data kunjungan pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection