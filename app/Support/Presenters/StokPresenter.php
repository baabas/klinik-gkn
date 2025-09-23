<?php

namespace App\Support\Presenters;

use App\Models\BarangKemasan;
use App\Models\BarangMedis;
use Illuminate\Support\Str;

class StokPresenter
{
    public static function format(int $qtyBase, string $baseUnit, ?array $kemasanList = null): string
    {
        $qtyBase = (int) $qtyBase;
        $baseUnitLabel = strtolower(trim($baseUnit) ?: 'unit');
        $formattedBase = number_format($qtyBase) . ' ' . $baseUnitLabel;

        $kemasanCollection = collect($kemasanList ?? [])
            ->map(function ($item) {
                if ($item instanceof BarangKemasan) {
                    return $item;
                }

                return tap(new BarangKemasan(), function (BarangKemasan $kemasan) use ($item) {
                    $kemasan->nama_kemasan = $item['nama_kemasan'] ?? $item['nama'] ?? '';
                    $kemasan->isi_per_kemasan = (int) ($item['isi_per_kemasan'] ?? $item['isi'] ?? 0);
                });
            })
            ->filter(fn (BarangKemasan $kemasan) => (int) $kemasan->isi_per_kemasan > 0)
            ->sortByDesc('isi_per_kemasan')
            ->values();

        if ($kemasanCollection->isEmpty()) {
            return $formattedBase;
        }

        $barangVirtual = new BarangMedis();
        $barangVirtual->setRelation('kemasanBarang', $kemasanCollection);

        $breakdown = $barangVirtual->breakdownPacks($qtyBase);

        $components = [];

        foreach ($kemasanCollection->take(2) as $kemasan) {
            $key = Str::slug($kemasan->nama_kemasan, '_');
            $components[] = number_format((int) ($breakdown[$key] ?? 0)) . ' ' . strtolower($kemasan->nama_kemasan);
        }

        $components[] = number_format((int) ($breakdown['base'] ?? $qtyBase)) . ' ' . $baseUnitLabel;

        return sprintf('%s (≈ %s)', $formattedBase, implode(', ', $components));
    }

    public static function descriptorDefaultPackaging(?BarangKemasan $kemasanDefault, string $baseUnit): string
    {
        if (! $kemasanDefault instanceof BarangKemasan) {
            return '-';
        }

        $baseUnitLabel = strtolower(trim($baseUnit) ?: 'unit');

        return sprintf(
            '%s @ %s %s',
            $kemasanDefault->nama_kemasan,
            number_format((int) $kemasanDefault->isi_per_kemasan),
            $baseUnitLabel
        );
    }
}
