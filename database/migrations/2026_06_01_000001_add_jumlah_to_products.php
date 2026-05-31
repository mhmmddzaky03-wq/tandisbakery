<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'jumlah')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('jumlah')->default(0)->after('satuan');
            });
        }

        if (Schema::hasTable('products') && Schema::hasTable('production_records')) {
            $products = DB::table('products')->get(['id', 'nama']);

            foreach ($products as $product) {
                $stock = (int) DB::table('production_records')
                    ->where('status', 'Berhasil')
                    ->whereRaw('LOWER(TRIM(product_name)) = ?', [mb_strtolower(trim($product->nama))])
                    ->sum('jumlah');

                DB::table('products')->where('id', $product->id)->update(['jumlah' => $stock]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'jumlah')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('jumlah');
            });
        }
    }
};
