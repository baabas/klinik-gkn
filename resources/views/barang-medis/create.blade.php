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
                
                <!-- Kategori Barang -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kategori_barang" class="form-label">Kategori Barang</label>
                        <select name="kategori_barang" id="kategori_barang" class="form-select" required>
                            <option value="">Pilih Kategori Barang</option>
                            <option value="Obat" {{ old('kategori_barang') == 'Obat' ? 'selected' : '' }}>Obat</option>
                            <option value="BMHP" {{ old('kategori_barang') == 'BMHP' ? 'selected' : '' }}>BMHP (Bahan Medis Habis Pakai)</option>
                            <option value="Alkes" {{ old('kategori_barang') == 'Alkes' ? 'selected' : '' }}>Alkes (Alat Kesehatan)</option>
                            <option value="APD" {{ old('kategori_barang') == 'APD' ? 'selected' : '' }}>APD (Alat Pelindung Diri)</option>
                        </select>
                    </div>
                </div>

                <!-- Kode dan Nama Barang -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode_obat" class="form-label">Kode Barang</label>
                        <input type="text" name="kode_obat" class="form-control" id="kode_obat" placeholder="Kode akan dibuat otomatis" value="{{ old('kode_obat') }}" readonly>
                        <small class="text-muted">Kode akan dibuat otomatis berdasarkan kategori barang</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_obat" class="form-label">Nama</label>
                        <input type="text" name="nama_obat" class="form-control" id="nama_obat" placeholder="Contoh: Paracetamol 500mg" value="{{ old('nama_obat') }}" required>
                    </div>
                </div>

                <!-- Kemasan dan Isi Kemasan -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="kemasan" class="form-label">Kemasan</label>
                        <input type="text" name="kemasan" class="form-control" id="kemasan" value="Box" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="isi_kemasan" class="form-label">Isi Kemasan</label>
                        <div class="input-group">
                            <input type="number" name="isi_kemasan_jumlah" class="form-control" id="isi_kemasan_jumlah" placeholder="10" value="{{ old('isi_kemasan_jumlah') }}" required>
                            <select name="isi_kemasan_satuan" id="isi_kemasan_satuan" class="form-select" required>
                                <option value="">Pilih</option>
                                <option value="strip" {{ old('isi_kemasan_satuan') == 'strip' ? 'selected' : '' }}>strip</option>
                                <option value="kotak" {{ old('isi_kemasan_satuan') == 'kotak' ? 'selected' : '' }}>kotak</option>
                                <option value="botol" {{ old('isi_kemasan_satuan') == 'botol' ? 'selected' : '' }}>botol</option>
                                <option value="vial" {{ old('isi_kemasan_satuan') == 'vial' ? 'selected' : '' }}>vial</option>
                                <option value="tube" {{ old('isi_kemasan_satuan') == 'tube' ? 'selected' : '' }}>tube</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="isi_per_satuan" class="form-label">Isi per <span id="satuan_label">strip</span></label>
                        <input type="number" name="isi_per_satuan" class="form-control" id="isi_per_satuan" placeholder="25" value="{{ old('isi_per_satuan') }}" required>
                    </div>
                </div>

                <!-- Satuan Terkecil -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="satuan_terkecil" class="form-label">Satuan Terkecil</label>
                        <select name="satuan_terkecil" id="satuan_terkecil" class="form-select" required>
                            <option value="">Pilih Satuan Terkecil</option>
                            <option value="Tablet" {{ old('satuan_terkecil') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Botol" {{ old('satuan_terkecil') == 'Botol' ? 'selected' : '' }}>Botol</option>
                            <option value="Pcs" {{ old('satuan_terkecil') == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                            <option value="Vial" {{ old('satuan_terkecil') == 'Vial' ? 'selected' : '' }}>Vial</option>
                            <option value="Tube" {{ old('satuan_terkecil') == 'Tube' ? 'selected' : '' }}>Tube</option>
                            <option value="Troches" {{ old('satuan_terkecil') == 'Troches' ? 'selected' : '' }}>Troches</option>
                            <option value="Kapsul" {{ old('satuan_terkecil') == 'Kapsul' ? 'selected' : '' }}>Kapsul</option>
                            <option value="Sirup" {{ old('satuan_terkecil') == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                    <a href="{{ route('barang-medis.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Update label "Isi per" ketika satuan kemasan berubah
        document.getElementById('isi_kemasan_satuan').addEventListener('change', function() {
            const selectedSatuan = this.value;
            const satuanLabel = document.getElementById('satuan_label');
            
            // Hanya update label jika ada pilihan yang dipilih (bukan default "Pilih")
            if (selectedSatuan && selectedSatuan !== '') {
                satuanLabel.textContent = selectedSatuan;
            } else {
                // Kembali ke default jika tidak ada yang dipilih
                satuanLabel.textContent = 'strip';
            }
        });

        // Generate preview kode barang berdasarkan kategori
        document.getElementById('kategori_barang').addEventListener('change', function() {
            const kategori = this.value;
            const kodeInput = document.getElementById('kode_obat');
            
            let prefix = '';
            switch(kategori) {
                case 'Obat':
                    prefix = 'OBT';
                    break;
                case 'BMHP':
                    prefix = 'BMHP';
                    break;
                case 'Alkes':
                    prefix = 'ALK';
                    break;
                case 'APD':
                    prefix = 'APD';
                    break;
                default:
                    prefix = '';
            }
            
            if (prefix) {
                kodeInput.placeholder = `${prefix}-XXXX (akan dibuat otomatis)`;
            } else {
                kodeInput.placeholder = 'Kode akan dibuat otomatis';
            }
        });
    </script>
@endsection
