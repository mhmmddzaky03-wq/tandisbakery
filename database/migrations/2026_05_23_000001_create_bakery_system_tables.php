<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->string('kode')->primary();
            $table->string('nama');
            $table->string('posisi');
            $table->string('grup');
            $table->string('sub_grup')->nullable();
            $table->timestamps();
        });

        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('deskripsi');
            $table->string('ref')->nullable();
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_transaction_id')->constrained('journal_transactions')->cascadeOnDelete();
            $table->string('account_kode');
            $table->integer('debit')->default(0);
            $table->integer('credit')->default(0);
            $table->timestamps();

            $table->foreign('account_kode')->references('kode')->on('accounts')->restrictOnDelete();
        });

        Schema::create('production_records', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->date('tanggal');
            $table->string('product_name');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->string('status');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('total_material_cost')->default(0);
            $table->foreignId('journal_transaction_id')->nullable()->constrained('journal_transactions')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('production_record_id')->nullable()->unique();
            $table->string('nama');
            $table->string('satuan');
            $table->unsignedInteger('jumlah')->default(0);
            $table->integer('harga');
            $table->timestamps();

            $table->foreign('production_record_id')
                ->references('id')
                ->on('production_records')
                ->nullOnDelete();
        });

        Schema::create('raw_materials', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama');
            $table->string('kategori', 20)->default('padat');
            $table->decimal('jumlah', 12, 4)->default(0);
            $table->string('satuan', 50)->default('kg');
            $table->decimal('min', 12, 4)->default(0);
            $table->integer('harga');
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis');
            $table->string('account_kode');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('account_kode')->references('kode')->on('accounts')->cascadeOnDelete();
        });

        Schema::create('operational_costs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->date('tanggal');
            $table->string('kat');
            $table->string('desk');
            $table->integer('jumlah');
            $table->string('jenis');
            $table->foreignId('journal_transaction_id')->nullable()->constrained('journal_transactions')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->date('tanggal');
            $table->integer('total');
            $table->string('metode');
            $table->integer('jumlah');
            $table->foreignId('journal_transaction_id')->nullable()->constrained('journal_transactions')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_transactions');
        Schema::dropIfExists('operational_costs');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('raw_materials');
        Schema::dropIfExists('products');
        Schema::dropIfExists('production_records');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('journal_transactions');
        Schema::dropIfExists('accounts');
    }
};
