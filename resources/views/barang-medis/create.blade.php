@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Tambah Barang Medis Baru</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('barang-medis.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama_obat" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_obat" id="nama_obat"
                               class="form-control @error('nama_obat') is-invalid @enderror"
                               placeholder="Contoh: Paracetamol 500mg" value="{{ old('nama_obat') }}" required>
                        @error('nama_obat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="tipe" class="form-label">Tipe</label>
                        <select name="tipe" id="tipe" class="form-select @error('tipe') is-invalid @enderror" required>
                            <option value="" disabled {{ old('tipe') ? '' : 'selected' }}>Pilih tipe barang</option>
                            <option value="OBAT" {{ old('tipe') === 'OBAT' ? 'selected' : '' }}>OBAT</option>
                            <option value="ALKES" {{ old('tipe') === 'ALKES' ? 'selected' : '' }}>ALKES</option>
                        </select>
                        @error('tipe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="satuan_dasar" class="form-label">Satuan Dasar</label>
                        <input type="text" name="satuan_dasar" id="satuan_dasar"
                               class="form-control @error('satuan_dasar') is-invalid @enderror"
                               placeholder="Contoh: tablet, kapsul, ml, pcs" value="{{ old('satuan_dasar') }}" required>
                        @error('satuan_dasar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Definisi Kemasan</h5>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-kemasan">Tambah Kemasan</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th class="w-50">Nama Kemasan</th>
                                <th class="w-25">Isi per Kemasan</th>
                                <th class="text-center">Default</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kemasan-rows">
                            @php
                                $oldKemasan = old('kemasan', [[
                                    'nama_kemasan' => null,
                                    'isi_per_kemasan' => null,
                                    'is_default' => true,
                                ]]);
                            @endphp
                            @foreach ($oldKemasan as $index => $row)
                                <tr class="kemasan-row" data-index="{{ $index }}">
                                    <td>
                                        <select name="kemasan[{{ $index }}][nama_kemasan]"
                                                class="form-select kemasan-select @error('kemasan.' . $index . '.nama_kemasan') is-invalid @enderror"
                                                required>
                                            <option value="" disabled {{ empty($row['nama_kemasan']) ? 'selected' : '' }}>Pilih nama kemasan</option>
                                            @foreach ($opsiKemasan as $opsi)
                                                <option value="{{ $opsi }}" {{ ($row['nama_kemasan'] ?? '') === $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                                            @endforeach
                                        </select>
                                        @error('kemasan.' . $index . '.nama_kemasan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" min="1" name="kemasan[{{ $index }}][isi_per_kemasan]"
                                               class="form-control isi-input @error('kemasan.' . $index . '.isi_per_kemasan') is-invalid @enderror"
                                               value="{{ $row['isi_per_kemasan'] ?? '' }}" required>
                                        @error('kemasan.' . $index . '.isi_per_kemasan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input default-checkbox"
                                                   name="kemasan[{{ $index }}][is_default]" value="1"
                                                   {{ !empty($row['is_default']) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($errors->has('kemasan'))
                    <div class="text-danger small mb-2">{{ $errors->first('kemasan') }}</div>
                @endif
                <p class="text-muted small">Semua konversi berasal dari definisi kemasan dan akan dipakai saat input stok.</p>

                <div class="alert alert-light border mt-3" id="preview-konversi">
                    Pilih kemasan default untuk melihat konversi.
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
                    <a href="{{ route('barang-medis.index') }}" class="btn btn-secondary w-100 w-sm-auto">Batal</a>
                    <button type="submit" class="btn btn-primary w-100 w-sm-auto">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script type="text/template" id="kemasan-row-template">
        <tr class="kemasan-row" data-index="__INDEX__">
            <td>
                <select name="kemasan[__INDEX__][nama_kemasan]" class="form-select kemasan-select" required>
                    <option value="" disabled selected>Pilih nama kemasan</option>
                    @foreach ($opsiKemasan as $opsi)
                        <option value="{{ $opsi }}">{{ $opsi }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" min="1" name="kemasan[__INDEX__][isi_per_kemasan]" class="form-control isi-input" required>
            </td>
            <td class="text-center">
                <div class="form-check d-inline-flex justify-content-center">
                    <input type="checkbox" class="form-check-input default-checkbox" name="kemasan[__INDEX__][is_default]" value="1">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button>
            </td>
        </tr>
    </script>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.getElementById('kemasan-rows');
            const addButton = document.getElementById('add-kemasan');
            const template = document.getElementById('kemasan-row-template').textContent.trim();
            const preview = document.getElementById('preview-konversi');
            const satuanInput = document.getElementById('satuan_dasar');

            let rowIndex = Array.from(tableBody.querySelectorAll('.kemasan-row')).reduce((max, row) => {
                const current = parseInt(row.getAttribute('data-index'), 10);
                return Number.isNaN(current) ? max : Math.max(max, current);
            }, -1);

            const ensureSingleDefault = (changedCheckbox) => {
                if (changedCheckbox.checked) {
                    tableBody.querySelectorAll('.default-checkbox').forEach((checkbox) => {
                        if (checkbox !== changedCheckbox) {
                            checkbox.checked = false;
                        }
                    });
                } else {
                    const anyChecked = Array.from(tableBody.querySelectorAll('.default-checkbox')).some((checkbox) => checkbox.checked);
                    if (!anyChecked) {
                        changedCheckbox.checked = true;
                    }
                }
                updatePreview();
            };

            const updatePreview = () => {
                const defaultRow = Array.from(tableBody.querySelectorAll('.kemasan-row')).find((row) => {
                    const checkbox = row.querySelector('.default-checkbox');
                    return checkbox && checkbox.checked;
                });

                if (!defaultRow) {
                    preview.textContent = 'Pilih kemasan default untuk melihat konversi.';
                    return;
                }

                const kemasanName = defaultRow.querySelector('.kemasan-select')?.value;
                const isi = defaultRow.querySelector('.isi-input')?.value;
                const satuan = satuanInput.value;

                if (kemasanName && isi) {
                    const satuanLabel = satuan ? satuan : 'satuan dasar';
                    preview.textContent = `1 ${kemasanName} = ${isi} ${satuanLabel}`;
                } else {
                    preview.textContent = 'Lengkapi data kemasan default untuk melihat konversi.';
                }
            };

            const bindRowEvents = (row) => {
                const checkbox = row.querySelector('.default-checkbox');
                if (checkbox) {
                    checkbox.addEventListener('change', function () {
                        ensureSingleDefault(this);
                    });
                }

                row.querySelectorAll('.kemasan-select, .isi-input').forEach((element) => {
                    element.addEventListener('input', updatePreview);
                    element.addEventListener('change', updatePreview);
                });

                const removeButton = row.querySelector('.remove-row');
                if (removeButton) {
                    removeButton.addEventListener('click', () => {
                        if (tableBody.children.length <= 1) {
                            return;
                        }

                        row.remove();

                        const hasDefault = Array.from(tableBody.querySelectorAll('.default-checkbox')).some((checkbox) => checkbox.checked);
                        if (!hasDefault && tableBody.querySelector('.default-checkbox')) {
                            tableBody.querySelector('.default-checkbox').checked = true;
                        }
                        updatePreview();
                    });
                }
            };

            Array.from(tableBody.querySelectorAll('.kemasan-row')).forEach((row) => {
                bindRowEvents(row);
            });

            addButton.addEventListener('click', () => {
                rowIndex += 1;
                const newRowHtml = template.replace(/__INDEX__/g, rowIndex);
                const tempWrapper = document.createElement('tbody');
                tempWrapper.innerHTML = newRowHtml;
                const newRow = tempWrapper.firstElementChild;
                tableBody.appendChild(newRow);
                bindRowEvents(newRow);
            });

            if (!Array.from(tableBody.querySelectorAll('.default-checkbox')).some((checkbox) => checkbox.checked)) {
                const firstCheckbox = tableBody.querySelector('.default-checkbox');
                if (firstCheckbox) {
                    firstCheckbox.checked = true;
                }
            }

            satuanInput.addEventListener('input', updatePreview);
            updatePreview();
        });
    </script>
@endpush
