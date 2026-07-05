<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE permintaan_barang MODIFY status ENUM('PENDING', 'APPROVED', 'REJECTED', 'PROCESSING', 'COMPLETED', 'RETUR') DEFAULT 'PENDING'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE permintaan_barang MODIFY status ENUM('PENDING', 'APPROVED', 'REJECTED', 'PROCESSING', 'COMPLETED') DEFAULT 'PENDING'");
        }
    }
};
