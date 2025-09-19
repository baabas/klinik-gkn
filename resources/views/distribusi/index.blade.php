@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Distribusi Obat ke Klinik</h1>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Klinik dengan Permintaan Siap Kirim (Status: DISETUJUI)</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Berikut adalah daftar klinik yang memiliki permintaan yang sudah disetujui dan siap untuk dibuatkan surat jalan/distribusi.</p>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Klinik</th>
                            <th class="text-center">Jumlah Permintaan Disetujui</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($klinikSiapKirim as $klinik)
                            <tr>
                                <td class="align-middle"><strong>{{ $klinik->nama_lokasi }}</strong></td>
                                <td class="text-center align-middle">{{ $klinik->permintaan_barang_count }}</td>
                                <td>
                                    <a href="{{ route('distribusi.create', $klinik->id) }}" class="btn btn-success">
                                        <i class="bi bi-box-seam"></i> Buat Pengiriman
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center p-4">Tidak ada permintaan yang disetujui dan siap untuk dikirim.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
