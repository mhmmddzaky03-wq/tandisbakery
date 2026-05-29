<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Unit;
use App\Services\RawMaterialRestockService;
use App\Support\FormatHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = RawMaterial::query()->with('restocks');

        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
        }

        $materials = $query->orderBy('id', 'asc')->get();
        $units = Unit::orderBy('nama')->get();
        $role = $request->user()->role;
        $viewName = $role === 'admin' ? 'admin.stok' : 'karyawan.persediaan';

        return view($viewName, compact('materials', 'search', 'units'));
    }

    public function store(Request $request, RawMaterialRestockService $restockService)
    {
        $this->normalizeJumlahInput($request);
        $this->normalizeMinInput($request);

        $data = $request->validate([
            'nama'   => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'satuan' => ['required', 'string', 'max:50', Rule::exists('units', 'nama')],
            'min'    => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'harga'  => ['required', 'integer', 'min:0'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['nama']);
        $jumlah = FormatHelper::normalizeQtyOne($data['jumlah']);
        $min = FormatHelper::normalizeQtyOne($data['min']);
        $harga = (int) $data['harga'];

        $material = RawMaterial::create([
            'id' => RawMaterial::generateNextId(),
            'nama' => $data['nama'],
            'jumlah' => 0,
            'harga' => 0,
            'satuan' => $data['satuan'],
            'min' => $min,
        ]);

        if ((float) $jumlah > 0) {
            $restockService->record(
                $material,
                now()->toDateString(),
                (float) $jumlah,
                $harga,
                'Stok awal',
            );
        } else {
            $material->harga = $harga;
            $material->saveQuietly();
        }

        return redirect()->back()->with('success',' Bahan baku '.$material->nama.' berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $material = RawMaterial::findOrFail($id);

        $this->normalizeMinInput($request);

        $data = $request->validate([
            'nama'   => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50', Rule::exists('units', 'nama')],
            'min'    => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['nama']);
        $data['min'] = FormatHelper::normalizeQtyOne($data['min']);

        $material->update($data);

        return redirect()->back()->with('success',' Bahan baku '.$material->nama.' berhasil diperbarui.');
    }

    public function restock(Request $request, string $id, RawMaterialRestockService $restockService)
    {
        $material = RawMaterial::findOrFail($id);

        $this->normalizeRestockJumlahInput($request);

        $data = $request->validate([
            'restock_tanggal' => ['required', 'date'],
            'restock_jumlah'  => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'restock_harga'   => ['required', 'integer', 'min:1'],
            'restock_catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $jumlah = FormatHelper::normalizeQtyOne($data['restock_jumlah']);

        $restockService->record(
            $material,
            $data['restock_tanggal'],
            (float) $jumlah,
            (int) $data['restock_harga'],
            $data['restock_catatan'] ?? null,
        );

        return redirect()->back()->with('success',' Bahan baku '.$material->nama.' berhasil direstock.');
    }

    public function destroy(string $id)
    {
        $material = RawMaterial::findOrFail($id);
        $nama = $material->nama;
        $material->delete();

        return redirect()->back()->with('success',' Bahan baku '.$nama.' berhasil dihapus.');
    }

    private function normalizeJumlahInput(Request $request): void
    {
        if (! $request->has('jumlah')) {
            return;
        }

        $raw = $request->input('jumlah');
        if ($raw === null || $raw === '') {
            return;
        }

        $request->merge([
            'jumlah' => FormatHelper::formatQtyInput($raw),
        ]);
    }

    private function normalizeMinInput(Request $request): void
    {
        if (! $request->has('min')) {
            return;
        }

        $raw = $request->input('min');
        if ($raw === null || $raw === '') {
            return;
        }

        $request->merge([
            'min' => FormatHelper::formatQtyInput($raw),
        ]);
    }

    private function normalizeRestockJumlahInput(Request $request): void
    {
        if (! $request->has('restock_jumlah')) {
            return;
        }

        $raw = $request->input('restock_jumlah');
        if ($raw === null || $raw === '') {
            return;
        }

        $request->merge([
            'restock_jumlah' => FormatHelper::formatQtyInput($raw),
        ]);
    }
}
