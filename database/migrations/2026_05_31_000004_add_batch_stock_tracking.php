<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_material_restocks', function (Blueprint $table) {
            if (! Schema::hasColumn('raw_material_restocks', 'sisa')) {
                $table->decimal('sisa', 12, 1)->default(0)->after('jumlah');
            }
        });

        Schema::table('production_material_usages', function (Blueprint $table) {
            if (! Schema::hasColumn('production_material_usages', 'raw_material_restock_id')) {
                $table->unsignedBigInteger('raw_material_restock_id')->nullable()->after('raw_material_id');
                $table->foreign('raw_material_restock_id')
                    ->references('id')
                    ->on('raw_material_restocks')
                    ->nullOnDelete();
            }
        });

        if (! Schema::hasColumn('raw_material_restocks', 'sisa')) {
            return;
        }

        foreach (DB::table('raw_materials')->orderBy('id')->get() as $material) {
            $remaining = (float) $material->jumlah;

            $restocks = DB::table('raw_material_restocks')
                ->where('raw_material_id', $material->id)
                ->orderBy('tanggal')
                ->orderBy('id')
                ->get();

            foreach ($restocks as $restock) {
                $allocated = min((float) $restock->jumlah, max(0, $remaining));
                DB::table('raw_material_restocks')->where('id', $restock->id)->update(['sisa' => $allocated]);
                $remaining -= $allocated;
            }

            if ($remaining > 0 && $restocks->isNotEmpty()) {
                $last = $restocks->last();
                DB::table('raw_material_restocks')
                    ->where('id', $last->id)
                    ->update(['sisa' => (float) $last->jumlah + $remaining]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('production_material_usages', function (Blueprint $table) {
            if (Schema::hasColumn('production_material_usages', 'raw_material_restock_id')) {
                $table->dropForeign(['raw_material_restock_id']);
                $table->dropColumn('raw_material_restock_id');
            }
        });

        Schema::table('raw_material_restocks', function (Blueprint $table) {
            if (Schema::hasColumn('raw_material_restocks', 'sisa')) {
                $table->dropColumn('sisa');
            }
        });
    }
};
