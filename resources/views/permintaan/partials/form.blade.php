@php
    $registeredDetails = collect(old('items', isset($permintaan)
        ? $permintaan->details
            ->whereNotNull('barang_id')
            ->map(fn ($detail) => [
                'barang_id' => $detail->barang_id,
                'barang_text' => $detail->barang?->nama_obat . ' (' . $detail->barang?->kode_obat . ')',
                'barang_kemasan_id' => $detail->barang_kemasan_id,
                'barang_kemasan_text' => $detail->kemasan,
                'jumlah' => (int) $detail->jumlah,
                'satuan' => $detail->barang?->satuan_dasar,
                'total_unit' => $detail->total_unit,
                'keterangan' => $detail->keterangan,
            ])
            ->values()
            ->toArray()
        : []));

    $newDetails = collect(old('new_items', isset($permintaan)
        ? $permintaan->details
            ->whereNull('barang_id')
            ->map(fn ($detail) => [
                'nama' => $detail->nama_barang_baru,
                'jumlah' => $detail->jumlah,
                'satuan' => $detail->satuan,
                'kemasan' => $detail->kemasan,
                'keterangan' => $detail->keterangan,
            ])
            ->values()
            ->toArray()
        : []));

    $registeredNextIndex = ($registeredDetails->keys()->map(fn ($key) => (int) $key)->max() ?? -1) + 1;
    $newNextIndex = ($newDetails->keys()->map(fn ($key) => (int) $key)->max() ?? -1) + 1;

    $tanggalDefault = old('tanggal');
    if (! $tanggalDefault) {
        $tanggalDefault = isset($permintaan) && optional($permintaan)->tanggal
            ? optional($permintaan->tanggal)->toDateString()
            : now()->toDateString();
    }

    $catatanDefault = old('catatan', optional($permintaan)->catatan);
@endphp

<form action="{{ $action }}" method="post" id="permintaan-form">
    @csrf
    @if(isset($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="tanggal" class="form-label">Tanggal Permintaan <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ $tanggalDefault }}" required>
        </div>
        <div class="col-md-8">
            <label for="catatan" class="form-label">Catatan</label>
            <textarea class="form-control" name="catatan" id="catatan" rows="1" placeholder="Catatan tambahan opsional">{{ $catatanDefault }}</textarea>
        </div>
    </div>

    <h5 class="mt-4">Obat Terdaftar</h5>
    <p class="text-muted">Pilih obat dari daftar master. Masukkan jumlah kemasan dan kemasan yang diminta.</p>

    <div class="table-responsive mb-3">
        <table class="table table-bordered align-middle" id="registered-items">
            <thead class="table-light">
                <tr>
                    <th style="width:30%">Obat</th>
                    <th style="width:18%">Kemasan</th>
                    <th style="width:12%">Jumlah</th>
                    <th style="width:15%">Satuan Dasar</th>
                    <th>Keterangan</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($registeredDetails as $index => $detail)
                    <tr data-index="{{ $index }}">
                        <td>
                            <select class="form-select select-barang" name="items[{{ $index }}][barang_id]" data-placeholder="Cari nama atau kode obat" required>
                                <option value="">Pilih barang</option>
                                @if($detail['barang_id'])
                                    <option value="{{ $detail['barang_id'] }}" selected>{{ $detail['barang_text'] }}</option>
                                @endif
                            </select>
                        </td>
                        <td>
                            <select class="form-select select-kemasan" name="items[{{ $index }}][barang_kemasan_id]" data-placeholder="Pilih kemasan" required>
                                <option value="">Pilih kemasan</option>
                                @if($detail['barang_kemasan_id'])
                                    <option value="{{ $detail['barang_kemasan_id'] }}" selected>{{ $detail['barang_kemasan_text'] }}</option>
                                @endif
                            </select>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="1" class="form-control input-jumlah" name="items[{{ $index }}][jumlah]" value="{{ $detail['jumlah'] }}" required>
                            </div>
                            <small class="text-muted conversion-text">{{ $detail['total_unit'] ? '≈ ' . number_format($detail['total_unit']) . ' ' . ($detail['satuan'] ?? '') : '' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark satuan-text">{{ $detail['satuan'] ?? '-' }}</span>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="items[{{ $index }}][keterangan]" value="{{ $detail['keterangan'] }}" placeholder="Keterangan (opsional)">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Hapus baris">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-registered text-muted">
                        <td colspan="6" class="text-center">Belum ada obat terdaftar ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-registered-item">
        <i class="bi bi-plus-circle"></i> Tambah Obat Terdaftar
    </button>

    <h5 class="mt-4">Permintaan Obat Baru</h5>
    <p class="text-muted">Isi bagian ini jika obat belum ada di master barang medis.</p>

    <div class="table-responsive mb-3">
        <table class="table table-bordered align-middle" id="new-items">
            <thead class="table-light">
                <tr>
                    <th>Nama Barang</th>
                    <th style="width:12%">Jumlah</th>
                    <th style="width:15%">Satuan</th>
                    <th style="width:20%">Kemasan</th>
                    <th>Keterangan</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($newDetails as $index => $detail)
                    <tr data-index="{{ $index }}">
                        <td>
                            <input type="text" class="form-control" name="new_items[{{ $index }}][nama]" value="{{ $detail['nama'] }}" placeholder="Nama barang" required>
                        </td>
                        <td>
                            <input type="number" min="0" step="0.01" class="form-control" name="new_items[{{ $index }}][jumlah]" value="{{ $detail['jumlah'] }}" required>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="new_items[{{ $index }}][satuan]" value="{{ $detail['satuan'] }}" placeholder="Contoh: Tablet" required>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="new_items[{{ $index }}][kemasan]" value="{{ $detail['kemasan'] }}" placeholder="Contoh: Box isi 10">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="new_items[{{ $index }}][keterangan]" value="{{ $detail['keterangan'] }}" placeholder="Keterangan (opsional)">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Hapus baris">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-new text-muted">
                        <td colspan="6" class="text-center">Tidak ada permintaan obat baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-new-item">
        <i class="bi bi-plus-circle"></i> Tambah Obat Baru
    </button>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('permintaan.index') }}" class="btn btn-light">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Draft</button>
    </div>
</form>

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const registeredTable = document.querySelector('#registered-items tbody');
            const newTable = document.querySelector('#new-items tbody');
            const addRegisteredBtn = document.querySelector('#add-registered-item');
            const addNewBtn = document.querySelector('#add-new-item');

            let registeredIndex = {{ $registeredNextIndex }};
            let newIndex = {{ $newNextIndex }};

            function initSelect2(element) {
                $(element).select2({
                    placeholder: element.dataset.placeholder || 'Pilih opsi',
                    width: '100%',
                    ajax: {
                        url: '{{ route('permintaan.barang.search') }}',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                    },
                    templateSelection: data => {
                        if (!data.id) {
                            return data.text;
                        }
                        element.closest('tr').dataset.barangSatuan = data.satuan || '';
                        updateSatuan(element.closest('tr'));
                        return data.text;
                    }
                });
            }

            function initKemasanSelect(select, barangId) {
                $(select).select2({
                    placeholder: select.dataset.placeholder || 'Pilih kemasan',
                    width: '100%'
                });

                if (barangId) {
                    select.closest('tr').dataset.currentKemasan = select.value;
                    fetchKemasan(select.closest('tr'), barangId);
                }
            }

            function fetchKemasan(row, barangId) {
                const kemasanSelect = row.querySelector('.select-kemasan');
                kemasanSelect.innerHTML = '<option value="">Memuat...</option>';
                const currentValue = row.dataset.currentKemasan || '';

                fetch(`{{ url('permintaan/barang') }}/${barangId}/kemasan`)
                    .then(response => response.json())
                    .then(data => {
                        kemasanSelect.innerHTML = '<option value="">Pilih kemasan</option>';
                        data.data.forEach(item => {
                            const option = new Option(item.text, item.id, false, false);
                            option.dataset.isi = item.isi;
                            kemasanSelect.append(option);
                        });
                        if (currentValue) {
                            $(kemasanSelect).val(currentValue).trigger('change');
                        } else {
                            $(kemasanSelect).val('').trigger('change');
                        }
                        row.dataset.barangSatuan = data.satuan || '';
                        updateSatuan(row);
                    });
            }

            function updateSatuan(row) {
                const satuanBadge = row.querySelector('.satuan-text');
                const satuan = row.dataset.barangSatuan || '-';
                if (satuanBadge) {
                    satuanBadge.textContent = satuan || '-';
                }
                updateConversion(row);
            }

            function updateConversion(row) {
                const jumlah = parseInt(row.querySelector('.input-jumlah')?.value || 0, 10);
                const kemasanSelect = row.querySelector('.select-kemasan');
                const option = kemasanSelect?.options[kemasanSelect.selectedIndex];
                const conversionText = row.querySelector('.conversion-text');
                const satuan = row.dataset.barangSatuan || '';

                if (jumlah > 0 && option && option.dataset.isi) {
                    const total = jumlah * parseInt(option.dataset.isi, 10);
                    conversionText.textContent = `≈ ${new Intl.NumberFormat('id-ID').format(total)} ${satuan}`;
                } else {
                    conversionText.textContent = '';
                }
            }

            function attachRowEvents(row) {
                const barangSelect = row.querySelector('.select-barang');
                const kemasanSelect = row.querySelector('.select-kemasan');
                const jumlahInput = row.querySelector('.input-jumlah');

                initSelect2(barangSelect);
                initKemasanSelect(kemasanSelect, barangSelect.value);

                $(barangSelect).on('select2:select', function (e) {
                    const barangId = e.params.data.id;
                    row.dataset.currentKemasan = '';
                    fetchKemasan(row, barangId);
                });

                $(kemasanSelect).on('select2:select', function (e) {
                    const option = e.params.data.element;
                    row.dataset.currentKemasan = option.value;
                    updateConversion(row);
                });

                jumlahInput?.addEventListener('input', () => updateConversion(row));

                row.querySelector('.remove-row')?.addEventListener('click', function () {
                    row.remove();
                    if (!registeredTable.querySelectorAll('tr[data-index]').length) {
                        registeredTable.insertAdjacentHTML('beforeend', `
                            <tr class="empty-registered text-muted">
                                <td colspan="6" class="text-center">Belum ada obat terdaftar ditambahkan.</td>
                            </tr>`);
                    }
                });
            }

            function addRegisteredRow(data = {}) {
                registeredTable.querySelector('.empty-registered')?.remove();
                const index = registeredIndex++;
                const row = document.createElement('tr');
                row.dataset.index = index;
                row.innerHTML = `
                    <td>
                        <select class="form-select select-barang" name="items[${index}][barang_id]" data-placeholder="Cari nama atau kode obat" required>
                            <option value="">Pilih barang</option>
                            ${data.barang_id ? `<option value="${data.barang_id}" selected>${data.barang_text}</option>` : ''}
                        </select>
                    </td>
                    <td>
                        <select class="form-select select-kemasan" name="items[${index}][barang_kemasan_id]" data-placeholder="Pilih kemasan" required>
                            <option value="">Pilih kemasan</option>
                            ${data.barang_kemasan_id ? `<option value="${data.barang_kemasan_id}" data-isi="${data.isi || 0}" selected>${data.barang_kemasan_text}</option>` : ''}
                        </select>
                    </td>
                    <td>
                        <input type="number" min="1" class="form-control input-jumlah" name="items[${index}][jumlah]" value="${data.jumlah || 1}" required>
                        <small class="text-muted conversion-text"></small>
                    </td>
                    <td><span class="badge bg-light text-dark satuan-text">${data.satuan || '-'}</span></td>
                    <td><input type="text" class="form-control" name="items[${index}][keterangan]" value="${data.keterangan || ''}" placeholder="Keterangan (opsional)"></td>
                    <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>`;

                registeredTable.appendChild(row);
                if (data.satuan) {
                    row.dataset.barangSatuan = data.satuan;
                }
                attachRowEvents(row);
                if (data.total_unit) {
                    row.querySelector('.conversion-text').textContent = `≈ ${new Intl.NumberFormat('id-ID').format(data.total_unit)} ${data.satuan || ''}`;
                }
            }

            function addNewRow(data = {}) {
                newTable.querySelector('.empty-new')?.remove();
                const index = newIndex++;
                const row = document.createElement('tr');
                row.dataset.index = index;
                row.innerHTML = `
                    <td><input type="text" class="form-control" name="new_items[${index}][nama]" value="${data.nama || ''}" placeholder="Nama barang" required></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="new_items[${index}][jumlah]" value="${data.jumlah || ''}" required></td>
                    <td><input type="text" class="form-control" name="new_items[${index}][satuan]" value="${data.satuan || ''}" placeholder="Contoh: Tablet" required></td>
                    <td><input type="text" class="form-control" name="new_items[${index}][kemasan]" value="${data.kemasan || ''}" placeholder="Contoh: Box isi 10"></td>
                    <td><input type="text" class="form-control" name="new_items[${index}][keterangan]" value="${data.keterangan || ''}" placeholder="Keterangan (opsional)"></td>
                    <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>`;

                row.querySelector('.remove-row').addEventListener('click', function () {
                    row.remove();
                    if (!newTable.querySelectorAll('tr[data-index]').length) {
                        newTable.insertAdjacentHTML('beforeend', `
                            <tr class="empty-new text-muted">
                                <td colspan="6" class="text-center">Tidak ada permintaan obat baru.</td>
                            </tr>`);
                    }
                });

                newTable.appendChild(row);
            }

            addRegisteredBtn.addEventListener('click', () => addRegisteredRow());
            addNewBtn.addEventListener('click', () => addNewRow());

            registeredTable.querySelectorAll('tr[data-index]').forEach(row => {
                attachRowEvents(row);
                const jumlahInput = row.querySelector('.input-jumlah');
                if (jumlahInput) {
                    jumlahInput.dispatchEvent(new Event('input'));
                }
            });

            newTable.querySelectorAll('tr[data-index]').forEach(row => {
                row.querySelector('.remove-row').addEventListener('click', function () {
                    row.remove();
                    if (!newTable.querySelectorAll('tr[data-index]').length) {
                        newTable.insertAdjacentHTML('beforeend', `
                            <tr class="empty-new text-muted">
                                <td colspan="6" class="text-center">Tidak ada permintaan obat baru.</td>
                            </tr>`);
                    }
                });
            });
        });
    </script>
@endpush
