<x-app-layout>
    <x-slot name="header">
        <h1 class="h2">Kartu Pasien Digital</h1>
    </x-slot>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Nomor Index Pasien: {{ $user->id }}</h4>
        </div>
        <div class="card-body p-4">
            @if($karyawan)
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>NIP:</strong><br> {{ $user->nip }}</p>
                        <p><strong>Nama:</strong><br> {{ $user->nama_karyawan }}</p>
                        <p><strong>Alamat:</strong><br> {{ $karyawan->alamat ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Jabatan:</strong><br> {{ $karyawan->jabatan ?? '-' }}</p>
                        <p><strong>Kantor:</strong><br> {{ $karyawan->kantor ?? '-' }}</p>
                    </div>
                </div>
            @else
                <p class="text-center text-danger">Data detail karyawan tidak ditemukan.</p>
            @endif
        </div>
        <div class="card-footer text-muted">
            Ini adalah kartu pasien digital. Tunjukkan saat akan berobat.
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Riwayat Kunjungan & Pengobatan</h5>
            @if(Auth::user()->roles()->where('name', 'DOKTER')->exists())
                <a href="{{ route('rekam-medis.create', $user->nip) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle"></i> Rekam Medis Baru
                </a>
            @endif
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Diagnosa & Riwayat Sakit</th>
                            <th>Pengobatan & Resep</th>
                            <th>Berobat Untuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user->rekamMedis as $rekam)
                            <tr>
                                <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($rekam->tanggal_kunjungan)->format('d-m-Y H:i') }}</td>
                                <td>
                                    @if($rekam->detailDiagnosa->isNotEmpty())
                                        <strong>Diagnosa:</strong>
                                        <ul class="list-unstyled mb-1 ps-3">
                                            @foreach($rekam->detailDiagnosa as $diagnosa)
                                                <li>- {{ $diagnosa->penyakit->nama_penyakit ?? 'N/A' }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if($rekam->riwayat_sakit)
                                        <strong>Catatan:</strong> {{ $rekam->riwayat_sakit }}
                                    @endif
                                </td>
                                <td>
                                    @if($rekam->resepObat->isNotEmpty())
                                        <strong>Resep:</strong>
                                        <ul class="list-unstyled mb-1 ps-3">
                                            @foreach($rekam->resepObat as $resep)
                                                <li>- {{ $resep->obat->nama_obat ?? 'N/A' }} (Qty: {{ $resep->kuantitas }})</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                     @if($rekam->pengobatan)
                                        <strong>Catatan:</strong> {{ $rekam->pengobatan }}
                                    @endif
                                </td>
                                <td>
                                    @if($rekam->nama_sa)
                                        <strong>{{ $rekam->nama_sa }}</strong> ({{ $rekam->jenis_kelamin_sa }})
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Belum ada riwayat kunjungan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
