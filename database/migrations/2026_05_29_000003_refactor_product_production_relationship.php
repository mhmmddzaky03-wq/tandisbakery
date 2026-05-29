<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('production_records', 'product_id')) {
            Schema::table('production_records', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            });
        }

        if (! Schema::hasColumn('products', 'production_record_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('production_record_id')->nullable()->unique()->after('id');
                $table->foreign('production_record_id')
                    ->references('id')
                    ->on('production_records')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'production_record_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['production_record_id']);
                $table->dropColumn('production_record_id');
            });
        }

        if (! Schema::hasColumn('production_records', 'product_id')) {
            Schema::table('production_records', function (Blueprint $table) {
                $table->string('product_id')->nullable()->after('tanggal');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            });
        }
    }
};
