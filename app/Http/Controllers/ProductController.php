<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductionRecord;
use App\Services\ProductStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

    public function show(string $id)
    {
        $product = Product::query()->with('productionRecord')->findOrFail($id);
        $productions = static::productionsForProduct($product);
        $productionBatchCount = $productions->count();

        $role = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.produk-show' : 'karyawan.produk-show';

        return view($viewName, compact('product', 'productions', 'productionBatchCount'));
    }

    public function store(Request $request, ProductStockService $stockService)
    {
        $data = $request->validate([
            'production_record_id' => [
                'required',
                'string',
                Rule::exists('production_records', 'id')->where('status', 'Berhasil'),
                Rule::unique('products', 'production_record_id'),
            ],
            'harga'  => ['required', 'integer', 'min:0'],
        ]);

        $production = ProductionRecord::findOrFail($data['production_record_id']);

        if (Product::existsForName($production->product_name)) {
            throw ValidationException::withMessages([
                'production_record_id' => __('messages.validation.product_name_exists'),
            ]);
        }

        Product::create([
            'id'                   => Product::generateNextId(),
            'production_record_id' => $production->id,
            'nama'                 => $production->product_name,
            'satuan'               => $production->satuan,
            'jumlah'               => $stockService->quantityForName($production->product_name),
            'harga'                => $data['harga'],
        ]);

        return redirect()->back()->with('success', __('messages.flash.product_registered'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'harga'  => ['required', 'integer', 'min:0'],
        ]);

        $product->update([
            'harga' => $data['harga'],
        ]);

        return redirect()->back()->with('success', __('messages.flash.product_updated'));
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->back()->with('success', __('messages.flash.product_deleted'));
    }

    private function availableProductionsQuery()
    {
        $registeredNames = Product::query()
            ->pluck('nama')
            ->map(fn (string $name) => Product::normalizeName($name))
            ->unique()
            ->values()
            ->all();

        return ProductionRecord::query()
            ->where('status', 'Berhasil')
            ->when($registeredNames !== [], function ($query) use ($registeredNames) {
                $query->whereNotIn(DB::raw('LOWER(TRIM(product_name))'), $registeredNames);
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id');
    }

    public static function productionsForProduct(?Product $product = null)
    {
        return ProductionRecord::query()
            ->where('status', 'Berhasil')
            ->when($product?->nama, function ($query) use ($product) {
                $query->whereRaw('LOWER(TRIM(product_name)) = ?', [Product::normalizeName($product->nama)]);
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }
}
