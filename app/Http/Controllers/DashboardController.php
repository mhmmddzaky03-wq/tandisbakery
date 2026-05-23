<?php

namespace App\Http\Controllers;

use App\Models\OperationalCost;
use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Models\SalesTransaction;
use App\Services\AccountingService;
use App\Support\FormatHelper;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(private AccountingService $accounting)
    {
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
        $salesMonth = (int) SalesTransaction::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('total');
        $salesLast30 = (int) SalesTransaction::where('tanggal', '>=', now()->subDays(30)->toDateString())->sum('total');

        $income = $this->accounting->incomeStatement($monthStart, $monthEnd);

        $salesTrend = SalesTransaction::orderBy('tanggal')
            ->get()
            ->groupBy(fn ($t) => Carbon::parse($t->tanggal)->format('d M'))
            ->map(fn ($group) => (int) $group->sum('total'))
            ->take(8);

        $fixedCosts = (int) OperationalCost::where('jenis', 'Fixed')->whereBetween('tanggal', [$monthStart, $monthEnd])->sum('jumlah');
        $variableCosts = (int) OperationalCost::where('jenis', 'Variable')->whereBetween('tanggal', [$monthStart, $monthEnd])->sum('jumlah');

        return view('admin.dashboard', [
            'stockCount' => $materials->count(),
            'lowStockCount' => $lowStock->count(),
            'successProduction' => $successProduction,
            'totalProduction' => $totalProduction,
            'operationalMonth' => $operationalMonth,
            'salesMonth' => $salesMonth,
            'salesLast30' => $salesLast30,
            'netProfit' => $income['net_profit'],
            'totalCosts' => $operationalMonth + $income['expenses'],
            'productions' => $productions,
            'salesTrend' => $salesTrend,
            'fixedCosts' => $fixedCosts,
            'variableCosts' => $variableCosts,
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
