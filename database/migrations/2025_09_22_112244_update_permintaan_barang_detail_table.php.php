<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permintaan_barang_detail')) {
            return;
        }

        if (! Schema::hasColumn('permintaan_barang_detail', 'kemasan_id')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->foreignId('kemasan_id')
                    ->nullable()
                    ->after('barang_id')
                    ->constrained('barang_kemasan')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('permintaan_barang_detail', 'jumlah_kemasan')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->unsignedInteger('jumlah_kemasan')
                    ->nullable()
                    ->after('kemasan_id');
            });
        }

        if (! Schema::hasColumn('permintaan_barang_detail', 'isi_per_kemasan')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->unsignedInteger('isi_per_kemasan')
                    ->nullable()
                    ->after('jumlah_kemasan');
            });
        }

        if (! Schema::hasColumn('permintaan_barang_detail', 'satuan_kemasan')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->string('satuan_kemasan')
                    ->nullable()
                    ->after('isi_per_kemasan');
            });
        }

        if (! Schema::hasColumn('permintaan_barang_detail', 'total_unit_dasar')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->unsignedBigInteger('total_unit_dasar')
                    ->nullable()
                    ->after('satuan_kemasan');
            });
        }

        if (! Schema::hasColumn('permintaan_barang_detail', 'base_unit')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->string('base_unit')
                    ->nullable()
                    ->after('total_unit_dasar');
            });
        }

        DB::table('permintaan_barang_detail')
            ->whereNotNull('barang_id')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                $kemasanIds = collect($rows)
                    ->pluck('barang_kemasan_id')
                    ->filter()
                    ->unique();
                $barangIds = collect($rows)
                    ->pluck('barang_id')
                    ->filter()
                    ->unique();

                $kemasanData = $kemasanIds->isNotEmpty()
                    ? DB::table('barang_kemasan')->whereIn('id', $kemasanIds)->get()->keyBy('id')
                    : collect();
                $barangData = $barangIds->isNotEmpty()
                    ? DB::table('barang_medis')->whereIn('id_obat', $barangIds)->get()->keyBy('id_obat')
                    : collect();

                foreach ($rows as $row) {
                    $kemasan = $kemasanData[$row->barang_kemasan_id] ?? null;
                    $barang = $barangData[$row->barang_id] ?? null;

                    $jumlahKemasan = is_null($row->jumlah) ? null : (int) $row->jumlah;
                    $isi = $kemasan->isi_per_kemasan ?? null;
                    $totalUnit = $row->total_unit ?? null;

                    if ($totalUnit === null && $jumlahKemasan !== null && $isi !== null) {
                        $totalUnit = $jumlahKemasan * $isi;
                    }

                    DB::table('permintaan_barang_detail')
                        ->where('id', $row->id)
                        ->update([
                            'kemasan_id' => $row->barang_kemasan_id,
                            'jumlah_kemasan' => $jumlahKemasan,
                            'isi_per_kemasan' => $isi,
                            'satuan_kemasan' => $kemasan->nama_kemasan ?? $row->kemasan,
                            'total_unit_dasar' => $totalUnit,
                            'base_unit' => $barang->satuan_dasar ?? $row->satuan,
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('permintaan_barang_detail')) {
            return;
        }

        if (Schema::hasColumn('permintaan_barang_detail', 'base_unit')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->dropColumn('base_unit');
            });
        }

        if (Schema::hasColumn('permintaan_barang_detail', 'total_unit_dasar')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->dropColumn('total_unit_dasar');
            });
        }

        if (Schema::hasColumn('permintaan_barang_detail', 'satuan_kemasan')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->dropColumn('satuan_kemasan');
            });
        }

        if (Schema::hasColumn('permintaan_barang_detail', 'isi_per_kemasan')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->dropColumn('isi_per_kemasan');
            });
        }

        if (Schema::hasColumn('permintaan_barang_detail', 'jumlah_kemasan')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->dropColumn('jumlah_kemasan');
            });
        }

        if (Schema::hasColumn('permintaan_barang_detail', 'kemasan_id')) {
            Schema::table('permintaan_barang_detail', function (Blueprint $table) {
                $table->dropConstrainedForeignId('kemasan_id');
            });
        }
    }
};
