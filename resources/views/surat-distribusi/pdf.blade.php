<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Distribusi - {{ $surat->nomor_surat }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 20px;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        
        /* Header - repeat on each page */
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #555;
        }
        .header .subtitle {
            font-size: 10px;
            color: #666;
        }

        /* Document Info */
        .doc-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .doc-info table {
            width: 100%;
        }
        .doc-info td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .doc-info .label {
            font-weight: bold;
            width: 150px;
        }
        .doc-number {
            font-size: 14px;
            font-weight: bold;
            color: #198754;
        }
        .validation-code {
            font-size: 16px;
            font-weight: bold;
            color: #0d6efd;
            font-family: monospace;
            letter-spacing: 2px;
        }

        /* Distribution Info */
        .distribution-info {
            margin-bottom: 20px;
        }
        .distribution-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .distribution-info td {
            padding: 8px 10px;
            border: 1px solid #dee2e6;
        }
        .distribution-info .from-to {
            background: #e9ecef;
            font-weight: bold;
            width: 100px;
        }

        /* Items Table */
        .items-section h3 {
            font-size: 12px;
            background: #198754;
            color: white;
            padding: 8px 10px;
            margin-bottom: 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead {
            display: table-header-group; /* Repeat header on each page */
        }
        .items-table tr {
            page-break-inside: avoid;
        }
        .items-table th {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
        .items-table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            font-size: 10px;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table tfoot td {
            background: #f8f9fa;
            font-weight: bold;
        }

        /* QR Section */
        .qr-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .qr-container {
            border: 2px dashed #198754;
            padding: 20px;
            text-align: center;
            background: #f8fff9;
            border-radius: 10px;
        }
        .qr-code {
            display: inline-block;
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .qr-code img {
            width: 150px;
            height: 150px;
        }
        .qr-instructions {
            font-size: 10px;
            color: #666;
            margin-top: 10px;
        }
        .qr-instructions strong {
            color: #198754;
        }

        /* Signatures */
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .signatures table {
            width: 100%;
        }
        .signatures td {
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signatures .sign-title {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .signatures .sign-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Notes */
        .notes {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 10px;
        }
        .notes strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>KLINIK GKN SEMARANG</h1>
            <h2>SURAT DISTRIBUSI OBAT & ALAT MEDIS</h2>
            <p class="subtitle">Sistem Manajemen Klinik & Pengadaan Barang Medis</p>
        </div>

        {{-- Document Info --}}
        <div class="doc-info">
            <table>
                <tr>
                    <td class="label">Nomor Surat</td>
                    <td>: <span class="doc-number">{{ $surat->nomor_surat }}</span></td>
                    <td class="label">Tanggal Distribusi</td>
                    <td>: {{ $surat->tanggal_distribusi->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Kode Validasi</td>
                    <td>: <span class="validation-code">{{ $surat->kode_validasi }}</span></td>
                    <td class="label">Dibuat Oleh</td>
                    <td>: {{ $surat->user->nama_karyawan ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Distribution Info --}}
        <div class="distribution-info">
            <table>
                <tr>
                    <td class="from-to">DARI</td>
                    <td>{{ $surat->lokasiAsal->nama_lokasi ?? '-' }}</td>
                    <td class="from-to">TUJUAN</td>
                    <td>{{ $surat->lokasiTujuan->nama_lokasi ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Items List --}}
        <div class="items-section">
            <h3>DAFTAR BARANG YANG DIDISTRIBUSIKAN</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="width: 100px;">Kode</th>
                        <th>Nama Barang</th>
                        <th style="width: 80px;">Kategori</th>
                        <th style="width: 80px;">Jumlah</th>
                        <th style="width: 70px;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surat->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $detail->barang->kode_obat ?? '-' }}</td>
                        <td>{{ $detail->barang->nama_obat ?? '-' }}</td>
                        <td class="text-center">{{ $detail->barang->kategori_barang ?? '-' }}</td>
                        <td class="text-center"><strong>{{ number_format($detail->jumlah) }}</strong></td>
                        <td class="text-center">{{ $detail->barang->satuan_terkecil ?? 'Pcs' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right">Total Item :</td>
                        <td class="text-center"><strong>{{ $surat->details->count() }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Notes --}}
        @if($surat->catatan)
        <div class="notes">
            <strong>Catatan:</strong> {{ $surat->catatan }}
        </div>
        @endif

        {{-- QR Code Section --}}
        <div class="qr-section">
            <div class="qr-container">
                <p style="font-weight: bold; font-size: 12px; margin-bottom: 10px;">
                    📱 SCAN QR CODE UNTUK VALIDASI
                </p>
                <div class="qr-code">
                    <img src="{{ $qrCode }}" alt="QR Code Validasi">
                </div>
                <div class="qr-instructions">
                    <p><strong>Petunjuk Validasi:</strong></p>
                    <p>1. Periksa semua barang sesuai daftar di atas</p>
                    <p>2. Jika sudah sesuai, scan QR Code dengan HP</p>
                    <p>3. WhatsApp akan terbuka dengan pesan konfirmasi</p>
                    <p>4. Kirim pesan tersebut sebagai bukti validasi</p>
                    <p style="margin-top: 5px;"><strong>No. WA Validator:</strong> {{ $surat->nomor_wa_validator }}</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Dicetak melalui Sistem Klinik GKN pada {{ $tanggal_cetak }}</p>
            <p>Dokumen ini sah tanpa tanda tangan basah</p>
        </div>
    </div>
</body>
</html>
