<?php

namespace App\Http\Controllers;

use App\Models\OperationalCost;
use App\Support\FormatHelper;
use Illuminate\Http\Request;

class OperationalCostController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('jenis');

        $allCosts = OperationalCost::all();
        $totalCost = (int) $allCosts->sum('jumlah');
        $fixedTotal = (int) $allCosts->where('jenis', 'Fixed')->sum('jumlah');
        $variableTotal = (int) $allCosts->where('jenis', 'Variable')->sum('jumlah');

        $query = OperationalCost::query();

        if ($filter && in_array($filter, ['Fixed', 'Variable'], true)) {
            $query->where('jenis', $filter);
        }

        $costs = $query->orderByDesc('tanggal')->orderByDesc('id')->get();

        $stats = [
            [
                'label' => __('page.total_cost'),
                'value' => FormatHelper::rupiah($totalCost),
                'tone'  => 'violet',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
            ],
            [
                'label' => __('page.fixed_cost_total'),
                'value' => FormatHelper::rupiah($fixedTotal),
                'tone'  => 'blue',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg>',
            ],
            [
                'label' => __('page.variable_cost_total'),
                'value' => FormatHelper::rupiah($variableTotal),
                'tone'  => 'amber',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M21 3v7h-7"/></svg>',
            ],
        ];

        $role = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.operasional' : 'karyawan.operasional';

        return view($viewName, compact('costs', 'filter', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'kat' => ['required', 'string', 'max:100'],
            'desk' => ['required', 'string', 'max:500'],
            'jumlah' => ['required', 'integer', 'min:0'],
            'jenis' => ['required', 'string', 'in:Fixed,Variable'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['kat', 'desk']);
        $data['id'] = $this->nextId();

        OperationalCost::create($data);

        return redirect()->back()->with('success', __('ui.flash_operational_created'));
    }

    public function update(Request $request, string $id)
    {
        $cost = OperationalCost::findOrFail($id);

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'kat' => ['required', 'string', 'max:100'],
            'desk' => ['required', 'string', 'max:500'],
            'jumlah' => ['required', 'integer', 'min:0'],
            'jenis' => ['required', 'string', 'in:Fixed,Variable'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['kat', 'desk']);

        $cost->update($data);

        return redirect()->back()->with('success', __('ui.flash_operational_updated'));
    }

    public function destroy(string $id)
    {
        OperationalCost::findOrFail($id)->delete();

        return redirect()->back()->with('success', __('ui.flash_operational_deleted'));
    }

    private function nextId(): string
    {
        $last = OperationalCost::orderBy('id', 'desc')->first();
        $newNum = $last ? (intval(substr($last->id, 2)) + 1) : 1;

        return 'BO'.str_pad((string) $newNum, 3, '0', STR_PAD_LEFT);
    }
}
