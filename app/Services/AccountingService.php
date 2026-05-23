<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\OperationalCost;
use App\Models\SalesTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AccountingService
{
    public function accountBalance(string $kode): int
    {
        $account = Account::find($kode);
        if (! $account) {
            return 0;
        }

        $debit = (int) JournalEntry::where('account_kode', $kode)->sum('debit');
        $credit = (int) JournalEntry::where('account_kode', $kode)->sum('credit');

        return $account->posisi === 'Debit'
            ? $debit - $credit
            : $credit - $debit;
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
    public function journalGroups(?string $search = null, ?string $from = null, ?string $to = null): Collection
    {
        $query = JournalTransaction::with(['entries.account'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('ref', 'like', "%{$search}%")
                    ->orWhereHas('entries', function ($eq) use ($search) {
                        $eq->where('account_kode', 'like', "%{$search}%");
                    });
            });
        }

        if ($from) {
            $query->whereDate('tanggal', '>=', $from);
        }

        if ($to) {
            $query->whereDate('tanggal', '<=', $to);
        }

        return $query->get()->groupBy(fn ($tx) => $tx->tanggal->format('Y-m-d'))->map(function ($group, $date) {
            $carbon = Carbon::parse($date);

            return [
                'tanggal' => $carbon->translatedFormat('j F Y'),
                'hari' => $carbon->translatedFormat('l'),
                'transactions' => $group,
                'entries' => $group->flatMap(function (JournalTransaction $tx) {
                    return $tx->entries->map(fn ($entry) => [
                        'ref' => $tx->ref ?? '',
                        'akun' => $entry->account_kode,
                        'uraian' => $tx->deskripsi,
                        'debit' => $entry->debit > 0 ? $entry->debit : 0,
                        'kredit' => $entry->credit > 0 ? $entry->credit : 0,
                    ]);
                }),
                'total_debit' => $group->sum(fn ($tx) => $tx->entries->sum('debit')),
                'total_kredit' => $group->sum(fn ($tx) => $tx->entries->sum('credit')),
            ];
        })->values();
    }

    public function generalLedger(string $accountKode, ?string $from = null, ?string $to = null): array
    {
        $account = Account::findOrFail($accountKode);

        $query = JournalEntry::with('transaction')
            ->where('account_kode', $accountKode)
            ->orderBy('id');

        if ($from) {
            $query->whereHas('transaction', fn ($q) => $q->whereDate('tanggal', '>=', $from));
        }

        if ($to) {
            $query->whereHas('transaction', fn ($q) => $q->whereDate('tanggal', '<=', $to));
        }

        $running = 0;
        $rows = $query->get()->map(function (JournalEntry $entry, int $index) use ($account, &$running) {
            $debit = (int) $entry->debit;
            $credit = (int) $entry->credit;

            if ($account->posisi === 'Debit') {
                $running += $debit - $credit;
            } else {
                $running += $credit - $debit;
            }

            return [
                'no' => $index + 1,
                'tgl' => $entry->transaction?->tanggal?->translatedFormat('d M') ?? '-',
                'ref' => $entry->transaction?->ref ?? $entry->transaction?->deskripsi ?? '-',
                'debit' => $debit,
                'kredit' => $credit,
                'saldo' => $running,
            ];
        });

        return [
            'account' => $account,
            'rows' => $rows,
            'balance' => $this->accountBalance($accountKode),
        ];
    }

    public function trialBalance(): Collection
    {
        return Account::orderBy('kode')->get()->map(function (Account $account) {
            $debit = (int) JournalEntry::where('account_kode', $account->kode)->sum('debit');
            $credit = (int) JournalEntry::where('account_kode', $account->kode)->sum('credit');

            return [
                'account' => $account,
                'debit' => $debit,
                'kredit' => $credit,
            ];
        });
    }

    public function incomeStatement(?string $from = null, ?string $to = null): array
    {
        $revenue = $this->sumByGroup('Revenue', $from, $to);
        $expenses = $this->sumByGroup('Expenses', $from, $to);
        $cogs = $this->accountBalance('5-110');

        $salesFromTrx = SalesTransaction::query()
            ->when($from, fn ($q) => $q->whereDate('tanggal', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('tanggal', '<=', $to))
            ->sum('total');

        $operational = OperationalCost::query()
            ->when($from, fn ($q) => $q->whereDate('tanggal', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('tanggal', '<=', $to))
            ->sum('jumlah');

        $grossRevenue = max($revenue, (int) $salesFromTrx);
        $totalExpenses = $expenses + (int) $operational;

        return [
            'sales' => $grossRevenue,
            'net_sales' => $grossRevenue,
            'cogs' => abs($cogs),
            'gross_profit' => $grossRevenue - abs($cogs),
            'operational' => (int) $operational,
            'expenses' => $expenses,
            'net_profit' => $grossRevenue - abs($cogs) - $totalExpenses,
        ];
    }

    public function balanceSheet(): array
    {
        $groups = ['Current Asset', 'Non-Current Asset', 'Liabilities', 'Equity', 'Revenue', 'Expenses'];

        $sections = [];
        foreach ($groups as $group) {
            $accounts = Account::where('grup', $group)->orderBy('kode')->get();
            $sections[$group] = $accounts->map(fn ($a) => [
                'account' => $a,
                'saldo' => $this->accountBalance($a->kode),
            ]);
        }

        $assets = collect($sections['Current Asset'] ?? [])->merge($sections['Non-Current Asset'] ?? [])->sum('saldo');
        $liabilities = collect($sections['Liabilities'] ?? [])->sum('saldo');
        $equity = collect($sections['Equity'] ?? [])->sum('saldo');

        return [
            'sections' => $sections,
            'total_assets' => $assets,
            'total_liabilities' => $liabilities,
            'total_equity' => $equity,
        ];
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

        if ($grup === 'Revenue') {
            return (int) $query->sum('credit') - (int) $query->sum('debit');
        }

        return (int) $query->sum('debit') - (int) $query->sum('credit');
    }
}
