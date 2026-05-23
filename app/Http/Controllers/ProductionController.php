<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionRecord;
use App\Models\Product;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = ProductionRecord::query();

        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
        }

        $records = $query->orderBy('tanggal', 'desc')->get();
        $products = Product::where('status', 'Aktif')->get();

        $total   = $records->count();
        $sukses  = $records->where('status', 'Berhasil')->count();
        $gagal   = $records->where('status', 'Gagal')->count();
        $rate    = $total > 0 ? round(($sukses / $total) * 100, 1).'%' : '0%';

        $stats = [
            ['label' => __('page.total_production'),    'value' => $total,  'tone' => 'blue'],
            ['label' => __('page.production_success'),  'value' => $sukses, 'tone' => 'green'],
            ['label' => __('page.production_failed'),   'value' => $gagal,  'tone' => 'rose'],
            ['label' => __('page.success_rate'),        'value' => $rate,   'tone' => 'slate'],
        ];

        $role     = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.produksi' : 'karyawan.produksi';

        return view($viewName, compact('records', 'products', 'search', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'product_id'   => ['nullable', 'string', 'exists:products,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'jumlah'       => ['required', 'integer', 'min:0'],
            'satuan'       => ['required', 'string', 'max:50'],
            'status'       => ['required', 'string', 'in:Berhasil,Gagal'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $lastId   = ProductionRecord::orderBy('id', 'desc')->first();
        $newNum   = $lastId ? (intval(substr($lastId->id, 3)) + 1) : 1;
        $data['id'] = 'PRD'.str_pad($newNum, 3, '0', STR_PAD_LEFT);

        ProductionRecord::create($data);

        return redirect()->back()->with('success', 'Data produksi berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $record = ProductionRecord::findOrFail($id);

        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'product_name' => ['required', 'string', 'max:255'],
            'jumlah'       => ['required', 'integer', 'min:0'],
            'satuan'       => ['required', 'string', 'max:50'],
            'status'       => ['required', 'string', 'in:Berhasil,Gagal'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $record->update($data);

        return redirect()->back()->with('success', 'Data produksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ProductionRecord::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Data produksi berhasil dihapus.');
    }
}
