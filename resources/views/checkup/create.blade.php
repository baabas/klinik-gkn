@extends('layouts.sidebar-layout')

@section('title', 'Tambah Data Check-up - ' . $user->nama_karyawan)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Medical Check-up Baru</h1>
</div>

{{-- [PERBAIKAN] Menentukan action route secara dinamis --}}
@php
    $actionRoute = $user->nip 
        ? route('checkup.store', $user->nip) 
        : route('checkup.store.non_karyawan', $user->nik);
@endphp

<form action="{{ $actionRoute }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            {{-- Form Kiri --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Hasil Pemeriksaan</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_pemeriksaan" class="form-label fw-bold">Tanggal Pemeriksaan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_pemeriksaan') is-invalid @enderror" id="tanggal_pemeriksaan" name="tanggal_pemeriksaan" value="{{ old('tanggal_pemeriksaan', now()->format('Y-m-d')) }}" required>
                            @error('tanggal_pemeriksaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tekanan_darah" class="form-label">Tekanan Darah (mmHg)</label>
                            <input type="text" class="form-control" id="tekanan_darah" name="tekanan_darah" value="{{ old('tekanan_darah') }}" placeholder="Contoh: 120/80">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="gula_darah" class="form-label">Gula Darah (mg/dL)</label>
                            <input type="text" class="form-control" id="gula_darah" name="gula_darah" value="{{ old('gula_darah') }}" placeholder="Contoh: 98">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="kolesterol" class="form-label">Kolesterol (mg/dL)</label>
                            <input type="text" class="form-control" id="kolesterol" name="kolesterol" value="{{ old('kolesterol') }}" placeholder="Contoh: 150">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="asam_urat" class="form-label">Asam Urat (mg/dL)</label>
                            <input type="text" class="form-control" id="asam_urat" name="asam_urat" value="{{ old('asam_urat') }}" placeholder="Contoh: 5.5">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Hasil Pengukuran Fisik</h5></div>
                 <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="berat_badan" class="form-label">Berat (Kg)</label>
                            <input type="text" class="form-control" id="berat_badan" name="berat_badan" value="{{ old('berat_badan') }}" placeholder="Contoh: 70">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tinggi_badan" class="form-label">Tinggi (cm)</label>
                            <input type="text" class="form-control" id="tinggi_badan" name="tinggi_badan" value="{{ old('tinggi_badan') }}" placeholder="Contoh: 170">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="indeks_massa_tubuh" class="form-label">IMT (kg/m²)</label>
                            <input type="text" class="form-control" id="indeks_massa_tubuh" name="indeks_massa_tubuh" value="{{ old('indeks_massa_tubuh') }}" placeholder="Otomatis jika BB/TB diisi" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="lingkar_perut" class="form-label">Lingkar Perut (cm)</label>
                            <input type="text" class="form-control" id="lingkar_perut" name="lingkar_perut" value="{{ old('lingkar_perut') }}" placeholder="Contoh: 85">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Form Kanan --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Informasi Pasien</h5><i class="bi bi-person-circle fs-4 text-primary"></i></div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">{{ $user->nama_karyawan }}</h5>
                    <p class="card-text text-muted mb-0">
                        @if($user->nip)
                            NIP: {{ $user->nip }}
                        @else
                            NIK: {{ $user->nik }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Keterangan Tambahan</h5></div>
                <div class="card-body">
                    <p class="form-text mt-0 mb-2">Isi jika check-up untuk keluarga.</p>
                     <div class="mb-3">
                        <label for="nama_sa" class="form-label">Nama Suami / Istri / Anak</label>
                        <input type="text" id="nama_sa" name="nama_sa" class="form-control" value="{{ old('nama_sa') }}" placeholder="Opsional">
                    </div>
                    <div>
                        <label for="jenis_kelamin_sa" class="form-label">Jenis Kelamin</label>
                        <select id="jenis_kelamin_sa" name="jenis_kelamin_sa" class="form-select">
                            <option value="" selected>Pilih...</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin_sa') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin_sa') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Simpan Data Check-up</button>
                {{-- [PERBAIKAN] Menentukan link Batal secara dinamis --}}
                @php
                    $cancelRoute = $user->nip 
                        ? route('pasien.show', $user->nip) 
                        : route('pasien.show_non_karyawan', $user->nik);
                @endphp
                <a href="{{ $cancelRoute }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    /* Improved form styling */
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-header h5 {
        color: #495057;
        font-weight: 600;
    }
    
    /* Form input improvements */
    .form-control {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Label styling */
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    /* Button improvements */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
    
    /* Card styling improvements */
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .col-md-3, .col-md-4, .col-md-6 {
            margin-bottom: 1rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
    
    /* IMT field styling */
    #indeks_massa_tubuh[readonly] {
        background-color: #f8f9fa;
        opacity: 1;
    }
    
    /* Form validation styling */
    .is-invalid {
        border-color: #dc3545;
    }
    
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto calculate IMT (BMI)
        const beratInput = document.getElementById('berat_badan');
        const tinggiInput = document.getElementById('tinggi_badan');
        const imtInput = document.getElementById('indeks_massa_tubuh');
        
        function calculateIMT() {
            const berat = parseFloat(beratInput.value);
            const tinggi = parseFloat(tinggiInput.value);
            
            if (berat > 0 && tinggi > 0) {
                // Convert cm to meters
                const tinggiM = tinggi / 100;
                const imt = berat / (tinggiM * tinggiM);
                imtInput.value = imt.toFixed(1);
                
                // Add color coding based on IMT ranges
                if (imt < 18.5) {
                    imtInput.className = 'form-control text-info';
                    imtInput.title = 'Underweight';
                } else if (imt < 25) {
                    imtInput.className = 'form-control text-success';
                    imtInput.title = 'Normal weight';
                } else if (imt < 30) {
                    imtInput.className = 'form-control text-warning';
                    imtInput.title = 'Overweight';
                } else {
                    imtInput.className = 'form-control text-danger';
                    imtInput.title = 'Obese';
                }
            } else {
                imtInput.value = '';
                imtInput.className = 'form-control';
                imtInput.title = '';
            }
        }
        
        // Add event listeners
        beratInput.addEventListener('input', calculateIMT);
        tinggiInput.addEventListener('input', calculateIMT);
        
        // Auto calculate on page load if values exist
        calculateIMT();
        
        // Form validation enhancement
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const tanggalPemeriksaan = document.getElementById('tanggal_pemeriksaan');
            
            if (!tanggalPemeriksaan.value) {
                e.preventDefault();
                tanggalPemeriksaan.focus();
                alert('Tanggal pemeriksaan harus diisi!');
                return false;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    });
</script>
@endpush