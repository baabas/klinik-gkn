<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pemakaian Obat</title>
    <style>
        body { font-family: sans-serif; font-size: 8px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h4, .header h5 { margin: 0; }
        table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
        th, td { border: 1px solid black; padding: 4px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
        .page-break { page-break-after: always; }
        .container { margin-bottom: 30px; }
    </style>
</head>
<body>
    {{-- ===================================================== --}}
    {{-- ============ BAGIAN 1: LAPORAN MINGGUAN ============= --}}
    {{-- ===================================================== --}}
    <div class="container">
        <div class="header">
            <h4>LAPORAN PEMAKAIAN OBAT (REKAP MINGGUAN)</h4>
            <h5>BULAN: {{ $filter['nama_bulan_upper'] }}</h5>
        </div>
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3%;">NO.</th>
                    <th rowspan="2" class="text-left" style="width: 20%;">NAMA OBAT</th>
                    <th rowspan="2">JUMLAH KETERSEDIAAN</th>
                    <th colspan="6">PENGELUARAN OBAT PER MINGGU</th>
                    <th rowspan="2">SISA OBAT</th>
                </tr>
                <tr>
                    <th>I</th> <th>II</th> <th>III</th> <th>IV</th> <th>V</th> <th>JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar_obat as $index => $obat)
                    @php
                        $pemakaian = $data_pemakaian_mingguan->get($obat->nama_obat);
                        $stok_saat_ini = $stok_obat->get($obat->nama_obat)->stok_saat_ini ?? 0;
                        $minggu1 = $pemakaian ? $pemakaian->where('minggu_ke', 1)->sum('jumlah') : 0;
                        $minggu2 = $pemakaian ? $pemakaian->where('minggu_ke', 2)->sum('jumlah') : 0;
                        $minggu3 = $pemakaian ? $pemakaian->where('minggu_ke', 3)->sum('jumlah') : 0;
                        $minggu4 = $pemakaian ? $pemakaian->where('minggu_ke', 4)->sum('jumlah') : 0;
                        $minggu5 = $pemakaian ? $pemakaian->where('minggu_ke', 5)->sum('jumlah') : 0;
                        $total_pengeluaran = $minggu1 + $minggu2 + $minggu3 + $minggu4 + $minggu5;
                        $stok_awal = $stok_saat_ini + $total_pengeluaran;
                        $sisa_obat = $stok_saat_ini;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $obat->nama_obat }}</td>
                        <td>{{ $stok_awal }}</td>
                        <td>{{ $minggu1 ?: '' }}</td>
                        <td>{{ $minggu2 ?: '' }}</td>
                        <td>{{ $minggu3 ?: '' }}</td>
                        <td>{{ $minggu4 ?: '' }}</td>
                        <td>{{ $minggu5 ?: '' }}</td>
                        <td>{{ $total_pengeluaran ?: '' }}</td>
                        <td>{{ $sisa_obat }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10">Tidak ada data pemakaian obat pada bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    {{-- ===================================================== --}}
    {{-- ============= BAGIAN 2: LAPORAN HARIAN ============== --}}
    {{-- ===================================================== --}}

    {{-- Tabel Tanggal 1-16 --}}
    <div class="container">
        <div class="header">
            <h4>LAPORAN PEMAKAIAN OBAT TANGGAL 01 S/D 16</h4>
            <h5>BULAN: {{ $filter['nama_bulan_upper'] }}</h5>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="text-left">NAMA OBAT</th>
                    @for ($i = 1; $i <= 16; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                    <th>JML</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar_obat as $obat)
                    <tr>
                        <td class="text-left">{{ $obat->nama_obat }}</td>
                        @php $sub_total_1 = 0; @endphp
                        @for ($i = 1; $i <= 16; $i++)
                            @php
                                $jumlah = $data_pemakaian_harian->get($obat->nama_obat) ? $data_pemakaian_harian->get($obat->nama_obat)->where('hari', $i)->sum('jumlah') : 0;
                                $sub_total_1 += $jumlah;
                            @endphp
                            <td>{{ $jumlah ?: '' }}</td>
                        @endfor
                        <td style="background-color: #f2f2f2; font-weight: bold;">{{ $sub_total_1 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="18">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tabel Tanggal 17-31 --}}
    <div class="container">
         <div class="header">
            <h4>LAPORAN PEMAKAIAN OBAT TANGGAL 17 S/D {{ $filter['jumlah_hari'] }}</h4>
            <h5>BULAN: {{ $filter['nama_bulan_upper'] }}</h5>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="text-left">NAMA OBAT</th>
                    @for ($i = 17; $i <= $filter['jumlah_hari']; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                    <th>JML</th>
                    <th>TOTAL BULAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar_obat as $obat)
                    <tr>
                        <td class="text-left">{{ $obat->nama_obat }}</td>
                        @php
                            $total_periode_1 = 0;
                            for ($i = 1; $i <= 16; $i++) {
                                $total_periode_1 += $data_pemakaian_harian->get($obat->nama_obat) ? $data_pemakaian_harian->get($obat->nama_obat)->where('hari', $i)->sum('jumlah') : 0;
                            }
                            $sub_total_2 = 0;
                        @endphp
                        @for ($i = 17; $i <= $filter['jumlah_hari']; $i++)
                            @php
                                $jumlah = $data_pemakaian_harian->get($obat->nama_obat) ? $data_pemakaian_harian->get($obat->nama_obat)->where('hari', $i)->sum('jumlah') : 0;
                                $sub_total_2 += $jumlah;
                            @endphp
                            <td>{{ $jumlah ?: '' }}</td>
                        @endfor
                        <td style="background-color: #f2f2f2; font-weight: bold;">{{ $sub_total_2 }}</td>
                        <td style="background-color: #e3e3e3; font-weight: bold;">{{ $total_periode_1 + $sub_total_2 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ ($filter['jumlah_hari'] - 16) + 3 }}">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>