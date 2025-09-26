@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h3 class="card-title h2">Dashboard</h3>
                <p class="card-text text-muted">Selamat datang di sistem informasi Klinik.</p>
            </div>
        </div>

        <div class="card text-center text-bg-primary mb-4">
            <div class="card-body">
                  <h5 class="card-title">Jumlah Kunjungan Hari Ini</h5>
                  <p class="display-3 fw-bold mb-0">{{ $kasus_hari_ini }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-3">Jumlah per Jenis Penyakit (Bulan Ini)</h5>
                        <div class="card-content-container flex-grow-1" style="max-height: 200px; overflow-y: auto;">
                            @if($data_penyakit->isNotEmpty())
                                <ul class="list-unstyled mb-0">
                                    @php $max_penyakit = $data_penyakit->max('jumlah') ?: 1; @endphp
                                    @foreach($data_penyakit as $penyakit)
                                        <li class="mb-2">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-truncate me-2" title="{{ $penyakit->nama_penyakit }}">{{ $penyakit->nama_penyakit }}</span>
                                                <strong class="text-nowrap">{{ $penyakit->jumlah }}</strong>
                                            </div>
                                            <div class="progress" style="height: 0.75rem;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($penyakit->jumlah / $max_penyakit) * 100 }}%;" aria-valuenow="{{ $penyakit->jumlah }}" aria-valuemin="0" aria-valuemax="{{ $max_penyakit }}"></div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center text-muted h-100 d-flex align-items-center justify-content-center">
                                    <p class="mb-0">Belum ada data.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                         <h5 class="card-title mb-3">Persentase Penyakit (Bulan Ini)</h5>
                         <div class="card-content-container flex-grow-1" style="max-height: 200px; overflow-y: auto;">
                             @if($data_penyakit->isNotEmpty() && $total_kasus_penyakit > 0)
                                <ul class="list-unstyled mb-0">
                                     @foreach($data_penyakit as $penyakit)
                                     <li class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                         <strong class="fs-4 text-nowrap">{{ number_format(($penyakit->jumlah / $total_kasus_penyakit) * 100, 2) }}%</strong>
                                         <span class="text-muted align-self-center text-truncate ms-2" title="{{ $penyakit->nama_penyakit }}">{{ $penyakit->nama_penyakit }}</span>
                                     </li>
                                     @endforeach
                                </ul>
                            @else
                                <div class="text-center text-muted h-100 d-flex align-items-center justify-content-center">
                                    <p class="mb-0">Belum ada data.</p>
                                </div>
                            @endif
                         </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                         <h5 class="card-title mb-3">Jumlah Pemakaian Obat (Bulan Ini)</h5>
                         <div class="card-content-container flex-grow-1" style="max-height: 200px; overflow-y: auto;">
                             @if($data_obat->isNotEmpty())
                                <ul class="list-unstyled mb-0">
                                     @php $max_obat = $data_obat->max('jumlah') ?: 1; @endphp
                                     @foreach ($data_obat as $obat)
                                         <li class="mb-2">
                                             <div class="d-flex justify-content-between mb-1">
                                                 <span class="text-truncate me-2" title="{{ $obat->nama_obat }}">{{ $obat->nama_obat }}</span>
                                                 <strong class="text-nowrap">{{ $obat->jumlah }}</strong>
                                             </div>
                                             <div class="progress" style="height: 0.75rem;">
                                                 <div class="progress-bar" role="progressbar" style="width: {{ ($obat->jumlah / $max_obat) * 100 }}%;" aria-valuenow="{{ $obat->jumlah }}" aria-valuemin="0" aria-valuemax="{{ $max_obat }}"></div>
                                             </div>
                                         </li>
                                     @endforeach
                                </ul>
                            @else
                                <div class="text-center text-muted h-100 d-flex align-items-center justify-content-center">
                                    <p class="mb-0">Belum ada data.</p>
                                </div>
                            @endif
                         </div>
                    </div>
                </div>
            </div>

             <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-3">Persentase Pemakaian Obat (Bulan Ini)</h5>
                        <div class="card-content-container flex-grow-1" style="max-height: 200px; overflow-y: auto;">
                            @if($data_obat->isNotEmpty() && $total_pemakaian_obat > 0)
                                <ul class="list-unstyled mb-0">
                                     @foreach($data_obat as $obat)
                                     <li class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                         <strong class="fs-4 text-nowrap">{{ number_format(($obat->jumlah / $total_pemakaian_obat) * 100, 2) }}%</strong>
                                         <span class="text-muted align-self-center text-truncate ms-2" title="{{ $obat->nama_obat }}">{{ $obat->nama_obat }}</span>
                                     </li>
                                     @endforeach
                                </ul>
                            @else
                                <div class="text-center text-muted h-100 d-flex align-items-center justify-content-center">
                                    <p class="mb-0">Belum ada data.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
<style>
    /* Custom scrollbar styling untuk dashboard cards */
    .card-content-container {
        scrollbar-width: thin;
        scrollbar-color: #dee2e6 #f8f9fa;
    }
    
    .card-content-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .card-content-container::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 3px;
    }
    
    .card-content-container::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }
    
    .card-content-container::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }
    
    /* Hover effect untuk scroll container */
    .card-content-container:hover::-webkit-scrollbar-thumb {
        background: #6c757d;
    }
    
    /* Smooth scrolling */
    .card-content-container {
        scroll-behavior: smooth;
    }
    
    /* Better spacing dan padding untuk scroll area */
    .card-content-container {
        padding-right: 8px;
        margin-right: -8px;
    }
    
    /* Ensure progress bars don't break layout */
    .progress {
        min-width: 100px;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .card-content-container {
            max-height: 180px;
        }
    }
    
    /* Fade effect at the bottom to indicate more content */
    .card-content-container::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 20px;
        background: linear-gradient(transparent, rgba(255,255,255,0.8));
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .card-content-container:hover::after {
        opacity: 1;
    }
    
    /* Make sure the card body uses flexbox properly */
    .card-body.d-flex.flex-column {
        min-height: 250px;
    }
    
    @media (max-width: 768px) {
        .card-body.d-flex.flex-column {
            min-height: 220px;
        }
    }
    
    /* Text truncation improvements */
    .text-truncate {
        max-width: 200px;
    }
    
    @media (max-width: 992px) {
        .text-truncate {
            max-width: 150px;
        }
    }
</style>
@endpush
