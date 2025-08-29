<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penyakit dan Kunjungan</title>
    <style>
        body { font-family: sans-serif; font-size: 8px; }
        .header { text-align: center; margin-bottom: 15px; }
        h4, h5 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px;}
        th, td { border: 1px solid black; padding: 4px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
        @page { margin: 20px 25px; }
    </style>
</head>
<body>
    <div class="header">
        <h4>LAPORAN JENIS PENYAKIT DAN KUNJUNGAN</h4>
        <h5>BULAN {{ strtoupper($filter['nama_bulan']) }}</h5>
    </div>

    {{-- HALAMAN 1: TANGGAL 1 - 16 --}}
    <strong>A. LAPORAN PENYAKIT TANGGAL 1-16</strong>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th class="text-left">NAMA PENYAKIT</th>
                <th>ICD 10</th>
                @for ($i = 1; $i <= 16; $i++) <th>{{ $i }}</th> @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($daftar_penyakit as $index => $penyakit)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $penyakit->nama_penyakit }}</td>
                <td>{{ $penyakit->kode_penyakit }}</td>
                @for ($hari = 1; $hari <= 16; $hari++)
                    @php $jumlah = $data_kasus->get($penyakit->nama_penyakit, collect())->where('hari', $hari)->sum('jumlah'); @endphp
                    <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>

    <strong>B. LAPORAN KUNJUNGAN TANGGAL 1-16</strong>
    <table>
         <thead>
            <tr>
                <th class="text-left">KUNJUNGAN</th>
                @for ($i = 1; $i <= 16; $i++) <th>{{ $i }}</th> @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($daftar_kantor as $kantor)
            <tr>
                <td class="text-left">{{ $kantor }}</td>
                @for ($hari = 1; $hari <= 16; $hari++)
                    @php $jumlah = $data_kunjungan->get($kantor, collect())->where('hari', $hari)->sum('jumlah'); @endphp
                    <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-after: always;"></div>

    {{-- HALAMAN 2: TANGGAL 17 - 31 --}}
    <strong>C. LAPORAN PENYAKIT TANGGAL 17-{{$filter['jumlah_hari']}}</strong>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th class="text-left">NAMA PENYAKIT</th>
                <th>ICD 10</th>
                @for ($i = 17; $i <= $filter['jumlah_hari']; $i++) <th>{{ $i }}</th> @endfor
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daftar_penyakit as $index => $penyakit)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $penyakit->nama_penyakit }}</td>
                <td>{{ $penyakit->kode_penyakit }}</td>
                @php $total_bulanan = 0; @endphp
                @for ($hari = 17; $hari <= $filter['jumlah_hari']; $hari++)
                    @php $jumlah = $data_kasus->get($penyakit->nama_penyakit, collect())->where('hari', $hari)->sum('jumlah'); @endphp
                    <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                @endfor
                @php $total_bulanan = $data_kasus->get($penyakit->nama_penyakit, collect())->sum('jumlah'); @endphp
                <td>{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <strong>D. LAPORAN KUNJUNGAN TANGGAL 17-{{$filter['jumlah_hari']}}</strong>
     <table>
         <thead>
            <tr>
                <th class="text-left">KUNJUNGAN</th>
                @for ($i = 17; $i <= $filter['jumlah_hari']; $i++) <th>{{ $i }}</th> @endfor
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daftar_kantor as $kantor)
            <tr>
                <td class="text-left">{{ $kantor }}</td>
                @for ($hari = 17; $hari <= $filter['jumlah_hari']; $hari++)
                    @php $jumlah = $data_kunjungan->get($kantor, collect())->where('hari', $hari)->sum('jumlah'); @endphp
                    <td>{{ $jumlah > 0 ? $jumlah : '' }}</td>
                @endfor
                @php $total_bulanan = $data_kunjungan->get($kantor, collect())->sum('jumlah'); @endphp
                <td>{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
