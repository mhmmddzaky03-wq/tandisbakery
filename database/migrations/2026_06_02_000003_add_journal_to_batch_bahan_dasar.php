<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batch_bahan_dasar', function (Blueprint $table) {
            $table->foreignId('journal_transaction_id')
                ->nullable()
                ->after('catatan')
                ->constrained('journal_transactions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('batch_bahan_dasar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('journal_transaction_id');
        });
    }
};
