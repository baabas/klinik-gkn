@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Proses Permintaan Obat</h1>
        <a href="{{ route('permintaan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    {{-- Form utama yang akan mengirim data update --}}
    <form action="{{ route('permintaan.update', $permintaan->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Kartu Informasi Header Permintaan --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light"><h5 class="mb-0">Informasi Permintaan</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><p class="mb-2"><strong>Kode:</strong> {{ $permintaan->kode_permintaan }}</p></div>
                    <div class="col-md-6"><p class="mb-2"><strong>Peminta:</strong> {{ $permintaan->userPeminta->nama_karyawan ?? 'N/A' }}</p></div>
                    <div class="col-md-6"><p class="mb-0"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->isoFormat('D MMMM YYYY') }}</p></div>
                    <div class="col-md-6"><p class="mb-0"><strong>Lokasi:</strong> {{ $permintaan->lokasiPeminta->nama_lokasi ?? 'N/A' }}</p></div>
                </div>
            </div>
        </div>

        {{-- Kartu Rincian Barang dengan Input Persetujuan --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light"><h5 class="mb-0">Rincian Obat untuk Diproses</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Obat</th>
                                <th class="text-center">Jumlah Diminta</th>
                                <th class="text-center" style="width: 20%;">Jumlah Disetujui</th>
                                <th class="text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permintaan->detail as $index => $item)
                                <tr class="align-middle">
                                    <td>
                                        <input type="hidden" name="detail[{{ $index }}][id]" value="{{ $item->id }}">
                                        @if ($item->barangMedis)
                                            <strong>{{ $item->barangMedis->nama_obat }}</strong>
                                        @else
                                            <strong>{{ $item->nama_barang_baru }}</strong>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->jumlah_diminta }}</td>
                                    <td class="text-center">
                                        <input type="number" name="detail[{{ $index }}][jumlah_disetujui]" class="form-control form-control-sm text-center" value="{{ old('detail.'.$index.'.jumlah_disetujui', $item->jumlah_diminta) }}" min="0">
                                    </td>
                                    <td class="text-center">
                                        @if ($item->barangMedis)
                                            <span class="badge bg-primary">Obat Terdaftar</span>
                                        @else
                                            <span class="badge bg-success">Request Baru</span>
                                            <br><small class="text-muted fst-italic">Tambahkan ke master terlebih dahulu</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel Aksi untuk Pengadaan --}}
        <div class="card shadow-sm mt-4">
            <div class="card-body text-end bg-light">
                <button type="submit" name="action" value="REJECTED" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin MENOLAK seluruh permintaan ini?')">Tolak Permintaan</button>
                <button type="submit" name="action" value="APPROVED" class="btn btn-success px-4">Simpan & Setujui Permintaan</button>
            </div>
        </div>
    </form>
@endsection
