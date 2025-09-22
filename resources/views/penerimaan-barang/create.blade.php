@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Input Penerimaan Barang</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('penerimaan-barang.store') }}" method="POST" id="formPenerimaan">
                @csrf
                
                <!-- Header Penerimaan -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Penerimaan</label>
                            <input type="date" name="tanggal_penerimaan" class="form-control" 
                                   required max="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nomor Faktur</label>
                            <input type="text" name="nomor_faktur" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Supplier</label>
                            <input type="text" name="supplier" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Lokasi Penerimaan</label>
                            <select name="id_lokasi" class="form-control" required>
                                @foreach($lokasiList as $lokasi)
                                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Container untuk Item Barang -->
                <div class="items-container mt-4">
                    <div class="item-row mb-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col">
                                        <h5>Item #1</h5>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Pilih Barang -->
                                <div class="form-group">
                                    <label>Pilih Barang</label>
                                    <select name="items[0][id_barang]" class="form-control select-barang" required>
                                        <option value="">Pilih Barang</option>
                                        @foreach($barangList as $barang)
                                            <option value="{{ $barang->id_obat }}" 
                                                    data-satuan="{{ $barang->satuan }}"
                                                    data-kemasan="{{ $barang->kemasan }}"
                                                    data-konversi="{{ $barang->jumlah_satuan_perkemasan * $barang->jumlah_unit_persatuan }}">
                                                {{ $barang->nama_obat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Container untuk Batch -->
                                <div class="batch-container">
                                    <div class="batch-row mt-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <div class="row">
                                                    <div class="col">
                                                        <h6 class="mb-0">Batch #1</h6>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button type="button" class="btn btn-danger btn-sm btn-remove-batch">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Nomor Batch</label>
                                                            <input type="text" name="items[0][batch][0][nomor]" 
                                                                   class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Tanggal Kadaluarsa</label>
                                                            <input type="date" name="items[0][batch][0][exp_date]" 
                                                                   class="form-control" required 
                                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Jumlah Kemasan</label>
                                                            <div class="input-group">
                                                                <input type="number" name="items[0][batch][0][jumlah_kemasan]" 
                                                                       class="form-control jumlah-kemasan" required min="1">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text kemasan-label">-</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col">
                                                        <small class="text-muted">
                                                            Total unit: <span class="total-unit">0</span> 
                                                            <span class="unit-label">unit</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Tambah Batch -->
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-info btn-sm btn-add-batch">
                                        <i class="fas fa-plus"></i> Tambah Batch
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Tambah Item -->
                <div class="form-group mt-3">
                    <button type="button" class="btn btn-primary" id="btnAddItem">
                        <i class="fas fa-plus"></i> Tambah Item Barang
                    </button>
                </div>

                <!-- Keterangan -->
                <div class="form-group mt-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Penerimaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 0;
    let batchCounts = {0: 0};

    // Update labels dan hitung konversi
    function updateLabelsAndCalculate(itemRow) {
        const select = itemRow.querySelector('.select-barang');
        const option = select.options[select.selectedIndex];
        
        if (select.value) {
            const kemasan = option.dataset.kemasan;
            const konversi = parseInt(option.dataset.konversi);
            
            // Update semua label kemasan dalam item ini
            itemRow.querySelectorAll('.kemasan-label').forEach(label => {
                label.textContent = kemasan;
            });

            // Hitung ulang semua total unit dalam item ini
            itemRow.querySelectorAll('.batch-row').forEach(batchRow => {
                const jumlahInput = batchRow.querySelector('.jumlah-kemasan');
                const totalUnitSpan = batchRow.querySelector('.total-unit');
                
                if (jumlahInput.value) {
                    const total = parseInt(jumlahInput.value) * konversi;
                    totalUnitSpan.textContent = total.toLocaleString();
                }
            });
        }
    }

    // Event handler untuk perubahan barang
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-barang')) {
            const itemRow = e.target.closest('.item-row');
            updateLabelsAndCalculate(itemRow);
        }
    });

    // Event handler untuk perubahan jumlah
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('jumlah-kemasan')) {
            const itemRow = e.target.closest('.item-row');
            updateLabelsAndCalculate(itemRow);
        }
    });

    // Tambah batch baru
    function addBatch(itemIndex, batchContainer) {
        const batchIndex = batchCounts[itemIndex]++;
        const template = document.querySelector('.batch-row').cloneNode(true);
        
        template.querySelector('h6').textContent = `Batch #${batchIndex + 2}`;
        
        template.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace('[0][batch][0]', `[${itemIndex}][batch][${batchIndex}]`);
            if (input.type !== 'select-one') {
                input.value = '';
            }
        });

        template.querySelector('.total-unit').textContent = '0';
        batchContainer.appendChild(template);
    }

    // Tambah item baru
    document.getElementById('btnAddItem').addEventListener('click', function() {
        itemCount++;
        batchCounts[itemCount] = 0;
        
        const template = document.querySelector('.item-row').cloneNode(true);
        template.querySelector('h5').textContent = `Item #${itemCount + 1}`;
        
        // Update nama fields
        template.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace('items[0]', `items[${itemCount}]`);
            if (input.type !== 'select-one') {
                input.value = '';
            }
        });

        // Reset batch container
        const batchContainer = template.querySelector('.batch-container');
        batchContainer.innerHTML = '';
        addBatch(itemCount, batchContainer);

        document.querySelector('.items-container').appendChild(template);
    });

    // Event delegation untuk tombol tambah/hapus batch
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-add-batch')) {
            const itemRow = e.target.closest('.item-row');
            const itemIndex = Array.from(itemRow.parentNode.children).indexOf(itemRow);
            const batchContainer = itemRow.querySelector('.batch-container');
            addBatch(itemIndex, batchContainer);
        }
        
        if (e.target.closest('.btn-remove-batch')) {
            const batchRow = e.target.closest('.batch-row');
            const batchContainer = batchRow.parentNode;
            if (batchContainer.querySelectorAll('.batch-row').length > 1) {
                batchRow.remove();
            }
        }

        if (e.target.closest('.btn-remove-item')) {
            const items = document.querySelectorAll('.item-row');
            if (items.length > 1) {
                e.target.closest('.item-row').remove();
            }
        }
    });

    // Validasi form
    document.getElementById('formPenerimaan').addEventListener('submit', function(e) {
        let isValid = true;
        const items = document.querySelectorAll('.item-row');

        items.forEach(item => {
            const barang = item.querySelector('.select-barang').value;
            const batches = item.querySelectorAll('.batch-row');
            
            if (!barang || batches.length === 0) {
                isValid = false;
            }

            batches.forEach(batch => {
                const inputs = batch.querySelectorAll('input[required]');
                inputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                    }
                });
            });
        });

        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua data penerimaan dengan benar');
        }
    });
});
</script>
@endpush
@endsection