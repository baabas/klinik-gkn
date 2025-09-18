@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Input Barang/Obat Masuk</h1>

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

            <form action="{{ route('barang-masuk.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label for="id_barang" class="form-label">Nama Barang</label>
                    <select name="id_barang" id="id_barang" class="form-select" required>
                        <option value="" disabled {{ old('id_barang') ? '' : 'selected' }}>Pilih Barang</option>
                        @foreach ($barang as $item)
                            <option value="{{ $item->id_obat }}" {{ old('id_barang') == $item->id_obat ? 'selected' : '' }}>
                                {{ $item->nama_obat }} ({{ $item->kode_obat }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="id_lokasi" class="form-label">Lokasi Penyimpanan</label>
                    <select name="id_lokasi" id="id_lokasi" class="form-select" required>
                        <option value="" disabled {{ old('id_lokasi') ? '' : 'selected' }}>Pilih Lokasi</option>
                        @foreach ($lokasi as $loc)
                            <option value="{{ $loc->id }}" {{ old('id_lokasi') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control"
                           value="{{ old('tanggal_masuk', now()->toDateString()) }}" required>
                </div>

                <div class="col-md-4">
                    <label for="jumlah_kemasan" class="form-label">Jumlah Kemasan</label>
                    <input type="number" name="jumlah_kemasan" id="jumlah_kemasan" class="form-control"
                           value="{{ old('jumlah_kemasan', 1) }}" min="1" required>
                </div>

                <div class="col-md-4">
                    <label for="satuan_kemasan" class="form-label">Jenis Kemasan</label>
                    <input type="text" name="satuan_kemasan" id="satuan_kemasan" class="form-control"
                           value="{{ old('satuan_kemasan') }}" placeholder="Contoh: Box, Botol">
                </div>

                <div class="col-md-4">
                    <label for="isi_per_kemasan" class="form-label">Isi per Kemasan</label>
                    <input type="number" name="isi_per_kemasan" id="isi_per_kemasan" class="form-control"
                           value="{{ old('isi_per_kemasan', 1) }}" min="1" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Satuan Dasar</label>
                    <input type="text" class="form-control" id="satuan_dasar" value="-" readonly>
                    <div class="form-text">Satuan akan mengikuti data master barang.</div>
                </div>

                <div class="col-md-4">
                    <label for="expired_at" class="form-label">Tanggal Kedaluwarsa</label>
                    <input type="date" name="expired_at" id="expired_at" class="form-control"
                           value="{{ old('expired_at') }}">
                </div>

                <div class="col-12">
                    <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="2"
                              placeholder="Contoh: Batch 09/2024">{{ old('keterangan') }}</textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const barangSelect = document.getElementById('id_barang');
        const satuanDasarInput = document.getElementById('satuan_dasar');
        const barangOptions = @json($barang->map(fn($item) => [
            'id' => $item->id_obat,
            'satuan' => $item->satuan,
        ]));

        function updateSatuanDasar() {
            const selectedId = barangSelect.value;
            const data = barangOptions.find(item => item.id.toString() === selectedId);
            satuanDasarInput.value = data ? data.satuan : '-';
        }

        barangSelect.addEventListener('change', updateSatuanDasar);
        updateSatuanDasar();
    });
</script>
@endpush
