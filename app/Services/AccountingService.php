<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\OperationalCost;
use App\Models\ProductionRecord;
use App\Models\RawMaterialRestock;
use App\Models\SalesTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AccountingService
{
    public function accountBalance(string $kode): int
    {
        return $this->accountBalanceAsOf($kode, null);
    }

    public function accountsWithBalances(?string $search = null): Collection
    {
        $query = Account::query()->orderBy('kode');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('grup', 'like', "%{$search}%");
            });
        }

        return $query->get()->map(function (Account $account) {
            return [
                'account' => $account,
                'saldo' => $this->accountBalance($account->kode),
            ];
        });
    }

    /**
     * @return Collection<int, array{transaction: JournalTransaction, entries: Collection}>
     */
    /**
     * @return array<string, string>
     */
    public function journalSourceOptions(): array
    {
        return [
            '' => __('reports.journal.all_sources'),
            'penjualan' => __('reports.journal.source_sales'),
            'operasional' => __('reports.journal.source_operational'),
            'produksi' => __('reports.journal.source_production'),
            'restock' => __('reports.journal.source_restock'),
            'saldo_awal' => __('reports.journal.source_opening'),
            'manual' => __('reports.journal.source_manual'),
        ];
    }

    public function journalSourceFilterLabel(?string $source): string
    {
        $options = $this->journalSourceOptions();

        return ($source && isset($options[$source])) ? $options[$source] : '';
    }

    public function journalGroups(?string $source = null, ?string $from = null, ?string $to = null): Collection
    {
        $query = JournalTransaction::with(['entries.account'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        $this->applyJournalTransactionFilters($query, $source, $from, $to);

        return $query->get()->groupBy(fn ($tx) => $tx->tanggal->format('Y-m-d'))->map(function ($group, $date) {
            $carbon = Carbon::parse($date);

            return [
                'tanggal' => $carbon->translatedFormat('j F Y'),
                'hari' => $carbon->translatedFormat('l'),
                'transactions' => $group->map(function (JournalTransaction $tx) {
                    $entries = $tx->entries;

                    return [
                        'id' => $tx->id,
                        'ref' => $tx->ref ?? '',
                        'deskripsi' => $tx->deskripsi,
                        'source' => $this->journalSource($tx->ref),
                        'can_delete' => $this->canDeleteJournal($tx->id, $tx->ref),
                        'entries' => $entries->map(fn ($entry) => [
                            'akun' => $entry->account_kode,
                            'nama_akun' => $entry->account?->nama ?? '',
                            'debit' => $entry->debit > 0 ? (int) $entry->debit : 0,
                            'kredit' => $entry->credit > 0 ? (int) $entry->credit : 0,
                        ]),
                        'total_debit' => (int) $entries->sum('debit'),
                        'total_kredit' => (int) $entries->sum('credit'),
                    ];
                })->values(),
                'total_debit' => $group->sum(fn ($tx) => $tx->entries->sum('debit')),
                'total_kredit' => $group->sum(fn ($tx) => $tx->entries->sum('credit')),
            ];
        })->values();
    }

    public function journalDeleteBlockedReason(int $journalId): ?string
    {
        if (SalesTransaction::where('journal_transaction_id', $journalId)->exists()) {
            return __('messages.journal.from_sales');
        }

        if (OperationalCost::where('journal_transaction_id', $journalId)->exists()) {
            return __('messages.journal.from_operational');
        }

        if (ProductionRecord::where('journal_transaction_id', $journalId)->exists()) {
            return __('messages.journal.from_production');
        }

        if (RawMaterialRestock::where('journal_transaction_id', $journalId)->exists()) {
            return __('messages.journal.from_restock');
        }

        $tx = JournalTransaction::find($journalId);
        if ($tx && $tx->ref === 'Opening Balance') {
            return __('messages.journal.opening_balance_locked');
        }

        return null;
    }

    /**
     * @return array{label: string, tone: string}
     */
    public function journalSource(?string $ref): array
    {
        $ref = $ref ?? '';

        if (str_starts_with($ref, 'SALES-')) {
            return ['label' => __('reports.journal.source_sales'), 'tone' => 'emerald'];
        }
        if (str_starts_with($ref, 'OPEX-')) {
            return ['label' => __('reports.journal.source_operational'), 'tone' => 'sky'];
        }
        if (str_starts_with($ref, 'PROD-')) {
            return ['label' => __('reports.journal.source_production'), 'tone' => 'violet'];
        }
        if (str_starts_with($ref, 'RESTOCK-')) {
            return ['label' => __('reports.journal.source_restock'), 'tone' => 'amber'];
        }
        if ($ref === 'Opening Balance') {
            return ['label' => __('reports.journal.source_opening'), 'tone' => 'slate'];
        }

        return ['label' => __('reports.journal.source_manual'), 'tone' => 'slate'];
    }

    public function canDeleteJournal(int $journalId, ?string $ref = null): bool
    {
        return $this->journalDeleteBlockedReason($journalId) === null;
    }

    public function journalTotals(?string $source = null, ?string $from = null, ?string $to = null): array
    {
        $query = JournalEntry::query()->whereHas('transaction', function ($q) use ($source, $from, $to) {
            $this->applyJournalTransactionFilters($q, $source, $from, $to);
        });

        $txQuery = JournalTransaction::query();
        $this->applyJournalTransactionFilters($txQuery, $source, $from, $to);

        return [
            'debit' => (int) (clone $query)->sum('debit'),
            'kredit' => (int) (clone $query)->sum('credit'),
            'transaksi' => (int) $txQuery->count(),
        ];
    }

    private function applyJournalTransactionFilters($query, ?string $source, ?string $from, ?string $to): void
    {
        if ($from) {
            $query->whereDate('tanggal', '>=', $from);
        }

        if ($to) {
            $query->whereDate('tanggal', '<=', $to);
        }

        if ($source) {
            $this->applyJournalSourceFilter($query, $source);
        }
    }

    private function applyJournalSourceFilter($query, string $source): void
    {
        match ($source) {
            'penjualan' => $query->where('ref', 'like', 'SALES-%'),
            'operasional' => $query->where('ref', 'like', 'OPEX-%'),
            'produksi' => $query->where('ref', 'like', 'PROD-%'),
            'restock' => $query->where('ref', 'like', 'RESTOCK-%'),
            'saldo_awal' => $query->where('ref', 'Opening Balance'),
            'manual' => $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNull('ref')->orWhere('ref', '');
                })->orWhere(function ($q2) {
                    $q2->where('ref', 'not like', 'SALES-%')
                        ->where('ref', 'not like', 'OPEX-%')
                        ->where('ref', 'not like', 'PROD-%')
                        ->where('ref', 'not like', 'RESTOCK-%')
                        ->where('ref', '!=', 'Opening Balance');
                });
            }),
            default => null,
        };
    }

    public function generalLedger(string $accountKode, ?string $from = null, ?string $to = null): array
    {
        $account = Account::findOrFail($accountKode);

        $openingBalance = $from ? $this->accountBalanceBefore($accountKode, $from) : 0;

        $entries = JournalEntry::with('transaction')
            ->where('account_kode', $accountKode)
            ->whereHas('transaction', function ($q) use ($from, $to) {
                if ($from) {
                    $q->whereDate('tanggal', '>=', $from);
                }
                if ($to) {
                    $q->whereDate('tanggal', '<=', $to);
                }
            })
            ->get()
            ->sortBy(fn (JournalEntry $entry) => [
                $entry->transaction?->tanggal?->format('Y-m-d') ?? '',
                $entry->id,
            ])
            ->values();

        $rows = collect();
        $running = $openingBalance;

        if ($from) {
            $rows->push([
                'no' => '',
                'tgl' => $from,
                'ref' => 'BEG. BALANCE',
                'debit' => 0,
                'kredit' => 0,
                'saldo' => $openingBalance,
                'is_opening' => true,
            ]);
        }

        foreach ($entries as $index => $entry) {
            $debit = (int) $entry->debit;
            $credit = (int) $entry->credit;

            if ($account->posisi === 'Debit') {
                $running += $debit - $credit;
            } else {
                $running += $credit - $debit;
            }

            $rows->push([
                'no' => $index + 1,
                'tgl' => $entry->transaction?->tanggal?->toDateString(),
                'ref' => $entry->transaction?->ref ?: ($entry->transaction?->deskripsi ?? '-'),
                'debit' => $debit,
                'kredit' => $credit,
                'saldo' => $running,
                'is_opening' => false,
            ]);
        }

        return [
            'account' => $account,
            'rows' => $rows,
            'opening_balance' => $openingBalance,
            'closing_balance' => $running,
            'from' => $from,
            'to' => $to,
        ];
    }

    public function accountBalanceBefore(string $kode, string $beforeDate): int
    {
        $account = Account::find($kode);
        if (! $account) {
            return 0;
        }

        $query = JournalEntry::query()
            ->where('account_kode', $kode)
            ->whereHas('transaction', fn ($q) => $q->whereDate('tanggal', '<', $beforeDate));

        $debit = (int) (clone $query)->sum('debit');
        $credit = (int) (clone $query)->sum('credit');

        return $account->posisi === 'Debit'
            ? $debit - $credit
            : $credit - $debit;
    }

    public function trialBalance(?string $asOf = null): Collection
    {
        $asOf = $asOf ?? config('trial_balance_snapshot.as_of', now()->toDateString());

        return Account::orderBy('kode')->get()->map(function (Account $account) use ($asOf) {
            $query = JournalEntry::query()
                ->where('account_kode', $account->kode)
                ->whereHas('transaction', fn ($q) => $q->whereDate('tanggal', '<=', $asOf));

            $rawDebit = (int) (clone $query)->sum('debit');
            $rawCredit = (int) (clone $query)->sum('credit');

            if ($rawDebit > 0 && $rawCredit === 0) {
                $tbDebit = $rawDebit;
                $tbCredit = 0;
            } elseif ($rawCredit > 0 && $rawDebit === 0) {
                $tbDebit = 0;
                $tbCredit = $rawCredit;
            } else {
                $balance = $this->accountBalanceAsOf($account->kode, $asOf);
                if ($balance >= 0) {
                    $tbDebit = $account->posisi === 'Debit' ? $balance : 0;
                    $tbCredit = $account->posisi === 'Credit' ? $balance : 0;
                } else {
                    $tbDebit = $account->posisi === 'Credit' ? abs($balance) : 0;
                    $tbCredit = $account->posisi === 'Debit' ? abs($balance) : 0;
                }
            }

            $subTotal = $rawDebit - $rawCredit;
            $forFs = $account->posisi === 'Debit' ? $subTotal : -$subTotal;

            return [
                'account' => $account,
                'debit' => $tbDebit,
                'kredit' => $tbCredit,
                'sub_total' => $subTotal,
                'for_fs' => $forFs,
            ];
        });
    }

    public function accountBalanceAsOf(string $kode, ?string $asOf = null): int
    {
        $account = Account::find($kode);
        if (! $account) {
            return 0;
        }

        $query = JournalEntry::query()
            ->where('account_kode', $kode);

        if ($asOf) {
            $query->whereHas('transaction', fn ($q) => $q->whereDate('tanggal', '<=', $asOf));
        }

        $debit = (int) (clone $query)->sum('debit');
        $credit = (int) (clone $query)->sum('credit');

        return $account->posisi === 'Debit'
            ? $debit - $credit
            : $credit - $debit;
    }

    public function incomeStatement(?string $from = null, ?string $to = null): array
    {
        $revenueLines = $this->incomeStatementLines('Revenues', $from, $to);
        $expenseLines = $this->incomeStatementLines('Expenses', $from, $to);

        $totalRevenue = (int) $revenueLines->sum('amount');
        $cogs = (int) $expenseLines->where('kode', '5-110')->sum('amount');
        $tax = (int) $expenseLines->where('kode', '5-190')->sum('amount');

        $operatingExpenseLines = $expenseLines
            ->reject(fn (array $row) => in_array($row['kode'], ['5-110', '5-190'], true))
            ->values();

        $totalOperatingExpenses = (int) $operatingExpenseLines->sum('amount');
        $grossProfit = $totalRevenue - $cogs;
        $incomeBeforeTax = $grossProfit - $totalOperatingExpenses;
        $netProfit = $incomeBeforeTax - $tax;

        return [
            'revenue_lines' => $revenueLines,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'operating_expense_lines' => $operatingExpenseLines,
            'total_operating_expenses' => $totalOperatingExpenses,
            'income_before_tax' => $incomeBeforeTax,
            'tax' => $tax,
            'net_profit' => $netProfit,
            'sales' => $totalRevenue,
            'net_sales' => $totalRevenue,
            'expenses' => $cogs + $totalOperatingExpenses + $tax,
        ];
    }

    /**
     * @return Collection<int, array{kode: string, nama: string, amount: int}>
     */
    private function incomeStatementLines(string $grup, ?string $from, ?string $to): Collection
    {
        return Account::where('grup', $grup)
            ->orderBy('kode')
            ->get()
            ->map(fn (Account $account) => [
                'kode' => $account->kode,
                'nama' => $account->nama,
                'amount' => $this->accountAmountInPeriod($account->kode, $from, $to),
            ])
            ->filter(fn (array $row) => $row['amount'] !== 0)
            ->values();
    }

    private function accountAmountInPeriod(string $kode, ?string $from, ?string $to): int
    {
        $account = Account::find($kode);
        if (! $account) {
            return 0;
        }

        $query = JournalEntry::query()->where('account_kode', $kode);

        if ($from || $to) {
            $query->whereHas('transaction', function ($q) use ($from, $to) {
                if ($from) {
                    $q->whereDate('tanggal', '>=', $from);
                }
                if ($to) {
                    $q->whereDate('tanggal', '<=', $to);
                }
            });
        }

        $debit = (int) (clone $query)->sum('debit');
        $credit = (int) (clone $query)->sum('credit');

        return $account->grup === 'Revenues'
            ? $credit - $debit
            : $debit - $credit;
    }

    public function balanceSheet(?string $asOf = null): array
    {
        $assets = $this->balanceSheetGroupSections(
            ['Current Asset', 'Non-Current Asset'],
            $asOf
        );
        $liabilities = $this->balanceSheetGroupSections(
            ['Current Liability'],
            $asOf
        );
        $equity = $this->balanceSheetGroupSections(
            ['Paid-In-Capital', 'Retained Earnings'],
            $asOf
        );

        $totalAssets = (int) $assets->sum('subtotal');
        $totalLiabilities = (int) $liabilities->sum('subtotal');
        $totalEquity = (int) $equity->sum('subtotal');
        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;
        $difference = $totalAssets - $totalLiabilitiesEquity;

        return [
            'as_of' => $asOf,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_equity' => $totalLiabilitiesEquity,
            'difference' => $difference,
            'is_balanced' => $difference === 0,
        ];
    }

    /**
     * @param  list<string>  $subGrups
     * @return Collection<int, array{key: string, label: string, lines: Collection, subtotal: int}>
     */
    private function balanceSheetGroupSections(array $subGrups, ?string $asOf): Collection
    {
        return collect($subGrups)
            ->map(function (string $subGrup) use ($asOf) {
                $lines = Account::where('sub_grup', $subGrup)
                    ->orderBy('kode')
                    ->get()
                    ->map(fn (Account $account) => [
                        'kode' => $account->kode,
                        'nama' => $account->nama,
                        'saldo' => $this->accountBalanceAsOf($account->kode, $asOf),
                    ])
                    ->filter(fn (array $row) => $row['saldo'] !== 0)
                    ->values();

                return [
                    'key' => $subGrup,
                    'label' => $this->balanceSheetSectionLabel($subGrup),
                    'lines' => $lines,
                    'subtotal' => (int) $lines->sum('saldo'),
                ];
            })
            ->filter(fn (array $section) => $section['lines']->isNotEmpty())
            ->values();
    }

    private function balanceSheetSectionLabel(string $subGrup): string
    {
        return match ($subGrup) {
            'Current Asset' => __('reports.balance_sheet.section_current_asset'),
            'Non-Current Asset' => __('reports.balance_sheet.section_non_current_asset'),
            'Current Liability' => __('reports.balance_sheet.section_current_liability'),
            'Paid-In-Capital' => __('reports.balance_sheet.section_paid_in_capital'),
            'Retained Earnings' => __('reports.balance_sheet.section_retained_earnings'),
            default => $subGrup,
        };
    }

    private function sumByGroup(string $grup, ?string $from, ?string $to): int
    {
        $kodes = Account::where('grup', $grup)->pluck('kode');

        $query = JournalEntry::whereIn('account_kode', $kodes);

        if ($from || $to) {
            $query->whereHas('transaction', function ($q) use ($from, $to) {
                if ($from) {
                    $q->whereDate('tanggal', '>=', $from);
                }
                if ($to) {
                    $q->whereDate('tanggal', '<=', $to);
                }
            });
        }

        if ($grup === 'Revenues') {
            return (int) $query->sum('credit') - (int) $query->sum('debit');
        }

        return (int) $query->sum('debit') - (int) $query->sum('credit');
    }
}
