@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label for="nama_obat" class="form-label">Nama Obat</label>
    <input type="text" name="nama_obat" id="nama_obat" class="form-control" value="{{ old('nama_obat', $obat->nama_obat ?? '') }}" required>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="kode_obat" class="form-label">Kode Obat</label>
        <input type="text" name="kode_obat" id="kode_obat" class="form-control" value="{{ old('kode_obat', $obat->kode_obat ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="stok_saat_ini" class="form-label">Stok Saat Ini</label>
        <input type="number" name="stok_saat_ini" id="stok_saat_ini" class="form-control" value="{{ old('stok_saat_ini', $obat->stok_saat_ini ?? '0') }}" required>
    </div>
</div>

 <div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="satuan_dasar" class="form-label">Satuan Dasar (Contoh: tablet, kapsul)</label>
        <input type="text" name="satuan_dasar" id="satuan_dasar" class="form-control" value="{{ old('satuan_dasar', $obat->satuan_dasar ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="kemasan" class="form-label">Kemasan (Contoh: Strip, Box)</label>
        <input type="text" name="kemasan" id="kemasan" class="form-control" value="{{ old('kemasan', $obat->kemasan ?? '') }}">
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <a href="{{ route('obat.index') }}" class="btn btn-secondary me-2">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> Simpan
    </button>
</div>
