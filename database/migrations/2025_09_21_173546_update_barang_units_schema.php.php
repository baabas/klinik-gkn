<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang_medis', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_medis', 'satuan_dasar')) {
                $table->string('satuan_dasar', 50)->default('')->after('tipe');
            }
        });

        if (Schema::hasColumn('barang_medis', 'satuan')) {
            DB::table('barang_medis')->update([
                'satuan_dasar' => DB::raw("COALESCE(satuan, '')"),
            ]);

            Schema::table('barang_medis', function (Blueprint $table) {
                $table->dropColumn('satuan');
            });
        }

        Schema::table('barang_medis', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_medis', 'stok')) {
                $table->unsignedBigInteger('stok')->default(0)->after('kategori');
            }
        });

        DB::table('barang_medis')->update([
            'stok' => DB::raw('(
                SELECT COALESCE(SUM(sb.jumlah), 0)
                FROM stok_barang sb
                WHERE sb.id_barang = barang_medis.id_obat
            )')
        ]);

        if (!Schema::hasTable('barang_kemasan')) {
            Schema::create('barang_kemasan', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('barang_id');
                $table->string('nama_kemasan', 100);
                $table->unsignedInteger('isi_per_kemasan');
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->foreign('barang_id')
                    ->references('id_obat')
                    ->on('barang_medis')
                    ->onDelete('cascade');

                $table->index(['barang_id', 'is_default']);
            });
        }

        Schema::table('stok_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('stok_histories', 'kemasan_id')) {
                $table->foreignId('kemasan_id')
                    ->nullable()
                    ->after('satuan_kemasan')
                    ->constrained('barang_kemasan')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('stok_histories', 'base_unit')) {
                $table->string('base_unit', 50)->nullable()->after('kemasan_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_histories', function (Blueprint $table) {
            if (Schema::hasColumn('stok_histories', 'kemasan_id')) {
                $table->dropForeign(['kemasan_id']);
                $table->dropColumn('kemasan_id');
            }

            if (Schema::hasColumn('stok_histories', 'base_unit')) {
                $table->dropColumn('base_unit');
            }
        });

        Schema::dropIfExists('barang_kemasan');

        if (!Schema::hasColumn('barang_medis', 'satuan')) {
            Schema::table('barang_medis', function (Blueprint $table) {
                $table->string('satuan', 50)->default('')->after('tipe');
            });

            DB::table('barang_medis')->update([
                'satuan' => DB::raw("COALESCE(satuan_dasar, '')"),
            ]);

            Schema::table('barang_medis', function (Blueprint $table) {
                $table->dropColumn('satuan_dasar');
            });
        }

        if (Schema::hasColumn('barang_medis', 'stok')) {
            Schema::table('barang_medis', function (Blueprint $table) {
                $table->dropColumn('stok');
            });
        }
    }
};
