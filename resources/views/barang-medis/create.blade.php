@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Tambah Barang Medis Baru</h1>

    {{-- Card Informasi Barang Baru yang Disetujui --}}
    @if(isset($approvedNewItems) && $approvedNewItems->count() > 0)
        <div class="card shadow-sm mb-4" id="approved-items-card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Barang Baru yang Menunggu Ditambahkan ke Master
                </h6>
            </div>
            <div class="card-body">
                <p class="text-mute            }
            
            // Event delegation approach (backup method)
            document.addEventListener('click', function(e) {
                const isDisabled = e.target.getAttribute('data-disabled') === 'true';
                
                if (e.target.id === 'prev-page') {
                    e.preventDefault();
                    e.stopPropagation();
                    if (currentPage > 1 && !isDisabled) {
                        goToPrevPage();
                    }
                } else if (e.target.id === 'next-page') {
                    e.preventDefault();
                    e.stopPropagation();
                    if (currentPage < totalPages && !isDisabled) {
                        goToNextPage();
                    }
                }
            });
            
            // Initial update
            updatePagination();
        }3">
                    <small>Berikut adalah daftar barang/obat baru yang telah disetujui dan menunggu untuk ditambahkan ke database master:</small>
                </p>
                <!-- Pagination Controls (Top) -->
                @if($approvedNewItems->count() > 6)
                    <div class="d-flex justify-content-between align-items-center mb-3 pagination-controls">
                        <div class="pagination-info">
                            <small class="text-muted">
                                Menampilkan <span id="showing-start">1</span> - <span id="showing-end">6</span> dari {{ $approvedNewItems->count() }} items
                            </small>
                        </div>
                        <div class="pagination-buttons">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-1" id="prev-page" data-disabled="true" onclick="handlePrevClick(event)">
                                <i class="bi bi-chevron-up"></i> Sebelumnya
                            </button>
                            <span class="badge bg-primary mx-2" id="page-indicator">Halaman <span id="current-page">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="next-page" onclick="handleNextClick(event)">
                                Selanjutnya <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Items Container -->
                <div class="row g-2" id="items-container">
                    @foreach($approvedNewItems as $index => $item)
                        <div class="col-md-6 col-lg-4 item-wrapper" 
                             data-index="{{ $index }}"
                             @if($index >= 6) style="display: none;" @endif>
                            <div class="border rounded p-2 bg-light item-card" 
                                 data-nama-barang="{{ $item->nama_barang_baru }}"
                                 data-kemasan="{{ $item->kemasan_barang_baru ?? 'Box' }}"
                                 style="cursor: pointer; transition: all 0.2s ease;">
                                <div class="fw-bold text-primary item-name" 
                                     title="Klik untuk auto-fill nama barang">
                                    {{ $item->nama_barang_baru }}
                                    <i class="bi bi-cursor-fill ms-1" style="font-size: 0.8em;"></i>
                                </div>
                                <small class="text-muted d-block">
                                    Kemasan: {{ $item->kemasan_barang_baru ?? 'Box' }} | 
                                    Qty: {{ $item->jumlah_disetujui }}
                                </small>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_permintaan)->format('d M Y') }}
                                </small>
                                <div class="auto-fill-hint text-info mt-1" style="font-size: 0.7em;">
                                    <i class="bi bi-info-circle me-1"></i>Klik nama untuk auto-fill
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            @if($approvedNewItems->count() > 6)
                                <small class="text-primary">
                                    <i class="bi bi-layers me-1"></i>
                                    {{ ceil($approvedNewItems->count() / 6) }} halaman tersedia
                                </small>
                            @endif
                            <small class="text-success fw-bold">
                                <i class="bi bi-cursor-fill me-1"></i>
                                Klik nama barang untuk auto-fill!
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .item-card {
            transition: all 0.3s ease;
            border: 2px solid #dee2e6 !important;
        }
        
        .item-card:hover {
            background-color: #e3f2fd !important;
            border-color: #2196f3 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .item-name {
            user-select: none;
            position: relative;
        }
        
        .item-name:hover {
            text-decoration: underline;
        }
        
        .auto-fill-hint {
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }
        
        .item-card:hover .auto-fill-hint {
            opacity: 1;
            font-weight: 500;
        }
        
        .item-card:hover .bi-cursor-fill {
            animation: cursorBlink 1s infinite;
        }
        
        @keyframes cursorBlink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }
        
        .nama-obat-highlighted {
            background-color: #fff3cd !important;
            border-color: #ffeaa7 !important;
            transition: all 0.3s ease !important;
        }
        
        .item-card.auto-filled {
            background-color: #d4edda !important;
            border-color: #c3e6cb !important;
        }
        
        .item-card.auto-filled:hover {
            background-color: #c3e6cb !important;
        }
        
        .item-card.auto-filled .item-name {
            color: #155724 !important;
        }
        
        /* Pagination Styles */
        .pagination-controls {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
        }
        
        .pagination-buttons button {
            transition: all 0.2s ease;
        }
        
        .pagination-buttons button:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .pagination-buttons button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        #page-indicator {
            font-size: 0.8rem;
            padding: 0.35rem 0.7rem;
        }
        
        .pagination-info {
            font-weight: 500;
            color: #6c757d;
        }
        
        /* Items container transition */
        #items-container {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Bottom pagination buttons */
        .pagination-buttons-bottom button {
            min-width: 120px;
            transition: all 0.2s ease;
        }
        
        .pagination-buttons-bottom button:hover:not(:disabled) {
            background-color: #0d6efd;
            color: white;
            transform: translateY(-1px);
        }
        
        /* Disabled button styles */
        .pagination-buttons-bottom button:disabled,
        .pagination-controls button:disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
        
        .pagination-controls {
            position: relative;
            margin-bottom: 30px;
        }
    </style>

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

            <form action="{{ route('barang-medis.store') }}" method="POST">
                @csrf
                
                <!-- Kategori Barang -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kategori_barang" class="form-label">Kategori Barang</label>
                        <select name="kategori_barang" id="kategori_barang" class="form-select" required>
                            <option value="">Pilih Kategori Barang</option>
                            <option value="Obat" {{ old('kategori_barang') == 'Obat' ? 'selected' : '' }}>Obat</option>
                            <option value="BMHP" {{ old('kategori_barang') == 'BMHP' ? 'selected' : '' }}>BMHP (Bahan Medis Habis Pakai)</option>
                            <option value="Alkes" {{ old('kategori_barang') == 'Alkes' ? 'selected' : '' }}>Alkes (Alat Kesehatan)</option>
                            <option value="APD" {{ old('kategori_barang') == 'APD' ? 'selected' : '' }}>APD (Alat Pelindung Diri)</option>
                        </select>
                    </div>
                </div>

                <!-- Kode dan Nama Barang -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode_obat" class="form-label">Kode Barang</label>
                        <input type="text" name="kode_obat" class="form-control" id="kode_obat" placeholder="Kode akan dibuat otomatis" value="{{ old('kode_obat') }}" readonly>
                        <small class="text-muted">Kode akan dibuat otomatis berdasarkan kategori barang</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_obat" class="form-label">Nama</label>
                        <input type="text" name="nama_obat" class="form-control" id="nama_obat" placeholder="Contoh: Paracetamol 500mg" value="{{ old('nama_obat') }}" required>
                    </div>
                </div>

                <!-- Kemasan dan Isi Kemasan -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="kemasan" class="form-label">Kemasan</label>
                        <input type="text" name="kemasan" class="form-control" id="kemasan" value="Box" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="isi_kemasan" class="form-label">Isi Kemasan</label>
                        <div class="input-group">
                            <input type="number" name="isi_kemasan_jumlah" class="form-control" id="isi_kemasan_jumlah" placeholder="10" value="{{ old('isi_kemasan_jumlah') }}" required>
                            <select name="isi_kemasan_satuan" id="isi_kemasan_satuan" class="form-select" required>
                                <option value="">Pilih</option>
                                @foreach(\App\Models\MasterIsiKemasan::where('is_active', true)->orderBy('nama_isi_kemasan')->get() as $isiKemasan)
                                    <option value="{{ strtolower($isiKemasan->nama_isi_kemasan) }}" 
                                            {{ old('isi_kemasan_satuan') == strtolower($isiKemasan->nama_isi_kemasan) ? 'selected' : '' }}>
                                        {{ $isiKemasan->nama_isi_kemasan }}
                                    </option>
                                @endforeach
                                <option value="lainnya" {{ old('isi_kemasan_satuan') == 'lainnya' ? 'selected' : '' }}>Lainnya (tulis manual)</option>
                            </select>
                        </div>
                        <!-- Field input tambahan untuk satuan kemasan baru -->
                        <div id="isi_kemasan_custom_field" style="display: none;" class="mt-2">
                            <input type="text" name="isi_kemasan_satuan_custom" id="isi_kemasan_satuan_custom" class="form-control" placeholder="Masukkan satuan kemasan baru" value="{{ old('isi_kemasan_satuan_custom') }}">
                            <small class="text-muted">Contoh: sachet, ampul, dll.</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="isi_per_satuan" class="form-label">Isi per <span id="satuan_label">strip</span></label>
                        <input type="number" name="isi_per_satuan" class="form-control" id="isi_per_satuan" placeholder="25" value="{{ old('isi_per_satuan') }}" required>
                    </div>
                </div>

                <!-- Satuan Terkecil -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="satuan_terkecil" class="form-label">Satuan Terkecil</label>
                        <select name="satuan_terkecil" id="satuan_terkecil" class="form-select" required>
                            <option value="">Pilih Satuan Terkecil</option>
                            @foreach(\App\Models\MasterSatuan::where('is_active', true)->orderBy('nama_satuan')->get() as $satuan)
                                <option value="{{ $satuan->nama_satuan }}" 
                                        {{ old('satuan_terkecil') == $satuan->nama_satuan ? 'selected' : '' }}>
                                    {{ $satuan->nama_satuan }}
                                </option>
                            @endforeach
                            <option value="lainnya" {{ old('satuan_terkecil') == 'lainnya' ? 'selected' : '' }}>Lainnya (tulis manual)</option>
                        </select>
                        <!-- Field input tambahan untuk satuan terkecil baru -->
                        <div id="satuan_terkecil_custom_field" style="display: none;" class="mt-2">
                            <input type="text" name="satuan_terkecil_custom" id="satuan_terkecil_custom" class="form-control" placeholder="Masukkan satuan terkecil baru" value="{{ old('satuan_terkecil_custom') }}">
                            
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                    <a href="{{ route('barang-medis.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Update label "Isi per" ketika satuan kemasan berubah
        document.getElementById('isi_kemasan_satuan').addEventListener('change', function() {
            const selectedSatuan = this.value;
            const satuanLabel = document.getElementById('satuan_label');
            const customField = document.getElementById('isi_kemasan_custom_field');
            const customInput = document.getElementById('isi_kemasan_satuan_custom');
            
            // Show/hide custom field untuk isi kemasan
            if (selectedSatuan === 'lainnya') {
                customField.style.display = 'block';
                customInput.required = true;
                satuanLabel.textContent = 'satuan';
            } else {
                customField.style.display = 'none';
                customInput.required = false;
                customInput.value = '';
                
                // Hanya update label jika ada pilihan yang dipilih (bukan default "Pilih")
                if (selectedSatuan && selectedSatuan !== '') {
                    satuanLabel.textContent = selectedSatuan;
                } else {
                    // Kembali ke default jika tidak ada yang dipilih
                    satuanLabel.textContent = 'strip';
                }
            }
        });

        // Handle dropdown Satuan Terkecil
        document.getElementById('satuan_terkecil').addEventListener('change', function() {
            const selectedSatuan = this.value;
            const customField = document.getElementById('satuan_terkecil_custom_field');
            const customInput = document.getElementById('satuan_terkecil_custom');
            
            // Show/hide custom field untuk satuan terkecil
            if (selectedSatuan === 'lainnya') {
                customField.style.display = 'block';
                customInput.required = true;
            } else {
                customField.style.display = 'none';
                customInput.required = false;
                customInput.value = '';
            }
        });

        // Generate preview kode barang berdasarkan kategori
        document.getElementById('kategori_barang').addEventListener('change', function() {
            const kategori = this.value;
            const kodeInput = document.getElementById('kode_obat');
            
            let prefix = '';
            switch(kategori) {
                case 'Obat':
                    prefix = 'OBT';
                    break;
                case 'BMHP':
                    prefix = 'BMHP';
                    break;
                case 'Alkes':
                    prefix = 'ALK';
                    break;
                case 'APD':
                    prefix = 'APD';
                    break;
                default:
                    prefix = '';
            }
            
            if (prefix) {
                kodeInput.placeholder = `${prefix}-XXXX (akan dibuat otomatis)`;
            } else {
                kodeInput.placeholder = 'Kode akan dibuat otomatis';
            }
        });

        // Auto-fill functionality for approved items
        document.addEventListener('DOMContentLoaded', function() {
            const itemCards = document.querySelectorAll('.item-card');
            const namaObatInput = document.getElementById('nama_obat');
            
            itemCards.forEach(card => {
                // Add hover effects
                card.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#e3f2fd';
                    this.style.borderColor = '#2196f3';
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '#f8f9fa';
                    this.style.borderColor = '#dee2e6';
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
                
                // Auto-fill on click
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const namaBarang = this.dataset.namaBarang;
                    const kemasan = this.dataset.kemasan;
                    
                    // Auto-fill nama barang
                    namaObatInput.value = namaBarang;
                    namaObatInput.focus();
                    
                    // Visual feedback - highlight the input field temporarily
                    namaObatInput.style.backgroundColor = '#fff3cd';
                    namaObatInput.style.borderColor = '#ffeaa7';
                    
                    // Mark this card as used for auto-fill
                    this.classList.add('auto-filled');
                    const hint = this.querySelector('.auto-fill-hint');
                    hint.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Sudah di-auto fill';
                    hint.classList.remove('text-info');
                    hint.classList.add('text-success');
                    
                    // Show success notification
                    showAutoFillNotification(namaBarang);
                    
                    // Remove highlight after animation
                    setTimeout(() => {
                        namaObatInput.style.backgroundColor = '';
                        namaObatInput.style.borderColor = '';
                    }, 1500);
                    
                    // Scroll to form for better UX
                    namaObatInput.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                });
            });
            
            // Reset auto-fill status when user manually edits the name field
            namaObatInput.addEventListener('input', function() {
                const allCards = document.querySelectorAll('.item-card.auto-filled');
                allCards.forEach(card => {
                    const currentName = card.dataset.namaBarang;
                    if (this.value !== currentName) {
                        card.classList.remove('auto-filled');
                        const hint = card.querySelector('.auto-fill-hint');
                        hint.innerHTML = '<i class="bi bi-info-circle me-1"></i>Klik nama untuk auto-fill';
                        hint.classList.remove('text-success');
                        hint.classList.add('text-info');
                    }
                });
            });
            
            // Initialize pagination with delay to ensure DOM is ready
            setTimeout(() => {
                initializePagination();
            }, 100);
        });
        
        // Pagination functionality
        function initializePagination() {
            const itemWrappers = document.querySelectorAll('.item-wrapper');
            const totalItems = itemWrappers.length;
            const itemsPerPage = 6;
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            let currentPage = 1;
            
            // Only initialize if more than 6 items
            if (totalItems <= itemsPerPage) {
                // Hide pagination controls if not needed
                const paginationControls = document.querySelectorAll('.pagination-controls');
                paginationControls.forEach(control => {
                    if (control) control.style.display = 'none';
                });
                return;
            }
            
            // Get pagination elements with fallback
            const prevButton = document.getElementById('prev-page');
            const nextButton = document.getElementById('next-page');
            const currentPageSpan = document.getElementById('current-page');
            

            const showingStart = document.getElementById('showing-start');
            const showingEnd = document.getElementById('showing-end');
            
            function updatePagination() {
                // Show loading state
                const itemsContainer = document.getElementById('items-container');
                if (itemsContainer) {
                    itemsContainer.style.opacity = '0.5';
                    itemsContainer.style.pointerEvents = 'none';
                }
                
                setTimeout(() => {
                    // Calculate range
                    const startIndex = (currentPage - 1) * itemsPerPage;
                    const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
                    
                    // Hide all items first
                    itemWrappers.forEach(wrapper => {
                        wrapper.style.display = 'none';
                    });
                    
                    // Show items for current page
                    for (let i = startIndex; i < endIndex; i++) {
                        if (itemWrappers[i]) {
                            itemWrappers[i].style.display = 'block';
                        }
                    }
                    
                    // Restore container state
                    if (itemsContainer) {
                        itemsContainer.style.opacity = '1';
                        itemsContainer.style.pointerEvents = 'auto';
                    }
                }, 100);
                
                // Update pagination info
                if (currentPageSpan) currentPageSpan.textContent = currentPage;
                if (showingStart) showingStart.textContent = startIndex + 1;
                if (showingEnd) showingEnd.textContent = endIndex;
                
                // Update button states
                const isFirstPage = currentPage === 1;
                const isLastPage = currentPage === totalPages;
                
                if (prevButton) {
                    // Don't use disabled attribute, use data attribute and styling instead
                    prevButton.removeAttribute('disabled');
                    prevButton.setAttribute('data-disabled', isFirstPage);
                    prevButton.style.opacity = isFirstPage ? '0.5' : '1';
                    prevButton.style.cursor = isFirstPage ? 'not-allowed' : 'pointer';
                    if (isFirstPage) {
                        prevButton.classList.add('btn-outline-secondary');
                        prevButton.classList.remove('btn-outline-primary');
                    } else {
                        prevButton.classList.add('btn-outline-primary');
                        prevButton.classList.remove('btn-outline-secondary');
                    }
                }
                if (nextButton) {
                    nextButton.removeAttribute('disabled');
                    nextButton.setAttribute('data-disabled', isLastPage);
                    nextButton.style.opacity = isLastPage ? '0.5' : '1';
                    nextButton.style.cursor = isLastPage ? 'not-allowed' : 'pointer';
                    if (isLastPage) {
                        nextButton.classList.add('btn-outline-secondary');
                        nextButton.classList.remove('btn-outline-primary');
                    } else {
                        nextButton.classList.add('btn-outline-primary');
                        nextButton.classList.remove('btn-outline-secondary');
                    }
                }
                
                // Add smooth scroll animation with slide effect
                const container = document.getElementById('items-container');
                if (container) {
                    // Fade out
                    container.style.opacity = '0';
                    container.style.transform = 'translateY(10px)';
                    
                    setTimeout(() => {
                        // Fade in with slide up
                        container.style.opacity = '1';
                        container.style.transform = 'translateY(0)';
                    }, 200);
                    
                    // Scroll to top of items container for better UX
                    container.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            }
            
            function goToNextPage() {
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                    showPageChangeNotification(currentPage, totalPages);
                }
            }
            
            function goToPrevPage() {
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                    showPageChangeNotification(currentPage, totalPages);
                }
            }
            
            // Direct onclick handlers
            function handlePrevClick(event) {
                event.preventDefault();
                event.stopPropagation();
                const isDisabled = event.target.getAttribute('data-disabled') === 'true';
                if (!isDisabled && currentPage > 1) {
                    goToPrevPage();
                }
            }
            
            function handleNextClick(event) {
                event.preventDefault();
                event.stopPropagation();
                const isDisabled = event.target.getAttribute('data-disabled') === 'true';
                if (!isDisabled && currentPage < totalPages) {
                    goToNextPage();
                }
            }
            
            // Event listeners (kept as backup)
            if (nextButton) nextButton.addEventListener('click', goToNextPage);
            if (prevButton) prevButton.addEventListener('click', goToPrevPage);
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                    return; // Don't interfere with form inputs
                }
                
                if (e.key === 'ArrowDown' && currentPage < totalPages) {
                    e.preventDefault();
                    goToNextPage();
                } else if (e.key === 'ArrowUp' && currentPage > 1) {
                    e.preventDefault();
                    goToPrevPage();
                }
            });
            
            // Initial update
            updatePagination();
        }
        
        function showPageChangeNotification(page, totalPages) {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-arrow-left-right me-2"></i>
                            Halaman ${page} dari ${totalPages}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = toastContainer.lastElementChild;
            
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 2000 });
                toast.show();
            } else {
                toastElement.style.display = 'block';
                setTimeout(() => {
                    toastElement.style.opacity = '0';
                    setTimeout(() => toastElement.remove(), 300);
                }, 2000);
            }
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
        
        function showAutoFillNotification(namaBarang) {
            // Create toast notification
            const toastHtml = `
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Nama barang "<strong>${namaBarang}</strong>" berhasil di-auto fill
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            // Add to toast container or create one
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            // Insert and show toast
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = toastContainer.lastElementChild;
            
            // Initialize Bootstrap toast if available
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
                toast.show();
            } else {
                // Fallback: show for 3 seconds without Bootstrap
                toastElement.style.display = 'block';
                setTimeout(() => {
                    toastElement.style.opacity = '0';
                    setTimeout(() => toastElement.remove(), 300);
                }, 3000);
            }
            
            // Remove toast element after hide
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }

        // Form validation before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const isiKemasanSatuan = document.getElementById('isi_kemasan_satuan').value;
            const isiKemasanCustom = document.getElementById('isi_kemasan_satuan_custom').value;
            const satuanTerkecil = document.getElementById('satuan_terkecil').value;
            const satuanTerkecilCustom = document.getElementById('satuan_terkecil_custom').value;
            
            // Validate custom fields
            if (isiKemasanSatuan === 'lainnya' && !isiKemasanCustom.trim()) {
                e.preventDefault();
                alert('Mohon isi satuan kemasan baru karena Anda memilih "Lainnya"');
                document.getElementById('isi_kemasan_satuan_custom').focus();
                return false;
            }
            
            if (satuanTerkecil === 'lainnya' && !satuanTerkecilCustom.trim()) {
                e.preventDefault();
                alert('Mohon isi satuan terkecil baru karena Anda memilih "Lainnya"');
                document.getElementById('satuan_terkecil_custom').focus();
                return false;
            }
        });

        // Initialize form state on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there are old values for custom fields (after validation error)
            const isiKemasanSatuan = document.getElementById('isi_kemasan_satuan').value;
            const satuanTerkecil = document.getElementById('satuan_terkecil').value;
            
            if (isiKemasanSatuan === 'lainnya') {
                document.getElementById('isi_kemasan_custom_field').style.display = 'block';
                document.getElementById('isi_kemasan_satuan_custom').required = true;
            }
            
            if (satuanTerkecil === 'lainnya') {
                document.getElementById('satuan_terkecil_custom_field').style.display = 'block';
                document.getElementById('satuan_terkecil_custom').required = true;
            }
        });
    </script>
@endsection
