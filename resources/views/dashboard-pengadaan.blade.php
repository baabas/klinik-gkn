@extends('layouts.top-nav-layout')

@section('content')
<div class="container-fluid px-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Dashboard Pengadaan</h1>
        <small class="text-muted">{{ date('d M Y, H:i') }}</small>
    </div>

    {{-- Baris pertama - Card Statistik Utama (Golden Ratio Layout) --}}
    <div class="row g-3 mb-4">
        <!-- Card utama dengan proporsi golden ratio -->
        <div class="col-lg-8">
            <div class="row g-3 h-100">
                <div class="col-md-6">
                    <div class="card border-left-success shadow-sm h-100">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-2">Permintaan Disetujui</div>
                                    <div class="h2 mb-0 font-weight-bold text-dark">{{ $permintaanApproved }}</div>
                                    <small class="text-muted">Ready untuk distribusi</small>
                                </div>
                                <div class="ms-3">
                                    <i class="bi bi-check-circle-fill text-success opacity-75" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-left-info shadow-sm h-100">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-2">Permintaan Selesai</div>
                                    <div class="h2 mb-0 font-weight-bold text-dark">{{ $permintaanCompleted }}</div>
                                    <small class="text-muted">Telah didistribusi</small>
                                </div>
                                <div class="ms-3">
                                    <i class="bi bi-check2-all text-info opacity-75" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar dengan proporsi golden ratio -->
        <div class="col-lg-4">
            <div class="row g-3 h-100">
                <div class="col-12">
                    <div class="card border-left-danger shadow-sm h-100">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-2">Stok Kritis</div>
                                    <div class="h2 mb-0 font-weight-bold text-danger">{{ $stokMenipis }}</div>
                                    <small class="text-muted">Perlu segera diproses</small>
                                </div>
                                <div class="ms-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger opacity-75" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris kedua - Info Card Secondary --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-12">
            <div class="card border-left-primary shadow-sm">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Master Data Barang</div>
                                    <div class="h4 mb-0 font-weight-bold text-dark d-inline">{{ $totalMasterBarang }}</div>
                                    <span class="text-muted ms-2">total jenis item dalam sistem</span>
                                </div>
                                <div class="ms-3">
                                    <i class="bi bi-archive-fill fs-2 text-primary opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('barang-medis.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-arrow-right me-1"></i>Kelola Barang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris ketiga - Content Area dengan Golden Ratio --}}
    <div class="row g-3">
        <!-- Area utama (62% golden ratio) -->
        <div class="col-lg-8">
            <div class="row g-3 h-100">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <h6 class="m-0 font-weight-bold">Permintaan Pending</h6>
                            <a href="{{ route('permintaan.index') }}" class="btn btn-outline-primary btn-sm">Kelola</a>
                        </div>
                        <div class="card-body p-3" style="max-height: 320px; overflow-y: auto;">
                            @forelse ($permintaanTerbaru as $permintaan)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $permintaan->kode_permintaan }}</div>
                                        <small class="text-muted d-block">{{ $permintaan->lokasiPeminta->nama_lokasi ?? 'N/A' }}</small>
                                        <small class="text-info">{{ $permintaan->userPeminta->nama_karyawan ?? 'N/A' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M') }}</small>
                                        <a href="{{ route('permintaan.edit', $permintaan->id) }}" class="btn btn-warning btn-sm mt-1">Proses</a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                                <p class="text-muted">Tidak ada permintaan pending.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light py-2">
                            <h6 class="m-0 font-weight-bold">Trending Barang</h6>
                            <small class="text-muted">Bulan ini</small>
                        </div>
                        <div class="card-body p-3" style="max-height: 320px; overflow-y: auto;">
                            @forelse ($trendingBarang as $barang)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ Str::limit($barang->nama_obat, 25) }}</div>
                                    <small class="text-muted">Kemasan: {{ $barang->kemasan }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary rounded-pill">{{ $barang->total_diminta }}</span>
                                    <small class="text-muted d-block">Box</small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="bi bi-graph-up fs-1 text-muted mb-2"></i>
                                <p class="text-muted">Belum ada data trending.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (38% golden ratio) -->
        <div class="col-lg-4">
            <div class="row g-3 h-100">
                <div class="col-12">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <h6 class="m-0 font-weight-bold">Stok Kritis</h6>
                            <a href="{{ route('barang-medis.index') }}" class="btn btn-outline-danger btn-sm">Kelola</a>
                        </div>
                        <div class="card-body p-3" style="max-height: 320px; overflow-y: auto;">
                            @forelse ($stokTerendah as $barang)
                                <tr>
                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ Str::limit($barang->nama_obat, 22) }}</div>
                                    <small class="text-muted">{{ $barang->kategori_barang }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold fs-5 me-2 {{ (int)$barang->stok_sum_jumlah < 10 ? 'text-danger' : ((int)$barang->stok_sum_jumlah < 30 ? 'text-warning' : 'text-success') }}">
                                            {{ (int)$barang->stok_sum_jumlah }}
                                        </span>
                                        <div class="text-center">
                                            <small class="text-muted d-block">{{ $barang->kemasan ?? 'Box' }}</small>
                                            @if((int)$barang->stok_sum_jumlah < 10)
                                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                            @elseif((int)$barang->stok_sum_jumlah < 30)
                                                <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle fs-1 text-success mb-2"></i>
                                <p class="text-muted">Semua stok dalam kondisi baik.</p>
                            </div>
                            @endforelse
                            
                            <!-- Distribusi Lokasi di bagian bawah sidebar -->
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="font-weight-bold mb-3">Distribusi Permintaan</h6>
                                @forelse ($distribusiLokasi as $lokasi)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="fw-bold">{{ Str::limit($lokasi->nama_lokasi, 18) }}</small>
                                        <span class="badge bg-info rounded-pill">{{ $lokasi->jumlah_permintaan }}</span>
                                    </div>
                                    @php 
                                        $maxPermintaan = $distribusiLokasi->max('jumlah_permintaan') ?: 1;
                                        $percentage = ($lokasi->jumlah_permintaan / $maxPermintaan) * 100;
                                    @endphp
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $lokasi->jumlah_permintaan }}"></div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-center text-muted small">Belum ada distribusi data.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Style kustom untuk dashboard golden ratio --}}
<style>
    .card .border-left-primary { border-left: .25rem solid #4e73df !important; }
    .card .border-left-success { border-left: .25rem solid #1cc88a !important; }
    .card .border-left-info { border-left: .25rem solid #36b9cc !important; }
    .card .border-left-warning { border-left: .25rem solid #f6c23e !important; }
    .card .border-left-danger { border-left: .25rem solid #e74a3b !important; }
    
    /* Golden ratio dashboard styles */
    .container-fluid { 
        max-height: 100vh; 
        overflow: hidden;
        padding: 1rem 1.5rem;
    }
    
    .card {
        transition: all 0.3s ease-in-out;
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    /* Main statistics cards */
    .card-body {
        padding: 1.5rem;
    }
    
    .card-header {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border-bottom: 1px solid #dee2e6;
        border-radius: 12px 12px 0 0 !important;
    }
    
    /* Large statistics display */
    .h2 {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    
    /* Content areas with perfect scrolling */
    .card-body::-webkit-scrollbar {
        width: 6px;
    }
    
    .card-body::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 6px;
    }
    
    .card-body::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #6c757d, #495057);
        border-radius: 6px;
    }
    
    .card-body::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #495057, #343a40);
    }
    
    /* Golden ratio proportions */
    @media (min-width: 992px) {
        .col-lg-8 { flex: 0 0 61.803%; max-width: 61.803%; }
        .col-lg-4 { flex: 0 0 38.197%; max-width: 38.197%; }
    }
    
    /* Enhanced visual hierarchy */
    .text-xs {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    /* Action buttons */
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .btn-outline-primary, .btn-outline-danger {
        border-width: 2px;
    }
    
    /* Progress bars */
    .progress {
        border-radius: 50px;
        background-color: #e9ecef;
    }
    
    .progress-bar {
        border-radius: 50px;
        background: linear-gradient(90deg, #17a2b8, #138496);
    }
    
    /* Status indicators with icons */
    .badge {
        font-size: 0.75rem;
        padding: 0.5em 0.75em;
    }
    
    /* Empty state icons */
    .bi.fs-1 {
        font-size: 3rem !important;
        opacity: 0.5;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container-fluid { padding: 0.75rem; }
        .h2 { font-size: 2rem; }
        .card-body { padding: 1rem; }
        .card-header { padding: 0.5rem 1rem; }
    }
    
    /* Remove unwanted borders */
    .border-bottom:last-child {
        border-bottom: none !important;
    }
    
    /* Hover effects for interactive elements */
    .border-bottom:hover {
        background-color: #f8f9fa;
        border-radius: 6px;
        margin: 0 -0.5rem;
        padding: 0.5rem !important;
    }
</style>
@endpush
