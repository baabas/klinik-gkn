<?php

namespace App\Http\Controllers;

use App\Models\SuratDistribusi;
use App\Models\MasterWhatsappValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class SuratDistribusiController extends Controller
{
    /**
     * Menampilkan daftar surat distribusi
     */
    public function index()
    {
        $user = Auth::user();
        
        $suratDistribusi = SuratDistribusi::with(['lokasiAsal', 'lokasiTujuan', 'user', 'details.barang'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('surat-distribusi.index', compact('suratDistribusi'));
    }

    /**
     * Menampilkan detail surat distribusi dan opsi cetak
     */
    public function show($id)
    {
        $surat = SuratDistribusi::with(['lokasiAsal', 'lokasiTujuan', 'user', 'details.barang'])
            ->findOrFail($id);

        // Ambil data WhatsApp Validator yang aktif
        $validators = MasterWhatsappValidator::active()->orderBy('nama_validator')->get();

        return view('surat-distribusi.show', compact('surat', 'validators'));
    }

    /**
     * Generate dan download PDF Surat Distribusi
     */
    public function printPdf($id)
    {
        $surat = SuratDistribusi::with(['lokasiAsal', 'lokasiTujuan', 'user', 'details.barang'])
            ->findOrFail($id);

        // Generate QR Code sebagai SVG (tidak memerlukan ext-gd)
        $qrCode = $this->generateQRCode($surat);

        $data = [
            'surat' => $surat,
            'qrCode' => $qrCode,
            'tanggal_cetak' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('surat-distribusi.pdf', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->download("Surat_Distribusi_{$surat->nomor_surat}.pdf");
    }

    /**
     * Preview PDF di browser
     */
    public function previewPdf($id)
    {
        $surat = SuratDistribusi::with(['lokasiAsal', 'lokasiTujuan', 'user', 'details.barang'])
            ->findOrFail($id);

        // Generate QR Code sebagai SVG (tidak memerlukan ext-gd)
        $qrCode = $this->generateQRCode($surat);

        $data = [
            'surat' => $surat,
            'qrCode' => $qrCode,
            'tanggal_cetak' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('surat-distribusi.pdf', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->stream("Surat_Distribusi_{$surat->nomor_surat}.pdf");
    }

    /**
     * Update nomor WA validator
     */
    public function updateWa(Request $request, $id)
    {
        $surat = SuratDistribusi::findOrFail($id);
        
        $validated = $request->validate([
            'nomor_wa_validator' => 'required|string|max:20',
        ]);

        // Format nomor WA (hapus spasi, ganti awalan 0 dengan 62)
        $nomorWa = preg_replace('/\s+/', '', $validated['nomor_wa_validator']);
        if (str_starts_with($nomorWa, '0')) {
            $nomorWa = '62' . substr($nomorWa, 1);
        } elseif (str_starts_with($nomorWa, '+62')) {
            $nomorWa = substr($nomorWa, 1);
        }

        $surat->update([
            'nomor_wa_validator' => $nomorWa,
        ]);

        return redirect()->back()->with('success', 'Nomor WA Validator berhasil diupdate.');
    }

    /**
     * Generate QR Code dengan URL WhatsApp
     * Menggunakan SVG output karena tidak memerlukan ext-gd
     */
    private function generateQRCode(SuratDistribusi $surat, string $format = 'svg'): string
    {
        $waUrl = $surat->generateWhatsAppUrl();

        // Selalu gunakan SVG karena tidak memerlukan ext-gd
        $options = new QROptions([
            'version'      => QRCode::VERSION_AUTO,
            'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'     => QRCode::ECC_L,
            'scale'        => 5,
            'imageBase64'  => true,  // Output sebagai data URI untuk embed di HTML/PDF
            'addQuietzone' => true,
            'svgUseFillAttributes' => true,
        ]);

        $qrcode = new QRCode($options);
        
        return $qrcode->render($waUrl);
    }
}
