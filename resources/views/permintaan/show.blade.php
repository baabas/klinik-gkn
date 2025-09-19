@extends('layouts.sidebar-layout')

@section('title', 'Detail Permintaan ' . $permintaan->kode_permintaan)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Permintaan Obat</h1>
        <a href="{{ route('permintaan.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    {{-- CARD 1: INFORMASI UTAMA PERMINTAAN --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Informasi Permintaan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Kode</dt>
                        <dd class="col-sm-8">: {{ $permintaan->kode_permintaan }}</dd>

                        <dt class="col-sm-4">Tanggal</dt>
                        <dd class="col-sm-8">: {{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->isoFormat('D MMMM YYYY') }}</dd>

                        <dt class="col-sm-4">Peminta</dt>
                        <dd class="col-sm-8">: {{ $permintaan->userPeminta->nama_karyawan ?? 'N/A' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                     <dl class="row">
                        <dt class="col-sm-4">Lokasi</dt>
                        <dd class="col-sm-8">: {{ $permintaan->lokasiPeminta->nama_lokasi ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">:
                            @switch($permintaan->status)
                                @case('PENDING')
                                    <span class="badge bg-warning text-dark">PENDING</span>
                                    @break
                                @case('APPROVED')
                                    <span class="badge bg-info">DISETUJUI</span>
                                    @break
                                @case('COMPLETED')
                                    <span class="badge bg-success">DITERIMA</span>
                                    @break
                                @case('REJECTED')
                                    <span class="badge bg-danger">DITOLAK</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $permintaan->status }}</span>
                            @endswitch
                        </dd>
                         <dt class="col-sm-4">Catatan</dt>
                         <dd class="col-sm-8">: {{ $permintaan->catatan ?: '-' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- [BARU] Tombol Aksi Konfirmasi Penerimaan untuk Dokter --}}
            @if(Auth::user()->hasRole('DOKTER') && $permintaan->status == 'APPROVED')
                <hr>
                <div class="mt-3 text-center">
                    <p class="mb-2">Barang sudah diterima di lokasi Anda? Klik tombol di bawah untuk menyelesaikan permintaan ini.</p>
                    <form action="{{ route('permintaan.terima', $permintaan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan permintaan ini? Stok akan diperbarui secara otomatis.');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle-fill me-2"></i> Konfirmasi Obat Diterima
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>

    {{-- CARD 2: DAFTAR BARANG YANG DIMINTA --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Rincian Obat Diminta</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Tipe</th>
                            <th>Satuan</th>
                            <th class="text-center">Jumlah Diminta</th>
                            <th class="text-center">Jumlah Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permintaan->detail as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{-- Cek apakah ini barang baru atau barang terdaftar --}}
                                    @if ($item->id_barang)
                                        {{ $item->barangMedis->nama_obat }}
                                    @else
                                        {{ $item->nama_barang_baru }}
                                        <span class="badge bg-success">Request Baru</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->id_barang)
                                        {{ $item->barangMedis->tipe }}
                                    @else
                                        {{ $item->tipe_barang_baru }}
                                    @endif
                                </td>
                                 <td>
                                    @if ($item->id_barang)
                                        {{ $item->barangMedis->satuan }}
                                    @else
                                        {{ $item->satuan_barang_baru }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->jumlah_diminta }}</td>
                                <td class="text-center">
                                    {{ $item->jumlah_disetujui ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada item obat dalam permintaan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
