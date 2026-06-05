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

        return redirect()->back()->with('success', __('messages.flash.unit_created'));
    }

    public function update(Request $request, int $id)
    {
        $unit = Unit::findOrFail($id);

        $data = $request->validate([
            'nama_satuan' => ['required', 'string', 'max:50', 'unique:units,nama,'.$unit->id],
        ]);

        if ($data['nama_satuan'] !== $unit->nama) {
            if ($unit->isProtected()) {
                return redirect()->back()->with('error', __('messages.flash.unit_protected_edit'));
            }

            if ($unit->isInUse()) {
                return redirect()->back()->with('error', __('messages.flash.unit_in_use_edit'));
            }
        }

        $unit->update(['nama' => $data['nama_satuan']]);

        return redirect()->back()->with('success', __('messages.flash.unit_updated'));
    }

    public function destroy(int $id)
    {
        $unit = Unit::findOrFail($id);

        if ($unit->isProtected()) {
            return redirect()->back()->with('error', __('messages.flash.unit_protected_delete'));
        }

        if ($unit->isInUse()) {
            return redirect()->back()->with('error', __('messages.flash.unit_in_use_delete'));
        }

        $unit->delete();

        return redirect()->back()->with('success', __('messages.flash.unit_deleted'));
    }
}
