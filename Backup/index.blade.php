@extends('layouts.sidebar-layout')

@section('content')
    <style>
        .table-permintaan .kode-chip{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, monospace;background:#f1f3f5;padding:.28rem .5rem;border-radius:.35rem;color:#212529;border:1px solid #e9ecef}
        .status-badge{font-size:.74rem;padding:.28rem .5rem}
        .table-permintaan td, .table-permintaan th{vertical-align:middle}
        .table-permintaan thead th{background:#fafafa}
        .search-form{width:300px}
        .action-col .btn{min-width:36px}
    </style>
    <h1 class="h2 mb-4">Daftar Permintaan Barang</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">

                {{-- Tombol Buat Baru hanya untuk DOKTER --}}
                @if(Auth::user()->hasRole('DOKTER'))
                    <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Buat Permintaan Baru
                    </a>
                @else
                    {{-- Beri div kosong agar form pencarian tetap di kanan --}}
                    <div></div>
                @endif

                {{-- Form Pencarian --}}
                <form action="{{ route('permintaan.index') }}" method="GET" class="d-flex" style="width: 300px;">
                    <input type="search" class="form-control me-2" name="search" placeholder="Cari Kode, Peminta..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-permintaan">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Peminta</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permintaan as $item)
                            <tr class="align-middle">
                                <td>{{ $loop->iteration + $permintaan->firstItem() - 1 }}</td>
                                <td><span class="kode-chip">{{ $item->kode_permintaan }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_permintaan)->isoFormat('D MMMM YYYY') }}</td>
                                <td>{{ $item->userPeminta->nama_karyawan ?? 'N/A' }}</td>
                                <td>{{ $item->lokasiPeminta->nama_lokasi ?? 'N/A' }}</td>
                                <td>
                                    @switch($item->status)
                                        @case('PENDING')
                                            <span class="badge bg-warning text-dark status-badge">PENDING</span>
                                            @break
                                        @case('APPROVED')
                                            <span class="badge bg-info status-badge">DISETUJUI</span>
                                            @break
                                        @case('COMPLETED')
                                            <span class="badge bg-success status-badge">SELESAI</span>
                                            @break
                                        @case('REJECTED')
                                            <span class="badge bg-danger status-badge">DITOLAK</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary status-badge">{{ $item->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="action-col d-flex gap-1">
                                        @if(Auth::user()->hasRole('PENGADAAN') && $item->status == 'PENDING')
                                            <a href="{{ route('permintaan.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Proses Permintaan"><i class="bi bi-pencil-square"></i></a>
                                        @else
                                            <a href="{{ route('permintaan.show', $item->id) }}" class="btn btn-info btn-sm" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data permintaan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $permintaan->links() }}
            </div>
        </div>
    </div>
@endsection
