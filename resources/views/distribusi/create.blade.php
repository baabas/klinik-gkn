@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Form Pengiriman Barang</h1>
        <a href="{{ route('distribusi.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Batal & Kembali
        </a>
    </div>

    <form action="{{ route('distribusi.store') }}" method="POST">
        @csrf
        {{-- Hidden input untuk menyimpan ID gudang pusat dan klinik tujuan --}}
        <input type="hidden" name="id_lokasi_sumber" value="{{ $gudangPusat->id }}">
        <input type="hidden" name="id_lokasi_tujuan" value="{{ $klinik->id }}">

        {{-- Kartu Informasi Pengiriman --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Detail Pengiriman</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Dari:</strong> Gudang Pusat</p>
                        <p class="mb-0"><strong>Ke:</strong> {{ $klinik->nama_lokasi }}</p>
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_distribusi" class="form-label"><strong>Tanggal Pengiriman:</strong></label>
                        <input type="date" class="form-control" id="tanggal_distribusi" name="tanggal_distribusi" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Rincian Barang untuk Dikirim --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Barang untuk Dikirim (Akumulasi dari Permintaan APPROVED)</h5>
            </div>
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th class="text-center">Total Disetujui</th>
                                <th class="text-center">Stok di Gudang</th>
                                <th class="text-center" style="width: 20%;">Jumlah Dikirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($barangUntukDikirim as $index => $item)
                                <tr class="align-middle">
                                    <td>
                                        {{-- Hidden input untuk setiap barang --}}
                                        <input type="hidden" name="barang[{{ $index }}][id_barang]" value="{{ $item->id_barang }}">
                                        <strong>{{ $item->barangMedis->nama_obat }}</strong>
                                        <small class="d-block text-muted">{{ $item->barangMedis->tipe }}</small>
                                    </td>
                                    <td class="text-center fs-5"><strong>{{ $item->total_disetujui }}</strong></td>
                                    <td class="text-center {{ $item->stok_gudang < $item->total_disetujui ? 'text-danger' : '' }}">
                                        {{ $item->stok_gudang }}
                                        @if ($item->stok_gudang < $item->total_disetujui)
                                            <i class="bi bi-exclamation-triangle-fill" title="Stok tidak mencukupi!"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="barang[{{ $index }}][jumlah_dikirim]"
                                               class="form-control text-center"
                                               value="{{ min($item->total_disetujui, $item->stok_gudang) }}"
                                               max="{{ $item->stok_gudang }}"
                                               min="0" required>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-4">Tidak ada barang yang perlu dikirim.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel Aksi --}}
        <div class="card shadow-sm mt-4">
            <div class="card-body text-end bg-light">
                <button type="submit" class="btn btn-primary px-4" onclick="return confirm('Anda yakin ingin memproses pengiriman ini? Stok akan otomatis diperbarui.')">
                    <i class="bi bi-send-check"></i> Proses & Kirim Barang
                </button>
            </div>
        </div>
    </form>
@endsection
