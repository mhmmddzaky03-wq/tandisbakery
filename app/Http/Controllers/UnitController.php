<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_satuan' => ['required', 'string', 'max:50', 'unique:units,nama'],
        ]);

        Unit::create(['nama' => $data['nama_satuan']]);

        return redirect()->back()->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $unit = Unit::findOrFail($id);

        $data = $request->validate([
            'nama_satuan' => ['required', 'string', 'max:50', 'unique:units,nama,'.$unit->id],
        ]);

        $unit->update(['nama' => $data['nama_satuan']]);

        return redirect()->back()->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Unit::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Satuan berhasil dihapus.');
    }
}
