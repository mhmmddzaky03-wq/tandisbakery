<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\ProductionMaterialUsage;
use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Models\RawMaterialRestock;
use App\Support\FormatHelper;
use App\Support\UnitConverter;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionMaterialService
{
    private const MATERIALS_ACCOUNT = '1-130';

    private const ADONAN_INVENTORY_ACCOUNT = '1-140';

    private const FINISHED_GOODS_ACCOUNT = '1-150';

    private const OTHER_EXPENSE_ACCOUNT = '5-180';

    /** @param  array<int, array{raw_material_id: string, raw_material_restock_id?: int|string|null, jumlah: float, satuan?: string}>  $lines */
    public function apply(ProductionRecord $record, array $lines): int
    {
        return DB::transaction(function () use ($record, $lines) {
            $this->assertValidLines($lines);
            $this->assertStockAvailable($lines);

            $totalCost = 0;

            foreach ($lines as $line) {
                $material = RawMaterial::lockForUpdate()->findOrFail($line['raw_material_id']);

                $usageUnit = $line['satuan'] ?? $material->satuan;
                UnitConverter::convertMaterial($material, $usageUnit);

                $qty = (float) $line['jumlah'];
                $qtyInMaterialUnit = UnitConverter::convert($qty, $usageUnit, $material->satuan) ?? $qty;
                $batchId = $line['raw_material_restock_id'] ?? null;

                if ($batchId) {
                    $batch = RawMaterialRestock::lockForUpdate()->findOrFail($batchId);

                    if ($batch->raw_material_id !== $material->id) {
                        throw ValidationException::withMessages([
                            'materials' => 'Batch stok tidak sesuai dengan bahan baku yang dipilih.',
                        ]);
                    }

                    $batchSisa = (float) $batch->sisa;

                    if ($qtyInMaterialUnit > $batchSisa + 0.000_1) {
                        throw ValidationException::withMessages([
                            'materials' => 'Stok batch '.$material->nama.' tidak cukup.',
                        ]);
                    }

                    $unitPrice = (int) $batch->harga;

                    $batch->sisa = max(0, $batchSisa - $qtyInMaterialUnit);
                    $batch->saveQuietly();
                } else {
                    $materialStock = (float) $material->jumlah;

                    if ($qtyInMaterialUnit > $materialStock + 0.000_1) {
                        throw ValidationException::withMessages([
                            'materials' => 'Stok '.$material->nama.' tidak cukup.',
                        ]);
                    }

                    $unitPrice = (int) $material->harga;
                }

                $lineTotal = (int) round($qtyInMaterialUnit * $unitPrice);

                ProductionMaterialUsage::create([
                    'production_record_id' => $record->id,
                    'raw_material_id' => $material->id,
                    'raw_material_restock_id' => $batchId,
                    'jumlah' => $qty,
                    'satuan' => $usageUnit,
                    'harga_satuan' => $unitPrice,
                    'total' => $lineTotal,
                ]);

                $material->jumlah = max(0, (float) $material->jumlah - $qtyInMaterialUnit);
                $material->saveQuietly();

                $totalCost += $lineTotal;
            }

            return $totalCost;
        });
    }

    public function reverse(ProductionRecord $record): void
    {
        DB::transaction(function () use ($record) {
            $record->load('materialUsages');

            foreach ($record->materialUsages as $usage) {
                $material = RawMaterial::lockForUpdate()->find($usage->raw_material_id);
                if ($material) {
                    $restoreQty = UnitConverter::convert(
                        (float) $usage->jumlah,
                        $usage->satuan,
                        $material->satuan
                    ) ?? (float) $usage->jumlah;

                    $material->jumlah = (float) $material->jumlah + $restoreQty;
                    $material->saveQuietly();

                    if ($usage->raw_material_restock_id) {
                        $batch = RawMaterialRestock::lockForUpdate()->find($usage->raw_material_restock_id);
                        if ($batch) {
                            $batch->sisa = (float) $batch->sisa + $restoreQty;
                            $batch->saveQuietly();
                        }
                    }
                }
            }

            $this->deleteJournal($record->journal_transaction_id);
            $record->materialUsages()->delete();

            $record->update([
                'total_material_cost' => 0,
                'journal_transaction_id' => null,
            ]);
        });
    }

    /** @param  array<int, array{raw_material_id?: string, raw_material_restock_id?: mixed, jumlah?: mixed, satuan?: string}>  $rawLines */
    public function normalizeLines(array $rawLines): array
    {
        $lines = [];

        foreach ($rawLines as $row) {
            $materialId = trim((string) ($row['raw_material_id'] ?? ''));
            $batchId = $row['raw_material_restock_id'] ?? null;
            $qtyRaw = $row['jumlah'] ?? null;
            $satuan = trim((string) ($row['satuan'] ?? ''));

            if ($materialId === '' && ($qtyRaw === null || $qtyRaw === '')) {
                continue;
            }

            $lines[] = [
                'raw_material_id' => $materialId,
                'raw_material_restock_id' => $batchId !== null && $batchId !== '' ? (int) $batchId : null,
                'jumlah' => FormatHelper::normalizeQtyOne(
                    FormatHelper::formatQtyInput($qtyRaw)
                ),
                'satuan' => $satuan,
            ];
        }

        return $lines;
    }

    /** @param  array<int, array{raw_material_id: string, raw_material_restock_id?: int|null, jumlah: float|null, satuan?: string}>  $lines */
    private function assertValidLines(array $lines): void
    {
        if (count($lines) === 0) {
            throw ValidationException::withMessages([
                'materials' => 'Minimal satu bahan baku wajib diisi.',
            ]);
        }

        $seenBatches = [];

        foreach ($lines as $index => $line) {
            $key = "materials.{$index}";

            if (empty($line['raw_material_id'])) {
                throw ValidationException::withMessages([
                    "{$key}.raw_material_id" => 'Pilih bahan baku',
                ]);
            }

            $material = RawMaterial::find($line['raw_material_id']);
            $batchId = $line['raw_material_restock_id'] ?? null;
            $usageUnit = $line['satuan'] ?? null;

            if ($material && $this->materialHasAvailableBatches($material->id) && empty($batchId)) {
                throw ValidationException::withMessages([
                    "{$key}.raw_material_restock_id" => 'Pilih batch stok',
                ]);
            }

            if (! empty($batchId)) {
                $batchId = (int) $batchId;
                if (isset($seenBatches[$batchId])) {
                    throw ValidationException::withMessages([
                        'materials' => 'Batch stok yang sama tidak boleh dipakai dua kali dalam satu produksi.',
                    ]);
                }

                $seenBatches[$batchId] = true;

                $batch = RawMaterialRestock::find($batchId);

                if ($batch && $material && $batch->raw_material_id !== $material->id) {
                    throw ValidationException::withMessages([
                        "{$key}.raw_material_restock_id" => 'Batch stok tidak sesuai bahan baku.',
                    ]);
                }
            }

            $qty = $line['jumlah'];
            if ($qty === null || $qty <= 0) {
                throw ValidationException::withMessages([
                    "{$key}.jumlah" => 'Takaran harus lebih dari 0.',
                ]);
            }

            if (empty($line['satuan'])) {
                throw ValidationException::withMessages([
                    "{$key}.satuan" => 'Pilih satuan.',
                ]);
            }

            if ($material && $usageUnit !== null && $usageUnit !== '') {
                if (! UnitConverter::canConvert($material->satuan, $usageUnit)) {
                    throw ValidationException::withMessages([
                        "{$key}.satuan" => 'Satuan tidak sesuai dengan bahan baku.',
                    ]);
                }
            }
        }
    }

    private function materialHasAvailableBatches(string $materialId): bool
    {
        return RawMaterialRestock::query()
            ->where('raw_material_id', $materialId)
            ->where('sisa', '>', 0)
            ->exists();
    }

    /** @param  array<int, array{raw_material_id: string, raw_material_restock_id?: int|null, jumlah: float, satuan?: string}>  $lines */
    private function assertStockAvailable(array $lines): void
    {
        $errors = [];

        foreach ($lines as $index => $line) {
            $material = RawMaterial::find($line['raw_material_id']);
            if (! $material) {
                continue;
            }

            $usageUnit = $line['satuan'] ?? $material->satuan;
            $needed = UnitConverter::convert((float) $line['jumlah'], $usageUnit, $material->satuan)
                ?? (float) $line['jumlah'];
            $batch = RawMaterialRestock::find($line['raw_material_restock_id'] ?? null);

            if ($batch) {
                $available = (float) $batch->sisa;
                $stockLabel = 'batch '.$material->nama;
            } else {
                $available = (float) $material->jumlah;
                $stockLabel = $material->nama;
            }

            if ($needed > $available + 0.000_1) {
                $displayAvailable = UnitConverter::convert($available, $material->satuan, $usageUnit) ?? $available;
                $errors["materials.{$index}.jumlah"] = 'Stok '.$stockLabel.' tidak cukup (tersedia '.FormatHelper::formatQtyOne($displayAvailable).' '.$usageUnit.').';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function createJournal(ProductionRecord $record, int $materialCost, int $bahanDasarCost): ?int
    {
        $totalCost = $materialCost + $bahanDasarCost;

        if ($totalCost <= 0) {
            return null;
        }

        $tx = JournalTransaction::create([
            'tanggal' => $record->tanggal,
            'deskripsi' => 'Produksi '.$record->product_name,
            'ref' => 'PROD-'.$record->id,
        ]);

        if ($record->status === 'Berhasil') {
            if ($materialCost > 0) {
                JournalEntry::create([
                    'journal_transaction_id' => $tx->id,
                    'account_kode' => self::MATERIALS_ACCOUNT,
                    'debit' => 0,
                    'credit' => $materialCost,
                ]);
            }

            if ($bahanDasarCost > 0) {
                JournalEntry::create([
                    'journal_transaction_id' => $tx->id,
                    'account_kode' => self::ADONAN_INVENTORY_ACCOUNT,
                    'debit' => 0,
                    'credit' => $bahanDasarCost,
                ]);
            }

            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::FINISHED_GOODS_ACCOUNT,
                'debit' => $totalCost,
                'credit' => 0,
            ]);
        } else {
            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::OTHER_EXPENSE_ACCOUNT,
                'debit' => $totalCost,
                'credit' => 0,
            ]);

            if ($materialCost > 0) {
                JournalEntry::create([
                    'journal_transaction_id' => $tx->id,
                    'account_kode' => self::MATERIALS_ACCOUNT,
                    'debit' => 0,
                    'credit' => $materialCost,
                ]);
            }

            if ($bahanDasarCost > 0) {
                JournalEntry::create([
                    'journal_transaction_id' => $tx->id,
                    'account_kode' => self::ADONAN_INVENTORY_ACCOUNT,
                    'debit' => 0,
                    'credit' => $bahanDasarCost,
                ]);
            }
        }

        return $tx->id;
    }

    private function deleteJournal(?int $journalId): void
    {
        if (! $journalId) {
            return;
        }

        JournalTransaction::where('id', $journalId)->delete();
    }

    public function updateProductionTotals(ProductionRecord $record): void
    {
        $record->load(['materialUsages', 'bahanDasarUsages']);

        $materialCost = (int) $record->materialUsages->sum('total');
        $bahanDasarCost = (int) $record->bahanDasarUsages->sum('total');
        $totalCost = $materialCost + $bahanDasarCost;

        $this->deleteJournal($record->journal_transaction_id);
        $journalId = $this->createJournal($record, $materialCost, $bahanDasarCost);

        $record->update([
            'total_material_cost' => $totalCost,
            'journal_transaction_id' => $journalId,
        ]);
    }
}
