<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pemakaian Obat</title>
    <style>
        body { font-family: sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h4, .header h5 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
        @page { margin: 20px 25px; }
    </style>
</head>
<body>
    <div class="header">
        <h4>LAPORAN PEMAKAIAN OBAT</h4>
        <h5>BULAN: {{ $nama_bulan_tahun }}</h5>
    </div>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">NO.</th>
                <th rowspan="2" class="text-left" style="width: 25%;">NAMA OBAT</th>
                <th rowspan="2">EXP OBAT</th>
                <th rowspan="2">JUMLAH KETERSEDIAAN</th>
                <th colspan="6">PENGELUARAN OBAT PER MINGGU</th>
                <th rowspan="2">SISA OBAT</th>
            </tr>
            <tr>
                <th>I</th>
                <th>II</th>
                <th>III</th>
                <th>IV</th>
                <th>V</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftar_obat as $index => $obat)
                @php
                    // Mengambil data pemakaian untuk obat saat ini
                    $pemakaian = $data_pemakaian->get($obat->nama_obat);

                    // Menghitung pemakaian per minggu
                    $minggu1 = $pemakaian ? $pemakaian->where('minggu_ke', 1)->sum('jumlah') : 0;
                    $minggu2 = $pemakaian ? $pemakaian->where('minggu_ke', 2)->sum('jumlah') : 0;
                    $minggu3 = $pemakaian ? $pemakaian->where('minggu_ke', 3)->sum('jumlah') : 0;
                    $minggu4 = $pemakaian ? $pemakaian->where('minggu_ke', 4)->sum('jumlah') : 0;
                    $minggu5 = $pemakaian ? $pemakaian->where('minggu_ke', 5)->sum('jumlah') : 0;

                    $total_pengeluaran = $minggu1 + $minggu2 + $minggu3 + $minggu4 + $minggu5;

                    // Menghitung stok awal (Stok saat ini + total pengeluaran)
                    $stok_awal = $obat->stok_saat_ini + $total_pengeluaran;
                    $sisa_obat = $obat->stok_saat_ini;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $obat->nama_obat }}</td>
                    <td>-</td>
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
                <tr>
                    <td colspan="11">Tidak ada data pemakaian obat pada bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
