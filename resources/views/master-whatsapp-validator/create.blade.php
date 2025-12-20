@extends('layouts.sidebar-layout')

@section('title', 'Tambah WhatsApp Validator')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Tambah WhatsApp Validator
                </h4>
                <a href="{{ route('master-whatsapp-validator.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('master-whatsapp-validator.store') }}" method="POST" id="formValidator">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nama_validator" class="form-label">Nama Validator <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nama_validator') is-invalid @enderror" 
                                   id="nama_validator" 
                                   name="nama_validator" 
                                   value="{{ old('nama_validator') }}"
                                   placeholder="Contoh: Admin Pengadaan 1"
                                   required>
                            @error('nama_validator')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nomor_wa" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                                <input type="text" 
                                       class="form-control @error('nomor_wa') is-invalid @enderror" 
                                       id="nomor_wa" 
                                       name="nomor_wa" 
                                       value="{{ old('nomor_wa') }}"
                                       placeholder="Contoh: 081234567890"
                                       required>
                                @error('nomor_wa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Format: 08xxxxxxxxxx (minimal 10 digit)</div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3"
                                      placeholder="Contoh: Validator utama untuk distribusi obat">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('master-whatsapp-validator.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
