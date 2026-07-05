@extends('layouts.sidebar-layout')

@section('content')
    <style>
        .page-head{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.25rem}
        .page-head h1{margin-bottom:.25rem}
        .page-head .subtitle{color:#6c757d;font-size:.925rem}
        .action-group{display:flex;gap:.5rem;flex-wrap:wrap}
        .action-group .btn{min-width:170px}
        .btn-soft-secondary{background:#f8f9fa;border:1px solid #dee2e6;color:#495057}
        .btn-soft-secondary:hover{background:#e9ecef;color:#212529}
        .card-header-clean{display:flex;justify-content:space-between;align-items:center;gap:1rem}
        .info-grid p{margin-bottom:.5rem}
        .table-detail thead th{font-weight:600;white-space:nowrap}
        .table-detail td,.table-detail th{vertical-align:middle}
        .approval-input{max-width:120px;margin:0 auto}
        .action-panel{display:flex;justify-content:flex-end;gap:.75rem;flex-wrap:wrap}
        .action-panel .btn{min-width:220px}
        @media (max-width: 768px){
            .page-head,.card-header-clean,.action-panel{flex-direction:column;align-items:stretch}
            .action-group .btn,.action-panel .btn{min-width:100%;width:100%}
        }
    </style>

    <div class="page-head">
        <div>
            <h1 class="h3 mb-0">Proses Permintaan Obat</h1>
            <div class="subtitle">Periksa rincian permintaan dan tentukan jumlah yang disetujui.</div>
        </div>
        <div class="action-group">
            <a href="{{ route('permintaan.index') }}" class="btn btn-soft-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- Form utama yang akan mengirim data update --}}
    <form action="{{ route('permintaan.update', $permintaan->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Kartu Informasi Header Permintaan --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light card-header-clean">
                <div>
                    <h5 class="mb-0">Informasi Permintaan</h5>
                    <small class="text-muted">Ringkasan data sebelum proses persetujuan.</small>
                </div>
                <span class="badge bg-secondary">{{ $permintaan->kode_permintaan }}</span>
            </div>
            <div class="card-body info-grid">
                <div class="row g-2">
                    <div class="col-md-6"><p><strong>Kode:</strong> {{ $permintaan->kode_permintaan }}</p></div>
                    <div class="col-md-6"><p><strong>Peminta:</strong> {{ $permintaan->userPeminta->nama_karyawan ?? 'N/A' }}</p></div>
                    <div class="col-md-6"><p class="mb-0"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->isoFormat('D MMMM YYYY') }}</p></div>
                    <div class="col-md-6"><p class="mb-0"><strong>Lokasi:</strong> {{ $permintaan->lokasiPeminta->nama_lokasi ?? 'N/A' }}</p></div>
                </div>
            </div>
        </div>

        {{-- Kartu Rincian Barang dengan Input Persetujuan --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light card-header-clean">
                <div>
                    <h5 class="mb-0">Rincian Obat untuk Diproses</h5>
                    <small class="text-muted">Sesuaikan jumlah yang disetujui sesuai stok dan kebutuhan.</small>
                </div>
                <span class="badge bg-info text-dark">{{ $permintaan->detail->count() }} item</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 table-detail">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Obat</th>
                                <th class="text-center">Jumlah Diminta</th>
                                <th class="text-center" style="width: 20%;">Jumlah Disetujui</th>
                                <th class="text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permintaan->detail as $index => $item)
                                <tr class="align-middle">
                                    <td>
                                        <input type="hidden" name="detail[{{ $index }}][id]" value="{{ $item->id }}">
                                        @if ($item->barangMedis)
                                            <strong>{{ $item->barangMedis->nama_obat }}</strong>
                                        @else
                                            <strong>{{ $item->nama_barang_baru }}</strong>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->jumlah_diminta }}</td>
                                    <td class="text-center">
                                        <input type="number" name="detail[{{ $index }}][jumlah_disetujui]" class="form-control form-control-sm text-center approval-input" value="{{ old('detail.'.$index.'.jumlah_disetujui', $item->jumlah_diminta) }}" min="0">
                                    </td>
                                    <td class="text-center">
                                        @if ($item->barangMedis)
                                            <span class="badge bg-primary">Obat Terdaftar</span>
                                        @else
                                            <span class="badge bg-success">Request Baru</span>
                                            <br><small class="text-muted fst-italic">Daftarkan Ke Daftar Barang Medis Terlebih dahulu</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel Aksi untuk Pengadaan --}}
        <div class="card shadow-sm mt-4">
            <div class="card-body bg-light">
                <div class="action-panel">
                    <button type="submit" name="action" value="REJECTED" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin MENOLAK seluruh permintaan ini?')">Tolak Permintaan</button>
                    <button type="submit" name="action" value="APPROVED" class="btn btn-success px-4">Simpan & Setujui Permintaan</button>
                </div>
            </div>
        </div>
    </form>
@endsection
