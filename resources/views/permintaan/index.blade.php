@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Daftar Permintaan Barang</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                {{-- Tombol Buat Baru hanya untuk DOKTER --}}
                @if(Auth::user()->hasRole('DOKTER'))
                    <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Buat Permintaan Baru
                    </a>
                @else
                    {{-- Beri div kosong agar form pencarian tetap di kanan --}}
                    <div class="flex-grow-1"></div>
                @endif

                {{-- Form Pencarian --}}
                <form action="{{ route('permintaan.index') }}" method="GET" class="d-flex flex-wrap gap-2" style="max-width: 420px;">
                    <input type="search" class="form-control" name="search" placeholder="Cari kode, peminta, lokasi..." value="{{ request('search') }}">
                    @php
                        $statusOptions = [
                            'PENDING' => 'Pending',
                            'APPROVED' => 'Disetujui',
                            'COMPLETED' => 'Selesai',
                            'REJECTED' => 'Ditolak',
                        ];
                    @endphp
                    <select name="status" class="form-select" style="max-width: 180px;">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        @if(request()->hasAny(['search', 'status']) && (request('search') || request('status')))
                            <a href="{{ route('permintaan.index') }}" class="btn btn-light" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
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
                                <td>{{ $item->kode_permintaan }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_permintaan)->isoFormat('D MMMM YYYY') }}</td>
                                <td>{{ $item->userPeminta->nama_karyawan ?? 'N/A' }}</td>
                                <td>{{ $item->lokasiPeminta->nama_lokasi ?? 'N/A' }}</td>
                                <td>
                                    @switch($item->status)
                                        @case('PENDING')
                                            <span class="badge bg-warning text-dark">PENDING</span>
                                            @break
                                        @case('APPROVED')
                                            <span class="badge bg-info">DISETUJUI</span>
                                            @break
                                        @case('COMPLETED')
                                            <span class="badge bg-success">SELESAI</span>
                                            @break
                                        @case('REJECTED')
                                            <span class="badge bg-danger">DITOLAK</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $item->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if(Auth::user()->hasRole('PENGADAAN') && $item->status == 'PENDING')
                                        <a href="{{ route('permintaan.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Proses Permintaan"><i class="bi bi-pencil-square"></i></a>
                                    @else
                                        <a href="{{ route('permintaan.show', $item->id) }}" class="btn btn-info btn-sm" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                                    @endif
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
