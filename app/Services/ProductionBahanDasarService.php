<?php

namespace App\Services;

use App\Models\BatchBahanDasar;
use App\Models\BahanDasar;
use App\Models\PemakaianBahanDasarProduksi;
use App\Models\ProductionRecord;
use App\Support\FormatHelper;
use App\Support\UnitConverter;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionBahanDasarService
{
    public function __construct(
        private readonly BahanDasarMaterialService $bahanDasarMaterialService,
    ) {}

    /** @param  array<int, array{bahan_dasar_id?: string, batch_bahan_dasar_id?: mixed, jumlah?: mixed, satuan?: string}>  $rawLines */
    public function normalizeLines(array $rawLines): array
    {
        $lines = [];

        foreach ($rawLines as $row) {
            $bahanDasarId = trim((string) ($row['bahan_dasar_id'] ?? ''));
            $batchId = $row['batch_bahan_dasar_id'] ?? null;
            $qtyRaw = $row['jumlah'] ?? null;
            $satuan = trim((string) ($row['satuan'] ?? ''));

            if ($bahanDasarId === '' && ($qtyRaw === null || $qtyRaw === '')) {
                continue;
            }

            $lines[] = [
                'bahan_dasar_id' => $bahanDasarId,
                'batch_bahan_dasar_id' => $batchId !== null && $batchId !== '' ? (int) $batchId : null,
                'jumlah' => FormatHelper::normalizeQtyOne(
                    FormatHelper::formatQtyInput($qtyRaw)
                ),
                'satuan' => $satuan,
            ];
        }

        return $lines;
    }

    /** @param  array<int, array{bahan_dasar_id: string, batch_bahan_dasar_id: int, jumlah: float, satuan?: string}>  $lines */
    public function apply(ProductionRecord $record, array $lines): int
    {
        if ($lines === []) {
            return 0;
        }

        return DB::transaction(function () use ($record, $lines) {
            $this->assertValidLines($lines);
            $this->assertStockAvailable($lines);

            $totalCost = 0;
            $affectedBahanDasarIds = [];

            foreach ($lines as $line) {
                $bahanDasar = BahanDasar::lockForUpdate()->findOrFail($line['bahan_dasar_id']);
                $batch = BatchBahanDasar::lockForUpdate()->findOrFail($line['batch_bahan_dasar_id']);

                if ($batch->bahan_dasar_id !== $bahanDasar->id) {
                    throw ValidationException::withMessages([
                        'bahan_dasar' => __('messages.validation.dough_batch_mismatch'),
                    ]);
                }

                $usageUnit = $line['satuan'] ?: $bahanDasar->satuan;
                $qty = (float) $line['jumlah'];
                $qtyInBaseUnit = UnitConverter::convert($qty, $usageUnit, $bahanDasar->satuan) ?? $qty;

                if ($qtyInBaseUnit > (float) $batch->sisa + 0.000_1) {
                    throw ValidationException::withMessages([
                        'bahan_dasar' => __('messages.validation.stock_batch_insufficient', ['name' => $bahanDasar->nama]),
                    ]);
                }

                $unitPrice = $batch->unitPricePerBase();
                $lineTotal = (int) round($qtyInBaseUnit * $unitPrice);

                PemakaianBahanDasarProduksi::create([
                    'production_record_id' => $record->id,
                    'batch_bahan_dasar_id' => $batch->id,
                    'bahan_dasar_id' => $bahanDasar->id,
                    'jumlah' => $qty,
                    'satuan' => $usageUnit,
                    'harga_satuan' => $unitPrice,
                    'total' => $lineTotal,
                ]);

                $batch->sisa = max(0, (float) $batch->sisa - $qtyInBaseUnit);
                $batch->saveQuietly();

                $affectedBahanDasarIds[$bahanDasar->id] = true;
                $totalCost += $lineTotal;
            }

            foreach (array_keys($affectedBahanDasarIds) as $bahanDasarId) {
                $item = BahanDasar::find($bahanDasarId);
                if ($item) {
                    $this->bahanDasarMaterialService->recalculateInventoryStats($item);
                }
            }

            return $totalCost;
        });
    }

    public function reverse(ProductionRecord $record): void
    {
        DB::transaction(function () use ($record) {
            $record->load('bahanDasarUsages.batchBahanDasar', 'bahanDasarUsages.bahanDasar');
            $affectedBahanDasarIds = [];

            foreach ($record->bahanDasarUsages as $usage) {
                $bahanDasar = BahanDasar::lockForUpdate()->find($usage->bahan_dasar_id);
                $batch = BatchBahanDasar::lockForUpdate()->find($usage->batch_bahan_dasar_id);

                if ($bahanDasar) {
                    $restoreQty = UnitConverter::convert(
                        (float) $usage->jumlah,
                        $usage->satuan,
                        $bahanDasar->satuan
                    ) ?? (float) $usage->jumlah;

                    if ($batch) {
                        $batch->sisa = (float) $batch->sisa + $restoreQty;
                        $batch->saveQuietly();
                    }

                    $affectedBahanDasarIds[$bahanDasar->id] = true;
                }
            }

            $record->bahanDasarUsages()->delete();

            foreach (array_keys($affectedBahanDasarIds) as $bahanDasarId) {
                $item = BahanDasar::find($bahanDasarId);
                if ($item) {
                    $this->bahanDasarMaterialService->recalculateInventoryStats($item);
                }
            }
        });
    }

    /** @param  array<int, array{bahan_dasar_id: string, batch_bahan_dasar_id: int, jumlah: float, satuan?: string}>  $lines */
    private function assertValidLines(array $lines): void
    {
        $seenBatches = [];

        foreach ($lines as $index => $line) {
            $key = "bahan_dasar.{$index}";

            if (empty($line['bahan_dasar_id'])) {
                throw ValidationException::withMessages([
                    "{$key}.bahan_dasar_id" => __('messages.validation.select_bahan_dasar'),
                ]);
            }

            $batchId = $line['batch_bahan_dasar_id'] ?? null;

            if (empty($batchId)) {
                throw ValidationException::withMessages([
                    "{$key}.batch_bahan_dasar_id" => __('messages.validation.select_dough_batch'),
                ]);
            }

            $batchId = (int) $batchId;

            if (isset($seenBatches[$batchId])) {
                throw ValidationException::withMessages([
                    'bahan_dasar' => __('messages.validation.duplicate_dough_batch'),
                ]);
            }

            $seenBatches[$batchId] = true;

            $bahanDasar = BahanDasar::find($line['bahan_dasar_id']);
            $batch = BatchBahanDasar::find($batchId);

            if ($batch && $bahanDasar && $batch->bahan_dasar_id !== $bahanDasar->id) {
                throw ValidationException::withMessages([
                    "{$key}.batch_bahan_dasar_id" => __('messages.validation.dough_batch_mismatch_short'),
                ]);
            }

            if ((float) ($line['jumlah'] ?? 0) <= 0) {
                throw ValidationException::withMessages([
                    "{$key}.jumlah" => __('messages.validation.dosage_must_be_positive'),
                ]);
            }
        }
    }

    /** @param  array<int, array{bahan_dasar_id: string, batch_bahan_dasar_id: int, jumlah: float, satuan?: string}>  $lines */
    private function assertStockAvailable(array $lines): void
    {
        $errors = [];

        foreach ($lines as $index => $line) {
            $bahanDasar = BahanDasar::find($line['bahan_dasar_id']);
            $batch = BatchBahanDasar::find($line['batch_bahan_dasar_id'] ?? null);

            if (! $bahanDasar || ! $batch) {
                continue;
            }

            $usageUnit = $line['satuan'] ?: $bahanDasar->satuan;
            $needed = UnitConverter::convert((float) $line['jumlah'], $usageUnit, $bahanDasar->satuan)
                ?? (float) $line['jumlah'];
            $available = (float) $batch->sisa;

            if ($needed > $available + 0.000_1) {
                $displayAvailable = UnitConverter::convert($available, $bahanDasar->satuan, $usageUnit) ?? $available;
                $errors["bahan_dasar.{$index}.jumlah"] = __('messages.validation.stock_insufficient_detail', [
                    'label' => __('messages.validation.stock_batch_label', ['name' => $bahanDasar->nama]),
                    'available' => FormatHelper::formatQtyOne($displayAvailable),
                    'unit' => $usageUnit,
                ]);
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
