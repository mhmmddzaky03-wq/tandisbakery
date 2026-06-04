<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_restocks', function (Blueprint $table) {
            $table->id();
            $table->string('raw_material_id');
            $table->date('tanggal');
            $table->string('kode_produksi', 100)->nullable();
            $table->date('expired')->nullable();
            $table->decimal('jumlah', 12, 1);
            $table->decimal('sisa', 12, 1)->default(0);
            $table->unsignedInteger('harga');
            $table->unsignedBigInteger('total');
            $table->string('catatan')->nullable();
            $table->foreignId('journal_transaction_id')->nullable()->constrained('journal_transactions')->nullOnDelete();
            $table->timestamps();

            $table->foreign('raw_material_id')
                ->references('id')
                ->on('raw_materials')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_restocks');
    }
};
