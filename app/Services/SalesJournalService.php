<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\SalesTransaction;
use Illuminate\Support\Facades\DB;

class SalesJournalService
{
    private const CASH_ACCOUNT = '1-110';

    private const SALES_ACCOUNT = '4-110';

    public function sync(SalesTransaction $sale): void
    {
        DB::transaction(function () use ($sale) {
            $this->deleteJournal($sale->journal_transaction_id);

            if ((int) $sale->total <= 0) {
                $sale->update(['journal_transaction_id' => null]);

                return;
            }

            $tx = JournalTransaction::create([
                'tanggal' => $sale->tanggal,
                'deskripsi' => 'Penjualan '.$sale->metode.' — '.$sale->jumlah.' transaksi',
                'ref' => 'SALES-'.$sale->id,
            ]);

            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::CASH_ACCOUNT,
                'debit' => (int) $sale->total,
                'credit' => 0,
            ]);

            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::SALES_ACCOUNT,
                'debit' => 0,
                'credit' => (int) $sale->total,
            ]);

            $sale->update(['journal_transaction_id' => $tx->id]);
        });
    }

    public function deleteForSale(SalesTransaction $sale): void
    {
        $this->deleteJournal($sale->journal_transaction_id);
    }

    public function backfillMissing(): int
    {
        $count = 0;

        SalesTransaction::query()
            ->whereNull('journal_transaction_id')
            ->orderBy('id')
            ->each(function (SalesTransaction $sale) use (&$count) {
                $this->sync($sale);
                $count++;
            });

        return $count;
    }

    private function deleteJournal(?int $journalId): void
    {
        if (! $journalId) {
            return;
        }

        JournalTransaction::where('id', $journalId)->delete();
    }
}
