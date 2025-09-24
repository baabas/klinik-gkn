@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Input Barang/Obat Masuk</h1>

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
                                                <input type="number" name="batches[0][jumlah_kemasan]" class="form-control"
                                                       min="1" required placeholder="Jumlah">
                                                <span class="input-group-text batch-kemasan-unit">Box</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Kadaluwarsa</label>
                                            <input type="date" name="batches[0][expired_at]" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Keterangan Batch</label>
                                            <input type="text" name="batches[0][keterangan]" class="form-control"
                                                   placeholder="Contoh: Batch A - Supplier X">
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
                                        <strong>Total Kemasan: <span id="total_kemasan">0</span></strong>
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
                        <label class="form-label">Tanggal Kadaluwarsa</label>
                        <input type="date" name="batches[${index}][expired_at]" class="form-control">
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
        document.querySelector('.batch-jumlah').addEventListener('input', calculateTotals);
        updateRemoveButtons();
    });
</script>
@endpush