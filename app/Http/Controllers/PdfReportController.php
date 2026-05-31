<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\SalesTransaction;
use App\Services\AccountingService;
use App\Support\PdfExporter;
use Illuminate\Http\Request;

class PdfReportController extends Controller
{
    public function __construct(private AccountingService $accounting)
    {
    }

    public function trialBalance(Request $request)
    {
        $asOf = $request->input('as_of', config('trial_balance_snapshot.as_of', now()->toDateString()));
        $rows = $this->accounting->trialBalance($asOf);
        $totalDebit = $rows->sum('debit');
        $totalKredit = $rows->sum('kredit');

        return PdfExporter::stream('pdf.trial-balance', [
            'title' => 'Trial Balance',
            'filterLabel' => PdfExporter::filterLabel(null, null, $asOf),
            'asOf' => $asOf,
            'rows' => $rows,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
            'difference' => $totalDebit - $totalKredit,
        ], 'trial-balance-'.$asOf);
    }

    public function generalLedger(Request $request)
    {
        $accounts = Account::orderBy('kode')->get();
        $defaultAccount = Account::where('kode', '6-100')->exists() ? '6-100' : ($accounts->first()?->kode ?? '1-110');
        $accountKode = $request->input('account', $defaultAccount);

        if (! Account::where('kode', $accountKode)->exists()) {
            $accountKode = $defaultAccount;
        }

        $from = $request->filled('from') ? $request->input('from') : null;
        $to = $request->filled('to') ? $request->input('to') : null;

        $ledger = $this->accounting->generalLedger($accountKode, $from, $to);

        return PdfExporter::stream('pdf.general-ledger', array_merge($ledger, [
            'title' => 'General Ledger',
            'filterLabel' => PdfExporter::filterLabel($ledger['from'], $ledger['to']),
            'accountKode' => $accountKode,
        ]), 'general-ledger-'.$accountKode);
    }

    public function journal(Request $request)
    {
        $sourceOptions = $this->accounting->journalSourceOptions();
        $source = $request->input('source', '');
        if ($source !== '' && ! array_key_exists($source, $sourceOptions)) {
            $source = '';
        }

        $from = $request->filled('from') ? $request->input('from') : null;
        $to = $request->filled('to') ? $request->input('to') : null;

        $journals = $this->accounting->journalGroups($source ?: null, $from, $to);
        $totals = $this->accounting->journalTotals($source ?: null, $from, $to);

        $filterLabel = PdfExporter::filterLabel($from, $to);
        $sourceLabel = $this->accounting->journalSourceFilterLabel($source ?: null);
        if ($sourceLabel) {
            $filterLabel = $filterLabel === 'Semua data'
                ? 'Sumber: '.$sourceLabel
                : $filterLabel.' · Sumber: '.$sourceLabel;
        }

        return PdfExporter::stream('pdf.journal', [
            'title' => 'Jurnal Umum',
            'filterLabel' => $filterLabel,
            'journals' => $journals,
            'totals' => $totals,
        ], 'jurnal-umum');
    }

    public function coa(Request $request)
    {
        $groupFilter = $request->input('grup');
        $accounts = $this->accounting->accountsWithBalances(null);

        if ($groupFilter && array_key_exists($groupFilter, config('coa.groups', []))) {
            $accounts = $accounts->filter(fn ($row) => $row['account']->grup === $groupFilter)->values();
        }

        $filterLabel = $groupFilter
            ? 'Grup: '.$groupFilter
            : PdfExporter::filterLabel(null, null);

        return PdfExporter::stream('pdf.coa', [
            'title' => 'Chart of Accounts',
            'filterLabel' => $filterLabel,
            'accounts' => $accounts,
            'groupFilter' => $groupFilter,
        ], 'chart-of-accounts');
    }

    public function balanceSheet(Request $request)
    {
        $asOf = $request->filled('as_of') ? $request->input('as_of') : null;
        $data = $this->accounting->balanceSheet($asOf);

        return PdfExporter::stream('pdf.balance-sheet', [
            'title' => 'Neraca Keuangan',
            'filterLabel' => $asOf
                ? PdfExporter::filterLabel(null, null, $asOf)
                : PdfExporter::filterLabel(null, null),
            'data' => $data,
        ], 'neraca-keuangan');
    }

    public function incomeStatement(Request $request)
    {
        [$from, $to] = $this->optionalPeriod($request);
        $data = $this->accounting->incomeStatement($from, $to);

        return PdfExporter::stream('pdf.income-statement', [
            'title' => 'Laba Rugi',
            'filterLabel' => PdfExporter::filterLabel($from, $to),
            'data' => $data,
        ], 'laba-rugi');
    }

    public function salesReport(Request $request)
    {
        [$from, $to] = $this->optionalPeriod($request);

        $sales = SalesTransaction::query()
            ->when($from, fn ($q) => $q->whereDate('tanggal', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('tanggal', '<=', $to))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return PdfExporter::stream('pdf.sales-report', [
            'title' => 'Laporan Penjualan',
            'filterLabel' => PdfExporter::filterLabel($from, $to),
            'sales' => $sales,
            'total' => (int) $sales->sum('total'),
        ], 'laporan-penjualan');
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
