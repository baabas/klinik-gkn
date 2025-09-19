@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Manajemen Obat & Alat Medis</h4>
            <div class="input-group w-50">
                <input type="text" class="form-control" id="searchInput" placeholder="Cari nama atau kode...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-primary" data-toggle="modal" data-target="#tambahBarangModal">
                    <i class="fas fa-plus"></i> Tambah Barang
                </button>
                <button class="btn btn-success" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Tipe</th>
                            <th>Stok GKN 1</th>
                            <th>Stok GKN 2</th>
                            <th>Total Stok</th>
                            <th>Min. Stok</th>
                            <th>Status Stok</th>
                            <th>Status Exp</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangList as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->kode_obat }}</td>
                            <td>{{ $item->nama_obat }}</td>
                            <td>
                                <span class="badge badge-{{ $item->tipe === 'OBAT' ? 'primary' : 'success' }}">
                                    {{ $item->tipe }}
                                </span>
                            </td>
                            <td class="text-right">
                                <span class="font-weight-bold {{ $item->stok_gkn1 < $item->min_stok ? 'text-danger' : '' }}">
                                    {{ number_format($item->stok_gkn1) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <span class="font-weight-bold {{ $item->stok_gkn2 < $item->min_stok ? 'text-danger' : '' }}">
                                    {{ number_format($item->stok_gkn2) }}
                                </span>
                            </td>
                            <td class="text-right">
                                {{ number_format($item->stok_gkn1 + $item->stok_gkn2) }}
                            </td>
                            <td class="text-right">{{ number_format($item->min_stok) }}</td>
                            <td>
                                @php
                                    $totalStok = $item->stok_gkn1 + $item->stok_gkn2;
                                    $status = $totalStok === 0 ? 'danger' : 
                                             ($totalStok <= $item->min_stok ? 'warning' : 'success');
                                    $statusText = $totalStok === 0 ? 'Habis' :
                                                ($totalStok <= $item->min_stok ? 'Minimal' : 'Cukup');
                                @endphp
                                <span class="badge badge-{{ $status }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                @if($item->status_exp === 'WARNING')
                                    <span class="badge badge-warning">Mendekati Kadaluarsa</span>
                                @elseif($item->status_exp === 'EXPIRED')
                                    <span class="badge badge-danger">Ada yang Kadaluarsa</span>
                                @else
                                    <span class="badge badge-success">OK</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm" 
                                            data-toggle="modal" 
                                            data-target="#detailModal{{ $item->id_obat }}">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm"
                                            onclick="editBarang({{ $item->id_obat }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm"
                                            onclick="tambahStok({{ $item->id_obat }})">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail untuk Pengadaan -->
@foreach($barangList as $item)
<div class="modal fade" id="detailModal{{ $item->id_obat }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail {{ $item->nama_obat }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Informasi Umum -->
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Barang:</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Kode</td>
                                <td>: {{ $item->kode_obat }}</td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td>: {{ $item->nama_obat }}</td>
                            </tr>
                            <tr>
                                <td>Tipe</td>
                                <td>: {{ $item->tipe }}</td>
                            </tr>
                            <tr>
                                <td>Satuan</td>
                                <td>: {{ $item->satuan }}</td>
                            </tr>
                            <tr>
                                <td>Kemasan</td>
                                <td>: {{ $item->kemasan }}</td>
                            </tr>
                            <tr>
                                <td>Konversi</td>
                                <td>: 1 {{ $item->kemasan }} = 
                                    {{ $item->jumlah_satuan_perkemasan }} {{ $item->satuan }} = 
                                    {{ $item->jumlah_satuan_perkemasan * $item->jumlah_unit_persatuan }} 
                                    {{ $item->satuan_terkecil }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Status Stok:</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Stok GKN 1</td>
                                <td>: {{ number_format($item->stok_gkn1) }} {{ $item->satuan }}</td>
                            </tr>
                            <tr>
                                <td>Stok GKN 2</td>
                                <td>: {{ number_format($item->stok_gkn2) }} {{ $item->satuan }}</td>
                            </tr>
                            <tr>
                                <td>Total Stok</td>
                                <td>: {{ number_format($item->stok_gkn1 + $item->stok_gkn2) }} {{ $item->satuan }}</td>
                            </tr>
                            <tr>
                                <td>Minimal Stok</td>
                                <td>: {{ number_format($item->min_stok) }} {{ $item->satuan }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>: <span class="badge badge-{{ $status }}">{{ $statusText }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Informasi Batch -->
                <h6 class="mt-3">Detail Batch:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>No. Batch</th>
                                <th>Exp. Date</th>
                                <th>Lokasi</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>No. Faktur</th>
                                <th>Supplier</th>
                                <th>Tgl. Terima</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->stokBatch as $batch)
                            <tr>
                                <td>{{ $batch->nomor_batch }}</td>
                                <td>{{ $batch->tanggal_kadaluarsa->format('d/m/Y') }}</td>
                                <td>{{ $batch->lokasi->nama_lokasi }}</td>
                                <td>{{ number_format($batch->jumlah_unit) }}</td>
                                <td>
                                    <span class="badge badge-{{ $batch->status_exp === 'OK' ? 'success' : 
                                        ($batch->status_exp === 'WARNING' ? 'warning' : 'danger') }}">
                                        {{ $batch->status_exp }}
                                    </span>
                                </td>
                                <td>{{ $batch->nomor_faktur }}</td>
                                <td>{{ $batch->supplier }}</td>
                                <td>{{ $batch->tanggal_penerimaan->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Riwayat Transaksi -->
                <h6 class="mt-3">Riwayat Transaksi (30 Hari Terakhir):</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Lokasi</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->riwayatTransaksi as $history)
                            <tr>
                                <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $history->jenis_transaksi }}</td>
                                <td>{{ $history->jumlah }}</td>
                                <td>{{ $history->lokasi->nama_lokasi }}</td>
                                <td>{{ $history->keterangan }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Edit Barang -->
<div class="modal fade" id="editBarangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('barang-medis.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Barang</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id_obat">
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text" id="editKode" name="kode_obat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" id="editNama" name="nama_obat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe</label>
                        <select id="editTipe" name="tipe" class="form-control" required>
                            <option value="OBAT">OBAT</option>
                            <option value="ALKES">ALKES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" id="editSatuan" name="satuan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kemasan</label>
                        <input type="text" id="editKemasan" name="kemasan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Minimal Stok</label>
                        <input type="number" id="editMinStok" name="min_stok" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Stok -->
<div class="modal fade" id="tambahStokModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('barang-medis.tambah-stok') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Stok</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="stokIdObat" name="id_obat">
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" required min="1">
                    </div>
                    <div class="form-group">
                        <label>No. Batch</label>
                        <input type="text" name="nomor_batch" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kadaluarsa</label>
                        <input type="date" name="tanggal_kadaluarsa" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <select name="id_lokasi" class="form-control" required>
                            @foreach($lokasiList as $lokasi)
                                <option value="{{ $lokasi->id_lokasi }}">{{ $lokasi->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. Faktur</label>
                        <input type="text" name="nomor_faktur" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <input type="text" name="supplier" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Fungsi pencarian
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Fungsi export Excel
    function exportToExcel() {
        window.location.href = '{{ route("barang-medis.export") }}';
    }

    // Fungsi edit barang
    function editBarang(idObat) {
        // Ambil data barang dengan AJAX
        fetch(`/barang-medis/${idObat}/edit`)
            .then(response => response.json())
            .then(data => {
                // Tampilkan modal edit dengan data yang diterima
                $('#editBarangModal').modal('show');
                // Isi form dengan data yang diterima
                document.getElementById('editId').value = data.id_obat;
                document.getElementById('editNama').value = data.nama_obat;
                document.getElementById('editKode').value = data.kode_obat;
                document.getElementById('editTipe').value = data.tipe;
                document.getElementById('editSatuan').value = data.satuan;
                document.getElementById('editKemasan').value = data.kemasan;
                document.getElementById('editMinStok').value = data.min_stok;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data barang');
            });
    }

    // Fungsi tambah stok
    function tambahStok(idObat) {
        // Tampilkan modal tambah stok
        $('#tambahStokModal').modal('show');
        // Set id_obat ke input hidden
        document.getElementById('stokIdObat').value = idObat;
    }
</script>
@endpush