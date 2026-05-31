<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('raw_materials', 'satuan')) {
            return;
        }

        Schema::table('raw_materials', function (Blueprint $table) {
            $table->string('satuan', 20)->default('kg')->after('jumlah');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('raw_materials', 'satuan')) {
            return;
        }

        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
};
