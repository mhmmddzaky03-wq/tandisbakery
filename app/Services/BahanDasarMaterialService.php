<?php

namespace App\Services;

use App\Models\BatchBahanDasar;
use App\Models\BahanDasar;
use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\PemakaianBahanBakuAdonan;
use App\Models\RawMaterial;
use App\Models\RawMaterialRestock;
use App\Support\FormatHelper;
use App\Support\UnitConverter;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BahanDasarMaterialService
{
    private const MATERIALS_ACCOUNT = '1-130';

    private const ADONAN_INVENTORY_ACCOUNT = '1-140';

    /** @param  array<int, array{raw_material_id?: string, raw_material_restock_id?: mixed, jumlah?: mixed, satuan?: string}>  $rawLines */
    public function normalizeLines(array $rawLines): array
    {
        return app(ProductionMaterialService::class)->normalizeLines($rawLines);
    }

    /** @param  array<int, array{raw_material_id: string, raw_material_restock_id?: int|null, jumlah: float, satuan?: string}>  $lines */
    public function applyBatch(BahanDasar $bahanDasar, array $lines, float $outputQty, string $tanggal, ?string $catatan = null): BatchBahanDasar
    {
        return DB::transaction(function () use ($bahanDasar, $lines, $outputQty, $tanggal, $catatan) {
            $this->assertValidLines($lines);
            $this->assertStockAvailable($lines);

            if ($outputQty <= 0) {
                throw ValidationException::withMessages([
                    'jumlah_hasil' => 'Jumlah adonan hasil wajib lebih dari 0.',
                ]);
            }

            $totalCost = 0;

            $batch = BatchBahanDasar::create([
                'bahan_dasar_id' => $bahanDasar->id,
                'tanggal' => $tanggal,
                'jumlah' => $outputQty,
                'sisa' => $outputQty,
                'total_biaya' => 0,
                'catatan' => $catatan,
            ]);

            foreach ($lines as $line) {
                $material = RawMaterial::lockForUpdate()->findOrFail($line['raw_material_id']);
                $usageUnit = $line['satuan'] ?? $material->satuan;
                $qty = (float) $line['jumlah'];
                $qtyInMaterialUnit = UnitConverter::convert($qty, $usageUnit, $material->satuan) ?? $qty;
                $batchId = $line['raw_material_restock_id'] ?? null;

                if ($batchId) {
                    $restockBatch = RawMaterialRestock::lockForUpdate()->findOrFail($batchId);

                    if ($restockBatch->raw_material_id !== $material->id) {
                        throw ValidationException::withMessages([
                            'materials' => 'Batch stok tidak sesuai dengan bahan baku yang dipilih.',
                        ]);
                    }

                    if ($qtyInMaterialUnit > (float) $restockBatch->sisa + 0.000_1) {
                        throw ValidationException::withMessages([
                            'materials' => 'Stok batch '.$material->nama.' tidak cukup.',
                        ]);
                    }

                    $unitPrice = (int) $restockBatch->harga;
                    $restockBatch->sisa = max(0, (float) $restockBatch->sisa - $qtyInMaterialUnit);
                    $restockBatch->saveQuietly();
                } else {
                    if ($qtyInMaterialUnit > (float) $material->jumlah + 0.000_1) {
                        throw ValidationException::withMessages([
                            'materials' => 'Stok '.$material->nama.' tidak cukup.',
                        ]);
                    }

                    $unitPrice = (int) $material->harga;
                }

                $lineTotal = (int) round($qtyInMaterialUnit * $unitPrice);

                PemakaianBahanBakuAdonan::create([
                    'batch_bahan_dasar_id' => $batch->id,
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

            $journalId = $this->createBatchJournal($batch, $bahanDasar, $totalCost, $tanggal);

            $batch->update([
                'total_biaya' => $totalCost,
                'journal_transaction_id' => $journalId,
            ]);

            $this->recalculateInventoryStats($bahanDasar->fresh());

            return $batch->fresh(['pemakaianBahanBaku.bahanBaku', 'pemakaianBahanBaku.batchBahanBaku']);
        });
    }

    public function reverseBatch(BatchBahanDasar $batch): void
    {
        DB::transaction(function () use ($batch) {
            $batch->load(['pemakaianBahanBaku', 'bahanDasar']);
            $bahanDasar = $batch->bahanDasar;

            if (! $bahanDasar) {
                return;
            }

            foreach ($batch->pemakaianBahanBaku as $usage) {
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
                        $restockBatch = RawMaterialRestock::lockForUpdate()->find($usage->raw_material_restock_id);

                        if ($restockBatch) {
                            $restockBatch->sisa = (float) $restockBatch->sisa + $restoreQty;
                            $restockBatch->saveQuietly();
                        }
                    }
                }
            }

            $this->deleteJournal($batch->journal_transaction_id);

            $batch->pemakaianBahanBaku()->delete();
            $batch->delete();

            $this->recalculateInventoryStats($bahanDasar->fresh());
        });
    }

    public function recalculateInventoryStats(BahanDasar $bahanDasar): void
    {
        $batches = BatchBahanDasar::query()
            ->where('bahan_dasar_id', $bahanDasar->id)
            ->get();

        $totalSisa = 0.0;
        $totalValue = 0;

        foreach ($batches as $batch) {
            $sisa = (float) $batch->sisa;

            if ($sisa <= 0) {
                continue;
            }

            $totalSisa += $sisa;
            $totalValue += $batch->remainingValue();
        }

        $bahanDasar->update([
            'jumlah' => $totalSisa,
            'harga' => $totalSisa > 0 ? (int) round($totalValue / $totalSisa) : 0,
        ]);
    }

    private function createBatchJournal(BatchBahanDasar $batch, BahanDasar $bahanDasar, int $totalCost, string $tanggal): ?int
    {
        if ($totalCost <= 0) {
            return null;
        }

        $tx = JournalTransaction::create([
            'tanggal' => $tanggal,
            'deskripsi' => 'Buat adonan '.$bahanDasar->nama,
            'ref' => 'ADON-'.$batch->id,
        ]);

        JournalEntry::create([
            'journal_transaction_id' => $tx->id,
            'account_kode' => self::ADONAN_INVENTORY_ACCOUNT,
            'debit' => $totalCost,
            'credit' => 0,
        ]);
        JournalEntry::create([
            'journal_transaction_id' => $tx->id,
            'account_kode' => self::MATERIALS_ACCOUNT,
            'debit' => 0,
            'credit' => $totalCost,
        ]);

        return $tx->id;
    }

    private function deleteJournal(?int $journalId): void
    {
        if (! $journalId) {
            return;
        }

        JournalTransaction::where('id', $journalId)->delete();
    }

    /** @param  array<int, array{raw_material_id: string, raw_material_restock_id?: int|null, jumlah: float, satuan?: string}>  $lines */
    private function assertValidLines(array $lines): void
    {
        if ($lines === []) {
            throw ValidationException::withMessages([
                'materials' => 'Pilih minimal satu bahan baku.',
            ]);
        }

        $seenBatches = [];

        foreach ($lines as $index => $line) {
            $key = "materials.{$index}";

            if (empty($line['raw_material_id'])) {
                throw ValidationException::withMessages([
                    "{$key}.raw_material_id" => 'Pilih bahan baku.',
                ]);
            }

            $batchId = $line['raw_material_restock_id'] ?? null;

            if (! empty($batchId)) {
                $batchId = (int) $batchId;

                if (isset($seenBatches[$batchId])) {
                    throw ValidationException::withMessages([
                        'materials' => 'Batch stok yang sama tidak boleh dipakai dua kali.',
                    ]);
                }

                $seenBatches[$batchId] = true;
            }

            if ((float) ($line['jumlah'] ?? 0) <= 0) {
                throw ValidationException::withMessages([
                    "{$key}.jumlah" => 'Takaran harus lebih dari 0.',
                ]);
            }
        }
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
            $restockBatch = RawMaterialRestock::find($line['raw_material_restock_id'] ?? null);

            if ($restockBatch) {
                $available = (float) $restockBatch->sisa;
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
}
