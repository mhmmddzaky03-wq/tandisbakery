<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis'); // Fixed | Variable
            $table->string('account_kode');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('account_kode')->references('kode')->on('accounts')->cascadeOnDelete();
        });

        Schema::table('operational_costs', function (Blueprint $table) {
            $table->foreignId('expense_category_id')->nullable()->after('id')->constrained('expense_categories')->nullOnDelete();
            $table->unsignedBigInteger('journal_transaction_id')->nullable()->after('jenis');
        });
    }

    public function down(): void
    {
        Schema::table('operational_costs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expense_category_id');
            $table->dropColumn('journal_transaction_id');
        });

        Schema::dropIfExists('expense_categories');
    }
};
