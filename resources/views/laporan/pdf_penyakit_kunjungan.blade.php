<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penyakit dan Kunjungan - {{ $filter['string'] }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 4px; text-align: center; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .bg-light { background-color: #f8f9fa; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3, .header h4 { margin: 0; }
        /* Page break untuk memastikan tabel kedua tidak terpotong */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h3>LAPORAN PENYAKIT & KUNJUNGAN PASIEN</h3>
        <h4>KLINIK GKN-1&2</h4>
        <h4>BULAN: {{ strtoupper($filter['nama_bulan']) }}</h4>
    </div>

    {{-- ============================================= --}}
    {{-- BAGIAN LAPORAN PENYAKIT --}}
    {{-- ============================================= --}}
    <p class="fw-bold">LAPORAN PENYAKIT</p>
    
    {{-- [BARU] Tabel Penyakit untuk Tanggal 1-16 --}}
    <table class="table">
        <thead class="bg-light">
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2" style="width: 80px;">Kode ICD10</th>
                <th rowspan="2" class="text-start" style="width: 200px;">Nama Penyakit</th>
                <th colspan="16">Tanggal</th>
            </tr>
            <tr>
                @for ($hari = 1; $hari <= 16; $hari++)
                    <th>{{ $hari }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($daftar_penyakit as $penyakit)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $penyakit->ICD10 }}</td>
                <td class="text-start">{{ $penyakit->nama_penyakit }}</td>
                @php
                    $kasus_penyakit = $data_kasus->get($penyakit->nama_penyakit);
                @endphp
                @for ($hari = 1; $hari <= 16; $hari++)
                    @php
                        $jumlah = $kasus_penyakit ? $kasus_penyakit->where('hari', $hari)->sum('jumlah') : 0;
                    @endphp
                    <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                @endfor
            </tr>
            @empty
            <tr>
                <td colspan="19">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- [BARU] Tabel Penyakit untuk Tanggal 17-31 --}}
    @if ($filter['jumlah_hari'] > 16)
    <table class="table">
        <thead class="bg-light">
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2" style="width: 80px;">Kode ICD10</th>
                <th rowspan="2" class="text-start" style="width: 200px;">Nama Penyakit</th>
                <th colspan="{{ $filter['jumlah_hari'] - 16 }}">Tanggal</th>
                <th rowspan="2">Jumlah Total</th>
            </tr>
            <tr>
                @for ($hari = 17; $hari <= $filter['jumlah_hari']; $hari++)
                    <th>{{ $hari }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($daftar_penyakit as $penyakit)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $penyakit->ICD10 }}</td>
                <td class="text-start">{{ $penyakit->nama_penyakit }}</td>
                @php
                    $total_bulanan = 0;
                    $kasus_penyakit = $data_kasus->get($penyakit->nama_penyakit);
                @endphp
                @for ($hari = 17; $hari <= $filter['jumlah_hari']; $hari++)
                    @php
                        $jumlah = $kasus_penyakit ? $kasus_penyakit->where('hari', $hari)->sum('jumlah') : 0;
                    @endphp
                    <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                @endfor
                {{-- Hitung total bulanan dari semua tanggal --}}
                @php
                    $total_bulanan = $kasus_penyakit ? $kasus_penyakit->sum('jumlah') : 0;
                @endphp
                <td class="fw-bold">{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ ($filter['jumlah_hari'] - 16) + 4 }}">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <div class="page-break"></div>

    {{-- ============================================= --}}
    {{-- BAGIAN LAPORAN KUNJUNGAN --}}
    {{-- ============================================= --}}
    <p class="fw-bold">LAPORAN KUNJUNGAN PER KANTOR</p>

    {{-- [BARU] Tabel Kunjungan untuk Tanggal 1-16 --}}
    <table class="table">
        <thead class="bg-light">
            <tr>
                <th rowspan="2" class="text-start" style="width: 200px;">Kantor</th>
                <th colspan="16">Tanggal</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 16; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($daftar_kantor as $kantor)
                @php
                    $kunjungan_kantor = $data_kunjungan->get($kantor);
                @endphp
                <tr>
                    <td class="text-start">{{ $kantor }}</td>
                    @for ($hari = 1; $hari <= 16; $hari++)
                        @php
                            $jumlah = $kunjungan_kantor ? $kunjungan_kantor->where('hari', $hari)->sum('jumlah') : 0;
                        @endphp
                        <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="17">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- [BARU] Tabel Kunjungan untuk Tanggal 17-31 --}}
     @if ($filter['jumlah_hari'] > 16)
    <table class="table">
        <thead class="bg-light">
            <tr>
                <th rowspan="2" class="text-start" style="width: 200px;">Kantor</th>
                <th colspan="{{ $filter['jumlah_hari'] - 16 }}">Tanggal</th>
                <th rowspan="2">Jumlah Total</th>
            </tr>
            <tr>
                @for ($i = 17; $i <= $filter['jumlah_hari']; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($daftar_kantor as $kantor)
                @php
                    $kunjungan_kantor = $data_kunjungan->get($kantor);
                    $total_bulanan = 0;
                @endphp
                <tr>
                    <td class="text-start">{{ $kantor }}</td>
                    @for ($hari = 17; $hari <= $filter['jumlah_hari']; $hari++)
                        @php
                            $jumlah = $kunjungan_kantor ? $kunjungan_kantor->where('hari', $hari)->sum('jumlah') : 0;
                        @endphp
                        <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                    @endfor
                    @php
                         $total_bulanan = $kunjungan_kantor ? $kunjungan_kantor->sum('jumlah') : 0;
                    @endphp
                    <td class="fw-bold">{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ ($filter['jumlah_hari'] - 16) + 3 }}">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
     @endif
</body>
</html>