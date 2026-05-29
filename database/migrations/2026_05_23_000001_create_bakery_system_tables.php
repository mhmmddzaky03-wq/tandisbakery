<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Production Records Table (source of truth — logged first)
        Schema::create('production_records', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. PRD001
            $table->date('tanggal');
            $table->string('product_name');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->string('status'); // Berhasil / Gagal
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 2. Products Table (registered from production history)
        Schema::create('products', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. P001
            $table->string('production_record_id')->nullable()->unique();
            $table->string('nama');
            $table->string('satuan');
            $table->integer('harga');
            $table->string('status')->default('Aktif'); // Aktif / Non-Aktif
            $table->timestamps();

            $table->foreign('production_record_id')
                ->references('id')
                ->on('production_records')
                ->nullOnDelete();
        });

        // 3. Raw Materials Table
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. SBB001
            $table->string('nama');
            $table->decimal('jumlah', 12, 4)->default(0);
            $table->string('satuan', 20)->default('kg');
            $table->decimal('min', 12, 4)->default(0);
            $table->integer('harga');
            $table->timestamps();
        });

        // 4. Sales Transactions Table
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. TRX001
            $table->date('tanggal');
            $table->integer('total');
            $table->string('metode'); // Cash / Transfer / Mix
            $table->integer('jumlah'); // transaction count
            $table->timestamps();
        });

        // 5. Operational Costs Table
        Schema::create('operational_costs', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. BO001
            $table->date('tanggal');
            $table->string('kat'); // Category: Kemasan, Air, Lainnya, Gaji
            $table->string('desk');
            $table->integer('jumlah');
            $table->string('jenis'); // Fixed / Variable
            $table->timestamps();
        });

        // 6. Accounts (COA) Table
        Schema::create('accounts', function (Blueprint $table) {
            $table->string('kode')->primary(); // e.g. 1-110
            $table->string('nama');
            $table->string('posisi'); // Debit / Credit
            $table->string('grup'); // Current Asset, Non-Current Asset, Liabilities, Equity, Revenue, Expenses
            $table->timestamps();
        });

        // 7. Journal Transactions (Header) Table
        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('deskripsi');
            $table->string('ref')->nullable();
            $table->timestamps();
        });

        // 8. Journal Entries (Double-Entry ledger lines) Table
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_transaction_id');
            $table->string('account_kode');
            $table->integer('debit')->default(0);
            $table->integer('credit')->default(0);
            $table->timestamps();

            $table->foreign('journal_transaction_id')->references('id')->on('journal_transactions')->onDelete('cascade');
            $table->foreign('account_kode')->references('kode')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('journal_transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('operational_costs');
        Schema::dropIfExists('sales_transactions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('production_records');
        Schema::dropIfExists('raw_materials');
    }
};
