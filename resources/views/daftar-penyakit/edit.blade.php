@extends('layouts.sidebar-layout')

@section('title', 'Edit Penyakit')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Edit Penyakit</h1>
                    <p class="mb-0 text-muted">Perbarui data penyakit: {{ $penyakit->ICD10 }}</p>
                </div>
                <div>
                    <a href="{{ route('daftar-penyakit.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
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
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-edit me-2"></i>Form Edit Penyakit
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            <form action="{{ route('daftar-penyakit.update', $penyakit->ICD10) }}" method="POST" id="editForm">
                                @csrf
                                @method('PUT')
                                
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
                                                   value="{{ old('ICD10', $penyakit->ICD10) }}" 
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
                                                   value="{{ old('nama_penyakit', $penyakit->nama_penyakit) }}" 
                                                   placeholder="Masukkan nama penyakit"
                                                   maxlength="255"
                                                   required>
                                            @error('nama_penyakit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <strong>Perhatian:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Mengubah kode ICD10 dapat mempengaruhi data diagnosa yang sudah ada</li>
                                                <li>Pastikan perubahan sudah benar sebelum menyimpan</li>
                                                <li>Data yang sudah diperbarui tidak dapat dikembalikan secara otomatis</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="small text-muted">
                                            <strong>Dibuat:</strong> 
                                            {{ $penyakit->created_at ? $penyakit->created_at->format('d/m/Y H:i:s') : '-' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-muted">
                                            <strong>Terakhir diperbarui:</strong> 
                                            {{ $penyakit->updated_at ? $penyakit->updated_at->format('d/m/Y H:i:s') : '-' }}
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-light me-2" onclick="resetForm()">
                                        <i class="fas fa-undo me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-2"></i>Perbarui Data
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
// Store original values for reset
const originalValues = {
    ICD10: '{{ $penyakit->ICD10 }}',
    nama_penyakit: '{{ addslashes($penyakit->nama_penyakit) }}'
};

// Auto uppercase ICD10 input
document.getElementById('ICD10').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Reset form function
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mengembalikan ke data asli?')) {
        document.getElementById('ICD10').value = originalValues.ICD10;
        document.getElementById('nama_penyakit').value = originalValues.nama_penyakit;
        
        // Remove validation classes
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
    }
}

// Form validation
document.getElementById('editForm').addEventListener('submit', function(e) {
    const icd10 = document.getElementById('ICD10').value.trim();
    const namaPenyakit = document.getElementById('nama_penyakit').value.trim();
    
    if (icd10 === '' || namaPenyakit === '') {
        e.preventDefault();
        alert('Semua field yang wajib diisi harus dilengkapi!');
        return false;
    }
    
    // Check if anything changed
    if (icd10 === originalValues.ICD10 && namaPenyakit === originalValues.nama_penyakit) {
        e.preventDefault();
        alert('Tidak ada perubahan data yang perlu disimpan.');
        return false;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memperbarui...';
    submitBtn.disabled = true;
    
    // Restore button after a delay if form submission fails
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});
</script>
@endpush