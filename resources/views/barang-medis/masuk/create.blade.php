@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Input Barang/Obat Masuk</h1>

    {{-- Card Note: Daftar Permintaan yang Disetujui --}}
    @if($approvedRequests->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Referensi Permintaan Disetujui
                </h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRequests" aria-expanded="true">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse show" id="collapseRequests">
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="bi bi-lightbulb me-1"></i>
                        Berikut adalah daftar permintaan yang telah disetujui dan memerlukan input barang masuk. Gunakan sebagai referensi untuk input stok.
                    </p>
                    
                    <div class="row">
                        @foreach($approvedRequests as $requestId => $items)
                            @php
                                $firstItem = $items->first();
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="card border {{ $selectedRequest && $selectedRequest->first()->id_permintaan == $requestId ? 'border-primary bg-light' : 'border-secondary' }}">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-1">{{ $firstItem->kode_permintaan }}</h6>
                                            @if($selectedRequest && $selectedRequest->first()->id_permintaan == $requestId)
                                                <span class="badge bg-primary">Dipilih</span>
                                            @endif
                                        </div>
                                        
                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse($firstItem->tanggal_permintaan)->isoFormat('D MMM YYYY') }}
                                            <br>
                                            <i class="bi bi-geo-alt me-1"></i>{{ $firstItem->nama_lokasi }}
                                            <br>
                                            <i class="bi bi-person me-1"></i>{{ $firstItem->nama_karyawan ?? $firstItem->user_nama }}
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <h6 class="small fw-bold mb-0">Items Disetujui: 
                                                    <span class="badge bg-info">{{ $items->count() }} item{{ $items->count() > 1 ? 's' : '' }}</span>
                                                </h6>
                                                @if($items->count() > 5)
                                                    <small class="text-muted">Total: {{ $items->sum('jumlah_disetujui') }} kemasan</small>
                                                @endif
                                            </div>
                                            @if($items->count() > 5)
                                                <button class="btn btn-sm btn-outline-primary toggle-items" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#items-{{ $firstItem->id_permintaan }}" 
                                                        aria-expanded="false">
                                                    <span class="show-text"><i class="bi bi-eye"></i> Lihat Semua</span>
                                                    <span class="hide-text d-none"><i class="bi bi-eye-slash"></i> Sembunyikan</span>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="items-container border rounded p-2 {{ $items->count() > 5 ? 'collapse' : '' }}" 
                                             id="items-{{ $firstItem->id_permintaan }}"
                                             style="max-height: 250px; overflow-y: auto; background-color: #f8f9fa;">
                                            @if($items->count() > 3 && $items->count() <= 5)
                                                <small class="text-info d-block mb-2">
                                                    <i class="bi bi-arrow-down"></i> Scroll untuk melihat semua items
                                                </small>
                                            @endif
                                            <ul class="list-unstyled small mb-0">
                                                @foreach($items as $item)
                                                    <li class="mb-2 p-2 bg-light rounded item-tracker" 
                                                        data-item-id="{{ $item->id_detail }}"
                                                        data-barang-id="{{ $item->id_barang }}"
                                                        data-barang-nama="{{ $item->id_barang ? $item->nama_obat : $item->nama_barang_baru }}"
                                                        data-jumlah-disetujui="{{ $item->jumlah_disetujui }}"
                                                        data-request-id="{{ $item->id_permintaan }}">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="flex-grow-1 d-flex align-items-start">
                                                                <!-- Checkbox untuk status completion -->
                                                                <div class="form-check me-2" style="margin-top: 2px;">
                                                                    <input class="form-check-input item-checkbox" type="checkbox" 
                                                                           id="item-{{ $item->id_detail }}" 
                                                                           data-item-id="{{ $item->id_detail }}"
                                                                           disabled readonly>
                                                                    <label class="form-check-label visually-hidden" for="item-{{ $item->id_detail }}">
                                                                        Item completed
                                                                    </label>
                                                                </div>
                                                                
                                                                <div class="item-content">
                                                                    <div class="item-info">
                                                                        <span class="badge bg-success me-2 required-qty">{{ $item->jumlah_disetujui }}</span>
                                                                        @if($item->id_barang)
                                                                            <strong class="item-name clickable-item-name" 
                                                                                    data-barang-id="{{ $item->id_barang }}"
                                                                                    data-nama="{{ $item->nama_obat }}"
                                                                                    data-kode="{{ $item->kode_obat }}"
                                                                                    data-kemasan="{{ $item->kemasan_diminta ?? 'Box' }}"
                                                                                    style="cursor: pointer; color: #0d6efd; text-decoration: underline;"
                                                                                    title="Klik untuk auto-fill nama barang">
                                                                                {{ $item->nama_obat }}
                                                                            </strong>
                                                                            <small class="text-muted">({{ $item->kode_obat }})</small>
                                                                            <small class="text-info ms-2 auto-fill-hint">
                                                                                <i class="bi bi-cursor-fill"></i> Klik untuk auto-fill
                                                                            </small>
                                                                        @else
                                                                            <strong class="item-name">{{ $item->nama_barang_baru }}</strong>
                                                                            <span class="badge bg-warning text-dark ms-1">Baru - Tidak dapat auto-fill</span>
                                                                        @endif
                                                                        <span class="ms-2 item-status" data-item-id="{{ $item->id_detail }}">
                                                                            <small class="text-warning">
                                                                                <i class="bi bi-clock"></i> Menunggu input
                                                                            </small>
                                                                        </span>
                                                                    </div>
                                                                    <small class="text-muted d-block mt-1">
                                                                        <i class="bi bi-box me-1"></i>Kemasan: {{ $item->kemasan_diminta ?? $item->kemasan_barang_baru ?? 'Box' }}
                                                                    </small>
                                                                    @if($item->catatan || $item->catatan_barang_baru)
                                                                        <small class="text-muted d-block">
                                                                            <i class="bi bi-chat-text me-1"></i>{{ $item->catatan ?? $item->catatan_barang_baru }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <a href="{{ route('permintaan.show', $requestId) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                    <i class="bi bi-eye me-1"></i>Detail
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success auto-fill-all-btn ms-1" 
                                                        data-request-id="{{ $requestId }}"
                                                        title="Auto-fill semua barang dalam permintaan ini">
                                                    <i class="bi bi-magic me-1"></i>Auto-Fill Semua
                                                </button>
                                            </div>
                                            <a href="{{ route('barang-masuk.create', ['request_id' => $requestId]) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-arrow-right me-1"></i>Pilih
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Multiple Barang Input Section (Hidden by default) -->
            <div id="multiple-barang-section" class="d-none">
                <div class="alert alert-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Mode Auto-Fill Semua Barang</strong>
                            <p class="mb-0 mt-1">Mengisi semua barang dalam permintaan sekaligus. Tanggal kadaluwarsa harus diisi manual untuk setiap batch.</p>
                            <div class="mt-2">
                                <span class="badge bg-secondary" id="items-count-badge">0 Item</span>
                                <span class="badge bg-info ms-1" id="total-batches-badge">0 Total Batch</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="cancel-multiple-mode">
                            <i class="bi bi-x-lg"></i> Batal
                        </button>
                    </div>
                </div>
                
                <form action="{{ route('barang-masuk.store-multiple') }}" method="POST" id="multiple-barang-form">
                    @csrf
                    <input type="hidden" name="request_id" id="multiple_request_id">
                    
                    <!-- Multiple Items Container with Scroll -->
                    <div class="multiple-items-wrapper" style="max-height: 70vh; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1rem; background-color: #f8f9fa;">
                        <div id="multiple-items-container">
                            <!-- Items will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3 sticky-bottom bg-white p-3 border-top">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save me-2"></i>Simpan Semua Barang
                        </button>
                    </div>
                </form>
            </div>

            <!-- Single Barang Input Form (Default) -->
            <div id="single-barang-section">
                <form action="{{ route('barang-masuk.store') }}" method="POST" class="row g-3">
                    @csrf
                <div class="col-md-6">
                    <label for="id_barang" class="form-label">Nama Barang</label>
                    <div class="position-relative">
                        <input type="text" 
                               id="search_barang" 
                               class="form-control" 
                               placeholder="Ketik untuk mencari barang..."
                               autocomplete="off">
                        <input type="hidden" name="id_barang" id="id_barang" required>
                        <input type="hidden" name="id_detail" id="id_detail">
                        
                        <!-- Current selected item info -->
                        <div id="selected_item_info" class="mt-2" style="display: none;">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                <span id="selected_item_text">Item dipilih untuk diinput</span>
                            </small>
                        </div>
                        
                        <!-- Loading indicator -->
                        <div id="search_loading" class="position-absolute end-0 top-50 translate-middle-y me-3" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                        <!-- Dropdown hasil search -->
                        <div id="dropdown_barang" class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto; display: none;">
                            @foreach ($barang as $item)
                                <a href="#" class="dropdown-item barang-option" 
                                   data-id="{{ $item->id_obat }}"
                                   data-kemasan="{{ $item->kemasan }}"
                                   data-isi-kemasan="{{ $item->isi_kemasan_jumlah }}"
                                   data-satuan-kemasan="{{ $item->isi_kemasan_satuan }}"
                                   data-isi-per-satuan="{{ $item->isi_per_satuan }}"
                                   data-satuan-terkecil="{{ $item->satuan_terkecil }}"
                                   data-text="{{ $item->nama_obat }} ({{ $item->kode_obat }}) - {{ $item->kategori_barang }}">
                                    <div class="fw-semibold">{{ $item->nama_obat }}</div>
                                    <small class="text-muted">{{ $item->kode_obat }} - {{ $item->kategori_barang }}</small>
                                </a>
                            @endforeach
                            
                            <!-- No results message -->
                            <div id="no_results" class="dropdown-item-text text-muted text-center py-3" style="display: none;">
                                <i class="bi bi-search"></i>
                                Tidak ada barang yang ditemukan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="id_lokasi" class="form-label">Lokasi Penyimpanan</label>
                    <select name="id_lokasi" id="id_lokasi" class="form-select" required>
                        <option value="" disabled {{ old('id_lokasi') ? '' : 'selected' }}>Pilih Lokasi</option>
                        @foreach ($lokasi as $loc)
                            <option value="{{ $loc->id }}" {{ old('id_lokasi') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control"
                           value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required readonly>
                    <small class="text-muted">Tanggal masuk otomatis diisi dengan hari ini</small>
                </div>

                <!-- Info Barang yang Dipilih (Read Only) -->
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">Informasi Barang yang Dipilih</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Kemasan</label>
                                    <input type="text" class="form-control-plaintext" id="info_kemasan" value="-" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Isi per Kemasan</label>
                                    <input type="text" class="form-control-plaintext" id="info_isi_kemasan" value="-" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Isi per Satuan</label>
                                    <input type="text" class="form-control-plaintext" id="info_isi_per_satuan" value="-" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Satuan Terkecil</label>
                                    <input type="text" class="form-control-plaintext" id="info_satuan_terkecil" value="-" readonly>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Informasi ini diambil dari data master barang yang telah disimpan sebelumnya.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multiple Batch Entry Section -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Detail Batch dengan Tanggal Kadaluwarsa</h6>
                            <button type="button" class="btn btn-sm btn-outline-success" id="add_batch_btn">
                                <i class="bi bi-plus-circle"></i> Tambah Batch
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="batch_container">
                                <!-- Initial batch row -->
                                <div class="batch-row mb-3 p-3 border rounded" data-index="0">
                                    <div class="row align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label">Jumlah Kemasan</label>
                                            <div class="input-group">
                                                <input type="number" name="batches[0][jumlah_kemasan]" class="form-control batch-jumlah"
                                                       min="1" required placeholder="Jumlah" value="{{ old('batches.0.jumlah_kemasan', 1) }}">
                                                <span class="input-group-text batch-kemasan-unit">Box</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Kadaluwarsa <span class="text-danger">*</span></label>
                                            <input type="date" name="batches[0][expired_at]" class="form-control" value="{{ old('batches.0.expired_at') }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Keterangan Batch</label>
                                            <input type="text" name="batches[0][keterangan]" class="form-control"
                                                   placeholder="Contoh: Batch A - Supplier X" value="{{ old('batches.0.keterangan') }}">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-batch" style="display: none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Summary -->
                            <div class="mt-3 p-2 bg-light rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Total Kemasan: <span id="total_kemasan">1</span></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Total Satuan: <span id="total_satuan">0</span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- General Keterangan -->
                <div class="col-12">
                    <label for="keterangan_umum" class="form-label">Keterangan Umum (Opsional)</label>
                    <textarea name="keterangan_umum" id="keterangan_umum" class="form-control" rows="2"
                              placeholder="Keterangan yang berlaku untuk semua batch, contoh: Pembelian dari Supplier ABC, No. PO: 12345">{{ old('keterangan_umum') }}</textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Data
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .dropdown-menu {
        border: 1px solid #dee2e6;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        z-index: 1000;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-item.active {
        background-color: #0d6efd;
        color: white;
    }
    
    #search_barang:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .position-relative .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 0.125rem;
    }
    
    .batch-row {
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6 !important;
        transition: all 0.3s ease;
    }
    
    .batch-row:hover {
        background-color: #e9ecef;
    }
    
    .remove-batch {
        opacity: 0.7;
    }
    
    .remove-batch:hover {
        opacity: 1;
    }
    
    /* Multiple Items Container Styles */
    .multiple-items-wrapper {
        scrollbar-width: thin;
        scrollbar-color: #6c757d #f8f9fa;
    }
    
    .multiple-items-wrapper::-webkit-scrollbar {
        width: 8px;
    }
    
    .multiple-items-wrapper::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 4px;
    }
    
    .multiple-items-wrapper::-webkit-scrollbar-thumb {
        background: #6c757d;
        border-radius: 4px;
    }
    
    .multiple-items-wrapper::-webkit-scrollbar-thumb:hover {
        background: #495057;
    }
    
    /* Sticky bottom for submit button */
    .sticky-bottom {
        position: sticky;
        bottom: 0;
        z-index: 10;
        margin-left: -15px;
        margin-right: -15px;
        margin-bottom: -15px;
        border-radius: 0 0 0.375rem 0.375rem;
        box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
    }
    
    /* Item cards in multiple mode */
    #multiple-items-container .card {
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
    }
    
    #multiple-items-container .card:last-child {
        margin-bottom: 0.5rem;
    }
    
    /* Enhanced batch summary styling */
    .batch-summary.text-success {
        background-color: #d1e7dd;
        border-color: #badbcc;
        color: #0f5132;
        border: 1px solid #badbcc;
        border-radius: 0.25rem;
        padding: 0.5rem;
    }
    
    .batch-summary.text-warning {
        background-color: #fff3cd;
        border-color: #ffecb5;
        color: #664d03;
        border: 1px solid #ffecb5;
        border-radius: 0.25rem;
        padding: 0.5rem;
    }
    
    .batch-summary.text-danger {
        background-color: #f8d7da;
        border-color: #f5c2c7;
        color: #842029;
        border: 1px solid #f5c2c7;
        border-radius: 0.25rem;
        padding: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Check completion status for all items
        checkItemCompletionStatus();
        
        const searchInput = document.getElementById('search_barang');
        const hiddenInput = document.getElementById('id_barang');
        const dropdown = document.getElementById('dropdown_barang');
        const barangOptions = document.querySelectorAll('.barang-option');
        const infoKemasan = document.getElementById('info_kemasan');
        const infoIsiKemasan = document.getElementById('info_isi_kemasan');
        const infoIsiPerSatuan = document.getElementById('info_isi_per_satuan');
        const infoSatuanTerkecil = document.getElementById('info_satuan_terkecil');
        const batchContainer = document.getElementById('batch_container');
        const addBatchBtn = document.getElementById('add_batch_btn');
        
        let currentBarang = null;
        let batchIndex = 1;

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const noResults = document.getElementById('no_results');
            let hasVisibleOptions = false;

            if (searchTerm.length > 0) {
                barangOptions.forEach(option => {
                    const text = option.dataset.text.toLowerCase();
                    if (text.includes(searchTerm)) {
                        option.style.display = 'block';
                        hasVisibleOptions = true;
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                noResults.style.display = hasVisibleOptions ? 'none' : 'block';
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
                hiddenInput.value = '';
                updateBarangInfo(null);
            }
        });

        // Handle barang selection
        barangOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                
                const id = this.dataset.id;
                const text = this.dataset.text;
                const kemasan = this.dataset.kemasan;
                const isiKemasan = this.dataset.isiKemasan;
                const satuanKemasan = this.dataset.satuanKemasan;
                const isiPerSatuan = this.dataset.isiPerSatuan;
                const satuanTerkecil = this.dataset.satuanTerkecil;

                // Update form
                searchInput.value = text;
                hiddenInput.value = id;
                dropdown.style.display = 'none';

                // Update info barang
                infoKemasan.value = kemasan || '-';
                infoIsiKemasan.value = `${isiKemasan} ${satuanKemasan}`;
                infoIsiPerSatuan.value = isiPerSatuan;
                infoSatuanTerkecil.value = satuanTerkecil;

                // Store current barang info
                currentBarang = {
                    kemasan: kemasan,
                    isiPerSatuan: parseInt(isiPerSatuan),
                    satuanTerkecil: satuanTerkecil
                };

                // Update all batch kemasan units
                document.querySelectorAll('.batch-kemasan-unit').forEach(unit => {
                    unit.textContent = kemasan || 'Box';
                });

                // Recalculate totals
                calculateTotals();
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.position-relative')) {
                dropdown.style.display = 'none';
            }
        });

        // Add batch functionality
        addBatchBtn.addEventListener('click', function() {
            if (!currentBarang) {
                alert('Silakan pilih barang terlebih dahulu');
                return;
            }

            const newBatchRow = createBatchRow(batchIndex);
            batchContainer.appendChild(newBatchRow);
            batchIndex++;
            updateRemoveButtons();
        });

        function createBatchRow(index) {
            const div = document.createElement('div');
            div.className = 'batch-row mb-3 p-3 border rounded';
            div.setAttribute('data-index', index);
            
            div.innerHTML = `
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Jumlah Kemasan</label>
                        <div class="input-group">
                            <input type="number" name="batches[${index}][jumlah_kemasan]" class="form-control batch-jumlah"
                                   min="1" required placeholder="Jumlah">
                            <span class="input-group-text batch-kemasan-unit">${currentBarang.kemasan || 'Box'}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Kadaluwarsa <span class="text-danger">*</span></label>
                        <input type="date" name="batches[${index}][expired_at]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Keterangan Batch</label>
                        <input type="text" name="batches[${index}][keterangan]" class="form-control"
                               placeholder="Contoh: Batch ${String.fromCharCode(65 + index)} - Supplier X">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-batch">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            // Add event listener for remove button
            div.querySelector('.remove-batch').addEventListener('click', function() {
                div.remove();
                updateRemoveButtons();
                calculateTotals();
            });

            // Add event listener for jumlah kemasan change
            div.querySelector('.batch-jumlah').addEventListener('input', calculateTotals);

            return div;
        }

        function updateBarangInfo(selectedOption) {
            if (selectedOption) {
                const kemasan = selectedOption.dataset.kemasan || '-';
                const isiKemasan = selectedOption.dataset.isiKemasan || '-';
                const satuanKemasan = selectedOption.dataset.satuanKemasan || '-';
                const isiPerSatuan = selectedOption.dataset.isiPerSatuan || '-';
                const satuanTerkecil = selectedOption.dataset.satuanTerkecil || '-';

                infoKemasan.value = kemasan;
                infoIsiKemasan.value = isiKemasan + ' ' + satuanKemasan;
                infoIsiPerSatuan.value = isiPerSatuan;
                infoSatuanTerkecil.value = satuanTerkecil;
            } else {
                infoKemasan.value = '-';
                infoIsiKemasan.value = '-';
                infoIsiPerSatuan.value = '-';
                infoSatuanTerkecil.value = '-';
            }
        }

        function updateRemoveButtons() {
            const batchRows = document.querySelectorAll('.batch-row');
            batchRows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-batch');
                if (batchRows.length > 1) {
                    removeBtn.style.display = 'block';
                } else {
                    removeBtn.style.display = 'none';
                }
            });
        }

        function calculateTotals() {
            if (!currentBarang) return;

            let totalKemasan = 0;
            document.querySelectorAll('.batch-jumlah').forEach(input => {
                const value = parseInt(input.value) || 0;
                totalKemasan += value;
            });

            const totalSatuan = totalKemasan * currentBarang.isiPerSatuan;

            document.getElementById('total_kemasan').textContent = totalKemasan;
            document.getElementById('total_satuan').textContent = `${totalSatuan} ${currentBarang.satuanTerkecil}`;
        }

        // Initial setup
        const initialBatchInput = document.querySelector('.batch-jumlah');
        if (initialBatchInput) {
            initialBatchInput.addEventListener('input', calculateTotals);
        }
        updateRemoveButtons();

        // Auto-fill functionality from reference card items
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('clickable-item-name')) {
                e.preventDefault();
                const barangId = e.target.getAttribute('data-barang-id');
                const namaBarang = e.target.getAttribute('data-nama');
                const kodeBarang = e.target.getAttribute('data-kode');
                
                // Get item detail ID from parent item-tracker
                const itemTracker = e.target.closest('.item-tracker');
                const itemDetailId = itemTracker ? itemTracker.getAttribute('data-item-id') : null;
                
                // Find the corresponding barang option
                const barangOption = document.querySelector(`[data-id="${barangId}"]`);
                if (barangOption && itemDetailId) {
                    // Auto-fill search input with readonly state
                    searchInput.value = `${namaBarang} (${kodeBarang})`;
                    searchInput.setAttribute('readonly', true);
                    searchInput.style.backgroundColor = '#e9ecef';
                    searchInput.style.cursor = 'not-allowed';
                    
                    // Set hidden inputs
                    hiddenInput.value = barangId;
                    document.getElementById('id_detail').value = itemDetailId;
                    
                    // Show selected item info
                    const selectedItemInfo = document.getElementById('selected_item_info');
                    const selectedItemText = document.getElementById('selected_item_text');
                    if (selectedItemInfo && selectedItemText) {
                        selectedItemText.textContent = `Dipilih: ${namaBarang} dari permintaan`;
                        selectedItemInfo.style.display = 'block';
                    }
                    
                    // Hide dropdown
                    dropdown.style.display = 'none';
                    
                    // Extract barang info from option
                    const kemasan = barangOption.getAttribute('data-kemasan');
                    const isiKemasan = barangOption.getAttribute('data-isi-kemasan');
                    const satuanKemasan = barangOption.getAttribute('data-satuan-kemasan');
                    const isiPerSatuan = barangOption.getAttribute('data-isi-per-satuan');
                    const satuanTerkecil = barangOption.getAttribute('data-satuan-terkecil');
                    
                    // Update info barang
                    infoKemasan.value = kemasan || '-';
                    infoIsiKemasan.value = `${isiKemasan} ${satuanKemasan}`;
                    infoIsiPerSatuan.value = isiPerSatuan;
                    infoSatuanTerkecil.value = satuanTerkecil;
                    
                    // Store current barang info
                    currentBarang = {
                        kemasan: kemasan,
                        isiPerSatuan: parseInt(isiPerSatuan),
                        satuanTerkecil: satuanTerkecil
                    };
                    
                    // Update all batch kemasan units
                    document.querySelectorAll('.batch-kemasan-unit').forEach(unit => {
                        unit.textContent = kemasan || 'Box';
                    });
                    
                    // Recalculate totals
                    calculateTotals();
                    
                    // Show success toast
                    showAutoFillToast(namaBarang);
                    
                    // Add reset button
                    addResetButton();
                    
                    // Scroll to form
                    document.querySelector('.card.shadow-sm').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            }
        });
        
        // Function to show auto-fill success toast
        function showAutoFillToast(namaBarang) {
            const toastHtml = `
                <div class="toast align-items-center text-bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Auto-fill berhasil: ${namaBarang}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = toastContainer.lastElementChild;
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
        
        // Function to add reset button
        function addResetButton() {
            // Remove existing reset button if any
            const existingReset = document.getElementById('reset-auto-fill');
            if (existingReset) {
                existingReset.remove();
            }
            
            // Create reset button
            const resetButton = document.createElement('button');
            resetButton.type = 'button';
            resetButton.id = 'reset-auto-fill';
            resetButton.className = 'btn btn-sm btn-outline-secondary mt-2';
            resetButton.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Reset Pilihan';
            
            // Add reset functionality
            resetButton.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.removeAttribute('readonly');
                searchInput.style.backgroundColor = '';
                searchInput.style.cursor = '';
                hiddenInput.value = '';
                
                // Reset info barang
                infoKemasan.value = '-';
                infoIsiKemasan.value = '-';
                infoIsiPerSatuan.value = '-';
                infoSatuanTerkecil.value = '-';
                
                // Reset current barang
                currentBarang = null;
                
                // Reset batch kemasan units
                document.querySelectorAll('.batch-kemasan-unit').forEach(unit => {
                    unit.textContent = 'Box';
                });
                
                // Remove reset button
                this.remove();
                
                // Show reset toast
                showResetToast();
            });
            
            // Insert after search input
            searchInput.parentNode.appendChild(resetButton);
        }
        
        // Function to show reset toast
        function showResetToast() {
            const toastHtml = `
                <div class="toast align-items-center text-bg-info border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Pilihan telah direset. Silakan pilih barang lagi.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = toastContainer.lastElementChild;
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
        
        // Auto-fill semua barang functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('auto-fill-all-btn') || e.target.closest('.auto-fill-all-btn')) {
                const button = e.target.classList.contains('auto-fill-all-btn') ? e.target : e.target.closest('.auto-fill-all-btn');
                const requestId = button.getAttribute('data-request-id');
                autoFillAllItems(requestId);
            }
            
            if (e.target.id === 'cancel-multiple-mode') {
                cancelMultipleMode();
            }
        });
        
        function autoFillAllItems(requestId) {
            // Collect all items from the selected request
            const requestCard = document.querySelector(`[data-request-id="${requestId}"]`).closest('.card');
            const items = requestCard.querySelectorAll('.item-tracker[data-barang-id]');
            
            const itemsData = [];
            items.forEach(item => {
                const barangId = item.getAttribute('data-barang-id');
                const barangNama = item.getAttribute('data-barang-nama');
                const jumlahDisetujui = item.getAttribute('data-jumlah-disetujui');
                const idDetail = item.getAttribute('data-item-id'); // Ambil id_detail
                
                // Find corresponding barang option to get full data
                const barangOption = document.querySelector(`[data-id="${barangId}"]`);
                if (barangOption && idDetail) {
                    itemsData.push({
                        id: barangId,
                        id_detail: idDetail, // Tambahkan id_detail
                        nama: barangNama,
                        jumlah: jumlahDisetujui,
                        kemasan: barangOption.getAttribute('data-kemasan'),
                        isiKemasan: barangOption.getAttribute('data-isi-kemasan'),
                        satuanKemasan: barangOption.getAttribute('data-satuan-kemasan'),
                        isiPerSatuan: barangOption.getAttribute('data-isi-per-satuan'),
                        satuanTerkecil: barangOption.getAttribute('data-satuan-terkecil'),
                        text: barangOption.getAttribute('data-text')
                    });
                }
            });
            
            if (itemsData.length === 0) {
                showToast('Tidak ada barang yang dapat di auto-fill dari permintaan ini.', 'warning');
                return;
            }
            
            // Switch to multiple mode
            switchToMultipleMode(requestId, itemsData);
        }
        
        function switchToMultipleMode(requestId, itemsData) {
            // Hide single barang section
            document.getElementById('single-barang-section').classList.add('d-none');
            
            // Show multiple barang section
            document.getElementById('multiple-barang-section').classList.remove('d-none');
            
            // Set request ID
            document.getElementById('multiple_request_id').value = requestId;
            
            // Generate multiple items form
            generateMultipleItemsForm(itemsData);
            
            // Initialize batch calculations for all items
            setTimeout(() => {
                itemsData.forEach((item, index) => {
                    calculateItemBatchTotals(index);
                });
                updateOverallStatistics();
            }, 100);
            
            // Show success toast
            showToast(`Auto-fill berhasil untuk ${itemsData.length} barang. Silakan isi tanggal kadaluwarsa untuk setiap batch.`, 'success');
            
            // Scroll to form
            document.getElementById('multiple-barang-section').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
        
        function generateMultipleItemsForm(itemsData) {
            const container = document.getElementById('multiple-items-container');
            container.innerHTML = '';
            
            itemsData.forEach((item, index) => {
                const itemHtml = `
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-box me-2"></i>
                                ${item.nama}
                            </h6>
                            <span class="badge bg-primary">${item.jumlah} ${item.kemasan}</span>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="items[${index}][id_detail]" value="${item.id_detail}">
                            <input type="hidden" name="items[${index}][id_barang]" value="${item.id}">
                            <input type="hidden" name="items[${index}][jumlah_kemasan]" value="${item.jumlah}">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Barang</label>
                                    <input type="text" class="form-control" value="${item.text}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                                    <select name="items[${index}][lokasi_default]" class="form-select item-lokasi-default" required>
                                        <option value="" disabled selected>Pilih Lokasi</option>
                                        @foreach ($lokasi as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->nama_lokasi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Batch Details</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm add-batch-btn" 
                                                data-item-index="${index}" data-kemasan="${item.kemasan || 'Box'}">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Batch
                                        </button>
                                    </div>
                                    <div class="batch-container" data-item-index="${index}">
                                        <!-- Default batch -->
                                        <div class="batch-row mb-3 p-3 border rounded" data-batch-index="0">
                                            <div class="row align-items-end">
                                                <div class="col-md-3">
                                                    <label class="form-label">Jumlah Kemasan</label>
                                                    <div class="input-group">
                                                        <input type="number" name="items[${index}][batches][0][jumlah_kemasan]" 
                                                               class="form-control batch-jumlah" min="1" max="${item.jumlah}" 
                                                               value="${item.jumlah}" required placeholder="Jumlah">
                                                        <span class="input-group-text">${item.kemasan || 'Box'}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Tanggal Kadaluwarsa <span class="text-danger">*</span></label>
                                                    <input type="date" name="items[${index}][batches][0][expired_at]" class="form-control" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Keterangan Batch</label>
                                                    <input type="text" name="items[${index}][batches][0][keterangan]" class="form-control"
                                                           placeholder="Contoh: Batch A - Supplier X">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-batch-btn d-none">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="batch-summary text-muted small">
                                        <span class="total-batches">Total Batch: 1</span> | 
                                        <span class="total-kemasan">Total Kemasan: ${item.jumlah} ${item.kemasan || 'Box'}</span> | 
                                        <span class="remaining-kemasan">Sisa: 0 ${item.kemasan || 'Box'}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Masuk</label>
                                    <input type="date" name="items[${index}][tanggal_masuk]" class="form-control" 
                                           value="{{ date('Y-m-d') }}" required readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total yang Harus Diinput</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control target-total" value="${item.jumlah}" readonly>
                                        <span class="input-group-text">${item.kemasan || 'Box'}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Info Barang -->
                            <div class="mt-3 p-2 bg-light rounded">
                                <div class="row text-sm">
                                    <div class="col-md-3">
                                        <strong>Kemasan:</strong> ${item.kemasan || 'Box'}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Isi per Kemasan:</strong> ${item.isiKemasan || '-'} ${item.satuanKemasan || ''}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Isi per Satuan:</strong> ${item.isiPerSatuan || '-'}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Satuan Terkecil:</strong> ${item.satuanTerkecil || '-'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            });
        }
        
        function cancelMultipleMode() {
            // Show single barang section
            document.getElementById('single-barang-section').classList.remove('d-none');
            
            // Hide multiple barang section
            document.getElementById('multiple-barang-section').classList.add('d-none');
            
            // Clear multiple items container
            document.getElementById('multiple-items-container').innerHTML = '';
            
            // Reset request ID
            document.getElementById('multiple_request_id').value = '';
            
            // Show info toast
            showToast('Mode auto-fill semua barang dibatalkan.', 'info');
        }
        
        function showToast(message, type = 'info') {
            const bgClass = type === 'success' ? 'text-bg-success' : 
                           type === 'danger' ? 'text-bg-danger' : 
                           type === 'warning' ? 'text-bg-warning' : 'text-bg-info';
            
            const icon = type === 'success' ? 'bi-check-circle-fill' : 
                        type === 'danger' ? 'bi-exclamation-triangle-fill' : 
                        type === 'warning' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill';
            
            const toastHtml = `
                <div class="toast align-items-center ${bgClass} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi ${icon} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = toastContainer.lastElementChild;
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 4000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
        
        // Handle add/remove batch in multiple items mode
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-batch-btn') || e.target.closest('.add-batch-btn')) {
                const button = e.target.classList.contains('add-batch-btn') ? e.target : e.target.closest('.add-batch-btn');
                const itemIndex = button.getAttribute('data-item-index');
                const kemasan = button.getAttribute('data-kemasan');
                addBatchToItem(itemIndex, kemasan);
            }
            
            if (e.target.classList.contains('remove-batch-btn') || e.target.closest('.remove-batch-btn')) {
                const button = e.target.classList.contains('remove-batch-btn') ? e.target : e.target.closest('.remove-batch-btn');
                const batchRow = button.closest('.batch-row');
                removeBatchFromItem(batchRow);
            }
        });
        
        // Handle batch quantity changes for calculation
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('batch-jumlah')) {
                const itemIndex = e.target.closest('.batch-container').getAttribute('data-item-index');
                calculateItemBatchTotals(itemIndex);
            }
        });
        
        function addBatchToItem(itemIndex, kemasan) {
            const container = document.querySelector(`[data-item-index="${itemIndex}"].batch-container`);
            const existingBatches = container.querySelectorAll('.batch-row');
            const newBatchIndex = existingBatches.length;
            
            const batchHtml = `
                <div class="batch-row mb-3 p-3 border rounded" data-batch-index="${newBatchIndex}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Jumlah Kemasan</label>
                            <div class="input-group">
                                <input type="number" name="items[${itemIndex}][batches][${newBatchIndex}][jumlah_kemasan]" 
                                       class="form-control batch-jumlah" min="1" required placeholder="Jumlah">
                                <span class="input-group-text">${kemasan}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Kadaluwarsa <span class="text-danger">*</span></label>
                            <input type="date" name="items[${itemIndex}][batches][${newBatchIndex}][expired_at]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Keterangan Batch</label>
                            <input type="text" name="items[${itemIndex}][batches][${newBatchIndex}][keterangan]" class="form-control"
                                   placeholder="Contoh: Batch ${String.fromCharCode(65 + newBatchIndex)} - Supplier X">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-batch-btn">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', batchHtml);
            updateRemoveBatchButtons(container);
            calculateItemBatchTotals(itemIndex);
            
            showToast(`Batch baru ditambahkan untuk item ini.`, 'success');
        }
        
        function removeBatchFromItem(batchRow) {
            const container = batchRow.closest('.batch-container');
            const itemIndex = container.getAttribute('data-item-index');
            
            // Don't allow removing if only one batch exists
            const remainingBatches = container.querySelectorAll('.batch-row');
            if (remainingBatches.length <= 1) {
                showToast('Minimal harus ada 1 batch per barang.', 'warning');
                return;
            }
            
            batchRow.remove();
            updateRemoveBatchButtons(container);
            calculateItemBatchTotals(itemIndex);
            
            showToast('Batch berhasil dihapus.', 'info');
        }
        
        function updateRemoveBatchButtons(container) {
            const batchRows = container.querySelectorAll('.batch-row');
            batchRows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-batch-btn');
                if (batchRows.length > 1) {
                    removeBtn.classList.remove('d-none');
                } else {
                    removeBtn.classList.add('d-none');
                }
            });
        }
        
        function calculateItemBatchTotals(itemIndex) {
            const container = document.querySelector(`[data-item-index="${itemIndex}"].batch-container`);
            const card = container.closest('.card');
            const batchInputs = container.querySelectorAll('.batch-jumlah');
            const targetTotal = parseInt(card.querySelector('.target-total').value) || 0;
            
            let totalBatches = batchInputs.length;
            let totalKemasan = 0;
            
            batchInputs.forEach(input => {
                const value = parseInt(input.value) || 0;
                totalKemasan += value;
            });
            
            const remaining = targetTotal - totalKemasan;
            const kemasan = card.querySelector('.input-group-text').textContent;
            
            // Update summary
            const summary = card.querySelector('.batch-summary');
            summary.querySelector('.total-batches').textContent = `Total Batch: ${totalBatches}`;
            summary.querySelector('.total-kemasan').textContent = `Total Kemasan: ${totalKemasan} ${kemasan}`;
            summary.querySelector('.remaining-kemasan').textContent = `Sisa: ${remaining} ${kemasan}`;
            
            // Add warning if over/under target
            summary.classList.remove('text-warning', 'text-danger', 'text-success');
            if (remaining < 0) {
                summary.classList.add('text-danger');
                summary.title = 'Jumlah batch melebihi target!';
            } else if (remaining > 0) {
                summary.classList.add('text-warning');  
                summary.title = 'Masih ada sisa yang belum dialokasikan ke batch!';
            } else {
                summary.classList.add('text-success');
                summary.title = 'Jumlah batch sudah sesuai target!';
            }
            
            // Update overall statistics
            updateOverallStatistics();
        }
        
        function updateOverallStatistics() {
            const itemsCountBadge = document.getElementById('items-count-badge');
            const totalBatchesBadge = document.getElementById('total-batches-badge');
            
            if (!itemsCountBadge || !totalBatchesBadge) return;
            
            const allItems = document.querySelectorAll('#multiple-items-container .card');
            const allBatches = document.querySelectorAll('#multiple-items-container .batch-row');
            
            itemsCountBadge.textContent = `${allItems.length} Item${allItems.length !== 1 ? 's' : ''}`;
            totalBatchesBadge.textContent = `${allBatches.length} Total Batch${allBatches.length !== 1 ? 'es' : ''}`;
        }

        // Handle old value restoration
        const oldBarangId = '{{ old("id_barang") }}';
        if (oldBarangId) {
            const oldOption = document.querySelector(`[data-id="${oldBarangId}"]`);
            if (oldOption) {
                searchInput.value = oldOption.dataset.text;
                hiddenInput.value = oldOption.dataset.id;
                updateBarangInfo(oldOption);
                
                // Trigger click to update currentBarang
                oldOption.click();
            }
        }
        
        // Add scroll indicator functionality
        document.addEventListener('DOMContentLoaded', function() {
            const itemsContainers = document.querySelectorAll('.items-container');
            itemsContainers.forEach(container => {
                // Check if scrollable and add class
                if (container.scrollHeight > container.clientHeight) {
                    container.classList.add('has-scroll');
                }
            });
            
            // Handle toggle buttons for items
            const toggleButtons = document.querySelectorAll('.toggle-items');
            toggleButtons.forEach(button => {
                const showText = button.querySelector('.show-text');
                const hideText = button.querySelector('.hide-text');
                const targetId = button.getAttribute('data-bs-target');
                const targetElement = document.querySelector(targetId);
                
                targetElement.addEventListener('shown.bs.collapse', function() {
                    showText.classList.add('d-none');
                    hideText.classList.remove('d-none');
                });
                
                targetElement.addEventListener('hidden.bs.collapse', function() {
                    showText.classList.remove('d-none');
                    hideText.classList.add('d-none');
                });
            });
            
            // Initialize item tracking system
            initializeItemTracking();
        });
        
        // Item tracking and auto-check functionality
        function initializeItemTracking() {
            // Load saved progress from localStorage
            loadItemProgress();
            
            // Watch for form submission
            const form = document.querySelector('form[action*="barang-masuk"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Before submitting, check and update item status
                    checkAndUpdateItemStatus();
                });
            }
            
            // Watch for changes in barang selection
            const searchInput = document.getElementById('search_barang');
            const hiddenInput = document.getElementById('id_barang');
            if (searchInput && hiddenInput) {
                // Monitor when item is selected
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('barang-option')) {
                        const selectedBarangId = e.target.dataset.id;
                        const selectedBarangName = e.target.dataset.text;
                        
                        // Highlight matching items in reference cards
                        highlightMatchingItems(selectedBarangId, selectedBarangName);
                    }
                });
            }
            
            // Monitor batch input changes
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('batch-jumlah')) {
                    calculateTotals();
                }
            });
        }
        
        function highlightMatchingItems(barangId, barangName) {
            // Remove previous highlights
            document.querySelectorAll('.item-tracker').forEach(item => {
                item.classList.remove('border-primary', 'bg-primary-subtle');
            });
            
            // Highlight matching items
            const matchingItems = document.querySelectorAll(`[data-barang-id="${barangId}"]`);
            matchingItems.forEach(item => {
                item.classList.add('border-primary', 'bg-primary-subtle');
            });
        }
    });

    /**
     * Check completion status for all items and update UI accordingly
     */
    function checkItemCompletionStatus() {
        const itemTrackers = document.querySelectorAll('.item-tracker');
        const requestCompletionTracker = {}; // Track completion by request
        
        // Collect all request IDs and item details
        itemTrackers.forEach(item => {
            const requestId = item.getAttribute('data-request-id');
            const itemId = item.getAttribute('data-item-id');
            
            if (!requestCompletionTracker[requestId]) {
                requestCompletionTracker[requestId] = {
                    total: 0,
                    completed: 0,
                    items: []
                };
            }
            
            requestCompletionTracker[requestId].total++;
            requestCompletionTracker[requestId].items.push({
                element: item,
                itemId: itemId
            });
        });
        
        // Check completion status via AJAX for each request
        Object.keys(requestCompletionTracker).forEach(requestId => {
            fetch(`/barang-masuk/check-completion/${requestId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateItemCompletionUI(requestId, data.completedItems, requestCompletionTracker[requestId]);
                }
            })
            .catch(error => {
                console.error('Error checking completion status:', error);
            });
        });
    }
    
    /**
     * Update UI based on completion status
     */
    function updateItemCompletionUI(requestId, completedItems, trackerData) {
        trackerData.items.forEach(({ element, itemId }) => {
            const checkbox = element.querySelector(`#item-${itemId}`);
            const statusSpan = element.querySelector(`.item-status[data-item-id="${itemId}"]`);
            const autoFillHint = element.querySelector('.auto-fill-hint');
            
            if (completedItems.includes(parseInt(itemId))) {
                // Mark as completed
                if (checkbox) {
                    checkbox.checked = true;
                    checkbox.classList.add('text-success');
                }
                
                if (statusSpan) {
                    statusSpan.innerHTML = '<small class="text-success"><i class="bi bi-check-circle-fill"></i> Sudah diinput</small>';
                }
                
                if (autoFillHint) {
                    autoFillHint.innerHTML = '<i class="bi bi-check-circle-fill"></i> Sudah diproses';
                    autoFillHint.classList.remove('text-info');
                    autoFillHint.classList.add('text-success');
                }
                
                // Add visual styling for completed items
                element.classList.add('completed-item');
                element.style.opacity = '0.7';
                
                trackerData.completed++;
            }
        });
        
        // Update card status if all items completed
        if (trackerData.completed === trackerData.total) {
            updateCardCompletionStatus(requestId, true);
        }
    }
    
    /**
     * Update card completion status
     */
    function updateCardCompletionStatus(requestId, isCompleted) {
        const requestCard = document.querySelector(`[data-request-id="${requestId}"]`)?.closest('.card');
        if (requestCard && isCompleted) {
            requestCard.classList.add('completed-request');
            requestCard.style.opacity = '0.6';
            
            // Add completion badge to card header
            const cardHeader = requestCard.querySelector('.card-header h6');
            if (cardHeader && !cardHeader.querySelector('.completion-badge')) {
                const badge = document.createElement('span');
                badge.className = 'badge bg-success ms-2 completion-badge';
                badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Semua item selesai';
                cardHeader.appendChild(badge);
            }
        }
    }
</script>
@endpush

@push('styles')
<style>
    /* Custom scrollbar for items container */
    .items-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .items-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .items-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .items-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Smooth scroll behavior */
    .items-container {
        scroll-behavior: smooth;
    }
    
    /* Hover effect for items */
    .items-container li:hover {
        background-color: #e3f2fd !important;
        transition: background-color 0.2s ease;
    }
    
    /* Gradient fade effect at bottom when scrollable */
    .items-container.has-scroll::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 20px;
        background: linear-gradient(transparent, #f8f9fa);
        pointer-events: none;
    }
    
    /* Item tracking styles */
    .item-tracker {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .item-tracker.item-completed {
        background-color: #d4edda !important;
    }
    
    /* Clickable item name styles */
    .clickable-item-name {
        transition: all 0.2s ease;
        border-radius: 4px;
        padding: 2px 4px;
        display: inline-block;
    }
    
    .clickable-item-name:hover {
        background-color: #e7f3ff;
        color: #0056b3 !important;
        text-decoration: none !important;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.15);
        transform: translateY(-1px);
    }
    
    .clickable-item-name:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 123, 255, 0.2);
    }
    
    .item-tracker.item-completed {
        border-color: #c3e6cb;
    }
    
    .item-tracker.border-primary {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.1rem rgba(13, 110, 253, 0.25);
    }
    
    .item-info {
        transition: opacity 0.3s ease;
    }
    
    .item-completed .item-name {
        text-decoration: line-through;
        opacity: 0.8;
    }
    
    .required-qty {
        position: relative;
    }
    
    .input-qty {
        position: relative;
        margin-left: -8px;
    }
    
    .progress-badge {
        font-size: 0.65rem;
        animation: pulse 2s infinite;
    }
    
    .progress-badge.bg-success {
        animation: none;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    /* Completed items styling */
    .completed-item {
        position: relative;
        border-left: 4px solid #198754 !important;
        background-color: #f8fff9 !important;
    }
    
    .completed-item::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        color: #198754;
        font-weight: bold;
        font-size: 1.2em;
    }
    
    .completed-request {
        border: 2px solid #198754 !important;
        background-color: #f8fff9 !important;
    }
    
    .completion-badge {
        animation: fadeInScale 0.5s ease-out;
    }
    
    @keyframes fadeInScale {
        0% { opacity: 0; transform: scale(0.8); }
        100% { opacity: 1; transform: scale(1); }
    }
    
    .item-checkbox:checked {
        background-color: #198754 !important;
        border-color: #198754 !important;
    }
</style>
@endpush
