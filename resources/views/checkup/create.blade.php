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
                            <input type="text" class="form-control" id="berat_badan" name="berat_badan" value="{{ old('berat_badan') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tinggi_badan" class="form-label">Tinggi (cm)</label>
                            <input type="text" class="form-control" id="tinggi_badan" name="tinggi_badan" value="{{ old('tinggi_badan') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="indeks_massa_tubuh" class="form-label">IMT (kg/m²)
                            <input type="text" class="form-control" id="indeks_massa_tubuh" name="indeks_massa_tubuh" value="{{ old('indeks_massa_tubuh') }}" placeholder="Otomatis jika BB/TB diisi">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="lingkar_perut" class="form-label">Lingkar Perut (cm)</label>
                            <input type="text" class="form-control" id="lingkar_perut" name="lingkar_perut" value="{{ old('lingkar_perut') }}">
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