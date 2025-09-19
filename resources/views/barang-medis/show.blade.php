@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h2 mb-1">Detail Barang: {{ $barang->nama_obat }}</h1>
            <p class="text-muted mb-0">Kode Barang: {{ $barang->kode_obat }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('barang-medis.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            @if(auth()->user()->hasRole('PENGADAAN'))
                <a href="{{ route('barang-medis.edit', $barang->id_obat) }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Edit Barang
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <h5 class="card-title">Informasi Barang</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Nama</dt>
                                <dd class="col-sm-7">{{ $barang->nama_obat }}</dd>
                                <dt class="col-sm-5">Tipe</dt>
                                <dd class="col-sm-7">
                                    <span class="badge {{ $barang->tipe === 'OBAT' ? 'bg-primary' : 'bg-success' }}">{{ $barang->tipe }}</span>
                                </dd>
                                <dt class="col-sm-5">Satuan</dt>
                                <dd class="col-sm-7">{{ $barang->satuan }}</dd>
                                <dt class="col-sm-5">Kemasan</dt>
                                <dd class="col-sm-7">{{ $barang->kemasan ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Dibuat Oleh</dt>
                                <dd class="col-sm-7">{{ $barang->creator->name ?? '-' }}</dd>
                                <dt class="col-sm-5">Tgl Dibuat</dt>
                                <dd class="col-sm-7">{{ optional($barang->created_at)->format('d/m/Y H:i') ?? '-' }}</dd>
                                <dt class="col-sm-5">Tgl Diperbarui</dt>
                                <dd class="col-sm-7">{{ optional($barang->updated_at)->format('d/m/Y H:i') ?? '-' }}</dd>
                                <dt class="col-sm-5">Riwayat Lengkap</dt>
                                <dd class="col-sm-7">
                                    <a href="{{ route('barang-medis.history', $barang->id_obat) }}">Lihat Riwayat &rarr;</a>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="border rounded p-3 bg-light text-center">
                        <p class="text-muted mb-1">Total Stok</p>
                        <h2 class="display-6 mb-2">{{ number_format($totalStok) }}</h2>
                        <p class="text-muted mb-0">{{ strtolower($barang->satuan) }}</p>
                    </div>
                </div>
            </div>

            @if($barang->stokMasukTerakhir)
                <hr>
                <h6 class="text-uppercase text-muted mb-3">Barang Masuk Terakhir</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="fw-semibold text-muted small">Tanggal</div>
                        <div>{{ optional($barang->stokMasukTerakhir->tanggal_transaksi)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="fw-semibold text-muted small">Jumlah Kemasan</div>
                        <div>
                            @if(!is_null($barang->stokMasukTerakhir->jumlah_kemasan))
                                {{ number_format($barang->stokMasukTerakhir->jumlah_kemasan) }} {{ $barang->stokMasukTerakhir->satuan_kemasan ?? 'kemasan' }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fw-semibold text-muted small">Isi per Kemasan</div>
                        <div>
                            @if(!is_null($barang->stokMasukTerakhir->isi_per_kemasan))
                                {{ number_format($barang->stokMasukTerakhir->isi_per_kemasan) }} {{ strtolower($barang->satuan) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fw-semibold text-muted small">Kedaluwarsa</div>
                        <div>{{ optional($barang->stokMasukTerakhir->expired_at)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Stok per Lokasi</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Lokasi</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Persentase</th>
                            <th>Terakhir Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokPerLokasi as $stok)
                            <tr>
                                <td>{{ $stok->lokasi->nama_lokasi ?? ('Lokasi #' . $stok->id_lokasi) }}</td>
                                <td class="text-end">{{ number_format($stok->jumlah) }}</td>
                                <td class="text-end">{{ $totalStok > 0 ? number_format(($stok->jumlah / max($totalStok, 1)) * 100, 1) : '0.0' }}%</td>
                                <td>{{ optional($stok->updated_at)->format('d/m/Y H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada stok yang tercatat untuk barang ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h5 class="card-title mb-0">Riwayat Transaksi Terbaru</h5>
                <a href="{{ route('barang-medis.history', $barang->id_obat) }}" class="btn btn-outline-primary btn-sm">
                    Lihat Semua Riwayat
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th class="text-end">Perubahan</th>
                            <th class="text-end">Stok Sesudah</th>
                            <th>Petugas</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentHistories as $history)
                            <tr>
                                <td>{{ optional($history->tanggal_transaksi)->format('d/m/Y') ?? $history->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $history->lokasi->nama_lokasi ?? '-' }}</td>
                                <td class="text-end">{{ number_format($history->perubahan) }}</td>
                                <td class="text-end">{{ number_format($history->stok_sesudah ?? 0) }}</td>
                                <td>{{ $history->user->name ?? '-' }}</td>
                                <td>{{ $history->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada riwayat stok yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
