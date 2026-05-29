<?php

namespace App\Http\Controllers;

use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Models\User;
use App\Services\ProductionMaterialService;
use App\Support\FormatHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = ProductionRecord::query()->with(['product', 'materialUsages.rawMaterial']);

        $allRecords = ProductionRecord::all();
        $total      = $allRecords->count();
        $sukses     = $allRecords->where('status', 'Berhasil')->count();
        $gagal      = $allRecords->where('status', 'Gagal')->count();
        $rate       = $total > 0 ? round(($sukses / $total) * 100, 1).'%' : '0%';

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $records = $query->orderByDesc('tanggal')->orderByDesc('id')->get();

        $stats = [
            [
                'label' => 'Total Produksi',
                'value' => $total,
                'tone'  => 'blue',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>',
            ],
            [
                'label' => 'Produksi Berhasil',
                'value' => $sukses,
                'tone'  => 'green',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
            ],
            [
                'label' => 'Produksi Gagal',
                'value' => $gagal,
                'tone'  => 'rose',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>',
            ],
            [
                'label' => 'Tingkat Keberhasilan',
                'value' => $rate,
                'tone'  => 'amber',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>',
            ],
        ];

        $materials = RawMaterial::orderBy('nama')->get(['id', 'nama', 'jumlah', 'satuan', 'harga']);
        $user = Auth::user();
        $role = $user instanceof User ? $user->role : 'admin';
        $viewName = $role === 'admin' ? 'admin.produksi' : 'karyawan.produksi';

        return view($viewName, compact('records', 'search', 'stats', 'materials'));
    }

    public function store(Request $request, ProductionMaterialService $materialService)
    {
        $this->normalizeMaterialInputs($request);

        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'product_name' => ['required', 'string', 'max:255'],
            'jumlah'       => ['required', 'integer', 'min:0'],
            'status'       => ['required', 'string', 'in:Berhasil,Gagal'],
            'notes'        => ['nullable', 'string', 'max:500'],
            'materials'    => ['required', 'array', 'min:1'],
            'materials.*.raw_material_id' => ['required', 'string', Rule::exists('raw_materials', 'id')],
            'materials.*.jumlah'          => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
        ]);

        $data['satuan'] = 'pcs';

        if ($data['status'] === 'Berhasil' && (int) $data['jumlah'] < 1) {
            return redirect()->back()->withInput()->withErrors([
                'jumlah' => 'Jumlah hasil wajib diisi jika status Berhasil.',
            ]);
        }

        $data = FormatHelper::applyTitleCase($data, ['product_name']);
        $lines = $materialService->normalizeLines($request->input('materials', []));
        $data['id'] = ProductionRecord::generateNextId();

        DB::transaction(function () use ($data, $lines, $materialService) {
            $record = ProductionRecord::create($data);
            $materialService->apply($record, $lines);
        });

        return redirect()->back()->with(
            'success',
            sprintf('Produksi %s berhasil disimpan.', $data['product_name'])
        );
    }

    public function update(Request $request, string $id, ProductionMaterialService $materialService)
    {
        $record = ProductionRecord::with('materialUsages')->findOrFail($id);

        $this->normalizeMaterialInputs($request);

        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'product_name' => ['required', 'string', 'max:255'],
            'jumlah'       => ['required', 'integer', 'min:0'],
            'status'       => ['required', 'string', 'in:Berhasil,Gagal'],
            'notes'        => ['nullable', 'string', 'max:500'],
            'materials'    => ['required', 'array', 'min:1'],
            'materials.*.raw_material_id' => ['required', 'string', Rule::exists('raw_materials', 'id')],
            'materials.*.jumlah'          => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
        ]);

        $data['satuan'] = 'pcs';

        if ($data['status'] === 'Berhasil' && (int) $data['jumlah'] < 1) {
            return redirect()->back()->withInput()->withErrors([
                'jumlah' => 'Jumlah hasil wajib diisi jika status Berhasil.',
            ]);
        }

        $data = FormatHelper::applyTitleCase($data, ['product_name']);

        if ($data['status'] === 'Gagal' && $record->product) {
            return redirect()->back()->withInput()->withErrors([
                'status' => 'Produksi ini sudah terdaftar sebagai produk. Ubah status produk terlebih dahulu atau hapus produk terkait.',
            ]);
        }

        $lines = $materialService->normalizeLines($request->input('materials', []));

        DB::transaction(function () use ($record, $data, $lines, $materialService) {
            $materialService->reverse($record);
            $record->update($data);
            $materialService->apply($record, $lines);
            $this->syncLinkedProduct($record);
        });

        return redirect()->back()->with(
            'success',
            sprintf('Produksi %s berhasil diperbarui.', $record->product_name)
        );
    }

    public function destroy(string $id, ProductionMaterialService $materialService)
    {
        $record = ProductionRecord::with('product')->findOrFail($id);

        if ($record->product) {
            return redirect()->back()->withErrors([
                'delete' => 'Data tidak dapat dihapus. Sudah terdaftar sebagai produk.',
            ]);
        }

        $nama = $record->product_name;

        DB::transaction(function () use ($record, $materialService) {
            $materialService->reverse($record);
            $record->delete();
        });

        return redirect()->back()->with(
            'success',
            sprintf('Produksi %s berhasil dihapus.', $nama)
        );
    }

    private function syncLinkedProduct(ProductionRecord $record): void
    {
        if (! $record->product) {
            return;
        }

        $record->product->update([
            'nama'   => $record->product_name,
            'satuan' => 'pcs',
        ]);
    }

    private function normalizeMaterialInputs(Request $request): void
    {
        $materials = $request->input('materials');
        if (! is_array($materials)) {
            return;
        }

        foreach ($materials as $index => $row) {
            if (! isset($row['jumlah']) || $row['jumlah'] === '') {
                continue;
            }

            $materials[$index]['jumlah'] = FormatHelper::formatQtyInput($row['jumlah']);
        }

        $request->merge(['materials' => $materials]);
    }
}
