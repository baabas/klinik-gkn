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
                    <label for="tanggal_transaksi" class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" class="form-control"
                           value="{{ old('tanggal_transaksi', now()->toDateString()) }}" required>
                </div>

                <div class="col-md-4">
                    <label for="jumlah_kemasan" class="form-label">Jumlah Kemasan</label>
                    <input type="number" name="jumlah_kemasan" id="jumlah_kemasan" class="form-control"
                           value="{{ old('jumlah_kemasan', 1) }}" min="1" required>
                </div>

                <div class="col-md-4">
                    <label for="kemasan_id" class="form-label">Jenis Kemasan</label>
                    <select name="kemasan_id" id="kemasan_id" class="form-select" required>
                        <option value="" disabled selected>Pilih jenis kemasan</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="isi_per_kemasan" class="form-label">Isi per Kemasan</label>
                    <input type="text" id="isi_per_kemasan" class="form-control" value="-" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Satuan Dasar</label>
                    <input type="text" class="form-control" id="satuan_dasar" value="-" readonly>
                    <div class="form-text">Semua stok disimpan dalam satuan dasar barang.</div>
                </div>

                <div class="col-12">
                    <div class="alert alert-info" id="konversi-preview" role="alert">
                        Pilih barang dan jenis kemasan untuk melihat konversi stok.
                    </div>
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

                <div class="col-12 d-flex flex-column flex-sm-row justify-content-end gap-2 mt-3">
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary w-100 w-sm-auto">Batal</a>
                    <button type="submit" class="btn btn-primary w-100 w-sm-auto">
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
        const kemasanSelect = document.getElementById('kemasan_id');
        const satuanDasarInput = document.getElementById('satuan_dasar');
        const isiPerKemasanInput = document.getElementById('isi_per_kemasan');
        const jumlahKemasanInput = document.getElementById('jumlah_kemasan');
        const previewElement = document.getElementById('konversi-preview');
        const barangOptions = @json($barang->map(fn($item) => [
            'id' => $item->id_obat,
            'satuan_dasar' => $item->satuan_dasar,
            'kemasan' => $item->kemasanBarang->map(fn($kemasan) => [
                'id' => $kemasan->id,
                'nama' => $kemasan->nama_kemasan,
                'isi' => $kemasan->isi_per_kemasan,
                'is_default' => (bool) $kemasan->is_default,
            ]),
        ]));

        const oldBarangId = @json(old('id_barang'));
        let initialKemasanId = @json(old('kemasan_id'));

        function findBarangData(id) {
            return barangOptions.find(item => item.id.toString() === id.toString());
        }

        function findKemasanData(barangData, kemasanId) {
            if (!barangData) return undefined;
            return barangData.kemasan.find(item => item.id.toString() === kemasanId.toString());
        }

        function renderKemasanOptions() {
            const selectedBarangId = barangSelect.value;
            if (!selectedBarangId) {
                kemasanSelect.innerHTML = '<option value="" disabled selected>Pilih jenis kemasan</option>';
                kemasanSelect.disabled = true;
                isiPerKemasanInput.value = '-';
                previewElement.textContent = 'Pilih barang dan jenis kemasan untuk melihat konversi stok.';
                satuanDasarInput.value = '-';
                return;
            }

            const barangData = findBarangData(selectedBarangId);

            kemasanSelect.innerHTML = '<option value="" disabled selected>Pilih jenis kemasan</option>';

            if (!barangData || !barangData.kemasan.length) {
                kemasanSelect.disabled = true;
                isiPerKemasanInput.value = '-';
                previewElement.textContent = 'Barang ini belum memiliki master kemasan. Tambahkan kemasan terlebih dahulu.';
                return;
            }

            kemasanSelect.disabled = false;

            barangData.kemasan.forEach(kemasan => {
                const option = document.createElement('option');
                option.value = kemasan.id;
                option.textContent = `${kemasan.nama} @ ${kemasan.isi} ${barangData.satuan_dasar}`;
                kemasanSelect.appendChild(option);
            });

            const defaultKemasan = initialKemasanId && findKemasanData(barangData, initialKemasanId)
                ? initialKemasanId
                : (barangData.kemasan.find(k => k.is_default)?.id ?? barangData.kemasan[0].id);

            kemasanSelect.value = defaultKemasan;
            initialKemasanId = null;
            updateKemasanRelated();
        }

        function updatePreview(totalUnit, jumlahKemasan, kemasanData, barangData) {
            if (!barangData) {
                previewElement.textContent = 'Pilih barang dan jenis kemasan untuk melihat konversi stok.';
                return;
            }

            if (!kemasanData) {
                previewElement.textContent = 'Barang ini belum memiliki master kemasan. Tambahkan kemasan terlebih dahulu.';
                return;
            }

            if (!jumlahKemasan || jumlahKemasan <= 0) {
                previewElement.textContent = `0 ${barangData.satuan_dasar} (isi per kemasan ${kemasanData.isi}).`;
                return;
            }

            previewElement.innerHTML = `<strong>${jumlahKemasan}</strong> ${kemasanData.nama} × <strong>${kemasanData.isi}</strong> ${barangData.satuan_dasar} = <strong>${totalUnit}</strong> ${barangData.satuan_dasar}`;
        }

        function updateKemasanRelated() {
            const selectedBarangId = barangSelect.value;
            const barangData = findBarangData(selectedBarangId);
            satuanDasarInput.value = barangData ? barangData.satuan_dasar : '-';

            const selectedKemasanId = kemasanSelect.value;
            const kemasanData = findKemasanData(barangData, selectedKemasanId);

            isiPerKemasanInput.value = kemasanData ? kemasanData.isi : '-';

            const jumlahKemasan = parseInt(jumlahKemasanInput.value, 10) || 0;
            const totalUnit = kemasanData ? jumlahKemasan * kemasanData.isi : 0;

            updatePreview(totalUnit, jumlahKemasan, kemasanData, barangData);
        }

        barangSelect.addEventListener('change', renderKemasanOptions);

        kemasanSelect.addEventListener('change', updateKemasanRelated);
        jumlahKemasanInput.addEventListener('input', updateKemasanRelated);

        if (oldBarangId) {
            barangSelect.value = oldBarangId;
        }

        renderKemasanOptions();
        updateKemasanRelated();
    });
</script>
@endpush
