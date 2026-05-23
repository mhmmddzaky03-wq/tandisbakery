<?php

namespace App\Http\Controllers;

use App\Models\OperationalCost;
use Illuminate\Http\Request;

class OperationalCostController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('jenis');

        $query = OperationalCost::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('kat', 'like', "%{$search}%")
                    ->orWhere('desk', 'like', "%{$search}%");
            });
        }

        if ($filter && in_array($filter, ['Fixed', 'Variable'], true)) {
            $query->where('jenis', $filter);
        }

        $costs = $query->orderByDesc('tanggal')->get();

        $totalCost = (int) $costs->sum('jumlah');
        $fixedTotal = (int) $costs->where('jenis', 'Fixed')->sum('jumlah');
        $variableTotal = (int) $costs->where('jenis', 'Variable')->sum('jumlah');

        $role = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.operasional' : 'karyawan.operasional';

        return view($viewName, compact('costs', 'search', 'filter', 'totalCost', 'fixedTotal', 'variableTotal'));
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

        $data['id'] = $this->nextId();

        OperationalCost::create($data);

        return redirect()->back()->with('success', __('Biaya operasional berhasil disimpan.'));
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

        $cost->update($data);

        return redirect()->back()->with('success', __('Biaya operasional berhasil diperbarui.'));
    }

    public function destroy(string $id)
    {
        OperationalCost::findOrFail($id)->delete();

        return redirect()->back()->with('success', __('Biaya operasional berhasil dihapus.'));
    }

    private function nextId(): string
    {
        $last = OperationalCost::orderBy('id', 'desc')->first();
        $newNum = $last ? (intval(substr($last->id, 2)) + 1) : 1;

        return 'BO'.str_pad((string) $newNum, 3, '0', STR_PAD_LEFT);
    }
}
