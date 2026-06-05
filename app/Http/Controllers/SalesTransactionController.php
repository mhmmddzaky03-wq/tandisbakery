<?php

namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use App\Services\SalesJournalService;
use App\Support\FormatHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesTransactionController extends Controller
{
    public function __construct(private SalesJournalService $salesJournal)
    {
    }

    public function index(Request $request)
    {
        $transactions = SalesTransaction::query()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $today = now()->toDateString();
        $todaySales = (int) SalesTransaction::whereDate('tanggal', $today)->sum('total');
        $todayCount = (int) SalesTransaction::whereDate('tanggal', $today)->sum('jumlah');

        $ic = static fn (string $paths): string => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$paths.'</svg>';

        $stats = [
            [
                'label' => __('sales.stats.today_total'),
                'value' => FormatHelper::rupiah($todaySales),
                'tone' => 'green',
                'icon' => $ic('<path d="M4 7h16M4 11h16M8 15h4M6 19h12"/>'),
            ],
            [
                'label' => __('sales.stats.today_count'),
                'value' => (string) $todayCount,
                'tone' => 'blue',
                'icon' => $ic('<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'),
            ],
            [
                'label' => __('sales.stats.total_records'),
                'value' => (string) $transactions->count(),
                'tone' => 'amber',
                'icon' => $ic('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/>'),
            ],
        ];

        $role = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.penjualan' : 'karyawan.penjualan';

        return view($viewName, compact('transactions', 'stats', 'todaySales', 'todayCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'total' => ['required', 'integer', 'min:0'],
            'metode' => ['required', 'string', 'in:Cash,Transfer,Mix'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $data['id'] = $this->nextId(SalesTransaction::class, 'TRS');

        DB::transaction(function () use ($data) {
            $sale = SalesTransaction::create($data);
            $this->salesJournal->sync($sale);
        });

        return redirect()->back()->with('success', __('messages.flash.sales_saved'));
    }

    public function update(Request $request, string $id)
    {
        $transaction = SalesTransaction::findOrFail($id);

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'total' => ['required', 'integer', 'min:0'],
            'metode' => ['required', 'string', 'in:Cash,Transfer,Mix'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($transaction, $data) {
            $transaction->update($data);
            $this->salesJournal->sync($transaction->fresh());
        });

        return redirect()->back()->with('success', __('messages.flash.sales_updated'));
    }

    public function destroy(string $id)
    {
        $transaction = SalesTransaction::findOrFail($id);

        DB::transaction(function () use ($transaction) {
            $this->salesJournal->deleteForSale($transaction);
            $transaction->delete();
        });

        return redirect()->back()->with('success', __('messages.flash.sales_deleted'));
    }

    private function nextId(string $model, string $prefix): string
    {
        $last = $model::orderBy('id', 'desc')->first();
        $newNum = $last ? (intval(substr($last->id, strlen($prefix))) + 1) : 1;

        return $prefix.str_pad((string) $newNum, 3, '0', STR_PAD_LEFT);
    }
}
