@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Edit Barang Medis</h1>

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

            <form action="{{ route('barang-medis.update', $barang->id_obat) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode_obat" class="form-label">Kode Barang</label>
                        <input type="text" name="kode_obat" class="form-control" id="kode_obat" value="{{ old('kode_obat', $barang->kode_obat) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_obat" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_obat" class="form-control" id="nama_obat" value="{{ old('nama_obat', $barang->nama_obat) }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tipe" class="form-label">Tipe</label>
                        <select name="tipe" id="tipe" class="form-select" required>
                            <option value="OBAT" {{ old('tipe', $barang->tipe) === 'OBAT' ? 'selected' : '' }}>OBAT</option>
                            <option value="ALKES" {{ old('tipe', $barang->tipe) === 'ALKES' ? 'selected' : '' }}>ALKES</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="satuan_dasar" class="form-label">Satuan Dasar</label>
                        <input type="text" name="satuan_dasar" class="form-control" id="satuan_dasar" value="{{ old('satuan_dasar', $barang->satuan_dasar) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kemasan" class="form-label">Kemasan (Opsional)</label>
                        <input type="text" name="kemasan" class="form-control" id="kemasan" value="{{ old('kemasan', $barang->kemasan) }}">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('barang-medis.show', $barang->id_obat) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
