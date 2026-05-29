<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\OperationalCost;
use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Models\SalesTransaction;
use App\Services\AccountingService;
use App\Services\OperationalCostService;
use App\Support\FormatHelper;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        private AccountingService $accounting,
        private OperationalCostService $operationalCosts,
    ) {
    }

    public function admin()
    {
        $materials = RawMaterial::all();
        $lowStock = $materials->filter(fn ($m) => (float) $m->jumlah <= (float) $m->min);

        $productions = ProductionRecord::orderByDesc('tanggal')->limit(5)->get();
        $totalProduction = ProductionRecord::count();
        $successProduction = ProductionRecord::where('status', 'Berhasil')->count();
        $failedProduction = ProductionRecord::where('status', 'Gagal')->count();

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $operationalMonth = (int) OperationalCost::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('jumlah');
        $periodTotals = $this->operationalCosts->periodTotals($monthStart, $monthEnd);
        $salesMonth = (int) SalesTransaction::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('total');
        $salesLast30 = (int) SalesTransaction::where('tanggal', '>=', now()->subDays(30)->toDateString())->sum('total');

        $income = $this->accounting->incomeStatement($monthStart, $monthEnd);

        $salesTrend = SalesTransaction::orderBy('tanggal')
            ->get()
            ->groupBy(fn ($t) => Carbon::parse($t->tanggal)->format('d M'))
            ->map(fn ($group) => (int) $group->sum('total'))
            ->take(8);

        $fixedCosts = $periodTotals['fixed'];
        $variableCosts = $periodTotals['variable'] + $periodTotals['restock'];

        $activityLogs = ActivityLog::query()->latest()->take(10)->get();

        return view('admin.dashboard', [
            'stockCount' => $materials->count(),
            'lowStockCount' => $lowStock->count(),
            'successProduction' => $successProduction,
            'totalProduction' => $totalProduction,
            'operationalMonth' => $operationalMonth,
            'salesMonth' => $salesMonth,
            'salesLast30' => $salesLast30,
            'netProfit' => $income['net_profit'],
            'totalCosts' => $income['expenses'],
            'productions' => $productions,
            'salesTrend' => $salesTrend,
            'fixedCosts' => $fixedCosts,
            'variableCosts' => $variableCosts,
            'activityLogs' => $activityLogs,
            'format' => FormatHelper::class,
        ]);
    }

    public function karyawan()
    {
        return view('karyawan.dashboard');
    }

    public function basket()
    {
        $today = now()->toDateString();
        $todaySales = SalesTransaction::whereDate('tanggal', $today)->get();
        $orderCount = $todaySales->count();
        $completed = $todaySales->where('total', '>', 0)->count();
        $pending = max(0, $orderCount - $completed);

        return view('basket.dashboard', compact('orderCount', 'completed', 'pending'));
    }
}
