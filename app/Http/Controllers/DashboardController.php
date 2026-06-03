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

        $totalProduction = ProductionRecord::count();
        $successProduction = ProductionRecord::where('status', 'Berhasil')->count();
        $failedProduction = ProductionRecord::where('status', 'Gagal')->count();
        $latestProduction = ProductionRecord::query()->orderByDesc('tanggal')->orderByDesc('id')->first();
        $recentFailedProduction = ProductionRecord::query()
            ->where('status', 'Gagal')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();
        $productionSuccessRate = $totalProduction > 0
            ? (int) round(($successProduction / $totalProduction) * 100)
            : 0;

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $operationalMonth = (int) OperationalCost::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('jumlah');
        $periodTotals = $this->operationalCosts->periodTotals($monthStart, $monthEnd);
        $salesMonth = (int) SalesTransaction::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('total');
        $salesLast30 = (int) SalesTransaction::where('tanggal', '>=', now()->subDays(30)->toDateString())->sum('total');

        $income = $this->accounting->incomeStatement($monthStart, $monthEnd);

        $salesChart = $this->salesTrendChart(14);
        $salesTrendLabel = $this->salesTrendChangeLabel(7);

        $fixedCosts = $periodTotals['fixed'];
        $variableCosts = $periodTotals['variable'];
        $restockCosts = $periodTotals['restock'];
        $totalCostComposition = $fixedCosts + $variableCosts + $restockCosts;

        $costChart = [
            'labels' => ['Biaya tetap', 'Biaya variabel', 'Restock bahan'],
            'values' => [$fixedCosts, $variableCosts, $restockCosts],
        ];

        $activityLogs = ActivityLog::query()->latest()->take(10)->get();

        $monthName = Carbon::now()->locale('id')->translatedFormat('F Y');

        return view('admin.dashboard', [
            'stockCount' => $materials->count(),
            'lowStockCount' => $lowStock->count(),
            'successProduction' => $successProduction,
            'totalProduction' => $totalProduction,
            'operationalMonth' => $operationalMonth,
            'salesMonth' => $salesMonth,
            'salesLast30' => $salesLast30,
            'salesTrendLabel' => $salesTrendLabel,
            'netProfit' => $income['net_profit'],
            'grossProfit' => $income['gross_profit'],
            'totalCosts' => $income['expenses'],
            'latestProduction' => $latestProduction,
            'recentFailedProduction' => $recentFailedProduction,
            'productionSuccessRate' => $productionSuccessRate,
            'failedProduction' => $failedProduction,
            'salesChart' => $salesChart,
            'costChart' => $costChart,
            'totalCostComposition' => $totalCostComposition,
            'activityLogs' => $activityLogs,
            'monthName' => $monthName,
        ]);
    }

    public function karyawan()
    {
        $today = now()->toDateString();
        $totalProduction = ProductionRecord::count();
        $todaySales = (int) SalesTransaction::whereDate('tanggal', $today)->sum('total');
        $latestProduction = ProductionRecord::query()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        return view('karyawan.dashboard', compact(
            'totalProduction',
            'todaySales',
            'latestProduction',
        ));
    }

    /**
     * @return array{labels: list<string>, values: list<int>, total: int, peak: int, peak_label: string}
     */
    private function salesTrendChart(int $days = 14): array
    {
        $labels = [];
        $values = [];
        $peak = 0;
        $peakLabel = '—';

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayTotal = (int) SalesTransaction::whereDate('tanggal', $date)->sum('total');
            $label = $date->format('d/m');

            $labels[] = $label;
            $values[] = $dayTotal;

            if ($dayTotal >= $peak) {
                $peak = $dayTotal;
                $peakLabel = $date->locale('id')->translatedFormat('D, d M');
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'total' => array_sum($values),
            'peak' => $peak,
            'peak_label' => $peakLabel,
        ];
    }

    private function salesTrendChangeLabel(int $days = 7): ?string
    {
        $currentStart = now()->subDays($days - 1)->startOfDay()->toDateString();
        $previousStart = now()->subDays($days * 2 - 1)->startOfDay()->toDateString();
        $previousEnd = now()->subDays($days)->endOfDay()->toDateString();

        $current = (int) SalesTransaction::where('tanggal', '>=', $currentStart)->sum('total');
        $previous = (int) SalesTransaction::whereBetween('tanggal', [$previousStart, $previousEnd])->sum('total');

        if ($previous === 0) {
            return $current > 0 ? '+100%' : null;
        }

        $pct = round((($current - $previous) / $previous) * 100, 1);
        $sign = $pct >= 0 ? '+' : '';

        return $sign.$pct.'%';
    }
}
