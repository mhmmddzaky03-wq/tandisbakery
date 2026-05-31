<?php

use App\Models\SalesTransaction;
use App\Services\SalesJournalService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->foreignId('journal_transaction_id')
                ->nullable()
                ->after('jumlah')
                ->constrained('journal_transactions')
                ->nullOnDelete();
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['account_kode']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreign('account_kode')
                ->references('kode')
                ->on('accounts')
                ->restrictOnDelete();
        });

        app(SalesJournalService::class)->backfillMissing();
    }

    public function down(): void
    {
        SalesTransaction::query()
            ->whereNotNull('journal_transaction_id')
            ->update(['journal_transaction_id' => null]);

        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('journal_transaction_id');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['account_kode']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreign('account_kode')
                ->references('kode')
                ->on('accounts')
                ->cascadeOnDelete();
        });
    }
};
