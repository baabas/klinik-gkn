@extends('layouts.sidebar-layout')

@section('title', 'Log Distribusi Barang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="bi bi-arrow-left-right me-2"></i> Log Distribusi Barang
                </h4>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('distribusi-barang.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lokasi Asal</label>
                                <select name="lokasi_asal" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach($lokasi as $lok)
                                        <option value="{{ $lok->id }}" {{ request('lokasi_asal') == $lok->id ? 'selected' : '' }}>
                                            {{ $lok->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lokasi Tujuan</label>
                                <select name="lokasi_tujuan" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach($lokasi as $lok)
                                        <option value="{{ $lok->id }}" {{ request('lokasi_tujuan') == $lok->id ? 'selected' : '' }}>
                                            {{ $lok->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('distribusi-barang.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    @if($distribusi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Barang</th>
                                        <th>Dari</th>
                                        <th>Ke</th>
                                        <th>Jumlah</th>
                                        <th>Dilakukan Oleh</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($distribusi as $dist)
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $dist->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <strong>{{ $dist->barang->nama_obat }}</strong><br>
                                                <small class="text-muted">{{ $dist->barang->kode_obat }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $dist->lokasiAsal->nama_lokasi }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ $dist->lokasiTujuan->nama_lokasi }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($dist->jumlah) }}</strong> {{ $dist->barang->satuan_terkecil }}
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $dist->user->name }}<br>
                                                    @if($dist->user->hasRole('DOKTER'))
                                                        <span class="badge bg-primary">DOKTER</span>
                                                    @else
                                                        <span class="badge bg-warning">PENGADAAN</span>
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                @if($dist->status === 'approved')
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif($dist->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('distribusi-barang.show', $dist->id_distribusi) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $distribusi->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i> Tidak ada data distribusi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
