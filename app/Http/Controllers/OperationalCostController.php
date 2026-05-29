<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\OperationalCost;
use App\Services\OperationalCostService;
use App\Support\FormatHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OperationalCostController extends Controller
{
    public function __construct(private OperationalCostService $service)
    {
    }

    public function index(Request $request)
    {
        $month = $this->resolveMonth($request->input('month'));
        $tab = $request->input('tab', 'transaksi') === 'rekap' ? 'rekap' : 'transaksi';
        $filter = $request->input('jenis');

        $periodStart = $month->copy()->startOfMonth()->toDateString();
        $periodEnd = $month->copy()->endOfMonth()->toDateString();

        $totals = $this->service->periodTotals($periodStart, $periodEnd);
        $variableTotal = $totals['variable'] + $totals['restock'];
        $grandTotal = $totals['fixed'] + $variableTotal;

        $ic = static fn (string $paths): string => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$paths.'</svg>';

        $stats = [
            [
                'label' => 'Total Biaya',
                'value' => FormatHelper::rupiah($grandTotal),
                'tone' => 'violet',
                'icon' => $ic('<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h14a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>'),
            ],
            [
                'label' => 'Biaya Tetap',
                'value' => FormatHelper::rupiah($totals['fixed']),
                'tone' => 'blue',
                'icon' => $ic('<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'),
            ],
            [
                'label' => 'Biaya Variabel',
                'value' => FormatHelper::rupiah($variableTotal),
                'tone' => 'amber',
                'icon' => $ic('<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'),
            ],
        ];

        $query = OperationalCost::query()
            ->with(['expenseCategory', 'journalTransaction'])
            ->whereDate('tanggal', '>=', $periodStart)
            ->whereDate('tanggal', '<=', $periodEnd);

        if ($filter && in_array($filter, ['Fixed', 'Variable'], true)) {
            $query->where('jenis', $filter);
        }

        $costs = $query->orderByDesc('tanggal')->orderByDesc('id')->get();

        $role = auth()->user()->role;

        $categories = ExpenseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('jenis');

        $allCategories = ExpenseCategory::query()
            ->withCount('operationalCosts')
            ->orderBy('sort_order')
            ->get();

        $canManageCategories = $role === 'admin';

        $summary = $tab === 'rekap'
            ? $this->service->monthlySummary($periodStart, $periodEnd)
            : null;

        $viewName = $role === 'admin' ? 'admin.operasional' : 'karyawan.operasional';

        return view($viewName, compact(
            'costs',
            'filter',
            'stats',
            'month',
            'tab',
            'categories',
            'allCategories',
            'canManageCategories',
            'summary',
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['id'] = $this->nextId();
        $data['desk'] = $data['desk'] ?? '';

        $cost = $this->service->record($data);

        return redirect()->back()->with('success',' Biaya '.$cost->kat.' berhasil disimpan.');
    }

    public function update(Request $request, string $id)
    {
        $cost = OperationalCost::findOrFail($id);
        $data = $this->validated($request);
        $data['desk'] = $data['desk'] ?? '';

        $cost = $this->service->update($cost, $data);

        return redirect()->back()->with('success',' Biaya '.$cost->kat.' berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $cost = OperationalCost::findOrFail($id);
        $name = $cost->kat;

        $this->service->delete($cost);

        return redirect()->back()->with('success',' Biaya '.$name.' berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tanggal' => ['required', 'date'],
            'expense_category_id' => [
                'required',
                Rule::exists('expense_categories', 'id')->where('is_active', true),
            ],
            'desk' => ['nullable', 'string', 'max:500'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ], [], [
            'expense_category_id' => 'Kategori',
            'desk' => 'Deskripsi',
            'jumlah' => 'Jumlah',
            'tanggal' => 'Tanggal',
        ]);
    }

    private function resolveMonth(?string $month): Carbon
    {
        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        }

        $latest = OperationalCost::max('tanggal');
        if ($latest) {
            return Carbon::parse($latest)->startOfMonth();
        }

        return now()->startOfMonth();
    }

    private function nextId(): string
    {
        $last = OperationalCost::orderBy('id', 'desc')->first();
        $newNum = $last ? (intval(substr($last->id, 2)) + 1) : 1;

        return 'BO'.str_pad((string) $newNum, 3, '0', STR_PAD_LEFT);
    }
}
