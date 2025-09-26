    @extends('layouts.sidebar-layout')

@section('title', 'Detail Barang Medis')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1">Detail Barang: {{ $barangMedi->nama_obat }}</h2>
                    <p class="mb-0 text-muted">Kode Barang: {{ $barangMedi->kode_obat }}</p>
                </div>
                <div>
                    <a href="{{ route('barang-medis.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('barang-medis.edit', $barangMedi->id_obat) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Barang
                    </a>
                </div>
            </div>

            <!-- Card untuk Informasi Barang -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informasi Barang</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Nama</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $barangMedi->nama_obat }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Kategori</strong>
                                </div>
                                <div class="col-sm-9">
                                    <span class="badge 
                                        @if($barangMedi->kategori_barang == 'Obat') bg-success
                                        @elseif($barangMedi->kategori_barang == 'BMHP') bg-info
                                        @elseif($barangMedi->kategori_barang == 'Alkes') bg-warning text-dark
                                        @else bg-danger
                                        @endif
                                        ">{{ $barangMedi->kategori_barang }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Satuan Terkecil</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $barangMedi->satuan_terkecil }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Kemasan Default</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $barangMedi->kemasan ?? '-' }} isi {{ $barangMedi->isi_kemasan_jumlah }} {{ $barangMedi->isi_kemasan_satuan }} @ {{ $barangMedi->isi_per_satuan }} {{ $barangMedi->satuan_terkecil }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Dibuat Oleh</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ Auth::user()->nama_karyawan ?? 'System' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Tgl Dibuat</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $barangMedi->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Tgl Diperbarui</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $barangMedi->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Batch Masuk Bulan Ini</strong>
                                </div>
                                <div class="col-sm-9">
                                    @php
                                        // Ambil stok masuk bulan ini untuk barang ini
                                        $stokMasukBulanIni = $barangMedi->stokHistories()
                                            ->whereMonth('created_at', now()->month)
                                            ->whereYear('created_at', now()->year)
                                            ->where('perubahan', '>', 0)
                                            ->get();
                                        
                                        $batchCount = $stokMasukBulanIni->count();
                                        $totalKemasan = $stokMasukBulanIni->sum('jumlah_kemasan');
                                    @endphp
                                    
                                    @if($batchCount > 0)
                                        <span class="badge bg-primary fs-6">
                                            {{ $batchCount }} batch : {{ $totalKemasan }} {{ $barangMedi->kemasan ?? 'Box' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary fs-6">
                                            0 batch : 0 {{ $barangMedi->kemasan ?? 'Box' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <p class="text-muted mb-1">Total Stok</p>
                                <h1 class="display-4 mb-0">
                                    {{ $barangMedi->stok->sum('jumlah') }}
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card untuk Stok per Lokasi -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Stok per Lokasi</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Lokasi</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Persentase</th>
                                    <th class="text-center">Terakhir Diperbarui</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalStokSatuan = $barangMedi->stok->sum('jumlah');
                                @endphp
                                @forelse($barangMedi->stok as $stok)
                                <tr>
                                    <td>
                                        @if($stok->lokasi)
                                            @if($stok->lokasi->id == 1)
                                                Distribusi ke Klinik GKN 1
                                            @elseif($stok->lokasi->id == 2) 
                                                Distribusi ke Klinik GKN 2
                                            @else
                                                Distribusi ke {{ $stok->lokasi->nama_lokasi }}
                                            @endif
                                        @else
                                            Lokasi tidak diketahui
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $stok->jumlah }}</td>
                                    <td class="text-center">
                                        @if($totalStokSatuan > 0)
                                            {{ number_format(($stok->jumlah / $totalStokSatuan) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $stok->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Belum ada stok di lokasi manapun
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card untuk Riwayat Transaksi -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Riwayat Transaksi</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Masuk</th>
                                    <th class="text-center">Jumlah Kemasan</th>
                                    <th class="text-center">Isi Per Kemasan</th>
                                    <th class="text-center">Total (satuan)</th>
                                    <th>Kadaluarsa</th>
                                    <th>Perubahan</th>
                                    <th class="text-center">Stok Sesudah</th>
                                    <th>Petugas</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatTerakhir as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->tanggal_transaksi ? \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d/m/Y') : \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $transaksi->jumlah_kemasan ?? '-' }}</td>
                                    <td class="text-center">{{ $transaksi->isi_per_kemasan ?? $barangMedi->isi_per_satuan }}</td>
                                    <td class="text-center">{{ ($transaksi->jumlah_kemasan ?? 1) * ($transaksi->isi_per_kemasan ?? $barangMedi->isi_per_satuan) }}</td>
                                    <td>{{ $transaksi->expired_at ? \Carbon\Carbon::parse($transaksi->expired_at)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">
                                        @if($transaksi->perubahan > 0)
                                            <span class="text-success">+{{ $transaksi->perubahan }}</span>
                                        @elseif($transaksi->perubahan < 0)
                                            <span class="text-danger">{{ $transaksi->perubahan }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $transaksi->stok_sesudah ?? '-' }}</td>
                                    <td>{{ $transaksi->user->nama_karyawan ?? '-' }}</td>
                                    <td>
                                        @if($transaksi->lokasi)
                                            @php
                                                $lokasiName = '';
                                                if($transaksi->lokasi->id == 1) {
                                                    $lokasiName = 'Klinik GKN 1';
                                                } elseif($transaksi->lokasi->id == 2) {
                                                    $lokasiName = 'Klinik GKN 2';
                                                } else {
                                                    $lokasiName = $transaksi->lokasi->nama_lokasi;
                                                }
                                                
                                                // Cek apakah ini distribusi/perpindahan antar klinik
                                                $isDistribusi = $transaksi->keterangan && 
                                                               (strpos($transaksi->keterangan, 'Distribusi') !== false || 
                                                                strpos($transaksi->keterangan, 'distribusi') !== false);
                                            @endphp
                                            
                                            @if($isDistribusi)
                                                {{-- Ini adalah distribusi/perpindahan antar klinik --}}
                                                @if($transaksi->perubahan > 0)
                                                    {{-- Stok bertambah = barang masuk dari klinik lain --}}
                                                    {{ $lokasiName }} ← 
                                                    @if(strpos($transaksi->keterangan, 'Lokasi ID 1') !== false)
                                                        Terdistribusi dari Klinik GKN 1
                                                    @elseif(strpos($transaksi->keterangan, 'Lokasi ID 2') !== false)
                                                        Terdistribusi dari Klinik GKN 2
                                                    @else
                                                        {{ $transaksi->keterangan }}
                                                    @endif
                                                @elseif($transaksi->perubahan < 0)
                                                    {{-- Stok berkurang = barang keluar ke klinik lain --}}
                                                    {{ $lokasiName }} → 
                                                    @if(strpos($transaksi->keterangan, 'Lokasi ID 1') !== false)
                                                        Distribusi ke Klinik GKN 1
                                                    @elseif(strpos($transaksi->keterangan, 'Lokasi ID 2') !== false)
                                                        Distribusi ke Klinik GKN 2
                                                    @else
                                                        {{ str_replace(['Distribusi ke ', 'Distribusi dari '], '', $transaksi->keterangan) }}
                                                    @endif
                                                @endif
                                            @else
                                                {{-- Ini adalah barang masuk biasa (pembelian/input) --}}
                                                {{ $lokasiName }}
                                                @if($transaksi->keterangan)
                                                    - {{ $transaksi->keterangan }}
                                                @endif
                                            @endif
                                        @else
                                            {{ $transaksi->keterangan ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        Belum ada riwayat stok yang tercatat.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination removed since riwayatTerakhir is limited to 10 records in controller --}}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.display-4 {
    font-size: 3.5rem;
    font-weight: 300;
    line-height: 1.2;
}

.table th {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    font-weight: 600;
    font-size: 0.875rem;
    white-space: nowrap;
}

.table td {
    font-size: 0.875rem;
    vertical-align: middle;
}

.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.card-title {
    color: #5a5c69;
    font-weight: 600;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
}
</style>
@endsection