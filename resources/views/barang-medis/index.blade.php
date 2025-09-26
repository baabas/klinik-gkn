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
</style>
@endpush

@section('content')
    <h1 class="h2 mb-4">Obat & Alat Medis</h1>

    <div class="card shadow-sm">
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
                </div>

                <form action="{{ route('barang-medis.index') }}" method="GET" class="d-flex" style="max-width: 320px;" id="search-form">
                    <input type="search" class="form-control me-2" name="search" id="search-input" placeholder="Cari Nama atau Kode..." value="{{ request('search') }}" autocomplete="off">
                    <button class="btn btn-outline-secondary" type="submit" id="search-btn"><i class="bi bi-search"></i></button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
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

    {{-- Modal untuk Distribusi Stok (Tidak ada perubahan di sini) --}}
    @foreach ($barang as $item)
    <div class="modal fade" id="distribusiModal-{{ $item->id_obat }}" tabindex="-1" aria-labelledby="distribusiModalLabel-{{ $item->id_obat }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="distribusiModalLabel-{{ $item->id_obat }}">Distribusi Stok: {{ $item->nama_obat }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('barang-medis.distribusi', $item->id_obat) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="lokasi_asal-{{ $item->id_obat }}" class="form-label">Dari Lokasi</label>
                            <select class="form-select" name="lokasi_asal" id="lokasi_asal-{{ $item->id_obat }}" required>
                                @if(Auth::user()->hasRole('PENGADAAN'))
                                    {{-- PENGADAAN bisa distribusi dari lokasi mana saja --}}
                                    <option value="1" data-stok="{{ (int)($item->stok_gkn1 ?? 0) }}">GKN 1 (Stok: {{ (int)($item->stok_gkn1 ?? 0) }})</option>
                                    <option value="2" data-stok="{{ (int)($item->stok_gkn2 ?? 0) }}">GKN 2 (Stok: {{ (int)($item->stok_gkn2 ?? 0) }})</option>
                                @elseif(Auth::user()->hasRole('DOKTER'))
                                    {{-- DOKTER hanya bisa distribusi dari lokasi mereka atau ke lokasi mereka --}}
                                    @if(Auth::user()->id_lokasi == 1)
                                        <option value="1" data-stok="{{ (int)($item->stok_gkn1 ?? 0) }}">GKN 1 (Stok: {{ (int)($item->stok_gkn1 ?? 0) }})</option>
                                        <option value="2" data-stok="{{ (int)($item->stok_gkn2 ?? 0) }}">GKN 2 (Stok: {{ (int)($item->stok_gkn2 ?? 0) }})</option>
                                    @elseif(Auth::user()->id_lokasi == 2)
                                        <option value="2" data-stok="{{ (int)($item->stok_gkn2 ?? 0) }}">GKN 2 (Stok: {{ (int)($item->stok_gkn2 ?? 0) }})</option>
                                        <option value="1" data-stok="{{ (int)($item->stok_gkn1 ?? 0) }}">GKN 1 (Stok: {{ (int)($item->stok_gkn1 ?? 0) }})</option>
                                    @endif
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="lokasi_tujuan-{{ $item->id_obat }}" class="form-label">Ke Lokasi</label>
                            <select class="form-select" name="lokasi_tujuan" id="lokasi_tujuan-{{ $item->id_obat }}" required>
                                @if(Auth::user()->hasRole('PENGADAAN'))
                                    {{-- PENGADAAN bisa distribusi ke lokasi mana saja --}}
                                    <option value="2">GKN 2</option>
                                    <option value="1">GKN 1</option>
                                @elseif(Auth::user()->hasRole('DOKTER'))
                                    {{-- DOKTER bisa distribusi ke lokasi manapun termasuk lokasi mereka sendiri --}}
                                    {{-- Karena mereka bisa menerima dari lokasi lain atau mengirim ke lokasi lain --}}
                                    <option value="1">GKN 1</option>
                                    <option value="2">GKN 2</option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah-{{ $item->id_obat }}" class="form-label">Jumlah Distribusi</label>
                            <input type="number" name="jumlah" id="jumlah-{{ $item->id_obat }}" class="form-control" required min="1" max="{{ (int)($item->stok_gkn1 ?? 0) }}">
                            <div class="form-text">
                                Stok tersedia di lokasi asal: <span class="stok-tersedia fw-bold">{{ (int)($item->stok_gkn1 ?? 0) }}</span>
                            </div>
                        </div>
                         <div class="alert alert-danger d-none" role="alert" id="warning-{{ $item->id_obat }}">
                            Jumlah distribusi tidak boleh melebihi stok yang tersedia dan lokasi tujuan tidak boleh sama.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn-{{ $item->id_obat }}">Simpan Distribusi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Live Search dengan Debounce
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
