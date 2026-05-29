<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductionRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = Product::query()->with('productionRecord');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('satuan', 'like', "%{$search}%")
                    ->orWhereHas('productionRecord', fn ($r) => $r->where('id', 'like', "%{$search}%"));
            });
        }

        $products              = $query->orderBy('id', 'asc')->get();
        $availableProductions  = $this->availableProductionsQuery()->get();

        $role     = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.produk' : 'karyawan.produk';

        return view($viewName, compact('products', 'availableProductions', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'production_record_id' => [
                'required',
                'string',
                Rule::exists('production_records', 'id')->where('status', 'Berhasil'),
                Rule::unique('products', 'production_record_id'),
            ],
            'harga'  => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Aktif,Non-Aktif'],
        ]);

        $production = ProductionRecord::findOrFail($data['production_record_id']);

        Product::create([
            'id'                   => Product::generateNextId(),
            'production_record_id' => $production->id,
            'nama'                 => $production->product_name,
            'satuan'               => $production->satuan,
            'harga'                => $data['harga'],
            'status'               => $data['status'],
        ]);

        return redirect()->back()->with('success', __('ui.flash_product_created'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'production_record_id' => [
                'required',
                'string',
                Rule::exists('production_records', 'id')->where('status', 'Berhasil'),
                Rule::unique('products', 'production_record_id')->ignore($product->id, 'id'),
            ],
            'harga'  => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Aktif,Non-Aktif'],
        ]);

        $production = ProductionRecord::findOrFail($data['production_record_id']);

        $product->update([
            'production_record_id' => $production->id,
            'nama'                 => $production->product_name,
            'satuan'               => $production->satuan,
            'harga'                => $data['harga'],
            'status'               => $data['status'],
        ]);

        return redirect()->back()->with('success', __('ui.flash_product_updated'));
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->back()->with('success', __('ui.flash_product_deleted'));
    }

    private function availableProductionsQuery()
    {
        return ProductionRecord::query()
            ->where('status', 'Berhasil')
            ->whereDoesntHave('product')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');
    }

    public static function productionsForProduct(?Product $product = null)
    {
        return ProductionRecord::query()
            ->where('status', 'Berhasil')
            ->where(function ($q) use ($product) {
                $q->whereDoesntHave('product');
                if ($product?->production_record_id) {
                    $q->orWhere('id', $product->production_record_id);
                }
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }
}
