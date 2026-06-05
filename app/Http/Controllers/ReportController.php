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
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $data = $this->accounting->incomeStatement($from, $to);
        $filterLabel = \App\Support\PdfExporter::filterLabel($from, $to);

        return view('admin.laba-rugi', compact('data', 'from', 'to', 'filterLabel'));
    }

    public function salesReport(Request $request)
    {

        [$from, $to] = $this->optionalPeriod($request);

        $sales = SalesTransaction::query()
            ->when($from, fn ($q) => $q->whereDate('tanggal', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('tanggal', '<=', $to))
            ->orderByDesc('tanggal')
            ->get();

        $total = (int) $sales->sum('total');

        $filterLabel = $from || $to
            ? \App\Support\PdfExporter::filterLabel($from, $to)
            : __('app.pages.sales_report_subtitle');

        return view('admin.laporan-penjualan', compact('sales', 'total', 'from', 'to', 'filterLabel'));
    }

    public function balanceSheet(Request $request)
    {
        $asOf = $request->input('as_of', config('trial_balance_snapshot.as_of', now()->toDateString()));
        $view = $request->input('view', 'ringkasan');
        if (! in_array($view, ['ringkasan', 'aset', 'pasiva'], true)) {
            $view = 'ringkasan';
        }

        $data = $this->accounting->balanceSheet($asOf);
        $filterLabel = \App\Support\PdfExporter::filterLabel(null, null, $asOf);

        return view('admin.neraca', compact('data', 'asOf', 'filterLabel', 'view'));
    }

    public function generalLedger(Request $request)
    {
        $accounts = Account::orderBy('kode')->get();
        $defaultAccount = Account::where('kode', '6-100')->exists() ? '6-100' : ($accounts->first()?->kode ?? '1-110');

        $accountKode = $request->input('account', $defaultAccount);
        $from = $request->input('from', '2025-06-01');
        $to = $request->input('to', config('trial_balance_snapshot.as_of', '2025-06-30'));

        if (! Account::where('kode', $accountKode)->exists()) {
            $accountKode = $defaultAccount;
        }

        $ledger = $this->accounting->generalLedger($accountKode, $from, $to);

        return view('admin.general-ledger', array_merge($ledger, [
            'accounts' => $accounts,
            'accountKode' => $accountKode,
        ]));
    }

    public function trialBalance(Request $request)
    {
        $asOf = $request->input('as_of', config('trial_balance_snapshot.as_of', now()->toDateString()));
        $rows = $this->accounting->trialBalance($asOf);
        $totalDebit = $rows->sum('debit');
        $totalKredit = $rows->sum('kredit');
        $difference = $totalDebit - $totalKredit;

        return view('admin.trial-balance', compact('rows', 'totalDebit', 'totalKredit', 'difference', 'asOf'));
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

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function optionalPeriod(Request $request): array
    {
        return [
            $request->filled('from') ? $request->input('from') : null,
            $request->filled('to') ? $request->input('to') : null,
        ];
    }
}
