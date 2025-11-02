@extends('layouts.sidebar-layout')

@section('title', 'Tambah Kantor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Tambah Kantor Baru
                    </h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('master-kantor.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_kantor" class="form-label">
                                Nama Kantor <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_kantor') is-invalid @enderror" 
                                   id="nama_kantor" 
                                   name="nama_kantor" 
                                   value="{{ old('nama_kantor') }}" 
                                   required>
                            @error('nama_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kode_kantor" class="form-label">
                                Kode Kantor
                            </label>
                            <input type="text" 
                                   class="form-control @error('kode_kantor') is-invalid @enderror" 
                                   id="kode_kantor" 
                                   name="kode_kantor" 
                                   value="{{ old('kode_kantor') }}">
                            @error('kode_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Opsional. Contoh: KPP-MDS</small>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Status Aktif
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('master-kantor.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
