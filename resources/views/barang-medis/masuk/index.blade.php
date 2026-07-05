@extends('layouts.sidebar-layout')

@section('content')
    <style>
        .page-head{display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid #e9ecef}
        .page-head h1{margin-bottom:.25rem;font-weight:700;letter-spacing:-.02em}
        .page-head .subtitle{color:#6c757d;font-size:.95rem}
        .action-group{display:flex;gap:.5rem;flex-wrap:wrap;align-items:center}
        .action-group .btn{min-width:170px;box-shadow:0 1px 2px rgba(0,0,0,.05)}
        .btn-soft-secondary{background:#fff;border:1px solid #d6dbe1;color:#495057;transition:all .2s ease}
        .btn-soft-secondary:hover{background:#f8f9fa;color:#212529;border-color:#cfd5dc;transform:translateY(-1px)}
        .card-stat{border:1px solid #e9ecef;box-shadow:0 .125rem .5rem rgba(0,0,0,.04);border-radius:.75rem}
        .card-stat .card-body{padding:1.25rem}
        .info-banner{border:1px solid #dbe4ff;background:#f8fbff}
        .filter-card{border:1px solid #e9ecef;box-shadow:0 .125rem .5rem rgba(0,0,0,.04);border-radius:.75rem}
        .filter-card .form-label{font-weight:600;color:#495057}
        .filter-card .form-control,.filter-card .form-select{border:1px solid #d8dde3;box-shadow:0 .125rem .25rem rgba(0,0,0,.03)}
        .table-wrap{border:1px solid #e9ecef;border-radius:.75rem;overflow:hidden}
        .table-wrap thead th{font-weight:700;white-space:nowrap;background:#f8f9fa}
        .table-wrap td,.table-wrap th{vertical-align:middle}
        .empty-state{padding:2rem 1rem;text-align:center;color:#6c757d}
        .empty-state i{font-size:2rem;color:#dee2e6;margin-bottom:.5rem}
        @media (max-width: 768px){
            .page-head{flex-direction:column;align-items:stretch}
            .action-group .btn{min-width:100%;width:100%}
        }
    </style>

    <div class="page-head">
        <div>
            <h1 class="h3 mb-0">Riwayat Barang Masuk</h1>
            <div class="subtitle">Pantau seluruh transaksi barang masuk dari pengadaan dan input langsung.</div>
        </div>
        <div class="action-group">
            <a href="{{ route('barang-medis.index') }}" class="btn btn-soft-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Barang
            </a>
            @if(Auth::user()->hasRole('PENGADAAN'))
                <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Input Barang Masuk
                </a>
            @endif
        </div>
    </div>

    <!-- Card Statistik -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card card-stat border-primary">
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
            <div class="card card-stat border-success">
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

    <div class="alert alert-info info-banner mb-3">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Informasi:</strong> Halaman ini menampilkan riwayat barang yang masuk dari input barang masuk oleh role Pengadaan, 
        baik yang berasal dari permintaan dokter maupun input langsung tanpa permintaan.
    </div>

    <div class="card shadow-sm filter-card">
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

            <div class="table-responsive table-wrap">
                <table class="table table-bordered table-striped table-hover mb-0">
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
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <div>Belum ada data barang masuk.</div>
                                    </div>
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
