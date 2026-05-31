<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['raw_materials', 'production_material_usages'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'satuan')) {
                DB::table($table)->where('satuan', 'liter')->update(['satuan' => 'L']);
            }
        }

        if (! Schema::hasTable('units')) {
            return;
        }

        $hasL = DB::table('units')->where('nama', 'L')->exists();
        $hasLiter = DB::table('units')->where('nama', 'liter')->exists();

        if ($hasLiter && ! $hasL) {
            DB::table('units')->where('nama', 'liter')->update(['nama' => 'L']);
        } elseif ($hasLiter && $hasL) {
            DB::table('units')->where('nama', 'liter')->delete();
        }
    }

    public function down(): void
    {
        foreach (['raw_materials', 'production_material_usages'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'satuan')) {
                DB::table($table)->where('satuan', 'L')->update(['satuan' => 'liter']);
            }
        }

        if (! Schema::hasTable('units')) {
            return;
        }

        if (DB::table('units')->where('nama', 'L')->exists() && ! DB::table('units')->where('nama', 'liter')->exists()) {
            DB::table('units')->where('nama', 'L')->update(['nama' => 'liter']);
        }
    }
};
