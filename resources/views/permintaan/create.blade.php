@extends('layouts.sidebar-layout')

@section('content')
    <style>
        .page-head{display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid #e9ecef}
        .page-head h1{margin-bottom:.25rem;font-weight:700;letter-spacing:-.02em}
        .page-head .subtitle{color:#6c757d;font-size:.95rem}
        .action-group{display:flex;gap:.5rem;flex-wrap:wrap;align-items:center}
        .action-group .btn{min-width:170px;box-shadow:0 1px 2px rgba(0,0,0,.05)}
        .btn-soft-secondary{background:#fff;border:1px solid #d6dbe1;color:#495057;transition:all .2s ease}
        .btn-soft-secondary:hover{background:#f8f9fa;color:#212529;border-color:#cfd5dc;transform:translateY(-1px)}
        .form-section-title{font-weight:700;font-size:1.05rem;margin-top:1.5rem;margin-bottom:1rem;color:#212529}
        .form-header-intro{display:flex;gap:1rem;margin-bottom:1.5rem;padding:1.25rem;background:#f8f9fa;border-radius:.75rem;border:1px solid #e9ecef}
        .form-header-intro .form-group{flex:1;margin-bottom:0}
        .form-header-intro .form-label{font-weight:600;font-size:.9rem;color:#495057}
        .form-header-intro .form-control,.form-header-intro .form-select{border:1px solid #dee2e6;box-shadow:0 .125rem .25rem rgba(0,0,0,.03)}
        .card-header-detail{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
        .card-header-detail h6{margin-bottom:0;font-weight:700;font-size:1rem;color:#212529}
        .card-header-detail small{color:#6c757d}
        .card-body-detail{min-height:120px;display:flex;align-items:center;justify-content:center}
        .detail-placeholder{text-align:center;color:#6c757d}
        .detail-placeholder i{font-size:2rem;color:#dee2e6;margin-bottom:.5rem}
        .barang-row{padding:.75rem;background:#f8f9fa;border-radius:.5rem;border:1px solid #e9ecef}
        @media (max-width: 768px){
            .page-head{flex-direction:column;align-items:stretch}
            .action-group .btn{min-width:100%;width:100%}
            .form-header-intro{flex-direction:column}
        }
    </style>

    <div class="page-head">
        <div>
            <h1 class="h3 mb-0">Buat Permintaan Barang Medis</h1>
            <div class="subtitle">Tambahkan barang yang diminta sesuai kebutuhan klinik.</div>
        </div>
        <div class="action-group">
            <a href="{{ route('permintaan.index') }}" class="btn btn-soft-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

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
                <div class="form-header-intro">
                    <div class="form-group" style="flex:0 0 35%">
                        <label for="tanggal_permintaan" class="form-label">Tanggal Permintaan</label>
                        <input type="date" class="form-control" id="tanggal_permintaan" name="tanggal_permintaan" value="{{ old('tanggal_permintaan', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group" style="flex:1">
                         <label for="catatan" class="form-label">Catatan (Opsional)</label>
                         <textarea class="form-control" id="catatan" name="catatan" rows="1" placeholder="Tambahkan catatan jika perlu...">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                <div class="form-section-title">
                    <i class="bi bi-list-check me-2" style="color:#0d6efd"></i>Detail Barang Medis
                </div>

                {{-- 1. Barang Medis yang Sudah Terdaftar --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light card-header-detail">
                        <div>
                            <h6>Barang Medis Terdaftar</h6>
                            <small>Pilih barang Medis yang sudah tersedia di master data</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" id="add-barang-btn" style="box-shadow:0 .125rem .5rem rgba(13,110,253,.2)">
                            <i class="bi bi-plus-circle me-1"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body card-body-detail" id="barang-terdaftar-wrapper">
                        {{-- Baris akan ditambahkan oleh JavaScript --}}
                        <div class="detail-placeholder" id="barang-terdaftar-placeholder">
                            <i class="bi bi-inbox"></i>
                            <p class="mb-0">Belum ada barang Medis ditambahkan</p>
                        </div>
                    </div>
                </div>

                {{-- 2. Request Barang medis Baru --}}
                <div class="card shadow-sm mb-4">
                     <div class="card-header bg-light card-header-detail">
                        <div>
                            <h6>Request Barang Medis Baru</h6>
                            <small>Tambahkan barang Medis yang belum ada di master data</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="add-barang-baru-btn" style="box-shadow:0 .125rem .5rem rgba(25,135,84,.2)">
                            <i class="bi bi-plus-circle me-1"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body card-body-detail" id="barang-baru-wrapper">
                        {{-- Baris akan ditambahkan oleh JavaScript --}}
                        <div class="detail-placeholder" id="barang-baru-placeholder">
                            <i class="bi bi-inbox"></i>
                            <p class="mb-0">Belum ada request barang Medis ditambahkan</p>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi Form --}}
                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-success" style="min-width:180px;padding:.6rem 1.2rem;font-weight:600;box-shadow:0 .25rem .75rem rgba(25,135,84,.15)">
                        <i class="bi bi-check-circle me-1"></i>Simpan Permintaan
                    </button>
                    <a href="{{ route('permintaan.index') }}" class="btn btn-soft-secondary" style="min-width:140px;padding:.6rem 1.2rem">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
        newRow.classList.add('row', 'g-2', 'align-items-end', 'mb-3', 'barang-row');
        newRow.innerHTML = `
            <div class="col-md-6">
                <label class="form-label small fw-600">Nama Barang</label>
                <select class="form-select barang-select select2-dropdown" name="barang[${barangIndex}][id]" required>
                    ${selectOptions}
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-600">Jumlah Kemasan</label>
                <div class="input-group">
                    <input type="number" class="form-control" name="barang[${barangIndex}][jumlah]" placeholder="Jumlah" min="1" required>
                    <span class="input-group-text">Box</span>
                </div>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn w-100"><i class="bi bi-trash"></i></button>
            </div>
        `;
        barangWrapper.appendChild(newRow);
        
        // Initialize Select2 on the new dropdown
        const newSelect = newRow.querySelector('.select2-dropdown');
        $(newSelect).select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Barang --',
            allowClear: true,
            width: '100%'
        });
        
        barangIndex++;
    });

    document.getElementById('add-barang-baru-btn').addEventListener('click', function() {
        if (barangBaruPlaceholder) barangBaruPlaceholder.style.display = 'none';

        const newRow = document.createElement('div');
        newRow.classList.add('row', 'g-2', 'align-items-end', 'mb-3', 'barang-row');
        newRow.innerHTML = `
            <div class="col-md-6">
                <label class="form-label small fw-600">Nama Barang Baru</label>
                <input type="text" class="form-control" name="barang_baru[${barangBaruIndex}][nama]" placeholder="Masukkan nama barang" required>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-600">Jumlah Kemasan</label>
                <div class="input-group">
                    <input type="number" class="form-control" name="barang_baru[${barangBaruIndex}][jumlah]" placeholder="Jumlah" min="1" required>
                    <span class="input-group-text">Box</span>
                </div>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn w-100"><i class="bi bi-trash"></i></button>
            </div>
        `;
        barangBaruWrapper.appendChild(newRow);
        barangBaruIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-row-btn')) {
            const row = e.target.closest('.barang-row');
            
            // Destroy Select2 instance before removing the row
            const select2Element = row.querySelector('.select2-dropdown');
            if (select2Element && $(select2Element).hasClass('select2-hidden-accessible')) {
                $(select2Element).select2('destroy');
            }
            
            row.remove();

            if (barangWrapper.querySelectorAll('.barang-row').length === 0 && barangPlaceholder) {
                barangPlaceholder.style.display = 'block';
            }
            if (barangBaruWrapper.querySelectorAll('.barang-row').length === 0 && barangBaruPlaceholder) {
                barangBaruPlaceholder.style.display = 'block';
            }
        }
    });


});
</script>
@endpush
