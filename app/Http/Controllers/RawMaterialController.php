<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Unit;
use App\Models\User;
use App\Services\RawMaterialRestockService;
use App\Support\FormatHelper;
use App\Support\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('kategori');
        $query = RawMaterial::query()->with('restocks')->withCount(['materialUsages', 'adonanUsages']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($filter && array_key_exists($filter, RawMaterial::kategoriOptions())) {
            $query->where('kategori', $filter);
        }

        $materials = $query->orderBy('id', 'asc')->get();
        $units = Unit::orderedForDisplay();
        $user = $request->user();
        $role = $user instanceof User ? $user->role : 'admin';
        $viewName = $role === 'admin' ? 'admin.stok' : 'karyawan.persediaan';

        return view($viewName, compact('materials', 'search', 'filter', 'units'));
    }

    public function show(Request $request, string $id)
    {
        $material = RawMaterial::query()
            ->with([
                'restocks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                'materialUsages.productionRecord.product',
                'materialUsages.restockBatch',
                'adonanUsages.batchBahanDasar.bahanDasar',
                'adonanUsages.batchBahanBaku',
            ])
            ->withCount(['materialUsages', 'adonanUsages'])
            ->findOrFail($id);

        $satuan = $material->satuan ?? 'kg';

        $usages = $material->materialUsages
            ->sort(function ($a, $b) {
                $dateA = $a->productionRecord?->tanggal?->format('Y-m-d') ?? '';
                $dateB = $b->productionRecord?->tanggal?->format('Y-m-d') ?? '';

                if ($dateA !== $dateB) {
                    return strcmp($dateB, $dateA);
                }

                return strcmp($b->productionRecord?->id ?? '', $a->productionRecord?->id ?? '');
            })
            ->values();

        $totalNilai = (int) round((float) $material->jumlah * (int) $material->harga);
        $totalUsageValue = (int) $usages->sum('total');
        $totalUsageQty = $usages->reduce(
            function (float $carry, $usage) use ($material): float {
                $converted = UnitConverter::convert((float) $usage->jumlah, $usage->satuan, $material->satuan);

                return $carry + ($converted ?? (float) $usage->jumlah);
            },
            0.0
        );

        $adonanUsages = $material->adonanUsages
            ->sort(function ($a, $b) {
                $dateA = $a->batchBahanDasar?->tanggal?->format('Y-m-d') ?? '';
                $dateB = $b->batchBahanDasar?->tanggal?->format('Y-m-d') ?? '';

                if ($dateA !== $dateB) {
                    return strcmp($dateB, $dateA);
                }

                return ($b->batch_bahan_dasar_id ?? 0) <=> ($a->batch_bahan_dasar_id ?? 0);
            })
            ->values();

        $totalAdonanUsageValue = (int) $adonanUsages->sum('total');
        $totalAdonanUsageQty = $adonanUsages->reduce(
            function (float $carry, $usage) use ($material): float {
                $converted = UnitConverter::convert((float) $usage->jumlah, $usage->satuan, $material->satuan);

                return $carry + ($converted ?? (float) $usage->jumlah);
            },
            0.0
        );

        $restockHistory = $material->restocks
            ->sort(function ($a, $b) {
                $dateA = $a->tanggal?->format('Y-m-d') ?? '';
                $dateB = $b->tanggal?->format('Y-m-d') ?? '';

                if ($dateA !== $dateB) {
                    return strcmp($dateB, $dateA);
                }

                return $b->id <=> $a->id;
            })
            ->values();

        $activeBatches = $material->restocks
            ->filter(fn ($restock) => (float) $restock->sisa > 0)
            ->sort(function ($a, $b) {
                if ($a->expired === null && $b->expired !== null) {
                    return 1;
                }
                if ($a->expired !== null && $b->expired === null) {
                    return -1;
                }
                if ($a->expired !== null && $b->expired !== null) {
                    $byExpired = $a->expired <=> $b->expired;
                    if ($byExpired !== 0) {
                        return $byExpired;
                    }
                }

                $byTanggal = ($a->tanggal?->format('Y-m-d') ?? '') <=> ($b->tanggal?->format('Y-m-d') ?? '');
                if ($byTanggal !== 0) {
                    return $byTanggal;
                }

                return $a->id <=> $b->id;
            })
            ->values();

        $batchSisaTotal = (float) $activeBatches->sum(fn ($restock) => (float) $restock->sisa);
        $untrackedStock = max(0, round((float) $material->jumlah - $batchSisaTotal, 1));

        $units = Unit::orderedForDisplay();
        $user = $request->user();
        $role = $user instanceof User ? $user->role : 'admin';
        $viewName = $role === 'admin' ? 'admin.stok-show' : 'karyawan.persediaan-show';

        return view($viewName, compact(
            'material',
            'usages',
            'adonanUsages',
            'satuan',
            'totalNilai',
            'totalUsageValue',
            'totalUsageQty',
            'totalAdonanUsageValue',
            'totalAdonanUsageQty',
            'activeBatches',
            'batchSisaTotal',
            'untrackedStock',
            'restockHistory',
            'units',
        ));
    }

    public function store(Request $request, RawMaterialRestockService $restockService)
    {
        $this->normalizeJumlahInput($request);
        $this->normalizeMinInput($request);

        $requiresBatch = (float) (FormatHelper::normalizeQtyOne($request->input('jumlah')) ?? 0) > 0;

        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', Rule::in(array_keys(RawMaterial::kategoriOptions()))],
            'jumlah'   => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'satuan'   => ['required', 'string', 'max:50', Rule::exists('units', 'nama')],
            'min'      => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'harga'    => ['required', 'integer', 'min:0'],
            'kode_produksi' => [Rule::requiredIf($requiresBatch), 'nullable', 'string', 'max:100'],
            'expired'  => [Rule::requiredIf($requiresBatch), 'nullable', 'date'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['nama']);
        $jumlah = FormatHelper::normalizeQtyOne($data['jumlah']);
        $min = FormatHelper::normalizeQtyOne($data['min']);
        $harga = (int) $data['harga'];

        $material = RawMaterial::create([
            'id' => RawMaterial::generateNextId(),
            'nama' => $data['nama'],
            'kategori' => $data['kategori'],
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
                true,
                $data['kode_produksi'] ?? null,
                $data['expired'] ?? null,
            );
        } else {
            $material->harga = $harga;
            $material->saveQuietly();
        }

        return redirect()->back()->with('success', __('messages.flash.raw_material_created', ['name' => $material->nama]));
    }

    public function update(Request $request, string $id)
    {
        $material = RawMaterial::with('restocks')->findOrFail($id);

        $this->normalizeMinInput($request);

        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', Rule::in(array_keys(RawMaterial::kategoriOptions()))],
            'satuan'   => ['required', 'string', 'max:50', Rule::exists('units', 'nama')],
            'min'      => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'kode_produksi' => [Rule::requiredIf($material->restocks->isNotEmpty()), 'nullable', 'string', 'max:100'],
            'expired'  => [Rule::requiredIf($material->restocks->isNotEmpty()), 'nullable', 'date'],
        ]);

        $data = FormatHelper::applyTitleCase($data, ['nama']);
        $data['min'] = FormatHelper::normalizeQtyOne($data['min']);

        $material->update([
            'nama' => $data['nama'],
            'kategori' => $data['kategori'],
            'satuan' => $data['satuan'],
            'min' => $data['min'],
        ]);

        $latestRestock = $material->latestRestock();
        if ($latestRestock) {
            $latestRestock->update([
                'kode_produksi' => filled($data['kode_produksi'] ?? null) ? trim($data['kode_produksi']) : null,
                'expired' => $data['expired'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', __('messages.flash.raw_material_updated', ['name' => $material->nama]));
    }

    public function restock(Request $request, string $id, RawMaterialRestockService $restockService)
    {
        $material = RawMaterial::findOrFail($id);

        $this->normalizeRestockJumlahInput($request);

        $data = $request->validate([
            'restock_tanggal' => ['required', 'date'],
            'restock_jumlah'  => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'restock_harga'   => ['required', 'integer', 'min:1'],
            'restock_kode_produksi' => ['required', 'string', 'max:100'],
            'restock_expired' => ['required', 'date'],
            'restock_catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $jumlah = FormatHelper::normalizeQtyOne($data['restock_jumlah']);

        $restockService->record(
            $material,
            $data['restock_tanggal'],
            (float) $jumlah,
            (int) $data['restock_harga'],
            $data['restock_catatan'] ?? null,
            true,
            $data['restock_kode_produksi'] ?? null,
            $data['restock_expired'] ?? null,
        );

        return redirect()->back()->with('success', __('messages.flash.raw_material_restocked', ['name' => $material->nama]));
    }

    public function destroy(string $id)
    {
        $material = RawMaterial::findOrFail($id);

        if (! $material->canBeDeleted()) {
            return redirect()->back()->with('error', __('messages.flash.raw_material_delete_blocked'));
        }

        $nama = $material->nama;
        $material->delete();

        return redirect()->back()->with('success', __('messages.flash.raw_material_deleted', ['name' => $nama]));
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
