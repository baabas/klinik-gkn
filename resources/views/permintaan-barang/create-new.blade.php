<x-app-layout>
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Form Permintaan Barang</h4>
            <div>
                <span class="badge badge-primary">Draft</span>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('permintaan-barang.store') }}" method="POST" id="formPermintaan">
                @csrf
                
                <!-- Header Permintaan -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nomor Permintaan</label>
                            <input type="text" class="form-control" value="{{ $nomorPermintaan }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Permintaan</label>
                            <input type="date" name="tanggal_permintaan" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lokasi Klinik</label>
                            <input type="text" class="form-control" 
                                   value="{{ $user->lokasi->nama_lokasi ?? 'Lokasi tidak ditemukan' }}" readonly>
                            <input type="hidden" name="id_lokasi" value="{{ $user->id_lokasi }}">
                        </div>
                    </div>
                </div>

                <!-- Barang Terdaftar -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Barang Terdaftar</h5>
                        <button type="button" class="btn btn-primary btn-sm" id="btnAddItem">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40%">Nama Barang</th>
                                        <th style="width: 15%">Jumlah</th>
                                        <th style="width: 20%">Total Unit</th>
                                        <th style="width: 15%">Stok Tersedia</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsContainer">
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[0][id_barang]" class="form-control select-barang" required>
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach($barangList as $barang)
                                                    <option value="{{ $barang->id_obat }}"
                                                            data-kemasan="{{ $barang->kemasan }}"
                                                            data-satuan="{{ $barang->satuan }}"
                                                            data-konversi="{{ $barang->jumlah_satuan_perkemasan * $barang->jumlah_unit_persatuan }}"
                                                            data-satuan-terkecil="{{ $barang->satuan_terkecil }}"
                                                            data-stok="{{ $barang->stok->where('id_lokasi', Auth::user()->id_lokasi)->first()->jumlah ?? 0 }}"
                                                            data-min-stok="{{ $barang->min_stok }}">
                                                        {{ $barang->kode_obat }} - {{ $barang->nama_obat }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="mt-1">
                                                <small class="text-muted info-konversi"></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="items[0][jumlah_kemasan]" 
                                                       class="form-control jumlah-kemasan" required min="1">
                                                <div class="input-group-append">
                                                    <span class="input-group-text kemasan-label">-</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" class="form-control total-unit" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text unit-label">-</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="stok-tersedia">0</span>
                                            <span class="satuan-terkecil-label">-</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Request Barang Baru -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Request Barang Baru</h5>
                        <button type="button" class="btn btn-success btn-sm" id="btnAddNewItem">
                            <i class="fas fa-plus"></i> Tambah Barang Baru
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 25%">Nama Barang</th>
                                        <th style="width: 15%">Tipe</th>
                                        <th style="width: 15%">Jumlah</th>
                                        <th style="width: 15%">Satuan</th>
                                        <th style="width: 20%">Spesifikasi</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="newItemsContainer">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="form-group">
                    <label>Catatan Permintaan</label>
                    <textarea name="catatan" class="form-control" rows="3" 
                              placeholder="Tambahkan catatan jika diperlukan"></textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="form-group mt-4 text-right">
                    <button type="button" class="btn btn-secondary mr-2" onclick="window.history.back()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Permintaan
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
    let newItemCount = 0;

    // Template untuk barang baru
    const newItemTemplate = `
        <tr class="new-item-row">
            <td>
                <input type="text" name="new_items[{index}][nama_barang]" class="form-control" required>
            </td>
            <td>
                <select name="new_items[{index}][tipe]" class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="OBAT">OBAT</option>
                    <option value="ALKES">ALKES</option>
                </select>
            </td>
            <td>
                <input type="number" name="new_items[{index}][jumlah]" class="form-control" required min="1">
                <small class="form-text text-muted">dalam unit terkecil</small>
            </td>
            <td>
                <input type="text" name="new_items[{index}][satuan]" class="form-control" 
                       required placeholder="tablet/piece/unit">
            </td>
            <td>
                <textarea name="new_items[{index}][spesifikasi]" class="form-control" rows="2" required
                          placeholder="Masukkan detail spesifikasi barang"></textarea>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-new-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    // Fungsi update informasi barang
    function updateItemInfo(row) {
        const select = row.querySelector('.select-barang');
        const option = select.options[select.selectedIndex];
        
        if (select.value) {
            const kemasan = option.dataset.kemasan;
            const satuan = option.dataset.satuan;
            const satuanTerkecil = option.dataset.satuanTerkecil;
            const konversi = parseInt(option.dataset.konversi);
            const stok = parseInt(option.dataset.stok);
            const minStok = parseInt(option.dataset.minStok);
            
            // Update labels
            row.querySelector('.kemasan-label').textContent = kemasan;
            row.querySelector('.unit-label').textContent = satuanTerkecil;
            row.querySelector('.satuan-terkecil-label').textContent = satuanTerkecil;
            row.querySelector('.stok-tersedia').textContent = stok;
            
            // Update info konversi
            row.querySelector('.info-konversi').textContent = 
                `1 ${kemasan} = ${konversi} ${satuanTerkecil}`;
            
            // Hitung total jika ada jumlah
            const jumlahInput = row.querySelector('.jumlah-kemasan');
            if (jumlahInput.value) {
                const total = parseInt(jumlahInput.value) * konversi;
                row.querySelector('.total-unit').value = total;

                // Peringatan jika permintaan melebihi stok
                if (total > stok) {
                    jumlahInput.classList.add('is-invalid');
                    row.querySelector('.total-unit').classList.add('is-invalid');
                } else {
                    jumlahInput.classList.remove('is-invalid');
                    row.querySelector('.total-unit').classList.remove('is-invalid');
                }
            }

            // Peringatan visual untuk stok minimum
            const stokSpan = row.querySelector('.stok-tersedia');
            if (stok <= minStok) {
                stokSpan.classList.add('text-warning');
                stokSpan.classList.add('font-weight-bold');
            } else {
                stokSpan.classList.remove('text-warning');
                stokSpan.classList.remove('font-weight-bold');
            }
        } else {
            // Reset semua label
            row.querySelector('.kemasan-label').textContent = '-';
            row.querySelector('.unit-label').textContent = '-';
            row.querySelector('.satuan-terkecil-label').textContent = '-';
            row.querySelector('.stok-tersedia').textContent = '0';
            row.querySelector('.info-konversi').textContent = '';
            row.querySelector('.total-unit').value = '';
        }
    }

    // Event handler untuk perubahan barang
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-barang')) {
            const row = e.target.closest('.item-row');
            updateItemInfo(row);
        }
    });

    // Event handler untuk perubahan jumlah
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('jumlah-kemasan')) {
            const row = e.target.closest('.item-row');
            updateItemInfo(row);
        }
    });

    // Tambah item barang terdaftar
    document.getElementById('btnAddItem').addEventListener('click', function() {
        itemCount++;
        const template = document.querySelector('.item-row').cloneNode(true);
        
        // Update nama fields
        template.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace('[0]', `[${itemCount}]`);
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }
        });

        // Reset informasi
        template.querySelector('.total-unit').value = '';
        template.querySelector('.stok-tersedia').textContent = '0';
        template.querySelector('.info-konversi').textContent = '';
        template.querySelector('.kemasan-label').textContent = '-';
        template.querySelector('.unit-label').textContent = '-';
        template.querySelector('.satuan-terkecil-label').textContent = '-';
        
        document.getElementById('itemsContainer').appendChild(template);
    });

    // Tambah barang baru
    document.getElementById('btnAddNewItem').addEventListener('click', function() {
        const template = newItemTemplate.replace(/{index}/g, newItemCount++);
        document.getElementById('newItemsContainer').insertAdjacentHTML('beforeend', template);
    });

    // Hapus item (delegasi event)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-item')) {
            const items = document.querySelectorAll('.item-row');
            if (items.length > 1) {
                e.target.closest('.item-row').remove();
            }
        }
        if (e.target.closest('.btn-remove-new-item')) {
            e.target.closest('.new-item-row').remove();
        }
    });

    // Validasi form sebelum submit
    document.getElementById('formPermintaan').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const items = document.querySelectorAll('.item-row');
        const newItems = document.querySelectorAll('.new-item-row');
        let hasValidItem = false;
        let hasError = false;

        // Cek barang terdaftar
        items.forEach(row => {
            const barang = row.querySelector('.select-barang').value;
            const jumlah = row.querySelector('.jumlah-kemasan').value;
            const total = row.querySelector('.total-unit').value;
            const stok = parseInt(row.querySelector('.stok-tersedia').textContent);

            if (barang && jumlah && jumlah > 0) {
                hasValidItem = true;
                if (parseInt(total) > stok) {
                    hasError = true;
                    alert('Jumlah permintaan melebihi stok tersedia');
                }
            }
        });

        // Cek barang baru
        newItems.forEach(row => {
            const inputs = row.querySelectorAll('input[required], select[required], textarea[required]');
            let isRowValid = true;
            inputs.forEach(input => {
                if (!input.value) {
                    isRowValid = false;
                }
            });
            if (isRowValid) {
                hasValidItem = true;
            }
        });

        if (!hasValidItem) {
            alert('Mohon tambahkan minimal satu item permintaan yang valid');
            return;
        }

        if (hasError) {
            return;
        }

        // Submit form jika semua validasi berhasil
        this.submit();
    });
});
</script>
@endpush
</x-app-layout>