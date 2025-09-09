@extends('layouts.sidebar-layout')

@section('title', 'Input Hasil Check-up - ' . $user->nama_karyawan)

@push('styles')
    <style>
        .card-header {
            background-color: #f8f9fa;
        }
        .form-section-title {
            font-weight: 600;
            color: var(--bs-info);
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Input Hasil Check-up</h1>
    </div>

    <form action="{{ route('checkup.store', $user->nip) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                {{-- Bagian Utama Form --}}

                <div class="mb-4">
                    <label for="tanggal_pemeriksaan" class="form-label fs-5 fw-bold">Tanggal Pemeriksaan <span class="text-danger">*</span></label>
                    <input type="date" id="tanggal_pemeriksaan" name="tanggal_pemeriksaan" class="form-control form-control-lg @error('tanggal_pemeriksaan') is-invalid @enderror" value="{{ old('tanggal_pemeriksaan', date('Y-m-d')) }}" required>
                    @error('tanggal_pemeriksaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
    
                {{-- 1. KARTU PEMERIKSAAN KLINIS --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 form-section-title">Pemeriksaan Klinis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tekanan_darah" class="form-label">Tekanan Darah</label>
                                <div class="input-group">
                                    <input type="text" id="tekanan_darah" name="tekanan_darah" class="form-control" value="{{ old('tekanan_darah') }}" placeholder="Contoh: 120/80">
                                    <span class="input-group-text">mmHg</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gula_darah" class="form-label">Gula Darah Sewaktu</label>
                                <div class="input-group">
                                    <input type="text" id="gula_darah" name="gula_darah" class="form-control" value="{{ old('gula_darah') }}" placeholder="Contoh: 98">
                                    <span class="input-group-text">mg/dL</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kolesterol" class="form-label">Kolesterol Total</label>
                                <div class="input-group">
                                    <input type="text" id="kolesterol" name="kolesterol" class="form-control" value="{{ old('kolesterol') }}" placeholder="Contoh: 150">
                                    <span class="input-group-text">mg/dL</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="asam_urat" class="form-label">Asam Urat</label>
                                <div class="input-group">
                                    <input type="text" id="asam_urat" name="asam_urat" class="form-control" value="{{ old('asam_urat') }}" placeholder="Contoh: 5.7">
                                    <span class="input-group-text">mg/dL</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. KARTU PENGUKURAN ANTROPOMETRI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 form-section-title">Pengukuran Antropometri</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="berat_badan" class="form-label">Berat Badan</label>
                                <div class="input-group">
                                    <input type="text" id="berat_badan" name="berat_badan" class="form-control" value="{{ old('berat_badan') }}" placeholder="Contoh: 70">
                                    <span class="input-group-text">Kg</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tinggi_badan" class="form-label">Tinggi Badan</label>
                                <div class="input-group">
                                    <input type="text" id="tinggi_badan" name="tinggi_badan" class="form-control" value="{{ old('tinggi_badan') }}" placeholder="Contoh: 175">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="indeks_massa_tubuh" class="form-label">Indeks Massa Tubuh (IMT)</label>
                                <div class="input-group">
                                    <input type="text" id="indeks_massa_tubuh" name="indeks_massa_tubuh" class="form-control" value="{{ old('indeks_massa_tubuh') }}" placeholder="Contoh: 22.8">
                                    <span class="input-group-text">kg/m²</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lingkar_perut" class="form-label">Lingkar Perut</label>
                                <div class="input-group">
                                    <input type="text" id="lingkar_perut" name="lingkar_perut" class="form-control" value="{{ old('lingkar_perut') }}" placeholder="Contoh: 80">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                {{-- Bagian Samping (Info Pasien & Opsional) --}}
                
                {{-- KARTU INFO PASIEN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Pasien</h5>
                        <i class="bi bi-person-circle fs-4 text-info"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $user->nama_karyawan }}</h5>
                        <p class="card-text text-muted mb-0">NIP: {{ $user->nip }}</p>
                    </div>
                </div>
                
                {{-- KARTU KETERANGAN TAMBAHAN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Keterangan Tambahan</h5>
                    </div>
                    <div class="card-body">
                        <p class="form-text mt-0 mb-2">Isi jika hasil check-up untuk keluarga.</p>
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

                {{-- TOMBOL AKSI --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-info btn-lg text-white">
                        <i class="bi bi-save"></i> Simpan Hasil Check-up
                    </button>
                    <a href="{{ route('pasien.show', $user->nip) }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>
        </div>
    </form>
@endsection