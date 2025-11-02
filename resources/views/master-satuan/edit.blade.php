@extends('layouts.sidebar-layout')

@section('title', 'Edit Satuan')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Satuan Terkecil</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('master-satuan.index') }}">Master Satuan</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-pencil"></i>
            Form Edit Satuan Terkecil
        </div>
        <div class="card-body">
            <form action="{{ route('master-satuan.update', $item->id_satuan) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_satuan" class="form-label">
                        Nama Satuan <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('nama_satuan') is-invalid @enderror" 
                           id="nama_satuan" 
                           name="nama_satuan" 
                           value="{{ old('nama_satuan', $item->nama_satuan) }}"
                           required>
                    @error('nama_satuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="singkatan" class="form-label">
                        Singkatan
                    </label>
                    <input type="text" 
                           class="form-control @error('singkatan') is-invalid @enderror" 
                           id="singkatan" 
                           name="singkatan" 
                           value="{{ old('singkatan', $item->singkatan) }}"
                           maxlength="20">
                    <small class="text-muted">Optional - Singkatan untuk kemudahan input</small>
                    @error('singkatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Status Aktif
                        </label>
                    </div>
                    <small class="text-muted">Satuan yang aktif akan muncul di dropdown</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('master-satuan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
