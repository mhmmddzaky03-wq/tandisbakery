<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\SalesTransaction;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private AccountingService $accounting)
    {
    }

    public function incomeStatement(Request $request)
    {
        [$from, $to] = $this->period($request);

        $data = $this->accounting->incomeStatement($from, $to);

        return view('admin.laba-rugi', compact('data', 'from', 'to'));
    }

    public function salesReport(Request $request)
    {
        [$from, $to] = $this->period($request);

        $sales = SalesTransaction::query()
            ->when($from, fn ($q) => $q->whereDate('tanggal', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('tanggal', '<=', $to))
            ->orderByDesc('tanggal')
            ->get();

        $total = (int) $sales->sum('total');

        return view('admin.laporan-penjualan', compact('sales', 'total', 'from', 'to'));
    }

    public function balanceSheet()
    {
        $data = $this->accounting->balanceSheet();

        return view('admin.neraca', compact('data'));
    }

    public function generalLedger(Request $request)
    {
        $accountKode = $request->input('account', '5-150');
        $from = $request->input('from');
        $to = $request->input('to');

        $accounts = Account::orderBy('kode')->get();

        if (! Account::where('kode', $accountKode)->exists()) {
            $accountKode = $accounts->first()?->kode ?? '1-110';
        }

        $ledger = $this->accounting->generalLedger($accountKode, $from, $to);

        return view('admin.general-ledger', array_merge($ledger, [
            'accounts' => $accounts,
            'accountKode' => $accountKode,
            'from' => $from,
            'to' => $to,
        ]));
    }

    public function trialBalance()
    {
        $rows = $this->accounting->trialBalance();
        $totalDebit = $rows->sum('debit');
        $totalKredit = $rows->sum('kredit');

        return view('admin.trial-balance', compact('rows', 'totalDebit', 'totalKredit'));
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function period(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        return [$from, $to];
    }
}
