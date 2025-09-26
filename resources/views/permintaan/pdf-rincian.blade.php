<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Permintaan Obat - {{ $permintaan->kode_permintaan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 12px;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .small {
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    {{-- Header sesuai format Kementerian --}}
    <div class="header">
        <h1>KEMENTERIAN KEUANGAN REPUBLIK INDONESIA</h1>
        <div>SEKRETARIAT JENDERAL</div>
        <div>PUSAT SISTEM INFORMASI DAN TEKNOLOGI KEUANGAN</div>
        <div>KANTOR PENGELOLAAN TEKNOLOGI INFORMASI DAN KOMUNIKASI</div>
        <div>DAN BARANG MILIK NEGARA</div>
        <br>
        <h2>PENGADAAN OBAT-OBATAN BALAI KESEHATAN GKN I & II SEMARANG</h2>
    </div>

    {{-- Informasi Permintaan --}}
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Nomor Permintaan</div>
            <div class="info-value">: {{ $permintaan->kode_permintaan }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Permintaan</div>
            <div class="info-value">: {{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->isoFormat('D MMMM YYYY') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Lokasi Peminta</div>
            <div class="info-value">: {{ $permintaan->lokasiPeminta->nama_lokasi ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Peminta</div>
            <div class="info-value">: {{ $permintaan->userPeminta->nama_karyawan ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status</div>
            <div class="info-value">: DITERIMA</div>
        </div>
    </div>

    {{-- Tabel Rincian Obat --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama</th>
                <th style="width: 12%;">Jumlah<br>(Satuan Terkecil)</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 20%;">Kemasan</th>
                <th style="width: 12%;">ED<br>(Tanggal Kadaluarsa)</th>
                <th style="width: 16%;">Tanggal Masuk</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permintaan->detail as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>
                    @if ($item->id_barang)
                        {{ $item->barangMedis->nama_obat }}
                    @else
                        {{ $item->nama_barang_baru }}
                    @endif
                </td>
                <td class="text-center">
                    @if($item->id_barang)
                        {{-- Konversi jumlah disetujui (kemasan) ke satuan terkecil --}}
                        {{ ($item->jumlah_disetujui ?? 0) * ($item->barangMedis->isi_kemasan_jumlah ?? 1) * ($item->barangMedis->isi_per_satuan ?? 1) }}
                    @else
                        {{ $item->jumlah_disetujui ?? 0 }}
                    @endif
                </td>
                <td class="text-center">
                    @if($item->id_barang)
                        {{ $item->barangMedis->satuan_terkecil ?? 'Pcs' }}
                    @else
                        Pcs
                    @endif
                </td>
                <td>
                    @if($item->id_barang)
                        {{ $item->barangMedis->kemasan ?? 'Box' }} isi {{ $item->barangMedis->isi_kemasan_jumlah ?? 1 }} {{ $item->barangMedis->isi_kemasan_satuan ?? 'strip' }} @ {{ $item->barangMedis->isi_per_satuan ?? 1 }} {{ $item->barangMedis->satuan_terkecil ?? 'Pcs' }}
                    @else
                        {{ $item->kemasan_barang_baru ?? 'Box' }}
                    @endif
                </td>
                <td class="text-center">
                    @if($item->id_barang && $item->barangMedis->stokHistories->isNotEmpty())
                        @php
                            $latestStock = $item->barangMedis->stokHistories->first();
                        @endphp
                        {{ $latestStock->expired_at ? \Carbon\Carbon::parse($latestStock->expired_at)->format('d/m/Y') : '-' }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($item->id_barang && $item->barangMedis->stokHistories->isNotEmpty())
                        @php
                            $latestStock = $item->barangMedis->stokHistories->first();
                        @endphp
                        {{ $latestStock->tanggal_transaksi ? \Carbon\Carbon::parse($latestStock->tanggal_transaksi)->format('d/m/Y') : \Carbon\Carbon::parse($latestStock->created_at)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada item obat dalam permintaan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer & Tanda Tangan --}}
    <div class="signature-section">
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div style="margin-top: 5px; font-weight: bold;">Pengadaan</div>
            <div class="signature-line">
                <div>(.............................)</div>
            </div>
        </div>
        <div class="signature-box">
            <div>Semarang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</div>
            <div style="margin-top: 5px; font-weight: bold;">Yang Menerima</div>
            <div class="signature-line">
                <div>({{ $permintaan->userPeminta->nama_karyawan ?? '..........................' }})</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="small">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB
        </div>
    </div>
</body>
</html>