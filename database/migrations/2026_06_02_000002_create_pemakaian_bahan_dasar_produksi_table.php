<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemakaian_bahan_dasar_produksi', function (Blueprint $table) {
            $table->id();
            $table->string('production_record_id');
            $table->foreignId('batch_bahan_dasar_id')->constrained('batch_bahan_dasar');
            $table->string('bahan_dasar_id');
            $table->decimal('jumlah', 14, 4);
            $table->string('satuan', 50);
            $table->unsignedBigInteger('harga_satuan')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->timestamps();

            $table->foreign('production_record_id')
                ->references('id')
                ->on('production_records')
                ->cascadeOnDelete();

            $table->foreign('bahan_dasar_id')
                ->references('id')
                ->on('bahan_dasar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemakaian_bahan_dasar_produksi');
    }
};
