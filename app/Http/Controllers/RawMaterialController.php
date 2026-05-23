<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawMaterial;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = RawMaterial::query();

        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
        }

        $materials = $query->orderBy('id', 'asc')->get();
        $role = auth()->user()->role;
        $viewName = $role === 'admin' ? 'admin.stok' : 'karyawan.persediaan';

        return view($viewName, compact('materials', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'     => ['required', 'string', 'unique:raw_materials,id'],
            'nama'   => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'min'    => ['required', 'numeric', 'min:0'],
            'harga'  => ['required', 'integer', 'min:0'],
        ]);

        RawMaterial::create($data);

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $material = RawMaterial::findOrFail($id);

        $data = $request->validate([
            'nama'   => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'min'    => ['required', 'numeric', 'min:0'],
            'harga'  => ['required', 'integer', 'min:0'],
        ]);

        $material->update($data);

        return redirect()->back()->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        RawMaterial::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Bahan baku berhasil dihapus.');
    }
}
