@extends('layouts.sidebar-layout')

@section('title', 'Daftar Pasien Baru')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pendaftaran Pasien Baru</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('pasien.store') }}" method="POST" id="registrationForm">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Tipe Pasien <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_pasien" id="tipe_karyawan" value="karyawan" {{ old('tipe_pasien', 'karyawan') == 'karyawan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_karyawan">
                                Pasien Karyawan (Sudah terdaftar di data kepegawaian)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_pasien" id="tipe_non_karyawan" value="non_karyawan" {{ old('tipe_pasien') == 'non_karyawan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_non_karyawan">
                                Pasien Non-Karyawan / Umum
                            </label>
                        </div>
                    </div>

                    <div id="form-karyawan">
                        <p class="text-muted">Masukkan NIP dan Email yang terdaftar untuk membuat akun pasien.</p>
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip" value="{{ old('nip') }}" inputmode="numeric" minlength="18" maxlength="18" pattern="\d{18}">
                            <div class="form-text">NIP harus terdiri dari 18 digit.</div>
                            @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div id="form-non-karyawan" class="d-none">
                        <p class="text-muted">Lengkapi data di bawah ini untuk mendaftarkan pasien baru.</p>
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}">
                            @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                            @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <a href="{{ route('pasien.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formKaryawan = document.getElementById('form-karyawan');
        const formNonKaryawan = document.getElementById('form-non-karyawan');

        const radioKaryawan = document.getElementById('tipe_karyawan');
        const radioNonKaryawan = document.getElementById('tipe_non_karyawan');

        function toggleForm() {
            if (radioKaryawan.checked) {
                formKaryawan.classList.remove('d-none');
                formNonKaryawan.classList.add('d-none');
            } else {
                formKaryawan.classList.add('d-none');
                formNonKaryawan.classList.remove('d-none');
            }
        }

        // Initial check on page load
        toggleForm();

        // Add event listeners
        radioKaryawan.addEventListener('change', toggleForm);
        radioNonKaryawan.addEventListener('change', toggleForm);
    });
</script>
@endpush
