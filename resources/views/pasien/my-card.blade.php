{{-- Menggunakan layout baru yang simpel tanpa sidebar --}}
@extends('layouts.pasien-layout')

@section('content')
    <div class="container">
        <h1 class="h2 mb-4">Kartu Pasien Saya</h1>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Informasi Pasien</h5>
            </div>
            <div class="card-body">
                {{-- ... (kode untuk menampilkan info pasien sama seperti di show.blade.php) ... --}}
                <p><strong>NIP:</strong> {{ $user->nip }}</p>
                <p><strong>Nama:</strong> {{ $user->nama_karyawan }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                {{-- Tambahkan info lain jika perlu --}}
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Rekam Medis</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal Kunjungan</th>
                                <th>Keluhan</th>
                                <th>Dokter Pemeriksa</th>
                                {{-- Tambah kolom lain jika perlu --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->rekamMedis as $rekam)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($rekam->tanggal_kunjungan)->isoFormat('D MMMM YYYY') }}</td>
                                <td>{{ $rekam->keluhan_utama }}</td>
                                <td>{{ $rekam->dokter->nama_karyawan ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada riwayat rekam medis.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
