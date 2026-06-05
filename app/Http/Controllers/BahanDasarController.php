<?php

namespace App\Http\Controllers;

use App\Models\BahanDasar;
use App\Models\PemakaianBahanDasarProduksi;
use App\Models\RawMaterial;
use App\Services\BahanDasarMaterialService;
use App\Support\FormatHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BahanDasarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = BahanDasar::query()->withCount('batches');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('id')->get();
        $materials = $this->materialsForForm();

        return view('admin.bahan-dasar', compact('items', 'search', 'materials'));
    }

    public function show(string $id)
    {
        $item = BahanDasar::query()
            ->with([
                'batches' => fn ($q) => $q->orderByDesc('tanggal')->orderByDesc('id'),
                'batches.pemakaianBahanBaku.bahanBaku',
                'batches.pemakaianBahanBaku.batchBahanBaku',
            ])
            ->withCount('batches')
            ->findOrFail($id);

        $satuan = $item->satuan ?? 'g';
        $batches = $item->batches;
        $activeBatches = $batches->filter(fn ($b) => (float) $b->sisa > 0)->values();
        $totalBiayaBatch = (int) $batches->sum('total_biaya');
        $totalNilai = (int) round((float) $item->jumlah * (int) $item->harga);
        $materials = $this->materialsForForm();

        $pemakaianProduksi = PemakaianBahanDasarProduksi::query()
            ->where('bahan_dasar_id', $id)
            ->with(['productionRecord', 'batchBahanDasar'])
            ->orderByDesc('created_at')
            ->get();

        $produksiTerpakai = $pemakaianProduksi
            ->groupBy('production_record_id')
            ->map(function ($usages) use ($satuan) {
                $record = $usages->first()?->productionRecord;
                $totalQty = $usages->sum(fn ($u) => (float) $u->jumlah);
                $totalBiaya = $usages->sum(fn ($u) => (int) $u->total);

                return (object) [
                    'record' => $record,
                    'usages' => $usages,
                    'total_qty' => $totalQty,
                    'total_biaya' => $totalBiaya,
                    'satuan' => $usages->first()?->satuan ?? $satuan,
                ];
            })
            ->filter(fn ($row) => $row->record !== null)
            ->sortByDesc(fn ($row) => $row->record->tanggal)
            ->values();

        return view('admin.bahan-dasar-show', compact(
            'item',
            'satuan',
            'batches',
            'activeBatches',
            'totalBiayaBatch',
            'totalNilai',
            'materials',
            'pemakaianProduksi',
            'produksiTerpakai',
        ));
    }

    public function store(Request $request, BahanDasarMaterialService $materialService)
    {
        $this->normalizeMinInput($request);
        $this->normalizeMaterialInputs($request);
        $this->normalizeJumlahHasilInput($request);

        $data = $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'satuan'        => ['required', 'string', 'max:50', Rule::in(['g', 'gram', 'kg'])],
            'min'           => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'tanggal'       => ['required', 'date'],
            'jumlah_hasil'  => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'catatan'       => ['nullable', 'string', 'max:500'],
            'materials'     => ['required', 'array', 'min:1'],
            'materials.*.raw_material_id' => ['required', 'string', Rule::exists('raw_materials', 'id')],
            'materials.*.raw_material_restock_id' => ['nullable', 'integer', Rule::exists('raw_material_restocks', 'id')],
            'materials.*.jumlah' => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'materials.*.satuan' => ['required', 'string', 'max:50'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['nama']);
        $satuan = $data['satuan'] === 'gram' ? 'g' : $data['satuan'];
        $outputQty = (float) FormatHelper::normalizeQtyOne($data['jumlah_hasil']);
        $lines = $materialService->normalizeLines($request->input('materials', []));

        DB::transaction(function () use ($materialService, $data, $satuan, $outputQty, $lines) {
            $item = BahanDasar::create([
                'id'     => BahanDasar::generateNextId(),
                'nama'   => $data['nama'],
                'satuan' => $satuan,
                'min'    => FormatHelper::normalizeQtyOne($data['min']),
                'jumlah' => 0,
                'harga'  => 0,
            ]);

            $materialService->applyBatch(
                $item,
                $lines,
                $outputQty,
                $data['tanggal'],
                $data['catatan'] ?? null,
            );
        });

        return redirect()->route('admin.bahan_dasar')->with('success', __('messages.flash.bahan_dasar_created', ['name' => $data['nama']]));
    }

    public function update(Request $request, string $id)
    {
        $item = BahanDasar::findOrFail($id);
        $this->normalizeMinInput($request);

        $data = $request->validate([
            'nama'   => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50', Rule::in(['g', 'gram', 'kg'])],
            'min'    => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['nama']);
        $satuan = $data['satuan'] === 'gram' ? 'g' : $data['satuan'];

        if ($satuan !== $item->satuan && $item->batches()->exists()) {
            throw ValidationException::withMessages([
                'satuan' => __('messages.validation.satuan_locked_after_batch'),
            ]);
        }

        $item->update([
            'nama'   => $data['nama'],
            'satuan' => $satuan,
            'min'    => FormatHelper::normalizeQtyOne($data['min']),
        ]);

        return redirect()->back()->with('success', __('messages.flash.bahan_dasar_updated', ['name' => $item->nama]));
    }

    public function buatAdonan(Request $request, string $id, BahanDasarMaterialService $materialService)
    {
        $item = BahanDasar::findOrFail($id);
        $this->normalizeMaterialInputs($request);
        $this->normalizeJumlahHasilInput($request);

        $data = $request->validate([
            'tanggal'       => ['required', 'date'],
            'jumlah_hasil'  => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'catatan'       => ['nullable', 'string', 'max:500'],
            'materials'     => ['required', 'array', 'min:1'],
            'materials.*.raw_material_id' => ['required', 'string', Rule::exists('raw_materials', 'id')],
            'materials.*.raw_material_restock_id' => ['nullable', 'integer', Rule::exists('raw_material_restocks', 'id')],
            'materials.*.jumlah' => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'materials.*.satuan' => ['required', 'string', 'max:50'],
        ]);

        $outputQty = (float) FormatHelper::normalizeQtyOne($data['jumlah_hasil']);
        $lines = $materialService->normalizeLines($request->input('materials', []));

        DB::transaction(function () use ($materialService, $item, $lines, $data, $outputQty) {
            $materialService->applyBatch(
                $item,
                $lines,
                $outputQty,
                $data['tanggal'],
                $data['catatan'] ?? null,
            );
        });

        return redirect()->back()->with('success', __('messages.flash.dough_created', ['name' => $item->nama]));
    }

    public function destroy(string $id)
    {
        $item = BahanDasar::findOrFail($id);

        if (! $item->canBeDeleted()) {
            return redirect()->back()->with('error', __('messages.flash.bahan_dasar_delete_blocked'));
        }

        $nama = $item->nama;
        $item->delete();

        return redirect()->route('admin.bahan_dasar')->with('success', __('messages.flash.bahan_dasar_deleted', ['name' => $nama]));
    }

    public function destroyBatch(string $id, int $batchId, BahanDasarMaterialService $materialService)
    {
        $item = BahanDasar::findOrFail($id);
        $batch = $item->batches()->findOrFail($batchId);

        if ((float) $batch->sisa < (float) $batch->jumlah - 0.000_1) {
            return redirect()->back()->with('error', __('messages.flash.dough_batch_delete_blocked'));
        }

        DB::transaction(fn () => $materialService->reverseBatch($batch));

        return redirect()->back()->with('success', __('messages.flash.dough_batch_deleted'));
    }

    private function materialsForForm()
    {
        return RawMaterial::query()
            ->with(['restocks' => function ($q) {
                $q->where('sisa', '>', 0)
                    ->orderByRaw('CASE WHEN expired IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('expired')
                    ->orderBy('tanggal')
                    ->orderBy('id');
            }])
            ->orderBy('nama')
            ->get(['id', 'nama', 'jumlah', 'satuan', 'harga']);
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

    private function normalizeJumlahHasilInput(Request $request): void
    {
        $jumlah = $request->input('jumlah_hasil');

        if ($jumlah === null || $jumlah === '') {
            return;
        }

        $request->merge(['jumlah_hasil' => FormatHelper::formatQtyInput($jumlah)]);
    }

    private function normalizeMinInput(Request $request): void
    {
        $min = $request->input('min');

        if ($min === null || $min === '') {
            return;
        }

        $request->merge(['min' => FormatHelper::formatQtyInput($min)]);
    }
}
