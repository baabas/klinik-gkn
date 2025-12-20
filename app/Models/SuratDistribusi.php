<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratDistribusi extends Model
{
    protected $table = 'surat_distribusi';
    protected $primaryKey = 'id_surat';

    protected $fillable = [
        'nomor_surat',
        'kode_validasi',
        'id_lokasi_asal',
        'id_lokasi_tujuan',
        'id_user',
        'tanggal_distribusi',
        'nomor_wa_validator',
        'catatan',
        'status',
        'validated_at',
        'validated_by',
    ];

    protected $casts = [
        'tanggal_distribusi' => 'date',
        'validated_at' => 'datetime',
    ];

    /**
     * Generate nomor surat unik
     */
    public static function generateNomorSurat(): string
    {
        $prefix = 'SD';
        $date = now()->format('Ymd');
        $lastSurat = self::whereDate('created_at', today())->orderBy('id_surat', 'desc')->first();
        
        if ($lastSurat) {
            $lastNumber = (int) substr($lastSurat->nomor_surat, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$date}-{$newNumber}";
    }

    /**
     * Generate kode validasi unik (untuk QR Code)
     */
    public static function generateKodeValidasi(): string
    {
        do {
            $kode = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('kode_validasi', $kode)->exists());

        return $kode;
    }

    /**
     * Generate URL WhatsApp dengan pesan validasi (versi pendek untuk QR)
     */
    public function generateWhatsAppUrl(): string
    {
        $nomor = preg_replace('/[^0-9]/', '', $this->nomor_wa_validator);
        
        // Pastikan format nomor Indonesia (62xxx)
        if (substr($nomor, 0, 1) === '0') {
            $nomor = '62' . substr($nomor, 1);
        }

        $lokasiAsal = $this->lokasiAsal->nama_lokasi ?? '-';
        $lokasiTujuan = $this->lokasiTujuan->nama_lokasi ?? '-';
        $tanggalDist = $this->tanggal_distribusi ? $this->tanggal_distribusi->format('d/m/Y') : '-';
        $tanggalJam = now()->format('d/m/Y H:i');
        
        // Pesan singkat untuk QR Code (agar tidak overflow)
        $pesan = "VALIDASI DISTRIBUSI\n"
               . "No: {$this->nomor_surat}\n"
               . "Kode: {$this->kode_validasi}\n"
               . "Dari: {$lokasiAsal}\n"
               . "Ke: {$lokasiTujuan}\n"
               . "Tgl: {$tanggalDist}\n"
               . "Item: {$this->details->count()} jenis\n"
               . "Validasi: {$tanggalJam}\n"
               . "Status: SESUAI";

        return "https://wa.me/{$nomor}?text=" . urlencode($pesan);
    }

    /**
     * Generate pesan WhatsApp lengkap (untuk tombol test WA)
     */
    public function generateWhatsAppUrlFull(): string
    {
        $nomor = preg_replace('/[^0-9]/', '', $this->nomor_wa_validator);
        
        if (substr($nomor, 0, 1) === '0') {
            $nomor = '62' . substr($nomor, 1);
        }

        $lokasiAsal = $this->lokasiAsal->nama_lokasi ?? '-';
        $lokasiTujuan = $this->lokasiTujuan->nama_lokasi ?? '-';
        $tanggalDist = $this->tanggal_distribusi ? $this->tanggal_distribusi->format('d/m/Y') : '-';
        
        // Build list barang
        $listBarang = "";
        foreach ($this->details as $index => $detail) {
            $namaBarang = $detail->barang->nama_obat ?? '-';
            $jumlah = $detail->jumlah;
            $satuan = $detail->barang->satuan_terkecil ?? 'pcs';
            $listBarang .= ($index + 1) . ". {$namaBarang}: {$jumlah} {$satuan}\n";
        }

        $tanggalJam = now()->format('d/m/Y H:i');
        
        $pesan = "*VALIDASI DISTRIBUSI OBAT*\n\n"
               . "No. Surat: {$this->nomor_surat}\n"
               . "Kode: {$this->kode_validasi}\n\n"
               . "Dari: {$lokasiAsal}\n"
               . "Ke: {$lokasiTujuan}\n"
               . "Tgl Distribusi: {$tanggalDist}\n\n"
               . "Daftar Barang:\n{$listBarang}\n"
               . "Waktu Validasi: {$tanggalJam}\n\n"
               . "Status: Barang diterima dan sesuai.\n\n"
               . "_Validator: [Nama]_";

        return "https://wa.me/{$nomor}?text=" . urlencode($pesan);
    }

    /**
     * Relasi ke LokasiKlinik (Asal)
     */
    public function lokasiAsal(): BelongsTo
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi_asal', 'id');
    }

    /**
     * Relasi ke LokasiKlinik (Tujuan)
     */
    public function lokasiTujuan(): BelongsTo
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi_tujuan', 'id');
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Relasi ke Detail Surat Distribusi
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailSuratDistribusi::class, 'id_surat', 'id_surat');
    }
}
