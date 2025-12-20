@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Riwayat Barang Masuk</h1>
        <div class="btn-group">
            <a href="{{ route('barang-medis.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Barang
            </a>
            @if(Auth::user()->hasRole('PENGADAAN'))
                <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Input Barang Masuk
                </a>
            @endif
        </div>
    </div>

    <!-- Card Statistik -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-box-seam text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Transaksi Barang Masuk</h6>
                            <h3 class="mb-0">{{ number_format($totalEntries) }}</h3>
                            <small class="text-muted">transaksi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-boxes text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Kemasan Masuk</h6>
                            <h3 class="mb-0">{{ number_format($totalKemasan) }}</h3>
                            <small class="text-muted">kemasan/box</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mb-3">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Informasi:</strong> Halaman ini menampilkan riwayat barang yang masuk dari input barang masuk oleh role Pengadaan, 
        baik yang berasal dari permintaan dokter maupun input langsung tanpa permintaan.
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('barang-masuk.index') }}" method="GET" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="q" class="form-label">Cari Nama/Kode</label>
                    <input type="search" name="q" id="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Contoh: Paracetamol">
                </div>
                <div class="col-md-3">
                    <label for="barang" class="form-label">Filter Barang</label>
                    <select name="barang" id="barang" class="form-select">
                        <option value="">Semua Barang</option>
                        @foreach($barang as $item)
                            <option value="{{ $item->id_obat }}" {{ request('barang') == $item->id_obat ? 'selected' : '' }}>
                                {{ $item->nama_obat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="tanggal" class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}" class="form-control">
                </div>
                <div class="col-md-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th style="width: 100px;">Tanggal Masuk</th>
                            <th>Nama Barang</th>
                            <th style="width: 120px;">Lokasi</th>
                            <th style="width: 100px;" class="text-center">Jumlah Kemasan</th>
                            <th style="width: 100px;" class="text-center">Isi per Kemasan</th>
                            <th style="width: 100px;" class="text-center">Total (Satuan)</th>
                            <th style="width: 100px;">Kedaluwarsa</th>
                            <th style="width: 100px;">Petugas</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr class="align-middle">
                                <td class="text-center">{{ $loop->iteration + $entries->firstItem() - 1 }}</td>
                                <td>
                                    {{ optional($entry->tanggal_transaksi)->format('d/m/Y') ?? $entry->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    <strong>{{ $entry->barang->nama_obat ?? '-' }}</strong>
                                    <div class="text-muted small">Kode: {{ $entry->barang->kode_obat ?? '-' }}</div>
                                </td>
                                <td>{{ $entry->lokasi->nama_lokasi ?? '-' }}</td>
                                <td class="text-center">
                                    @if($entry->jumlah_kemasan)
                                        <strong class="text-success">{{ number_format($entry->jumlah_kemasan) }}</strong>
                                        @if($entry->satuan_kemasan)
                                            <div class="text-muted small">{{ $entry->satuan_kemasan }}</div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($entry->isi_per_kemasan)
                                        {{ number_format($entry->isi_per_kemasan) }}
                                        <div class="text-muted small">{{ strtolower($entry->barang->satuan_terkecil ?? '') }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <strong class="text-success">+{{ number_format($entry->perubahan) }}</strong>
                                    <div class="text-muted small">{{ strtolower($entry->barang->satuan_terkecil ?? '') }}</div>
                                </td>
                                <td>
                                    @if($entry->expired_at)
                                        {{ $entry->expired_at->format('d/m/Y') }}
                                        @php
                                            $daysUntilExpiry = now()->diffInDays($entry->expired_at, false);
                                        @endphp
                                        @if($daysUntilExpiry < 0)
                                            <span class="badge bg-danger small">Expired</span>
                                        @elseif($daysUntilExpiry < 30)
                                            <span class="badge bg-warning text-dark small">{{ ceil($daysUntilExpiry) }} hari</span>
                                        @elseif($daysUntilExpiry < 90)
                                            <span class="badge bg-info text-dark small">{{ ceil($daysUntilExpiry) }} hari</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $entry->user->nama_karyawan ?? 'admin' }}</td>
                                <td>
                                    <small>{{ $entry->keterangan }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <div class="text-muted mt-2">Belum ada data barang masuk.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $entries->links() }}
            </div>
        </div>
    </div>
@endsection
