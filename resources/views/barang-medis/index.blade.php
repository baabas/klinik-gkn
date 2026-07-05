@extends('layouts.sidebar-layout')

@push('styles')
<style>
    /* Custom table styling */
    .table-responsive {
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        position: relative;
    }
    
    .table th {
        background-color: #f8f9fa !important;
        font-weight: 600;
        font-size: 0.875rem;
        white-space: nowrap;
        border-bottom: 2px solid #dee2e6;
        vertical-align: middle;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .table th.sticky-end {
        position: sticky;
        right: 0;
        z-index: 11;
        box-shadow: -2px 0 4px rgba(0,0,0,0.1);
    }
    
    .table td {
        vertical-align: middle;
        font-size: 0.875rem;
        padding: 0.75rem 0.5rem;
    }
    
    .table td.sticky-action-column,
    .table td:last-child {
        position: sticky;
        right: 0;
        background-color: white;
        box-shadow: -2px 0 4px rgba(0,0,0,0.1);
        z-index: 5;
    }
    
    .table tbody tr:hover td.sticky-action-column,
    .table tbody tr:hover td:last-child {
        background-color: #f8f9fa;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Badge improvements */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Button group styling */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Compact action buttons */
    .d-flex.gap-1 > * {
        margin-bottom: 2px;
    }
    
    /* Responsive font sizes */
    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.75rem;
            padding: 0.5rem 0.25rem;
        }
        
        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
    }
    
    /* Improved code styling */
    code {
        background-color: #e9ecef;
        color: #495057;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.85em;
    }
    
    /* Status indicators */
    .text-danger { color: #dc3545 !important; }
    .text-warning { color: #fd7e14 !important; }
    .text-success { color: #198754 !important; }
    .text-primary { color: #0d6efd !important; }

    /* Multi Distribusi Bar */
    .multi-distribusi-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #198754 0%, #157347 100%);
        color: white;
        padding: 1rem 2rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .multi-distribusi-bar .btn {
        font-weight: 500;
    }

    /* Checkbox styling */
    .barang-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .barang-checkbox:checked {
        background-color: #198754;
        border-color: #198754;
    }

    tr.selected-row {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    /* Multi distribusi modal improvements */
    #multi-distribusi-items input[type="number"] {
        text-align: center;
    }

    #multi-distribusi-items .btn-remove-item:hover {
        background-color: #dc3545;
        color: white;
    }
</style>
@endpush

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Obat & Alat Medis</h5>
                <div class="text-muted small">Kelola data obat, alat medis, dan stok</div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div class="d-flex flex-wrap align-items-center navigation-buttons" role="group" aria-label="Navigation buttons">
                    <a href="{{ route('permintaan.index') }}" class="btn btn-outline-primary position-relative me-2">
                        <i class="bi bi-file-earmark-text"></i> Daftar Permintaan
                        @if(Auth::user()->akses === 'PENGADAAN' && isset($pengadaanNotifications) && $pengadaanNotifications['pending_requests'] > 0)
                            <span class="notification-badge badge rounded-pill bg-danger">
                                {{ $pengadaanNotifications['pending_requests'] }}
                                <span class="visually-hidden">permintaan pending</span>
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-clipboard-data"></i> Riwayat Barang Masuk
                    </a>
                    @if(Auth::user()->akses === 'PENGADAAN')
                        <a href="{{ route('barang-masuk.create') }}" class="btn btn-success position-relative me-2">
                            <i class="bi bi-box-arrow-in-down"></i> Input Barang Masuk
                            @if(isset($pengadaanNotifications) && $pengadaanNotifications['approved_for_input'] > 0)
                                <span class="notification-badge badge rounded-pill bg-warning text-dark">
                                    {{ $pengadaanNotifications['approved_for_input'] }}
                                    <span class="visually-hidden">barang siap input</span>
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('barang-medis.create') }}" class="btn btn-primary position-relative me-2">
                            <i class="bi bi-plus-circle"></i> Tambah Barang Baru
                            @if(isset($pengadaanNotifications) && $pengadaanNotifications['new_items_to_add'] > 0)
                                <span class="notification-badge badge rounded-pill bg-info">
                                    {{ $pengadaanNotifications['new_items_to_add'] }}
                                    <span class="visually-hidden">item baru untuk ditambah</span>
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('barang-medis.printPdf') }}" class="btn btn-outline-danger" target="_blank">
                            <i class="bi bi-filetype-pdf"></i> Print PDF
                        </a>
                    @endif
                    <a href="{{ route('surat-distribusi.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-text"></i> Surat Distribusi
                    </a>
                </div>

                <form action="{{ route('barang-medis.index') }}" method="GET" class="d-flex" style="max-width: 320px;" id="search-form">
                    <input type="search" class="form-control me-2" name="search" id="search-input" placeholder="Cari Nama atau Kode..." value="{{ request('search') }}" autocomplete="off">
                    <button class="btn btn-outline-secondary" type="submit" id="search-btn"><i class="bi bi-search"></i></button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            @if(Auth::user()->hasRole('DOKTER') || Auth::user()->hasRole('PENGADAAN'))
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="select-all-checkbox" title="Pilih Semua">
                            </th>
                            @endif
                            <th style="width: 50px;" class="text-center">No</th>
                            <th style="width: 100px;" class="text-center">Kode</th>
                            <th style="min-width: 200px;">Nama Obat/Alat Medis</th>
                            <th style="width: 100px;" class="text-center">Kategori</th>
                            <th style="width: 80px;" class="text-center">Kemasan</th>
                            <th style="width: 120px;" class="text-center">Isi Kemasan</th>
                            <th style="width: 120px;" class="text-center">Isi per Satuan</th>
                            <th style="width: 100px;" class="text-center">Satuan Terkecil</th>
                            <th style="width: 120px;" class="text-center">Tanggal Masuk Terakhir</th>
                            <th style="width: 120px;" class="text-center">Kadaluarsa Terdekat</th>
                            <th style="width: 90px;" class="text-center">Stok GKN 1</th>
                            <th style="width: 90px;" class="text-center">Stok GKN 2</th>
                            <th style="width: 90px;" class="text-center">Total Stok</th>
                            <th style="width: 200px;" class="text-center sticky-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="barang-table-body">
                        @include('barang-medis.partials.table-body', ['barang' => $barang])
                    </tbody>
                </table>
            </div>

            {{-- Floating Action Bar untuk Multi Distribusi --}}
            @if(Auth::user()->hasRole('DOKTER') || Auth::user()->hasRole('PENGADAAN'))
            <div id="multi-distribusi-bar" class="multi-distribusi-bar" style="display: none;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <strong><span id="selected-count">0</span> obat dipilih</strong>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger btn-sm" id="clear-selection">
                            <i class="bi bi-x-circle"></i> Batalkan
                        </button>
                        <button type="button" class="btn btn-success" id="btn-multi-distribusi" data-bs-toggle="modal" data-bs-target="#multiDistribusiModal">
                            <i class="bi bi-truck"></i> Distribusi <span id="selected-count-btn">0</span> Obat
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Loading indicator -->
            <div id="loading-indicator" class="text-center my-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Mencari data...</div>
            </div>

            <!-- Pagination -->
            <div class="mt-3" id="pagination-container">
                {{ $barang->links() }}
            </div>
        </div>
    </div>

    {{-- Modal untuk Multi Distribusi --}}
    @if(Auth::user()->hasRole('DOKTER') || Auth::user()->hasRole('PENGADAAN'))
    <div class="modal fade" id="multiDistribusiModal" tabindex="-1" aria-labelledby="multiDistribusiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="multiDistribusiModalLabel">
                        <i class="bi bi-truck me-2"></i>Distribusi Multi Obat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('barang-medis.distribusi-multi') }}" method="POST" id="multi-distribusi-form">
                    @csrf
                    <div class="modal-body">
                        {{-- Pilihan Lokasi --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="multi_lokasi_asal" class="form-label fw-bold">Dari Lokasi</label>
                                <select class="form-select" name="lokasi_asal" id="multi_lokasi_asal" required>
                                    @if(Auth::user()->hasRole('PENGADAAN'))
                                        <option value="1">GKN 1</option>
                                        <option value="2">GKN 2</option>
                                    @elseif(Auth::user()->hasRole('DOKTER'))
                                        @if(Auth::user()->id_lokasi == 1)
                                            <option value="1">GKN 1</option>
                                            <option value="2">GKN 2</option>
                                        @else
                                            <option value="2">GKN 2</option>
                                            <option value="1">GKN 1</option>
                                        @endif
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="multi_lokasi_tujuan" class="form-label fw-bold">Ke Lokasi</label>
                                <select class="form-select" name="lokasi_tujuan" id="multi_lokasi_tujuan" required>
                                    @if(Auth::user()->hasRole('PENGADAAN'))
                                        <option value="2">GKN 2</option>
                                        <option value="1">GKN 1</option>
                                    @elseif(Auth::user()->hasRole('DOKTER'))
                                        <option value="1">GKN 1</option>
                                        <option value="2">GKN 2</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        {{-- Nomor WA Validator --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nomor_wa_validator" class="form-label fw-bold">
                                    <i class="bi bi-whatsapp text-success"></i> Nomor WhatsApp Validator
                                </label>
                                <select class="form-select" name="nomor_wa_validator" id="nomor_wa_validator" required>
                                    <option value="">-- Pilih Validator --</option>
                                    @foreach($validators as $validator)
                                        <option value="{{ $validator->nomor_wa }}" data-nama="{{ $validator->nama_validator }}">
                                            {{ $validator->nama_validator }} ({{ $validator->nomor_wa }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Nomor WA pihak ketiga yang akan menerima konfirmasi validasi</div>
                            </div>
                            <div class="col-md-6">
                                <label for="catatan_distribusi" class="form-label fw-bold">Catatan (Opsional)</label>
                                <input type="text" class="form-control" name="catatan" id="catatan_distribusi" 
                                       placeholder="Catatan tambahan..." maxlength="500">
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                <strong>Surat Distribusi akan dibuat otomatis</strong> dengan QR Code untuk validasi via WhatsApp.
                                <br><small>Masukkan jumlah yang akan didistribusikan untuk setiap obat di bawah ini.</small>
                            </div>
                        </div>

                        {{-- Daftar Obat yang Dipilih --}}
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Obat</th>
                                        <th class="text-center" style="width: 120px;">Stok Tersedia</th>
                                        <th class="text-center" style="width: 150px;">Jumlah Distribusi</th>
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="multi-distribusi-items">
                                    {{-- Items akan di-populate oleh JavaScript --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-danger d-none mt-3" role="alert" id="multi-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <span id="multi-warning-text">Pastikan semua jumlah valid dan tidak melebihi stok yang tersedia.</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="submit-multi-distribusi">
                            <i class="bi bi-check-circle me-1"></i> Proses Distribusi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ========================================
    // Multi Distribusi Feature
    // ========================================
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const multiDistribusiBar = document.getElementById('multi-distribusi-bar');
    const selectedCountSpan = document.getElementById('selected-count');
    const selectedCountBtnSpan = document.getElementById('selected-count-btn');
    const clearSelectionBtn = document.getElementById('clear-selection');
    const multiDistribusiItems = document.getElementById('multi-distribusi-items');
    const multiLokasiAsal = document.getElementById('multi_lokasi_asal');
    const multiLokasiTujuan = document.getElementById('multi_lokasi_tujuan');
    const multiWarning = document.getElementById('multi-warning');
    const multiWarningText = document.getElementById('multi-warning-text');
    const submitMultiDistribusi = document.getElementById('submit-multi-distribusi');

    let selectedItems = new Map(); // Map to store selected items with their data

    // Function to update selection count and show/hide bar
    function updateSelectionUI() {
        const count = selectedItems.size;
        if (selectedCountSpan) selectedCountSpan.textContent = count;
        if (selectedCountBtnSpan) selectedCountBtnSpan.textContent = count;
        
        if (multiDistribusiBar) {
            if (count > 0) {
                multiDistribusiBar.style.display = 'block';
            } else {
                multiDistribusiBar.style.display = 'none';
            }
        }

        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.barang-checkbox');
        const checkedCount = document.querySelectorAll('.barang-checkbox:checked').length;
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allCheckboxes.length > 0 && checkedCount === allCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < allCheckboxes.length;
        }
    }

    // Function to handle checkbox change
    function handleCheckboxChange(checkbox) {
        const id = checkbox.value;
        const row = checkbox.closest('tr');
        
        if (checkbox.checked) {
            selectedItems.set(id, {
                id: id,
                nama: checkbox.dataset.nama,
                stokGkn1: parseInt(checkbox.dataset.stokGkn1) || 0,
                stokGkn2: parseInt(checkbox.dataset.stokGkn2) || 0,
                satuan: checkbox.dataset.satuan || 'Pcs'
            });
            if (row) row.classList.add('selected-row');
        } else {
            selectedItems.delete(id);
            if (row) row.classList.remove('selected-row');
        }
        
        updateSelectionUI();
    }

    // Initialize checkbox listeners
    function initCheckboxListeners() {
        document.querySelectorAll('.barang-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleCheckboxChange(this);
            });
        });
    }

    // Function to restore selected checkboxes after search/pagination
    function restoreSelectedCheckboxes() {
        document.querySelectorAll('.barang-checkbox').forEach(checkbox => {
            const id = checkbox.value;
            if (selectedItems.has(id)) {
                checkbox.checked = true;
                const row = checkbox.closest('tr');
                if (row) row.classList.add('selected-row');
            }
        });
    }

    // Select all checkbox handler
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.barang-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                handleCheckboxChange(checkbox);
            });
        });
    }

    // Clear selection handler
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            document.querySelectorAll('.barang-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                const row = checkbox.closest('tr');
                if (row) row.classList.remove('selected-row');
            });
            selectedItems.clear();
            updateSelectionUI();
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        });
    }

    // Function to get stok based on selected lokasi asal
    function getStokByLokasiAsal(item) {
        const lokasiAsal = multiLokasiAsal ? multiLokasiAsal.value : '1';
        return lokasiAsal === '1' ? item.stokGkn1 : item.stokGkn2;
    }

    // Function to populate modal with selected items
    function populateMultiDistribusiModal() {
        if (!multiDistribusiItems) return;
        
        multiDistribusiItems.innerHTML = '';
        let index = 0;
        
        selectedItems.forEach((item, id) => {
            const stok = getStokByLokasiAsal(item);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-center">${++index}</td>
                <td>
                    <strong>${item.nama}</strong>
                    <input type="hidden" name="items[${index-1}][id_barang]" value="${item.id}">
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary stok-display" data-stok-gkn1="${item.stokGkn1}" data-stok-gkn2="${item.stokGkn2}">
                        ${stok} ${item.satuan}
                    </span>
                </td>
                <td>
                    <input type="number" 
                           name="items[${index-1}][jumlah]" 
                           class="form-control form-control-sm jumlah-input" 
                           min="1" 
                           max="${stok}" 
                           value="1" 
                           required
                           data-id="${item.id}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-item" data-id="${item.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            multiDistribusiItems.appendChild(row);
        });

        // Add event listeners to remove buttons
        multiDistribusiItems.querySelectorAll('.btn-remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                selectedItems.delete(id);
                
                // Uncheck the checkbox in the table
                const checkbox = document.querySelector(`.barang-checkbox[value="${id}"]`);
                if (checkbox) {
                    checkbox.checked = false;
                    const row = checkbox.closest('tr');
                    if (row) row.classList.remove('selected-row');
                }
                
                updateSelectionUI();
                populateMultiDistribusiModal();
                
                // Close modal if no items left
                if (selectedItems.size === 0) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('multiDistribusiModal'));
                    if (modal) modal.hide();
                }
            });
        });

        // Add event listeners to validate jumlah inputs
        multiDistribusiItems.querySelectorAll('.jumlah-input').forEach(input => {
            input.addEventListener('input', validateMultiDistribusi);
        });

        validateMultiDistribusi();
    }

    // Function to validate multi distribusi form
    function validateMultiDistribusi() {
        if (!multiLokasiAsal || !multiLokasiTujuan) return;
        
        const lokasiAsal = multiLokasiAsal.value;
        const lokasiTujuan = multiLokasiTujuan.value;
        let isValid = true;
        let errorMessages = [];

        // Check if lokasi sama
        if (lokasiAsal === lokasiTujuan) {
            isValid = false;
            errorMessages.push('Lokasi asal dan tujuan tidak boleh sama.');
        }

        // Check each item
        multiDistribusiItems.querySelectorAll('.jumlah-input').forEach(input => {
            const jumlah = parseInt(input.value) || 0;
            const max = parseInt(input.max) || 0;
            
            if (jumlah <= 0) {
                isValid = false;
                input.classList.add('is-invalid');
            } else if (jumlah > max) {
                isValid = false;
                input.classList.add('is-invalid');
                errorMessages.push(`Jumlah melebihi stok yang tersedia.`);
            } else {
                input.classList.remove('is-invalid');
            }
        });

        // Show/hide warning
        if (multiWarning) {
            if (!isValid && errorMessages.length > 0) {
                multiWarningText.textContent = errorMessages.join(' ');
                multiWarning.classList.remove('d-none');
            } else {
                multiWarning.classList.add('d-none');
            }
        }

        // Enable/disable submit button
        if (submitMultiDistribusi) {
            submitMultiDistribusi.disabled = !isValid || selectedItems.size === 0;
        }
    }

    // Update stok display when lokasi asal changes
    if (multiLokasiAsal) {
        multiLokasiAsal.addEventListener('change', function() {
            // Update stok display and max values
            multiDistribusiItems.querySelectorAll('tr').forEach(row => {
                const stokDisplay = row.querySelector('.stok-display');
                const jumlahInput = row.querySelector('.jumlah-input');
                
                if (stokDisplay && jumlahInput) {
                    const stokGkn1 = parseInt(stokDisplay.dataset.stokGkn1) || 0;
                    const stokGkn2 = parseInt(stokDisplay.dataset.stokGkn2) || 0;
                    const newStok = this.value === '1' ? stokGkn1 : stokGkn2;
                    
                    // Get satuan from the item
                    const id = jumlahInput.dataset.id;
                    const item = selectedItems.get(id);
                    const satuan = item ? item.satuan : 'Pcs';
                    
                    stokDisplay.textContent = `${newStok} ${satuan}`;
                    jumlahInput.max = newStok;
                    
                    // Reset value if exceeds new max
                    if (parseInt(jumlahInput.value) > newStok) {
                        jumlahInput.value = newStok > 0 ? 1 : 0;
                    }
                }
            });
            
            validateMultiDistribusi();
        });
    }

    if (multiLokasiTujuan) {
        multiLokasiTujuan.addEventListener('change', validateMultiDistribusi);
    }

    // Populate modal when opened
    const multiDistribusiModal = document.getElementById('multiDistribusiModal');
    if (multiDistribusiModal) {
        multiDistribusiModal.addEventListener('show.bs.modal', function() {
            populateMultiDistribusiModal();
        });
    }

    // Initialize checkbox listeners on page load
    initCheckboxListeners();

    // Function to handle pagination clicks
    function initPaginationListeners() {
        const paginationLinks = document.querySelectorAll('#pagination-container a.page-link');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url) {
                    // Extract search query from current search input
                    const searchQuery = searchInput ? searchInput.value : '';
                    const urlObj = new URL(url);
                    if (searchQuery.trim()) {
                        urlObj.searchParams.set('search', searchQuery);
                    }
                    
                    // Load page via AJAX
                    loadPage(urlObj.toString());
                }
            });
        });
    }

    // Function to load page content via AJAX
    function loadPage(url) {
        loadingIndicator.style.display = 'block';
        tableBody.style.opacity = '0.5';

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tableBody.innerHTML = data.table_body;
                paginationContainer.innerHTML = data.pagination;
                loadingIndicator.style.display = 'none';
                tableBody.style.opacity = '1';
                
                initializeModals();
                initCheckboxListeners();
                initPaginationListeners();
                restoreSelectedCheckboxes();
                updateSelectionUI();
            }
        })
        .catch(error => {
            console.error('Error loading page:', error);
            loadingIndicator.style.display = 'none';
            tableBody.style.opacity = '1';
        });
    }

    // Initialize pagination listeners on page load
    initPaginationListeners();

    // ========================================
    // Live Search dengan Debounce
    // ========================================
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('barang-table-body');
    const paginationContainer = document.getElementById('pagination-container');
    const loadingIndicator = document.getElementById('loading-indicator');
    const searchForm = document.getElementById('search-form');
    let searchTimeout;

    // Debounce function untuk menunda pencarian
    function debounce(func, wait) {
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(searchTimeout);
                func(...args);
            };
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(later, wait);
        };
    }

    // Function untuk melakukan pencarian AJAX
    function performSearch(query) {
        // Show loading indicator
        loadingIndicator.style.display = 'block';
        tableBody.style.opacity = '0.5';

        // Buat URL untuk AJAX request
        const url = new URL('{{ route("api.barang-medis.search") }}');
        if (query.trim()) {
            url.searchParams.append('search', query);
        }

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update table body dengan data baru
                tableBody.innerHTML = data.table_body;
                
                // Update pagination
                paginationContainer.innerHTML = data.pagination;
                
                // Hide loading indicator
                loadingIndicator.style.display = 'none';
                tableBody.style.opacity = '1';

                // Re-initialize modal functionality untuk data baru
                initializeModals();
                
                // Re-initialize checkbox listeners untuk data baru
                initCheckboxListeners();
                
                // Re-initialize pagination listeners untuk data baru
                initPaginationListeners();
                
                // Restore previously selected checkboxes
                restoreSelectedCheckboxes();
                updateSelectionUI();
            } else {
                // Handle server-side error
                loadingIndicator.style.display = 'none';
                tableBody.style.opacity = '1';
                tableBody.innerHTML = data.table_body || `
                    <tr>
                        <td colspan="14" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                            <div>${data.message || 'Terjadi kesalahan saat mencari data.'}</div>
                        </td>
                    </tr>
                `;
                paginationContainer.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Hide loading indicator dan show error
            loadingIndicator.style.display = 'none';
            tableBody.style.opacity = '1';
            tableBody.innerHTML = `
                <tr>
                    <td colspan="14" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                        <div>Terjadi kesalahan koneksi. Silakan coba lagi.</div>
                    </td>
                </tr>
            `;
            paginationContainer.innerHTML = '';
        });
    }

    // Debounced search function dengan delay 500ms
    const debouncedSearch = debounce(performSearch, 500);

    // Event listener untuk input pencarian
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value;
            debouncedSearch(query);
        });

        // Prevent form submission untuk live search
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch(searchInput.value);
        });
    }

    // Function untuk initialize modal functionality
    function initializeModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            const lokasiAsalSelect = modal.querySelector('select[name="lokasi_asal"]');
            const lokasiTujuanSelect = modal.querySelector('select[name="lokasi_tujuan"]');
            const jumlahInput = modal.querySelector('input[name="jumlah"]');
            const stokTersediaSpan = modal.querySelector('.stok-tersedia');
            const submitBtn = modal.querySelector('button[type="submit"]');
            const warningAlert = modal.querySelector('.alert');

            if (!lokasiAsalSelect || !lokasiTujuanSelect || !jumlahInput) return;

            function validateForm() {
                const asal = lokasiAsalSelect.value;
                const tujuan = lokasiTujuanSelect.value;
                const jumlah = parseInt(jumlahInput.value, 10) || 0;
                const maxStok = parseInt(jumlahInput.max, 10) || 0;
                let isValid = true;

                if (jumlah > maxStok || jumlah <= 0) {
                    isValid = false;
                }
                if (asal === tujuan) {
                    isValid = false;
                }

                if (!isValid) {
                    warningAlert.classList.remove('d-none');
                } else {
                    warningAlert.classList.add('d-none');
                }
                submitBtn.disabled = !isValid;
            }

            function updateStokTersedia() {
                const selectedOption = lokasiAsalSelect.options[lokasiAsalSelect.selectedIndex];
                const stok = selectedOption ? selectedOption.getAttribute('data-stok') : 0;
                stokTersediaSpan.textContent = stok;
                jumlahInput.max = stok;
                validateForm();
            }

            lokasiAsalSelect.addEventListener('change', updateStokTersedia);
            lokasiTujuanSelect.addEventListener('change', validateForm);
            jumlahInput.addEventListener('input', validateForm);
            updateStokTersedia();
        });
    }

    // Initialize modals on page load
    initializeModals();

    // Modal distribution functionality
    document.querySelectorAll('.modal').forEach(modal => {
        const lokasiAsalSelect = modal.querySelector('select[name="lokasi_asal"]');
        const lokasiTujuanSelect = modal.querySelector('select[name="lokasi_tujuan"]');
        const jumlahInput = modal.querySelector('input[name="jumlah"]');
        const stokTersediaSpan = modal.querySelector('.stok-tersedia');
        const submitBtn = modal.querySelector('button[type="submit"]');
        const warningAlert = modal.querySelector('.alert');
        function validateForm() {
            const asal = lokasiAsalSelect.value;
            const tujuan = lokasiTujuanSelect.value;
            const jumlah = parseInt(jumlahInput.value, 10) || 0;
            const maxStok = parseInt(jumlahInput.max, 10) || 0;
            let isValid = true;
            if (jumlah > maxStok || jumlah <= 0) {
                isValid = false;
            }
            if (asal === tujuan) {
                isValid = false;
            }
            if (!isValid) {
                warningAlert.classList.remove('d-none');
            } else {
                warningAlert.classList.add('d-none');
            }
            submitBtn.disabled = !isValid;
        }
        function updateStokTersedia() {
            const selectedOption = lokasiAsalSelect.options[lokasiAsalSelect.selectedIndex];
            const stok = selectedOption ? selectedOption.getAttribute('data-stok') : 0;
            stokTersediaSpan.textContent = stok;
            jumlahInput.max = stok;
            validateForm();
        }
        lokasiAsalSelect.addEventListener('change', updateStokTersedia);
        lokasiTujuanSelect.addEventListener('change', validateForm);
        jumlahInput.addEventListener('input', validateForm);
        updateStokTersedia();
    });
});
</script>

<style>
    /* Styling untuk notification badge */
    .notification-badge {
        font-size: 0.7rem !important;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        animation: pulse-notification 2s infinite;
    }

    .notification-badge.bg-danger {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .notification-badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .notification-badge.bg-info {
        background-color: #0dcaf0 !important;
        color: #212529 !important;
    }

    /* Animasi pulse untuk menarik perhatian */
    @keyframes pulse-notification {
        0% {
            transform: translate(-50%, -50%) scale(1);
        }
        50% {
            transform: translate(-50%, -50%) scale(1.1);
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
        }
    }

    /* Hover effect untuk buttons dengan notification */
    .btn.position-relative:hover .notification-badge {
        animation: none;
        transform: translate(-50%, -50%) scale(1.05);
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        .navigation-buttons {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }
        
        .navigation-buttons .btn {
            margin-right: 0 !important;
            margin-bottom: 0;
            width: 100%;
            text-align: center;
        }
        
        .navigation-buttons .btn .notification-badge {
            font-size: 0.6rem !important;
            min-width: 16px;
            height: 16px;
            top: -6px !important;
            right: -6px !important;
        }
    }
    
    /* Navigation buttons styling */
    .navigation-buttons {
        gap: 0.5rem;
    }
    
    .navigation-buttons .btn {
        transition: all 0.2s ease;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: relative;
        overflow: visible; /* Allow badges to show outside button bounds */
    }
    
    .navigation-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        z-index: 1;
    }
    
    /* Fix notification badge positioning */
    .navigation-buttons .btn .notification-badge {
        position: absolute !important;
        top: -8px !important;
        right: -8px !important;
        z-index: 10 !important;
        transform: none !important;
        font-size: 0.7rem !important;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        animation: pulse-notification 2s infinite;
    }
</style>
@endpush
