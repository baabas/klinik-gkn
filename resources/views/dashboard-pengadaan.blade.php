@extends('layouts.top-nav-layout')

@section('content')
    <h1 class="h2 mb-4">Dashboard Pengadaan</h1>

    {{-- Baris untuk Card Statistik --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Permintaan Diajukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $permintaanPending }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-inbox-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stok Menipis (&lt;50)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stokMenipis }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jenis Item</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMasterBarang }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-archive-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris untuk Tabel --}}
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">5 Permintaan Terbaru (Diajukan)</h6>
                    <a href="{{ route('permintaan.index') }}">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse ($permintaanTerbaru as $permintaan)
                                <tr>
                                    <td>
                                        <strong>{{ $permintaan->kode }}</strong><br>
                                        <small class="text-muted">{{ $permintaan->lokasi->nama_lokasi ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-end">
                                        {{ optional($permintaan->tanggal)->diffForHumans() ?? $permintaan->created_at->diffForHumans() }}<br>
                                        <a href="{{ route('permintaan.show', $permintaan->id) }}" class="btn btn-warning btn-sm mt-1">Proses Permintaan</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td class="text-center p-4">Tidak ada permintaan pending.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">5 Stok Terendah</h6>
                    <a href="{{ route('barang-medis.index') }}">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                           <tbody>
                                @forelse ($stokTerendah as $barang)
                                <tr>
                                    <td>
                                        {{ $barang->nama_obat }}<br>
                                        <small class="text-muted">{{ $barang->tipe }}</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="fs-5">{{ number_format((int)$barang->stok_sum_jumlah) }}</strong><br>
                                        <small class="text-muted">{{ \App\Support\Presenters\StokPresenter::formatWithDefault($barang, (int)$barang->stok_sum_jumlah) }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr><td class="text-center p-4">Semua stok dalam kondisi aman.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
{{-- Style kustom untuk card di dashboard --}}
<style>
    .card .border-left-primary { border-left: .25rem solid #4e73df !important; }
    .card .border-left-success { border-left: .25rem solid #1cc88a !important; }
    .card .border-left-info { border-left: .25rem solid #36b9cc !important; }
    .card .border-left-warning { border-left: .25rem solid #f6c23e !important; }
    .card .border-left-danger { border-left: .25rem solid #e74a3b !important; }
    .text-gray-300 { color: #dddfeb !important; }
</style>
@endpush
