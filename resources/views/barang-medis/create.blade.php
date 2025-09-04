@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Tambah Barang Medis Baru</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('barang-medis.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode_obat" class="form-label">Kode Barang</label>
                        <input type="text" name="kode_obat" class="form-control" id="kode_obat" placeholder="Contoh: OBT-0001 / ALK-0001" value="{{ old('kode_obat') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_obat" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_obat" class="form-control" id="nama_obat" placeholder="Contoh: Paracetamol 500mg" value="{{ old('nama_obat') }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tipe" class="form-label">Tipe</label>
                        <select name="tipe" id="tipe" class="form-select" required>
                            <option value="OBAT" {{ old('tipe') == 'OBAT' ? 'selected' : '' }}>OBAT</option>
                            <option value="ALKES" {{ old('tipe') == 'ALKES' ? 'selected' : '' }}>ALKES</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control" id="satuan" placeholder="Contoh: Tablet, Box, Pcs" value="{{ old('satuan') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kemasan" class="form-label">Kemasan (Opsional)</label>
                        <input type="text" name="kemasan" class="form-control" id="kemasan" placeholder="Contoh: Strip isi 10" value="{{ old('kemasan') }}">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                    <a href="{{ route('barang-medis.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
