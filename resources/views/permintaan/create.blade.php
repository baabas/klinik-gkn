@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Buat Permintaan Obat Baru</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('permintaan.store') }}" method="POST">
                @csrf
                {{-- Bagian Header Form --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_permintaan" class="form-label">Tanggal Permintaan</label>
                        <input type="date" class="form-control" id="tanggal_permintaan" name="tanggal_permintaan" value="{{ old('tanggal_permintaan', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-8">
                         <label for="catatan" class="form-label">Catatan (Opsional)</label>
                         <textarea class="form-control" id="catatan" name="catatan" rows="1">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                <hr>

                {{-- Bagian Detail Barang --}}
                <h5 class="mb-3">Detail Obat</h5>

                {{-- 1. Barang yang Sudah Terdaftar --}}
                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        Obat Terdaftar
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-barang-btn">
                            <i class="bi bi-plus-circle"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body" id="barang-terdaftar-wrapper">
                        {{-- Baris akan ditambahkan oleh JavaScript --}}
                        <p class="text-muted mb-0" id="barang-terdaftar-placeholder">Belum ada Obat terdaftar yang ditambahkan.</p>
                    </div>
                </div>

                {{-- 2. Request Barang Baru --}}
                <div class="card">
                     <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        Request Obat Baru
                        <button type="button" class="btn btn-sm btn-outline-success" id="add-barang-baru-btn">
                            <i class="bi bi-plus-circle"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body" id="barang-baru-wrapper">
                        {{-- Baris akan ditambahkan oleh JavaScript --}}
                        <p class="text-muted mb-0" id="barang-baru-placeholder">Belum ada request Obat baru.</p>
                    </div>
                </div>

                {{-- Tombol Aksi Form --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Permintaan</button>
                    <a href="{{ route('permintaan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let barangIndex = 0;
    let barangBaruIndex = 0;

    const selectOptions = `<option value="" disabled selected>-- Pilih Barang --</option>@foreach($barangMedis as $item)<option value="{{ $item->id_obat }}" data-kode="{{ $item->kode_obat }}" data-satuan="{{ $item->satuan }}" data-kemasan="{{ $item->kemasan ?? '' }}">{{ $item->kode_obat }} - {{ $item->nama_obat }}</option>@endforeach`;

    const barangWrapper = document.getElementById('barang-terdaftar-wrapper');
    const barangPlaceholder = document.getElementById('barang-terdaftar-placeholder');
    const barangBaruWrapper = document.getElementById('barang-baru-wrapper');
    const barangBaruPlaceholder = document.getElementById('barang-baru-placeholder');

    document.getElementById('add-barang-btn').addEventListener('click', function() {
        if (barangPlaceholder) barangPlaceholder.style.display = 'none';

        const newRow = document.createElement('div');
        newRow.classList.add('row', 'g-2', 'align-items-center', 'mb-3', 'barang-row');
        newRow.innerHTML = `
            <div class="col-md-3">
                <select class="form-select barang-select" name="barang[${barangIndex}][id]" required>
                    ${selectOptions}
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="barang[${barangIndex}][jumlah]" placeholder="Jumlah" min="1" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control satuan-input" name="barang[${barangIndex}][satuan]" placeholder="Satuan" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control kemasan-input" name="barang[${barangIndex}][kemasan]" placeholder="Kemasan (opsional)">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control catatan-input" name="barang[${barangIndex}][catatan]" placeholder="Keterangan (opsional)">
            </div>
            <div class="col-md-1 text-end"><button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="bi bi-trash"></i></button></div>
        `;
        barangWrapper.appendChild(newRow);
        barangIndex++;
    });

    document.getElementById('add-barang-baru-btn').addEventListener('click', function() {
        if (barangBaruPlaceholder) barangBaruPlaceholder.style.display = 'none';

        const newRow = document.createElement('div');
        newRow.classList.add('row', 'g-2', 'align-items-center', 'mb-3', 'barang-row');
        newRow.innerHTML = `
            <div class="col-md-3"><input type="text" class="form-control" name="barang_baru[${barangBaruIndex}][nama]" placeholder="Nama Barang Baru" required></div>
            <div class="col-md-2"><input type="number" class="form-control" name="barang_baru[${barangBaruIndex}][jumlah]" placeholder="Jumlah" min="1" required></div>
            <div class="col-md-2"><input type="text" class="form-control" name="barang_baru[${barangBaruIndex}][satuan]" placeholder="Satuan" required></div>
            <div class="col-md-2"><input type="text" class="form-control" name="barang_baru[${barangBaruIndex}][kemasan]" placeholder="Kemasan (opsional)"></div>
            <div class="col-md-2"><input type="text" class="form-control" name="barang_baru[${barangBaruIndex}][catatan]" placeholder="Keterangan (opsional)"></div>
            <div class="col-md-1 text-end"><button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="bi bi-trash"></i></button></div>
        `;
        barangBaruWrapper.appendChild(newRow);
        barangBaruIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-row-btn')) {
            e.target.closest('.barang-row').remove();

            if (barangWrapper.querySelectorAll('.barang-row').length === 0 && barangPlaceholder) {
                barangPlaceholder.style.display = 'block';
            }
            if (barangBaruWrapper.querySelectorAll('.barang-row').length === 0 && barangBaruPlaceholder) {
                barangBaruPlaceholder.style.display = 'block';
            }
        }
    });

    function isiDataBarang(selectElement) {
        if (!selectElement) {
            return;
        }

        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (!selectedOption) {
            return;
        }

        const row = selectElement.closest('.barang-row');
        if (!row) {
            return;
        }

        const satuanInput = row.querySelector('.satuan-input');
        const kemasanInput = row.querySelector('.kemasan-input');

        if (satuanInput && selectedOption.dataset.satuan !== undefined) {
            satuanInput.value = selectedOption.dataset.satuan || '';
        }

        if (kemasanInput && selectedOption.dataset.kemasan !== undefined) {
            kemasanInput.value = selectedOption.dataset.kemasan || '';
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('barang-select')) {
            isiDataBarang(e.target);
        }
    });
});
</script>
@endpush
