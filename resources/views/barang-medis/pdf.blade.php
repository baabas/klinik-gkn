<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Obat & Alat Medis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .kategori-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .kategori-obat {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .kategori-box {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        .kemasan-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            background-color: #fff3e0;
            color: #f57c00;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .stok-info {
            font-size: 10px;
        }
        .stok-lokasi {
            margin-bottom: 2px;
        }
        .total-stok {
            font-weight: bold;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KLINIK GKN</h1>
        <h2>Daftar Obat & Alat Medis</h2>
    </div>

    <div class="info">
        <div class="info-item"><strong>Tanggal Cetak:</strong> {{ $tanggal_cetak }}</div>
        <div class="info-item"><strong>Dicetak oleh:</strong> {{ $nama_user }}</div>
        <div class="info-item"><strong>Total Item:</strong> {{ $barangMedis->count() }} item</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 8%;">Kode</th>
                <th style="width: 25%;">Nama Obat/Alat Medis</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 12%;">Kemasan</th>
                <th style="width: 8%;">Isi Kemasan</th>
                <th style="width: 8%;">Isi per Satuan</th>
                <th style="width: 8%;">Satuan Terkecil</th>
                <th style="width: 14%;">Stok per Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangMedis as $index => $barang)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $barang->kode_obat }}</td>
                <td>
                    <strong>{{ $barang->nama_obat }}</strong>
                    @if($barang->deskripsi)
                        <br><small style="color: #666;">{{ Str::limit($barang->deskripsi, 50) }}</small>
                    @endif
                </td>
                <td class="text-center">
                    <span class="kategori-badge {{ $barang->kategori == 'Obat' ? 'kategori-obat' : 'kategori-box' }}">
                        {{ $barang->kategori }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="kemasan-badge">{{ $barang->kemasan }}</span>
                </td>
                <td class="text-center">{{ $barang->isi_kemasan }}</td>
                <td class="text-center">{{ $barang->isi_per_satuan }}</td>
                <td class="text-center">{{ $barang->satuan_terkecil }}</td>
                <td class="stok-info">
                    @php
                        $totalStok = 0;
                        $stokByLokasi = $barang->stok->groupBy('id_lokasi_klinik');
                    @endphp
                    
                    @forelse($stokByLokasi as $idLokasi => $stokLokasi)
                        @php
                            $jumlahStok = $stokLokasi->sum('jumlah_stok');
                            $totalStok += $jumlahStok;
                            $namaLokasi = $stokLokasi->first()->lokasiKlinik->nama_lokasi ?? 'Unknown';
                        @endphp
                        <div class="stok-lokasi">
                            <strong>{{ $namaLokasi }}:</strong> {{ number_format($jumlahStok) }}
                        </div>
                    @empty
                        <div class="stok-lokasi" style="color: #d32f2f;">Tidak ada stok</div>
                    @endforelse
                    
                    @if($totalStok > 0)
                        <div class="total-stok" style="margin-top: 5px; border-top: 1px solid #eee; padding-top: 2px;">
                            Total: {{ number_format($totalStok) }}
                        </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Klinik GKN</p>
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
    </div>
</body>
</html>