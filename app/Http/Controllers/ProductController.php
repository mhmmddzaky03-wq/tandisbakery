<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Product::query();

        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('satuan', 'like', "%{$search}%");
        }

        $products = $query->orderBy('id', 'asc')->get();
        
        $role = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.produk' : 'karyawan.produk';
        
        return view($viewName, compact('products', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'unique:products,id'],
            'nama' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50'],
            'harga' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Aktif,Non-Aktif'],
        ]);

        Product::create($data);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50'],
            'harga' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Aktif,Non-Aktif'],
        ]);

        $product->update($data);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus.');
    }
}
