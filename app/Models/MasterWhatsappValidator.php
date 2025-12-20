<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterWhatsappValidator extends Model
{
    protected $fillable = [
        'nama_validator',
        'nomor_wa',
        'keterangan',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk hanya mengambil validator yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Format nomor WhatsApp untuk ditampilkan
     */
    public function getFormattedNomorWaAttribute()
    {
        return $this->nomor_wa;
    }
}
