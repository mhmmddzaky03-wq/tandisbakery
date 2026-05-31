<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_material_restocks', function (Blueprint $table) {
            if (! Schema::hasColumn('raw_material_restocks', 'kode_produksi')) {
                $table->string('kode_produksi', 100)->nullable()->after('tanggal');
            }
            if (! Schema::hasColumn('raw_material_restocks', 'expired')) {
                $table->date('expired')->nullable()->after('kode_produksi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('raw_material_restocks', function (Blueprint $table) {
            if (Schema::hasColumn('raw_material_restocks', 'expired')) {
                $table->dropColumn('expired');
            }
            if (Schema::hasColumn('raw_material_restocks', 'kode_produksi')) {
                $table->dropColumn('kode_produksi');
            }
        });
    }
};
