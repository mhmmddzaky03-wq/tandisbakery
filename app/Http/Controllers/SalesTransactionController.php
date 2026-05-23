<?php

namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use Illuminate\Http\Request;

class SalesTransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = SalesTransaction::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('metode', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderByDesc('tanggal')->get();

        $today = now()->toDateString();
        $todaySales = SalesTransaction::whereDate('tanggal', $today)->sum('total');
        $todayCount = SalesTransaction::whereDate('tanggal', $today)->sum('jumlah');

        $role = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.penjualan' : 'karyawan.penjualan';

        return view($viewName, compact('transactions', 'search', 'todaySales', 'todayCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'total' => ['required', 'integer', 'min:0'],
            'metode' => ['required', 'string', 'in:Cash,Transfer,Mix'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $data['id'] = $this->nextId(SalesTransaction::class, 'TRX');

        SalesTransaction::create($data);

        return redirect()->back()->with('success', __('Transaksi penjualan berhasil disimpan.'));
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

        $transaction->update($data);

        return redirect()->back()->with('success', __('Transaksi penjualan berhasil diperbarui.'));
    }

    public function destroy(string $id)
    {
        SalesTransaction::findOrFail($id)->delete();

        return redirect()->back()->with('success', __('Transaksi penjualan berhasil dihapus.'));
    }

    private function nextId(string $model, string $prefix): string
    {
        $last = $model::orderBy('id', 'desc')->first();
        $newNum = $last ? (intval(substr($last->id, strlen($prefix))) + 1) : 1;

        return $prefix.str_pad((string) $newNum, 3, '0', STR_PAD_LEFT);
    }
}
