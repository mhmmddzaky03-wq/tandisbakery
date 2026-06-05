<?php

namespace Database\Seeders\Support;

use App\Models\BatchBahanDasar;
use App\Models\BahanDasar;
use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Models\RawMaterialRestock;
use App\Services\BahanDasarMaterialService;
use App\Services\ProductionBahanDasarService;
use App\Services\ProductionMaterialService;
use App\Services\ProductStockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeederHelpers
{
    public static function truncateAll(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'pemakaian_bahan_dasar_produksi',
            'pemakaian_bahan_baku_adonan',
            'production_material_usages',
            'batch_bahan_dasar',
            'bahan_dasar',
            'raw_material_restocks',
            'operational_costs',
            'sales_transactions',
            'production_records',
            'products',
            'journal_entries',
            'journal_transactions',
            'raw_materials',
            'activity_logs',
            'expense_categories',
            'accounts',
            'sessions',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    public static function ensureRawMaterialStock(string $materialId, float $qty, string $unit, string $tanggal): void
    {
        $material = RawMaterial::findOrFail($materialId);
        $needed = \App\Support\UnitConverter::convert($qty, $unit, $material->satuan) ?? $qty;

        $hasSufficientBatch = RawMaterialRestock::query()
            ->where('raw_material_id', $materialId)
            ->where('sisa', '>=', $needed)
            ->exists();

        if ($hasSufficientBatch) {
            return;
        }

        app(\App\Services\RawMaterialRestockService::class)->record(
            $material,
            $tanggal,
            max($needed, 0.001),
            (int) $material->harga,
            'Auto-restock seeder',
            createJournal: false,
        );
    }

    /** @param  array<int, array{id: string, qty: float, unit: string}>  $lines */
    public static function toRawMaterialLines(array $lines): array
    {
        $result = [];

        foreach ($lines as $line) {
            $material = RawMaterial::findOrFail($line['id']);
            $usageUnit = $line['unit'];
            $neededInBase = \App\Support\UnitConverter::convert(
                (float) $line['qty'],
                $usageUnit,
                $material->satuan,
            ) ?? (float) $line['qty'];

            $batch = RawMaterialRestock::query()
                ->where('raw_material_id', $material->id)
                ->where('sisa', '>=', $neededInBase)
                ->orderBy('tanggal')
                ->orderBy('id')
                ->first();

            if (! $batch) {
                $batch = RawMaterialRestock::query()
                    ->where('raw_material_id', $material->id)
                    ->where('sisa', '>', 0)
                    ->orderByDesc('sisa')
                    ->orderBy('tanggal')
                    ->first();
            }

            $result[] = [
                'raw_material_id' => $material->id,
                'raw_material_restock_id' => $batch?->id,
                'jumlah' => $line['qty'],
                'satuan' => $usageUnit,
            ];
        }

        return $result;
    }

    public static function createBahanDasarBatch(
        string $bahanDasarId,
        string $tanggal,
        ?string $catatan = null,
        float $scale = 1,
    ): BatchBahanDasar {
        $bom = RecipeCatalog::bahanDasarBom()[$bahanDasarId];
        $bahanDasar = BahanDasar::findOrFail($bahanDasarId);

        $scaledLines = array_map(
            fn (array $line) => [
                'id' => $line['id'],
                'qty' => $line['qty'] * $scale,
                'unit' => $line['unit'],
            ],
            $bom['lines'],
        );

        foreach ($scaledLines as $line) {
            self::ensureRawMaterialStock($line['id'], $line['qty'], $line['unit'], $tanggal);
        }

        $lines = self::toRawMaterialLines($scaledLines);

        return app(BahanDasarMaterialService::class)->applyBatch(
            $bahanDasar,
            $lines,
            (float) $bom['output'] * $scale,
            $tanggal,
            $catatan,
        );
    }

    public static function ensureBahanDasarStock(
        string $bahanDasarId,
        float $neededQty,
        string $unit,
        string $tanggal,
    ): void {
        $bahanDasar = BahanDasar::findOrFail($bahanDasarId);
        $neededInBase = \App\Support\UnitConverter::convert($neededQty, $unit, $bahanDasar->satuan) ?? $neededQty;
        $bom = RecipeCatalog::bahanDasarBom()[$bahanDasarId];
        $batchOutput = (float) $bom['output'];

        $hasSufficientBatch = BatchBahanDasar::query()
            ->where('bahan_dasar_id', $bahanDasarId)
            ->where('sisa', '>=', $neededInBase)
            ->exists();

        if ($hasSufficientBatch) {
            return;
        }

        $scale = max(1, (int) ceil($neededInBase / $batchOutput));
        self::createBahanDasarBatch($bahanDasarId, $tanggal, 'Auto-restock seeder', (float) $scale);
    }

    /**
     * @param  array<int, array{bahan_dasar_id: string, qty: float, unit: string}>  $bahanDasarUsages
     * @param  array<int, array{id: string, qty: float, unit: string}>  $rawLines
     */
    public static function createProduction(
        string $id,
        string $tanggal,
        string $productName,
        int $qty,
        string $satuan,
        array $bahanDasarUsages,
        array $rawLines,
        string $status = 'Berhasil',
        ?string $notes = null,
    ): ProductionRecord {
        $record = ProductionRecord::create([
            'id' => $id,
            'tanggal' => $tanggal,
            'product_name' => $productName,
            'jumlah' => $status === 'Berhasil' ? $qty : 0,
            'satuan' => $satuan,
            'status' => $status,
            'notes' => $notes ?? '-',
            'total_material_cost' => 0,
        ]);

        $materialService = app(ProductionMaterialService::class);
        $bahanDasarService = app(ProductionBahanDasarService::class);

        if ($rawLines !== []) {
            foreach ($rawLines as $line) {
                self::ensureRawMaterialStock($line['id'], $line['qty'], $line['unit'], $tanggal);
            }

            $materialService->apply($record, self::toRawMaterialLines($rawLines));
        }

        if ($bahanDasarUsages !== []) {
            $bdLines = [];

            foreach ($bahanDasarUsages as $usage) {
                $bahanDasar = BahanDasar::findOrFail($usage['bahan_dasar_id']);
                $usageUnit = $usage['unit'];
                $neededInBase = \App\Support\UnitConverter::convert(
                    (float) $usage['qty'],
                    $usageUnit,
                    $bahanDasar->satuan,
                ) ?? (float) $usage['qty'];

                self::ensureBahanDasarStock($usage['bahan_dasar_id'], $usage['qty'], $usageUnit, $tanggal);

                $batch = BatchBahanDasar::query()
                    ->where('bahan_dasar_id', $usage['bahan_dasar_id'])
                    ->where('sisa', '>=', $neededInBase)
                    ->orderBy('tanggal')
                    ->orderBy('id')
                    ->first();

                if (! $batch) {
                    $batch = BatchBahanDasar::query()
                        ->where('bahan_dasar_id', $usage['bahan_dasar_id'])
                        ->where('sisa', '>', 0)
                        ->orderByDesc('sisa')
                        ->first();
                }

                if (! $batch) {
                    continue;
                }

                $bdLines[] = [
                    'bahan_dasar_id' => $usage['bahan_dasar_id'],
                    'batch_bahan_dasar_id' => $batch->id,
                    'jumlah' => $usage['qty'],
                    'satuan' => $usageUnit,
                ];
            }

            if ($bdLines !== []) {
                $bahanDasarService->apply($record, $bdLines);
            }
        }

        $materialService->updateProductionTotals($record->fresh());
        app(ProductStockService::class)->afterProductionSaved($record->fresh());

        return $record->fresh();
    }

    public static function ensureStockForDemo(): void
    {
        $restockTargets = [
            'SBB001' => 80,
            'SBB002' => 40,
            'SBB003' => 15,
            'SBB004' => 50,
            'SBB005' => 10,
            'SBB006' => 5,
            'SBB007' => 3,
            'SBB008' => 5,
            'SBB009' => 40,
            'SBB010' => 200,
            'SBB011' => 80,
            'SBB012' => 50,
            'SBB013' => 20,
            'SBB014' => 10,
            'SBB015' => 10,
            'SBB016' => 0.5,
            'SBB017' => 8,
            'SBB018' => 25,
            'SBB019' => 60,
            'SBB020' => 30,
            'SBB021' => 5,
            'SBB022' => 5,
            'SBB023' => 2,
            'SBB024' => 2,
            'SBB025' => 3,
            'SBB026' => 15,
            'SBB027' => 2,
            'SBB028' => 10,
            'SBB029' => 30,
            'SBB030' => 5,
        ];

        $tanggal = now()->subDays(3)->toDateString();

        foreach ($restockTargets as $materialId => $targetQty) {
            $material = RawMaterial::find($materialId);

            if (! $material) {
                continue;
            }

            $current = (float) $material->fresh()->jumlah;

            if ($current >= $targetQty) {
                continue;
            }

            $needed = $targetQty - $current;

            app(\App\Services\RawMaterialRestockService::class)->record(
                $material,
                $tanggal,
                $needed,
                (int) $material->harga,
                'Stok awal demo presentasi',
                createJournal: false,
            );
        }
    }
}
