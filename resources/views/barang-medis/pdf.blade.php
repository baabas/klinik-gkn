<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Obat &amp; Alat Medis</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
            color: #333;
        }
        .header {
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            margin: 4px 0 0;
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        .info {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 10px;
        }
        .info div {
            min-width: 160px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
        }
        thead {
            background-color: #f2f2f2;
            display: table-header-group;
        }
        tbody tr {
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .nama-obat {
            font-weight: bold;
            font-size: 9px;
        }
        .stok-breakdown {
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px solid #eee;
            font-size: 8px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Klinik GKN</h1>
        <h2>Daftar Obat &amp; Alat Medis</h2>
    </div>

    <div class="info">
        <div><strong>Tanggal Cetak:</strong> {{ $tanggal_cetak }}</div>
        <div><strong>Dicetak oleh:</strong> {{ $nama_user }}</div>
        <div><strong>Total Item:</strong> {{ $barangMedis->count() }} barang</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th style="width: 70px;" class="text-center">Kode</th>
                <th>Nama Obat/Alat Medis</th>
                <th style="width: 70px;" class="text-center">Kategori</th>
                <th style="width: 70px;" class="text-center">Kemasan</th>
                <th style="width: 70px;" class="text-center">Isi Kemasan</th>
                <th style="width: 80px;" class="text-center">Isi per Satuan</th>
                <th style="width: 70px;" class="text-center">Satuan Terkecil</th>
                <th style="width: 80px;" class="text-center">Tgl Masuk Terakhir</th>
                <th style="width: 80px;" class="text-center">Kadaluarsa Terdekat</th>
                <th style="width: 70px;" class="text-center">Stok GKN 1</th>
                <th style="width: 70px;" class="text-center">Stok GKN 2</th>
                <th style="width: 80px;" class="text-center">Total Stok</th>
            </tr>
        </thead>
        <tbody>
             @forelse ($barangMedis as $index => $barang)
                @php
                    $stokGkn1 = (int) ($barang->stok_gkn1 ?? 0);
                    $stokGkn2 = (int) ($barang->stok_gkn2 ?? 0);
                    $totalStok = (int) ($barang->stok_sum_jumlah ?? 0);
                    $tanggalMasuk = $barang->tanggal_masuk_terakhir
                        ? \Illuminate\Support\Carbon::parse($barang->tanggal_masuk_terakhir)->format('d/m/Y')
                        : '-';
                    $kadaluarsa = $barang->expired_terdekat
                        ? \Illuminate\Support\Carbon::parse($barang->expired_terdekat)->format('d/m/Y')
                        : '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $barang->kode_obat ?? '-' }}</td>
                    <td>
                        <div class="nama-obat">{{ $barang->nama_obat }}</div>
                        <div class="stok-breakdown">
                            @forelse ($barang->stok->groupBy('id_lokasi') as $stokLokasi)
                                @php
                                    $lokasi = $stokLokasi->first()->lokasi->nama_lokasi ?? 'Lokasi Tidak Diketahui';
                                    $jumlah = $stokLokasi->sum('jumlah');
                                @endphp
                                <div>{{ $lokasi }}: {{ number_format($jumlah) }}</div>
                            @empty
                                <div>Tidak ada data stok</div>
                            @endforelse
                        </div>
                    </td>
                    <td class="text-center">{{ $barang->kategori_barang ?? '-' }}</td>
                    <td class="text-center">{{ $barang->kemasan ?? '-' }}</td>
                    <td class="text-center">
                        @if($barang->isi_kemasan_jumlah && $barang->isi_kemasan_satuan)
                            {{ $barang->isi_kemasan_jumlah }} {{ $barang->isi_kemasan_satuan }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($barang->isi_per_satuan && $barang->satuan_terkecil)
                            {{ $barang->isi_per_satuan }} {{ $barang->satuan_terkecil }}
                        @elseif($barang->isi_per_satuan)
                            {{ $barang->isi_per_satuan }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ $barang->satuan_terkecil ?? '-' }}</td>
                    <td class="text-center">{{ $tanggalMasuk }}</td>
                    <td class="text-center">{{ $kadaluarsa }}</td>
                    <td class="text-right">{{ number_format($stokGkn1) }}</td>
                    <td class="text-right">{{ number_format($stokGkn2) }}</td>
                    <td class="text-right">{{ number_format($totalStok) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data obat &amp; alat medis untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak melalui Sistem Klinik GKN pada {{ $tanggal_cetak }}
    </div>
</body>
</html>
