<?php

namespace App\Services;

use App\Models\ExpenseCategory;
use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\OperationalCost;
use App\Models\RawMaterialRestock;
use Illuminate\Support\Facades\DB;

class OperationalCostService
{
    private const CASH_ACCOUNT = '1-110';

    public function record(array $data): OperationalCost
    {
        return DB::transaction(function () use ($data) {
            $category = ExpenseCategory::findOrFail($data['expense_category_id']);
            $journalId = $this->createJournal(
                $data['tanggal'],
                (int) $data['jumlah'],
                $category,
                $data['desk'] ?? null,
                $data['id']
            );

            return OperationalCost::create([
                'id' => $data['id'],
                'expense_category_id' => $category->id,
                'tanggal' => $data['tanggal'],
                'kat' => $category->nama,
                'desk' => $data['desk'] ?? '',
                'jumlah' => (int) $data['jumlah'],
                'jenis' => $category->jenis,
                'journal_transaction_id' => $journalId,
            ]);
        });
    }

    public function update(OperationalCost $cost, array $data): OperationalCost
    {
        return DB::transaction(function () use ($cost, $data) {
            $category = ExpenseCategory::findOrFail($data['expense_category_id']);
            $this->deleteJournal($cost->journal_transaction_id);

            $journalId = $this->createJournal(
                $data['tanggal'],
                (int) $data['jumlah'],
                $category,
                $data['desk'] ?? null,
                $cost->id
            );

            $cost->update([
                'expense_category_id' => $category->id,
                'tanggal' => $data['tanggal'],
                'kat' => $category->nama,
                'desk' => $data['desk'] ?? '',
                'jumlah' => (int) $data['jumlah'],
                'jenis' => $category->jenis,
                'journal_transaction_id' => $journalId,
            ]);

            return $cost->fresh();
        });
    }

    public function delete(OperationalCost $cost): void
    {
        DB::transaction(function () use ($cost) {
            $this->deleteJournal($cost->journal_transaction_id);
            $cost->delete();
        });
    }

    public function backfillMissingJournals(): int
    {
        $count = 0;

        OperationalCost::query()
            ->whereNull('journal_transaction_id')
            ->with('expenseCategory')
            ->each(function (OperationalCost $cost) use (&$count) {
                $category = $cost->expenseCategory;
                if (! $category) {
                    return;
                }

                $journalId = $this->createJournal(
                    $cost->tanggal->format('Y-m-d'),
                    (int) $cost->jumlah,
                    $category,
                    $cost->desk,
                    $cost->id
                );

                if ($journalId) {
                    $cost->update(['journal_transaction_id' => $journalId]);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * @return array{
     *     fixed: array{rows: array<int, array{label: string, amount: int}>, total: int},
     *     variable: array{rows: array<int, array{label: string, amount: int, from_restock?: bool}>, total: int},
     *     grand_total: int
     * }
     */
    public function monthlySummary(string $from, string $to): array
    {
        $categories = ExpenseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $sums = OperationalCost::query()
            ->whereDate('tanggal', '>=', $from)
            ->whereDate('tanggal', '<=', $to)
            ->selectRaw('expense_category_id, SUM(jumlah) as total')
            ->groupBy('expense_category_id')
            ->pluck('total', 'expense_category_id');

        $restockTotal = (int) RawMaterialRestock::query()
            ->whereDate('tanggal', '>=', $from)
            ->whereDate('tanggal', '<=', $to)
            ->sum('total');

        $fixedRows = [];
        $variableRows = [];

        foreach ($categories as $category) {
            $amount = (int) ($sums[$category->id] ?? 0);
            if ($amount <= 0) {
                continue;
            }

            $row = ['label' => $category->nama, 'amount' => $amount];

            if ($category->jenis === 'Fixed') {
                $fixedRows[] = $row;
            } else {
                $variableRows[] = $row;
            }
        }

        if ($restockTotal > 0) {
            array_unshift($variableRows, [
                'label' => 'Belanja Bahan Baku',
                'amount' => $restockTotal,
                'from_restock' => true,
            ]);
        }

        $fixedTotal = array_sum(array_column($fixedRows, 'amount'));
        $variableTotal = array_sum(array_column($variableRows, 'amount'));

        return [
            'fixed' => [
                'rows' => $fixedRows,
                'total' => $fixedTotal,
            ],
            'variable' => [
                'rows' => $variableRows,
                'total' => $variableTotal,
            ],
            'grand_total' => $fixedTotal + $variableTotal,
        ];
    }

    /**
     * @return array{fixed: int, variable: int, restock: int, operational: int}
     */
    public function periodTotals(string $from, string $to): array
    {
        $fixed = (int) OperationalCost::query()
            ->where('jenis', 'Fixed')
            ->whereDate('tanggal', '>=', $from)
            ->whereDate('tanggal', '<=', $to)
            ->sum('jumlah');

        $variable = (int) OperationalCost::query()
            ->where('jenis', 'Variable')
            ->whereDate('tanggal', '>=', $from)
            ->whereDate('tanggal', '<=', $to)
            ->sum('jumlah');

        $restock = (int) RawMaterialRestock::query()
            ->whereDate('tanggal', '>=', $from)
            ->whereDate('tanggal', '<=', $to)
            ->sum('total');

        return [
            'fixed' => $fixed,
            'variable' => $variable,
            'restock' => $restock,
            'operational' => $fixed + $variable,
        ];
    }

    private function createJournal(
        string $tanggal,
        int $amount,
        ExpenseCategory $category,
        ?string $note,
        string $refId,
    ): ?int {
        if ($amount <= 0) {
            return null;
        }

        $description = 'Biaya operasional '.$category->nama;
        if ($note) {
            $description .= ' — '.$note;
        }

        $tx = JournalTransaction::create([
            'tanggal' => $tanggal,
            'deskripsi' => $description,
            'ref' => 'OPEX-'.$refId,
        ]);

        JournalEntry::create([
            'journal_transaction_id' => $tx->id,
            'account_kode' => $category->account_kode,
            'debit' => $amount,
            'credit' => 0,
        ]);

        JournalEntry::create([
            'journal_transaction_id' => $tx->id,
            'account_kode' => self::CASH_ACCOUNT,
            'debit' => 0,
            'credit' => $amount,
        ]);

        return $tx->id;
    }

    private function deleteJournal(?int $journalId): void
    {
        if (! $journalId) {
            return;
        }

        JournalTransaction::where('id', $journalId)->delete();
    }
}
