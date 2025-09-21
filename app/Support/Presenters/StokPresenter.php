<?php

namespace App\Support\Presenters;

use App\Models\BarangMedis;

class StokPresenter
{
    public static function formatWithDefault(BarangMedis $barang, ?int $totalUnits = null): string
    {
        $totalUnits = $totalUnits ?? (int) $barang->stok;
        $satuanDasar = trim((string) $barang->satuan_dasar);
        $satuanLabel = $satuanDasar !== '' ? strtolower($satuanDasar) : 'unit';

        $formatted = number_format($totalUnits) . ' ' . $satuanLabel;

        $defaultKemasan = $barang->relationLoaded('defaultKemasan')
            ? $barang->defaultKemasan
            : $barang->defaultKemasan()->first();

        if ($defaultKemasan && $defaultKemasan->isi_per_kemasan > 0) {
            $ratio = $totalUnits / $defaultKemasan->isi_per_kemasan;
            $approx = $ratio == (int) $ratio
                ? number_format((int) $ratio)
                : number_format($ratio, 2);

            $formatted .= ' (≈ ' . $approx . ' ' . strtolower($defaultKemasan->nama_kemasan)
                . ' @ ' . number_format($defaultKemasan->isi_per_kemasan) . ' ' . $satuanLabel . ')';
        }

        return $formatted;
    }
}
