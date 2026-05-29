<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Support\FormatHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    private const DEFAULT_EXPENSE_ACCOUNT = '5-180';

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $maxOrder = (int) ExpenseCategory::max('sort_order');
        $data['sort_order'] = $maxOrder + 10;
        $data['is_active'] = true;
        $data['account_kode'] = self::DEFAULT_EXPENSE_ACCOUNT;
        $data['nama'] = FormatHelper::titleCase($data['nama']) ?? $data['nama'];

        $category = ExpenseCategory::create($data);

        return redirect()->back()->with('success',' Kategori '.$category->nama.' berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $data = $this->validated($request, $category);
        $data['nama'] = FormatHelper::titleCase($data['nama']) ?? $data['nama'];
        $data['is_active'] = $request->boolean('is_active');

        $category->update($data);

        $category->operationalCosts()->update([
            'kat' => $data['nama'],
            'jenis' => $data['jenis'],
        ]);

        return redirect()->back()->with('success',' Kategori '.$category->nama.' berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $category = ExpenseCategory::withCount('operationalCosts')->findOrFail($id);

        if ($category->operational_costs_count > 0) {
            return redirect()->back()->with('error',' Kategori '.$category->nama.' tidak bisa dihapus karena sudah dipakai. Nonaktifkan saja.');
        }

        $name = $category->nama;
        $category->delete();

        return redirect()->back()->with('success',' Kategori '.$name.' berhasil dihapus.');
    }

    private function validated(Request $request, ?ExpenseCategory $category = null): array
    {
        return $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('expense_categories', 'nama')->ignore($category?->id),
            ],
            'jenis' => ['required', 'string', 'in:Fixed,Variable'],
        ], [], [
            'nama' => 'Nama Kategori',
            'jenis' => 'Jenis',
        ]);
    }
}
