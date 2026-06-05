<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductionMaterialUsage;
use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Models\User;
use App\Models\BahanDasar;
use App\Models\BatchBahanDasar;
use App\Models\PemakaianBahanDasarProduksi;
use App\Services\ProductionMaterialService;
use App\Services\ProductionBahanDasarService;
use App\Services\ProductStockService;
use App\Support\FormatHelper;
use App\Support\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = ProductionRecord::query()->with([
            'product',
            'materialUsages.rawMaterial',
            'materialUsages.restockBatch',
            'bahanDasarUsages.bahanDasar',
            'bahanDasarUsages.batchBahanDasar',
        ]);

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
                'label' => __('production.stats.total'),
                'value' => $total,
                'tone'  => 'blue',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>',
            ],
            [
                'label' => __('production.stats.success'),
                'value' => $sukses,
                'tone'  => 'green',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
            ],
            [
                'label' => __('production.stats.failed'),
                'value' => $gagal,
                'tone'  => 'rose',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>',
            ],
            [
                'label' => __('production.stats.success_rate'),
                'value' => $rate,
                'tone'  => 'amber',
                'icon'  => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>',
            ],
        ];

        $materials = $this->materialsForProductionForm($records->pluck('id'));
        $bahanDasarItems = $this->bahanDasarForProductionForm($records->pluck('id'));

        $user = Auth::user();
        $role = $user instanceof User ? $user->role : 'admin';
        $viewName = $role === 'admin' ? 'admin.produksi' : 'karyawan.produksi';

        $catalogProductNames = Product::query()->orderBy('nama')->pluck('nama');

        return view($viewName, compact('records', 'search', 'stats', 'materials', 'bahanDasarItems', 'catalogProductNames'));
    }

    public function show(string $id)
    {
        $record = ProductionRecord::query()
            ->with([
                'product',
                'materialUsages.rawMaterial',
                'materialUsages.restockBatch',
                'bahanDasarUsages.bahanDasar',
                'bahanDasarUsages.batchBahanDasar',
            ])
            ->findOrFail($id);

        $catalogProduct = Product::findByName($record->product_name);
        $stockContribution = $record->status === 'Berhasil' ? (int) $record->jumlah : 0;
        $materialCount = $record->materialUsages->count();
        $bahanDasarCount = $record->bahanDasarUsages->count();
        $materials = $this->materialsForProductionForm(collect([$record->id]));
        $bahanDasarItems = $this->bahanDasarForProductionForm(collect([$record->id]));

        $user = Auth::user();
        $role = $user instanceof User ? $user->role : 'admin';
        $viewName = $role === 'admin' ? 'admin.produksi-show' : 'karyawan.produksi-show';

        return view($viewName, compact(
            'record',
            'catalogProduct',
            'stockContribution',
            'materialCount',
            'bahanDasarCount',
            'materials',
            'bahanDasarItems',
        ));
    }

    public function store(Request $request, ProductionMaterialService $materialService, ProductionBahanDasarService $bahanDasarService, ProductStockService $stockService)
    {
        $this->normalizeMaterialInputs($request);
        $this->normalizeBahanDasarInputs($request);
        $this->normalizeJumlahInput($request);
        $this->applyBahanDasarToggleToRequest($request);

        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'product_name' => ['required', 'string', 'max:255'],
            'jumlah'       => ['required', 'integer', 'min:0'],
            'status'       => ['required', 'string', 'in:Berhasil,Gagal'],
            'notes'        => ['nullable', 'string', 'max:500'],
            'use_bahan_dasar' => ['nullable', 'boolean'],
            'materials'    => ['nullable', 'array'],
            'materials.*.raw_material_id' => ['required_with:materials', 'string', Rule::exists('raw_materials', 'id')],
            'materials.*.raw_material_restock_id' => ['nullable', 'integer', Rule::exists('raw_material_restocks', 'id')],
            'materials.*.jumlah'          => ['required_with:materials', 'string', 'regex:/^\d+(\.\d)?$/'],
            'materials.*.satuan'          => ['required_with:materials', 'string', 'max:50'],
            'bahan_dasar'  => ['nullable', 'array'],
            'bahan_dasar.*.bahan_dasar_id' => ['required_with:bahan_dasar', 'string', Rule::exists('bahan_dasar', 'id')],
            'bahan_dasar.*.batch_bahan_dasar_id' => ['required_with:bahan_dasar', 'integer', Rule::exists('batch_bahan_dasar', 'id')],
            'bahan_dasar.*.jumlah' => ['required_with:bahan_dasar', 'string', 'regex:/^\d+(\.\d)?$/'],
            'bahan_dasar.*.satuan' => ['required_with:bahan_dasar', 'string', 'max:50'],
        ]);

        $materialLines = $materialService->normalizeLines($request->input('materials', []));
        $bahanDasarLines = $bahanDasarService->normalizeLines($request->input('bahan_dasar', []));
        $this->assertMaterialInputRules($request, $materialLines, $bahanDasarLines);

        $data['satuan'] = 'pcs';

        if ($data['status'] === 'Berhasil' && (int) $data['jumlah'] < 1) {
            return redirect()->back()->withInput()->withErrors([
                'jumlah' => __('messages.validation.production_qty_required_when_success'),
            ]);
        }

        $data = FormatHelper::applyTitleCase($data, ['product_name']);
        $data['id'] = ProductionRecord::generateNextId();

        DB::transaction(function () use ($data, $materialLines, $bahanDasarLines, $materialService, $bahanDasarService, $stockService) {
            $record = ProductionRecord::create($data);

            if ($materialLines !== []) {
                $materialService->apply($record, $materialLines);
            }

            if ($bahanDasarLines !== []) {
                $bahanDasarService->apply($record, $bahanDasarLines);
            }

            $materialService->updateProductionTotals($record->fresh());
            $stockService->afterProductionSaved($record->fresh());
        });

        return redirect()->back()->with(
            'success',
            __('messages.flash.production_saved', ['name' => $data['product_name']])
        );
    }

    public function update(Request $request, string $id, ProductionMaterialService $materialService, ProductionBahanDasarService $bahanDasarService, ProductStockService $stockService)
    {
        $record = ProductionRecord::with(['materialUsages', 'bahanDasarUsages'])->findOrFail($id);
        $before = $record->replicate();

        $this->normalizeMaterialInputs($request);
        $this->normalizeBahanDasarInputs($request);
        $this->normalizeJumlahInput($request);
        $this->applyBahanDasarToggleToRequest($request);

        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'product_name' => ['required', 'string', 'max:255'],
            'jumlah'       => ['required', 'integer', 'min:0'],
            'status'       => ['required', 'string', 'in:Berhasil,Gagal'],
            'notes'        => ['nullable', 'string', 'max:500'],
            'use_bahan_dasar' => ['nullable', 'boolean'],
            'materials'    => ['nullable', 'array'],
            'materials.*.raw_material_id' => ['required_with:materials', 'string', Rule::exists('raw_materials', 'id')],
            'materials.*.raw_material_restock_id' => ['nullable', 'integer', Rule::exists('raw_material_restocks', 'id')],
            'materials.*.jumlah'          => ['required_with:materials', 'string', 'regex:/^\d+(\.\d)?$/'],
            'materials.*.satuan'          => ['required_with:materials', 'string', 'max:50'],
            'bahan_dasar'  => ['nullable', 'array'],
            'bahan_dasar.*.bahan_dasar_id' => ['required_with:bahan_dasar', 'string', Rule::exists('bahan_dasar', 'id')],
            'bahan_dasar.*.batch_bahan_dasar_id' => ['required_with:bahan_dasar', 'integer', Rule::exists('batch_bahan_dasar', 'id')],
            'bahan_dasar.*.jumlah' => ['required_with:bahan_dasar', 'string', 'regex:/^\d+(\.\d)?$/'],
            'bahan_dasar.*.satuan' => ['required_with:bahan_dasar', 'string', 'max:50'],
        ]);

        $materialLines = $materialService->normalizeLines($request->input('materials', []));
        $bahanDasarLines = $bahanDasarService->normalizeLines($request->input('bahan_dasar', []));
        $this->assertMaterialInputRules($request, $materialLines, $bahanDasarLines);

        $data['satuan'] = 'pcs';

        if ($data['status'] === 'Berhasil' && (int) $data['jumlah'] < 1) {
            return redirect()->back()->withInput()->withErrors([
                'jumlah' => __('messages.validation.production_qty_required_when_success'),
            ]);
        }

        $data = FormatHelper::applyTitleCase($data, ['product_name']);

        DB::transaction(function () use ($record, $before, $data, $materialLines, $bahanDasarLines, $materialService, $bahanDasarService, $stockService) {
            $materialService->reverse($record);
            $bahanDasarService->reverse($record);
            $record->update($data);

            if ($materialLines !== []) {
                $materialService->apply($record, $materialLines);
            }

            if ($bahanDasarLines !== []) {
                $bahanDasarService->apply($record, $bahanDasarLines);
            }

            $materialService->updateProductionTotals($record->fresh());
            $stockService->afterProductionSaved($record->fresh(), $before);
        });

        return redirect()->back()->with(
            'success',
            __('messages.flash.production_updated', ['name' => $record->product_name])
        );
    }

    public function destroy(string $id, ProductionMaterialService $materialService, ProductionBahanDasarService $bahanDasarService, ProductStockService $stockService)
    {
        $record = ProductionRecord::findOrFail($id);
        $nama = $record->product_name;
        $productName = $record->product_name;

        DB::transaction(function () use ($record, $materialService, $bahanDasarService, $stockService, $productName) {
            $materialService->reverse($record);
            $bahanDasarService->reverse($record);
            $record->delete();
            $stockService->syncForName($productName);
        });

        return redirect()->back()->with(
            'success',
            __('messages.flash.production_deleted', ['name' => $nama])
        );
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

    private function materialsForProductionForm($recordIds)
    {
        $ids = collect($recordIds)->filter()->values();

        $linkedBatchIds = ProductionMaterialUsage::query()
            ->whereIn('production_record_id', $ids)
            ->whereNotNull('raw_material_restock_id')
            ->pluck('raw_material_restock_id')
            ->unique()
            ->values();

        $materials = RawMaterial::query()
            ->with(['restocks' => function ($q) use ($linkedBatchIds) {
                $q->where(function ($q) use ($linkedBatchIds) {
                    $q->where('sisa', '>', 0);
                    if ($linkedBatchIds->isNotEmpty()) {
                        $q->orWhereIn('id', $linkedBatchIds);
                    }
                })
                    ->orderByRaw('CASE WHEN expired IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('expired')
                    ->orderBy('tanggal')
                    ->orderBy('id');
            }])
            ->orderBy('nama')
            ->get(['id', 'nama', 'jumlah', 'satuan', 'harga']);

        $usageAdjustments = ProductionMaterialUsage::query()
            ->whereIn('production_record_id', $ids)
            ->whereNotNull('raw_material_restock_id')
            ->get()
            ->groupBy('raw_material_restock_id');

        foreach ($materials as $material) {
            foreach ($material->restocks as $restock) {
                foreach ($usageAdjustments->get($restock->id, collect()) as $usage) {
                    if ($usage->raw_material_id !== $material->id) {
                        continue;
                    }

                    $restore = UnitConverter::convert(
                        (float) $usage->jumlah,
                        $usage->satuan,
                        $material->satuan
                    ) ?? (float) $usage->jumlah;

                    $restock->sisa = (float) $restock->sisa + $restore;
                }
            }

            $material->setRelation(
                'restocks',
                $material->restocks->filter(fn ($restock) => (float) $restock->sisa > 0)->values()
            );
        }

        return $materials;
    }

    private function bahanDasarForProductionForm($recordIds)
    {
        $ids = collect($recordIds)->filter()->values();

        $linkedBatchIds = PemakaianBahanDasarProduksi::query()
            ->whereIn('production_record_id', $ids)
            ->pluck('batch_bahan_dasar_id')
            ->unique()
            ->values();

        $items = BahanDasar::query()
            ->with(['batches' => function ($q) use ($linkedBatchIds) {
                $q->where(function ($q) use ($linkedBatchIds) {
                    $q->where('sisa', '>', 0);
                    if ($linkedBatchIds->isNotEmpty()) {
                        $q->orWhereIn('id', $linkedBatchIds);
                    }
                })
                    ->orderByDesc('tanggal')
                    ->orderByDesc('id');
            }])
            ->where(function ($q) use ($linkedBatchIds) {
                $q->where('jumlah', '>', 0);
                if ($linkedBatchIds->isNotEmpty()) {
                    $q->orWhereIn('id', BatchBahanDasar::query()->whereIn('id', $linkedBatchIds)->pluck('bahan_dasar_id'));
                }
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'jumlah', 'satuan', 'harga']);

        $usageAdjustments = PemakaianBahanDasarProduksi::query()
            ->whereIn('production_record_id', $ids)
            ->get()
            ->groupBy('batch_bahan_dasar_id');

        foreach ($items as $item) {
            foreach ($item->batches as $batch) {
                foreach ($usageAdjustments->get($batch->id, collect()) as $usage) {
                    if ($usage->bahan_dasar_id !== $item->id) {
                        continue;
                    }

                    $restore = \App\Support\UnitConverter::convert(
                        (float) $usage->jumlah,
                        $usage->satuan,
                        $item->satuan
                    ) ?? (float) $usage->jumlah;

                    $batch->sisa = (float) $batch->sisa + $restore;
                }
            }

            $item->setRelation(
                'batches',
                $item->batches->filter(fn ($batch) => (float) $batch->sisa > 0)->values()
            );
        }

        return $items->filter(fn ($item) => $item->batches->isNotEmpty() || (float) $item->jumlah > 0)->values();
    }

    private function applyBahanDasarToggleToRequest(Request $request): void
    {
        if (! $request->boolean('use_bahan_dasar')) {
            $request->merge(['bahan_dasar' => null]);
        }
    }

    private function normalizeBahanDasarInputs(Request $request): void
    {
        $rows = $request->input('bahan_dasar');

        if (! is_array($rows)) {
            return;
        }

        foreach ($rows as $index => $row) {
            if (! isset($row['jumlah']) || $row['jumlah'] === '') {
                continue;
            }

            $rows[$index]['jumlah'] = FormatHelper::formatQtyInput($row['jumlah']);
        }

        $request->merge(['bahan_dasar' => $rows]);
    }

    private function normalizeJumlahInput(Request $request): void
    {
        $jumlah = $request->input('jumlah');

        if ($jumlah === null || $jumlah === '') {
            return;
        }

        $request->merge(['jumlah' => FormatHelper::formatIntegerInput($jumlah)]);
    }

    /** @param  array<int, array<string, mixed>>  $materialLines */
    /** @param  array<int, array<string, mixed>>  $bahanDasarLines */
    private function assertMaterialInputRules(Request $request, array $materialLines, array $bahanDasarLines): void
    {
        $useBahanDasar = $request->boolean('use_bahan_dasar');

        if ($useBahanDasar) {
            if ($bahanDasarLines === []) {
                throw ValidationException::withMessages([
                    'bahan_dasar' => __('messages.validation.bahan_dasar_required'),
                ]);
            }

            return;
        }

        if ($materialLines === []) {
            throw ValidationException::withMessages([
                'materials' => __('messages.validation.materials_required'),
            ]);
        }
    }
}
