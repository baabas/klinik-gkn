@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Daftar Obat & Alat Medis</h4>
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
                            <th>Satuan</th>
                            <th>Status</th>
                            <th>Detail</th>
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
                            <td>{{ $item->satuan }}</td>
                            <td>
                                @if($item->status_exp === 'WARNING')
                                    <span class="badge badge-warning">Mendekati Kadaluarsa</span>
                                @elseif($item->status_exp === 'EXPIRED')
                                    <span class="badge badge-danger">Kadaluarsa</span>
                                @else
                                    <span class="badge badge-success">Tersedia</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" 
                                        data-toggle="modal" 
                                        data-target="#detailModal{{ $item->id_obat }}">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail untuk Dokter -->
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
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Stok:</h6>
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
                        </table>
                    </div>
                </div>
                
                <!-- Informasi Batch -->
                <h6 class="mt-3">Informasi Batch Tersedia:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>No. Batch</th>
                                <th>Exp. Date</th>
                                <th>Lokasi</th>
                                <th>Stok</th>
                                <th>Status</th>
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
@endsection