@extends('layouts.sidebar-layout')

@section('title', 'Detail Distribusi Barang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="bi bi-arrow-left-right me-2"></i> Detail Distribusi Barang
                </h4>
                <a href="{{ route('distribusi-barang.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
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

            <div class="row">
                <!-- Detail Distribusi -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Informasi Distribusi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Tanggal & Waktu:</strong>
                                </div>
                                <div class="col-md-8">
                                    {{ $distribusi->created_at->format('d/m/Y H:i:s') }}
                                    <small class="text-muted">({{ $distribusi->created_at->diffForHumans() }})</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>ID Distribusi:</strong>
                                </div>
                                <div class="col-md-8">
                                    <code>#{{ $distribusi->id_distribusi }}</code>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Barang:</strong>
                                </div>
                                <div class="col-md-8">
                                    <strong>{{ $distribusi->barang->nama_obat }}</strong><br>
                                    <small class="text-muted">Kode: {{ $distribusi->barang->kode_obat }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Jumlah Distribusi:</strong>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="text-primary mb-0">
                                        {{ number_format($distribusi->jumlah) }} {{ $distribusi->barang->satuan_terkecil }}
                                    </h5>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Lokasi Asal:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="badge bg-info fs-6">
                                        <i class="bi bi-geo-alt"></i> {{ $distribusi->lokasiAsal->nama_lokasi }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Lokasi Tujuan:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-geo-alt-fill"></i> {{ $distribusi->lokasiTujuan->nama_lokasi }}
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Dilakukan Oleh:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-person-circle fs-3 text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $distribusi->user->name }}</strong><br>
                                            <small class="text-muted">{{ $distribusi->user->email }}</small><br>
                                            @if($distribusi->user->hasRole('DOKTER'))
                                                <span class="badge bg-primary mt-1">DOKTER</span>
                                            @elseif($distribusi->user->hasRole('PENGADAAN'))
                                                <span class="badge bg-warning mt-1">PENGADAAN</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($distribusi->keterangan)
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>Keterangan:</strong>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="alert alert-light mb-0">
                                            {{ $distribusi->keterangan }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Status & Validasi -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Status Distribusi</h5>
                        </div>
                        <div class="card-body text-center">
                            @if($distribusi->status === 'approved')
                                <div class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-success mb-3">DISETUJUI</h5>
                            @elseif($distribusi->status === 'pending')
                                <div class="mb-3">
                                    <i class="bi bi-clock-fill text-warning" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-warning mb-3">PENDING</h5>
                            @else
                                <div class="mb-3">
                                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-danger mb-3">DITOLAK</h5>
                            @endif

                            @if($distribusi->validated_at)
                                <hr>
                                <div class="text-start">
                                    <small class="text-muted d-block mb-2">
                                        <strong>Divalidasi oleh:</strong>
                                    </small>
                                    <p class="mb-1">
                                        {{ $distribusi->validator->name }}
                                    </p>
                                    <small class="text-muted">
                                        {{ $distribusi->validated_at->format('d/m/Y H:i') }}
                                    </small>

                                    @if($distribusi->validation_note)
                                        <div class="alert alert-secondary mt-3 mb-0">
                                            <small>
                                                <strong>Catatan:</strong><br>
                                                {{ $distribusi->validation_note }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline Info -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Timeline</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="bi bi-circle-fill text-primary me-2" style="font-size: 0.5rem;"></i>
                                    <small>
                                        <strong>Dibuat:</strong><br>
                                        {{ $distribusi->created_at->format('d/m/Y H:i:s') }}
                                    </small>
                                </li>
                                @if($distribusi->validated_at)
                                    <li>
                                        <i class="bi bi-circle-fill text-{{ $distribusi->status === 'approved' ? 'success' : 'danger' }} me-2" style="font-size: 0.5rem;"></i>
                                        <small>
                                            <strong>{{ $distribusi->status === 'approved' ? 'Disetujui' : 'Ditolak' }}:</strong><br>
                                            {{ $distribusi->validated_at->format('d/m/Y H:i:s') }}
                                        </small>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
