<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_dasar', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama');
            $table->decimal('jumlah', 14, 4)->default(0);
            $table->string('satuan', 50)->default('g');
            $table->decimal('min', 14, 4)->default(0);
            $table->unsignedBigInteger('harga')->default(0);
            $table->timestamps();
        });

        Schema::create('batch_bahan_dasar', function (Blueprint $table) {
            $table->id();
            $table->string('bahan_dasar_id');
            $table->date('tanggal');
            $table->decimal('jumlah', 14, 4);
            $table->decimal('sisa', 14, 4);
            $table->unsignedBigInteger('total_biaya')->default(0);
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->foreign('bahan_dasar_id')
                ->references('id')
                ->on('bahan_dasar')
                ->cascadeOnDelete();
        });

        Schema::create('pemakaian_bahan_baku_adonan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_bahan_dasar_id')->constrained('batch_bahan_dasar')->cascadeOnDelete();
            $table->string('raw_material_id');
            $table->foreignId('raw_material_restock_id')->nullable()->constrained('raw_material_restocks')->nullOnDelete();
            $table->decimal('jumlah', 14, 4);
            $table->string('satuan', 50);
            $table->unsignedBigInteger('harga_satuan')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->timestamps();

            $table->foreign('raw_material_id')
                ->references('id')
                ->on('raw_materials')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemakaian_bahan_baku_adonan');
        Schema::dropIfExists('batch_bahan_dasar');
        Schema::dropIfExists('bahan_dasar');
    }
};
