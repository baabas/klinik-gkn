@extends('layouts.sidebar-layout')

@section('title', 'Tambah Penyakit')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Tambah Penyakit</h1>
                    <p class="mb-0 text-muted">Tambah data penyakit baru ke dalam daftar ICD10</p>
                </div>
                <a href="{{ route('daftar-penyakit.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-plus-circle me-2"></i>Form Tambah Penyakit
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            <form action="{{ route('daftar-penyakit.store') }}" method="POST" id="createForm">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="ICD10" class="form-label">
                                                Kode ICD10 <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('ICD10') is-invalid @enderror" 
                                                   id="ICD10" 
                                                   name="ICD10" 
                                                   value="{{ old('ICD10') }}" 
                                                   placeholder="Contoh: A00.0"
                                                   maxlength="20"
                                                   required>
                                            @error('ICD10')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Kode akan otomatis diubah menjadi huruf besar
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="nama_penyakit" class="form-label">
                                                Nama Penyakit <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('nama_penyakit') is-invalid @enderror" 
                                                   id="nama_penyakit" 
                                                   name="nama_penyakit" 
                                                   value="{{ old('nama_penyakit') }}" 
                                                   placeholder="Masukkan nama penyakit"
                                                   maxlength="255"
                                                   required>
                                            @error('nama_penyakit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-lightbulb"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <strong>Tips Pengisian:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Kode ICD10 harus unik dan sesuai standar WHO</li>
                                                <li>Nama penyakit diisi dengan lengkap dan jelas</li>
                                                <li>Pastikan data sudah benar sebelum menyimpan</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-light me-2" onclick="resetForm()">
                                        <i class="fas fa-undo me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto uppercase ICD10 input
document.getElementById('ICD10').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Reset form function
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mengosongkan form?')) {
        document.getElementById('createForm').reset();
        // Remove validation classes
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
    }
}

// Form validation
document.getElementById('createForm').addEventListener('submit', function(e) {
    const icd10 = document.getElementById('ICD10').value.trim();
    const namaPenyakit = document.getElementById('nama_penyakit').value.trim();
    
    if (icd10 === '' || namaPenyakit === '') {
        e.preventDefault();
        alert('Semua field yang wajib diisi harus dilengkapi!');
        return false;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
    
    // Restore button after a delay if form submission fails
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});
</script>
@endpush