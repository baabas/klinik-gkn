@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Permintaan Barang Baru</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('permintaan-barang-baru.store') }}" method="POST">
                @csrf
                
                <!-- Informasi Barang -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipe</label>
                            <select name="tipe" class="form-control" required>
                                <option value="OBAT">Obat</option>
                                <option value="ALKES">Alat Kesehatan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Informasi Satuan -->
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kemasan</label>
                            <input type="text" name="kemasan" class="form-control" required 
                                   placeholder="Contoh: Box, Botol">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control" required 
                                   placeholder="Contoh: Strip, Ampul">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Satuan Terkecil</label>
                            <input type="text" name="satuan_terkecil" class="form-control" required 
                                   placeholder="Contoh: Tablet, Kapsul">
                        </div>
                    </div>
                </div>

                <!-- Informasi Konversi -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jumlah Satuan per Kemasan</label>
                            <div class="input-group">
                                <input type="number" name="jumlah_satuan_perkemasan" class="form-control" 
                                       required min="1" id="satuanPerKemasan">
                                <div class="input-group-append">
                                    <span class="input-group-text satuan-label">satuan</span>
                                </div>
                            </div>
                            <small class="text-muted">Contoh: 10 strip per box</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jumlah Unit per Satuan</label>
                            <div class="input-group">
                                <input type="number" name="jumlah_unit_persatuan" class="form-control" 
                                       required min="1" id="unitPerSatuan">
                                <div class="input-group-append">
                                    <span class="input-group-text unit-label">unit</span>
                                </div>
                            </div>
                            <small class="text-muted">Contoh: 10 tablet per strip</small>
                        </div>
                    </div>
                </div>

                <!-- Preview Konversi -->
                <div class="alert alert-info mt-3" id="previewKonversi">
                    1 <span class="kemasan-label">kemasan</span> = 
                    <span id="totalSatuan">0</span> <span class="satuan-label">satuan</span> = 
                    <span id="totalUnit">0</span> <span class="unit-label">unit</span>
                </div>

                <!-- Spesifikasi -->
                <div class="form-group mt-3">
                    <label>Spesifikasi Barang</label>
                    <textarea name="spesifikasi" class="form-control" rows="3" required
                              placeholder="Jelaskan spesifikasi barang secara detail"></textarea>
                </div>

                <!-- Jumlah yang Diminta -->
                <div class="form-group mt-3">
                    <label>Jumlah Permintaan (dalam kemasan)</label>
                    <div class="input-group">
                        <input type="number" name="jumlah_permintaan" class="form-control" 
                               required min="1" id="jumlahPermintaan">
                        <div class="input-group-append">
                            <span class="input-group-text kemasan-label">kemasan</span>
                        </div>
                    </div>
                    <small class="text-muted">
                        Total unit yang akan diterima: <span id="totalPermintaan">0</span> 
                        <span class="unit-label">unit</span>
                    </small>
                </div>

                <!-- Alasan Permintaan -->
                <div class="form-group mt-3">
                    <label>Alasan Permintaan</label>
                    <textarea name="alasan_permintaan" class="form-control" rows="3" required
                              placeholder="Jelaskan alasan permintaan barang baru ini"></textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const satuanPerKemasan = document.getElementById('satuanPerKemasan');
    const unitPerSatuan = document.getElementById('unitPerSatuan');
    const jumlahPermintaan = document.getElementById('jumlahPermintaan');
    
    // Update label ketika input berubah
    document.querySelector('[name="kemasan"]').addEventListener('input', function(e) {
        document.querySelectorAll('.kemasan-label').forEach(el => el.textContent = e.target.value || 'kemasan');
    });
    
    document.querySelector('[name="satuan"]').addEventListener('input', function(e) {
        document.querySelectorAll('.satuan-label').forEach(el => el.textContent = e.target.value || 'satuan');
    });
    
    document.querySelector('[name="satuan_terkecil"]').addEventListener('input', function(e) {
        document.querySelectorAll('.unit-label').forEach(el => el.textContent = e.target.value || 'unit');
    });
    
    // Hitung konversi
    function hitungKonversi() {
        const satuan = parseInt(satuanPerKemasan.value) || 0;
        const unit = parseInt(unitPerSatuan.value) || 0;
        const jumlah = parseInt(jumlahPermintaan.value) || 0;
        
        document.getElementById('totalSatuan').textContent = satuan;
        document.getElementById('totalUnit').textContent = satuan * unit;
        document.getElementById('totalPermintaan').textContent = satuan * unit * jumlah;
    }
    
    // Event listeners untuk perhitungan
    satuanPerKemasan.addEventListener('input', hitungKonversi);
    unitPerSatuan.addEventListener('input', hitungKonversi);
    jumlahPermintaan.addEventListener('input', hitungKonversi);
});
</script>
@endpush
@endsection