<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resep Obat - {{ $rekamMedis->id_rekam_medis }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', 'Consolas', monospace;
            font-size: 9px;
            line-height: 1.3;
            margin: 3mm;
            width: 74mm;
            max-width: 74mm;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header .subtitle {
            margin-top: 2px;
            font-size: 10px;
            font-weight: bold;
        }

        .info-section {
            margin-bottom: 6px;
            font-size: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            line-height: 1.5;
        }

        .info-label {
            font-weight: bold;
            width: 30%;
        }

        .info-value {
            width: 70%;
            text-align: right;
            word-wrap: break-word;
        }

        .separator {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .obat-section {
            margin-top: 10px;
        }

        .obat-item {
            border-top: 1px dashed #000;
            padding: 4px 0;
        }

        .obat-item:first-child {
            border-top: 2px solid #000;
        }

        .obat-nama {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 4px;
        }

        .obat-detail {
            margin-left: 5px;
            font-size: 8px;
            line-height: 1.6;
        }

        .obat-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .footer {
            margin-top: 8px;
            border-top: 2px dashed #000;
            padding-top: 5px;
            text-align: center;
            font-size: 8px;
        }

        .footer p {
            margin: 4px 0;
        }

        .footer .note {
            font-weight: bold;
            margin-top: 8px;
        }

        @media print {
            body {
                margin: 0;
                padding: 3mm;
                width: 74mm;
                max-width: 74mm;
            }

            .no-print {
                display: none;
            }
        }

        /* Button untuk preview (tidak akan muncul saat print) */
        .action-buttons {
            position: fixed;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
            z-index: 9999;
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background-color: #007bff;
            color: white;
        }

        .btn-print:hover {
            background-color: #0056b3;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background-color: #545b62;
        }

        @media print {
            .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    {{-- Action Buttons (hanya muncul saat preview) --}}
    <div class="action-buttons no-print">
        <a href="{{ route('pasien.index') }}" class="btn-action btn-back">
            ← Kembali ke Daftar Pasien
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            🖨️ Print Ulang
        </button>
    </div>

    {{-- Header Klinik --}}
    <div class="header">
        <h2>KLINIK GKN</h2>
        <div class="subtitle">RESEP OBAT</div>
    </div>

    {{-- Informasi Pasien --}}
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">No. RM</span>
            <span class="info-value">{{ $rekamMedis->id_rekam_medis }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">
                @if($rekamMedis->tanggal_kunjungan instanceof \Carbon\Carbon)
                    {{ $rekamMedis->tanggal_kunjungan->format('d/m/Y H:i') }}
                @else
                    {{ \Carbon\Carbon::parse($rekamMedis->tanggal_kunjungan)->format('d/m/Y H:i') }}
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Pasien</span>
            <span class="info-value">
                @php
                    $namaPasien = null;
                    
                    // KARYAWAN: Cek users.nama_karyawan atau karyawan.nama_karyawan
                    if ($rekamMedis->nip_pasien) {
                        // Priority 1: Dari users.nama_karyawan
                        if ($rekamMedis->pasien && !empty($rekamMedis->pasien->nama_karyawan)) {
                            $namaPasien = $rekamMedis->pasien->nama_karyawan;
                        } 
                        // Priority 2: Dari karyawan.nama_karyawan
                        elseif ($rekamMedis->pasien && $rekamMedis->pasien->karyawan && !empty($rekamMedis->pasien->karyawan->nama_karyawan)) {
                            $namaPasien = $rekamMedis->pasien->karyawan->nama_karyawan;
                        }
                    }
                    
                    // NON-KARYAWAN: Cek dari users.nama_karyawan via relasi
                    if (!$namaPasien && $rekamMedis->nik_pasien) {
                        if ($rekamMedis->pasienNonKaryawan && $rekamMedis->pasienNonKaryawan->user && !empty($rekamMedis->pasienNonKaryawan->user->nama_karyawan)) {
                            $namaPasien = $rekamMedis->pasienNonKaryawan->user->nama_karyawan;
                        }
                    }
                    
                    // FALLBACK: Jika nama tidak ditemukan, tampilkan identifier
                    if (!$namaPasien) {
                        if ($rekamMedis->nip_pasien) {
                            $namaPasien = 'NIP: ' . $rekamMedis->nip_pasien;
                        } elseif ($rekamMedis->nik_pasien) {
                            $namaPasien = 'NIK: ' . substr($rekamMedis->nik_pasien, 0, 10) . '...';
                        } else {
                            $namaPasien = '-';
                        }
                    }
                @endphp
                {{ $namaPasien }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Dokter</span>
            <span class="info-value">
                @php
                    $namaDokter = '-';
                    // Priority 1: Langsung dari users.nama_karyawan (paling reliable)
                    if ($rekamMedis->dokter && !empty($rekamMedis->dokter->nama_karyawan)) {
                        $namaDokter = $rekamMedis->dokter->nama_karyawan;
                    } 
                    // Priority 2: Dari karyawan.nama_karyawan (jika ada extended info)
                    elseif ($rekamMedis->dokter && $rekamMedis->dokter->karyawan && !empty($rekamMedis->dokter->karyawan->nama_karyawan)) {
                        $namaDokter = $rekamMedis->dokter->karyawan->nama_karyawan;
                    } 
                    // Priority 3: Fallback tampilkan NIP
                    elseif ($rekamMedis->dokter && !empty($rekamMedis->dokter->nip)) {
                        $namaDokter = 'NIP: ' . $rekamMedis->dokter->nip;
                    } 
                    // Priority 4: Fallback tampilkan User ID
                    elseif ($rekamMedis->id_dokter) {
                        $namaDokter = 'User ID: ' . $rekamMedis->id_dokter;
                    }
                @endphp
                {{ $namaDokter }}
            </span>
        </div>
    </div>

    {{-- List Obat --}}
    <div class="obat-section">
        @forelse($rekamMedis->resepObat as $index => $resep)
        <div class="obat-item">
            <div class="obat-nama">{{ $index + 1 }}. {{ $resep->obat->nama_obat ?? 'Obat tidak diketahui' }}</div>
            <div class="obat-detail">
                <div class="obat-detail-row">
                    <span>Jumlah:</span>
                    <span>{{ $resep->jumlah }} {{ $resep->obat->satuan_terkecil ?? 'unit' }}</span>
                </div>
                @if($resep->dosis)
                <div class="obat-detail-row">
                    <span>Dosis:</span>
                    <span><strong>{{ $resep->dosis }}</strong></span>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="obat-item">
            <div class="obat-nama">Tidak ada resep obat</div>
        </div>
        @endforelse
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p class="note">Tempelkan struk ini pada kemasan obat</p>
        <div class="separator"></div>
        <p>Semoga lekas sembuh!</p>
        <p style="margin-top: 8px; font-size: 9px;">
            Dicetak: {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>

    {{-- Auto Print Script --}}
    <script>
        // Auto print saat halaman dimuat
        window.onload = function() {
            // Tunggu sebentar agar halaman ter-render sempurna
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // Optional: Auto close setelah print (bisa diaktifkan jika diinginkan)
        window.onafterprint = function() {
            // Uncomment baris di bawah jika ingin auto close setelah print
            // setTimeout(function() {
            //     window.close();
            // }, 1000);
        };
    </script>
</body>
</html>
