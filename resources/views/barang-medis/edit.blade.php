@extends('layouts.sidebar-layout')

@section('title', 'Edit Barang Medis')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Edit Barang Medis</h4>
                <a href="{{ route('barang-medis.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('barang-medis.update', $barangMedi->id_obat) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Kolom Kiri - Data Barang -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">Informasi Barang</h5>
                                
                                <div class="mb-3">
                                    <label for="nama_obat" class="form-label">Nama Obat/Alat Medis <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_obat') is-invalid @enderror" 
                                           id="nama_obat" name="nama_obat" 
                                           value="{{ old('nama_obat', $barangMedi->nama_obat) }}" required>
                                    @error('nama_obat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="kode_obat" class="form-label">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_obat" name="kode_obat" 
                                           value="{{ $barangMedi->kode_obat }}" readonly>
                                    <div class="form-text">Kode tidak dapat diubah</div>
                                </div>

                                <div class="mb-3">
                                    <label for="kategori_barang" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select @error('kategori_barang') is-invalid @enderror" 
                                            id="kategori_barang" name="kategori_barang" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Obat" {{ old('kategori_barang', $barangMedi->kategori_barang) == 'Obat' ? 'selected' : '' }}>Obat</option>
                                        <option value="BMHP" {{ old('kategori_barang', $barangMedi->kategori_barang) == 'BMHP' ? 'selected' : '' }}>BMHP</option>
                                        <option value="Alkes" {{ old('kategori_barang', $barangMedi->kategori_barang) == 'Alkes' ? 'selected' : '' }}>Alkes</option>
                                        <option value="APD" {{ old('kategori_barang', $barangMedi->kategori_barang) == 'APD' ? 'selected' : '' }}>APD</option>
                                    </select>
                                    @error('kategori_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan - Koreksi Stok -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-warning">Koreksi Stok</h5>
                                <div class="alert alert-info">
                                    <strong>Info:</strong> Gunakan bagian ini untuk mengoreksi stok jika ada kesalahan input sebelumnya.
                                </div>

                                @foreach($barangMedi->stok as $stok)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>{{ $stok->lokasi->nama_lokasi ?? 'Lokasi Tidak Dikenal' }}</strong>
                                        <small class="text-muted">(Stok saat ini: {{ $stok->jumlah }} {{ $barangMedi->satuan_terkecil }})</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="koreksi_kemasan_{{ $stok->id_lokasi }}" class="form-label">Koreksi Kemasan</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" 
                                                           id="koreksi_kemasan_{{ $stok->id_lokasi }}" 
                                                           name="koreksi[{{ $stok->id_lokasi }}][kemasan]" 
                                                           min="0" step="1" placeholder="0">
                                                    <span class="input-group-text">{{ $barangMedi->kemasan ?? 'Box' }}</span>
                                                </div>
                                                <div class="form-text">Masukkan jumlah kemasan untuk menambah/mengurangi stok</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="koreksi_type_{{ $stok->id_lokasi }}" class="form-label">Jenis Koreksi</label>
                                                <select class="form-select" name="koreksi[{{ $stok->id_lokasi }}][type]">
                                                    <option value="">Tidak ada perubahan</option>
                                                    <option value="tambah">Tambah Stok (+)</option>
                                                    <option value="kurang">Kurangi Stok (-)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <label for="koreksi_expired_{{ $stok->id_lokasi }}" class="form-label">Tanggal Kadaluarsa</label>
                                                <input type="date" class="form-control" 
                                                       id="koreksi_expired_{{ $stok->id_lokasi }}" 
                                                       name="koreksi[{{ $stok->id_lokasi }}][expired_at]">
                                                <div class="form-text">Opsional: untuk stok yang ditambahkan</div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <label for="koreksi_keterangan_{{ $stok->id_lokasi }}" class="form-label">Keterangan Koreksi</label>
                                                <input type="text" class="form-control" 
                                                       id="koreksi_keterangan_{{ $stok->id_lokasi }}" 
                                                       name="koreksi[{{ $stok->id_lokasi }}][keterangan]" 
                                                       placeholder="Alasan koreksi stok">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="history.back()">
                                <i class="fas fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto calculate total satuan when kemasan changed
    const kemasanInputs = document.querySelectorAll('input[name*="[kemasan]"]');
    const isiKemasanJumlah = {{ $barangMedi->isi_kemasan_jumlah ?? 1 }};
    const isiPerSatuan = {{ $barangMedi->isi_per_satuan ?? 1 }};
    
    kemasanInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const lokasi = this.name.match(/\[(\d+)\]/)[1];
            const kemasan = parseInt(this.value) || 0;
            // Hitung total: kemasan × isi kemasan × isi per satuan
            const totalSatuan = kemasan * isiKemasanJumlah * isiPerSatuan;
            
            // Show preview of total satuan
            let preview = this.parentNode.parentNode.querySelector('.total-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'form-text total-preview text-primary';
                this.parentNode.parentNode.appendChild(preview);
            }
            
            if (kemasan > 0) {
                preview.textContent = '= ' + totalSatuan + ' {{ $barangMedi->satuan_terkecil ?? "satuan" }}';
            } else {
                preview.textContent = '';
            }
        });
    });
});
</script>
@endpush