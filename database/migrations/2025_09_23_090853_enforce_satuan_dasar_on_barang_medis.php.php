<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_medis', function (Blueprint $table) {
            if (! Schema::hasColumn('barang_medis', 'satuan_dasar')) {
                $table->string('satuan_dasar', 50)->default('')->after('tipe');
            }
        });

        DB::table('barang_medis')->whereNotNull('satuan_dasar')->update([
            'satuan_dasar' => DB::raw('LOWER(TRIM(satuan_dasar))'),
        ]);

        DB::table('barang_medis')
            ->whereNotIn('satuan_dasar', ['kaplet', 'tablet', 'kapsul', 'pcs'])
            ->update(['satuan_dasar' => 'pcs']);

        DB::statement("ALTER TABLE barang_medis ADD CONSTRAINT chk_barang_medis_satuan_dasar CHECK (satuan_dasar IN ('kaplet','tablet','kapsul','pcs'))");
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE barang_medis DROP CHECK chk_barang_medis_satuan_dasar');
        } catch (Throwable $e) {
            // noop for databases that do not support dropping named checks
        }
    }
};
