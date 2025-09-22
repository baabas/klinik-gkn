@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h2 mb-0">Detail Permintaan {{ $permintaan->kode }}</h1>
            <p class="text-muted mb-0">Status saat ini: <span class="{{ $permintaan->status_badge_class }}">{{ $permintaan->status_label }}</span></p>
        </div>
        <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5">Kode</dt>
                        <dd class="col-7">{{ $permintaan->kode }}</dd>
                        <dt class="col-5">Tanggal</dt>
                        <dd class="col-7">{{ optional($permintaan->tanggal)->translatedFormat('d F Y') }}</dd>
                        <dt class="col-5">Peminta</dt>
                        <dd class="col-7">{{ $permintaan->peminta?->display_name ?? '-' }}</dd>
                        <dt class="col-5">Lokasi</dt>
                        <dd class="col-7">{{ $permintaan->lokasi?->nama_lokasi ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Catatan</h6>
                    <p class="mb-0">{{ $permintaan->catatan ?: 'Tidak ada catatan tambahan.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Detail Permintaan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Barang</th>
                            <th style="width:12%">Jumlah</th>
                            <th style="width:15%">Kemasan</th>
                            <th style="width:18%">Konversi Satuan Dasar</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permintaan->details as $detail)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $detail->barang?->nama_obat ?? $detail->nama_barang_baru }}</div>
                                    @if($detail->barang)
                                        <small class="text-muted">{{ $detail->barang->kode_obat }} • {{ $detail->barang->satuan_dasar }}</small>
                                    @else
                                        <span class="badge bg-warning text-dark">Barang baru</span>
                                    @endif
                                </td>
                                <td>
                                    @if($detail->barang)
                                        {{ $detail->jumlah_kemasan_label ?? '-' }}
                                    @else
                                        {{ rtrim(rtrim(number_format($detail->jumlah, 2, ',', '.'), '0'), ',') }} {{ $detail->satuan ?? '' }}
                                    @endif
                                </td>
                                <td>
                                    @if($detail->barang)
                                        {{ $detail->satuan_kemasan ?? $detail->kemasan ?? '-' }}
                                    @else
                                        {{ $detail->kemasan ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $detail->konversi_label ?? '-' }}</td>
                                <td>{{ $detail->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @if(auth()->user()?->hasRole('DOKTER') && $permintaan->isDraft() && $permintaan->peminta_id === auth()->id())
            <a href="{{ route('permintaan.edit', $permintaan) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit Draft
            </a>
            <form action="{{ route('permintaan.submit', $permintaan) }}" method="post" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Ajukan permintaan ini?')">
                    <i class="bi bi-send me-1"></i> Ajukan
                </button>
            </form>
            <form action="{{ route('permintaan.destroy', $permintaan) }}" method="post" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Hapus draft permintaan?')">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </form>
        @endif

        @if(auth()->user()?->hasRole(['PENGADAAN', 'ADMIN']))
            @if($permintaan->isDiajukan())
                <form action="{{ route('permintaan.approve', $permintaan) }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Setujui</button>
                </form>
                <form action="{{ route('permintaan.reject', $permintaan) }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak permintaan ini?')">
                        <i class="bi bi-x-circle me-1"></i> Tolak
                    </button>
                </form>
            @elseif($permintaan->isDisetujui())
                <div class="card border-primary w-100">
                    <div class="card-header bg-primary text-white">
                        Proses Pemenuhan Stok
                    </div>
                    <div class="card-body">
                        <form action="{{ route('permintaan.fulfill', $permintaan) }}" method="post">
                            @csrf
                            <p class="text-muted">Pilih barang baru yang ingin otomatis ditambahkan ke master barang medis.</p>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width:5%"></th>
                                            <th>Nama Barang Baru</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($permintaan->details->whereNull('barang_id') as $detail)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="buat_barang_baru[]" value="{{ $detail->id }}" class="form-check-input">
                                                </td>
                                                <td>{{ $detail->nama_barang_baru }}</td>
                                                <td>{{ $detail->jumlah }}</td>
                                                <td>{{ $detail->satuan }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-muted text-center">Tidak ada permintaan barang baru.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-down me-1"></i> Tandai Dipenuhi</button>
                        </form>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
