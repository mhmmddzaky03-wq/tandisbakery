<?php

namespace Database\Seeders;

use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use Illuminate\Database\Seeder;

/**
 * Saldo awal dari Excel — hanya transaksi "Opening Balance", tidak menghapus jurnal operasional lain.
 */
class OpeningBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $snapshot = config('trial_balance_snapshot');
        $accounts = $snapshot['accounts'];
        $asOf = $snapshot['as_of'] ?? '2025-06-30';

        $this->plugCapital($accounts);

        $existing = JournalTransaction::query()
            ->where('ref', 'Opening Balance')
            ->first();

        if ($existing) {
            JournalEntry::query()
                ->where('journal_transaction_id', $existing->id)
                ->delete();
            $existing->delete();
        }

        $tx = JournalTransaction::create([
            'tanggal' => $asOf,
            'deskripsi' => 'Saldo awal per '.date('d-M-Y', strtotime($asOf)),
            'ref' => 'Opening Balance',
        ]);

        foreach ($accounts as $kode => $amounts) {
            if ($amounts['debit'] === 0 && $amounts['credit'] === 0) {
                continue;
            }

            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => $kode,
                'debit' => $amounts['debit'],
                'credit' => $amounts['credit'],
            ]);
        }
    }

    /** @param  array<string, array{debit: int, credit: int}>  $accounts */
    private function plugCapital(array &$accounts): void
    {
        $totalDebit = array_sum(array_column($accounts, 'debit'));
        $totalCredit = array_sum(array_column($accounts, 'credit'));
        $diff = $totalDebit - $totalCredit;

        $accounts['3-110']['debit'] = 0;
        $accounts['3-110']['credit'] = ($accounts['3-110']['credit'] ?? 0) + $diff;
    }
}
