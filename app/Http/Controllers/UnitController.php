<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function store(Request $request)
    {
        Unit::ensureProtectedExist();

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

        if ($data['nama_satuan'] !== $unit->nama) {
            if ($unit->isProtected()) {
                return redirect()->back()->with('error', 'Satuan kg dan L tidak dapat diubah.');
            }

            if ($unit->isInUse()) {
                return redirect()->back()->with('error', 'Satuan tidak dapat diubah karena masih dipakai pada bahan baku.');
            }
        }

        $unit->update(['nama' => $data['nama_satuan']]);

        return redirect()->back()->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $unit = Unit::findOrFail($id);

        if ($unit->isProtected()) {
            return redirect()->back()->with('error', 'Satuan kg dan L tidak dapat dihapus.');
        }

        if ($unit->isInUse()) {
            return redirect()->back()->with('error', 'Satuan tidak dapat dihapus karena masih dipakai pada bahan baku.');
        }

        $unit->delete();

        return redirect()->back()->with('success', 'Satuan berhasil dihapus.');
    }
}
