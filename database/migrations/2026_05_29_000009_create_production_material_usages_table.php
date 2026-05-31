<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_records', function (Blueprint $table) {
            if (! Schema::hasColumn('production_records', 'total_material_cost')) {
                $table->unsignedBigInteger('total_material_cost')->default(0)->after('notes');
            }
            if (! Schema::hasColumn('production_records', 'journal_transaction_id')) {
                $table->foreignId('journal_transaction_id')->nullable()->after('total_material_cost')
                    ->constrained('journal_transactions')->nullOnDelete();
            }
        });

        if (Schema::hasTable('production_material_usages')) {
            return;
        }

        Schema::create('production_material_usages', function (Blueprint $table) {
            $table->id();
            $table->string('production_record_id');
            $table->string('raw_material_id');
            $table->decimal('jumlah', 12, 1);
            $table->string('satuan', 50);
            $table->unsignedInteger('harga_satuan');
            $table->unsignedBigInteger('total');
            $table->timestamps();

            $table->foreign('production_record_id')
                ->references('id')
                ->on('production_records')
                ->cascadeOnDelete();

            $table->foreign('raw_material_id')
                ->references('id')
                ->on('raw_materials')
                ->restrictOnDelete();

            $table->unique(['production_record_id', 'raw_material_id'], 'prod_mat_usage_rec_mat_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_material_usages');

        Schema::table('production_records', function (Blueprint $table) {
            if (Schema::hasColumn('production_records', 'journal_transaction_id')) {
                $table->dropForeign(['journal_transaction_id']);
                $table->dropColumn('journal_transaction_id');
            }
            if (Schema::hasColumn('production_records', 'total_material_cost')) {
                $table->dropColumn('total_material_cost');
            }
        });
    }
};
