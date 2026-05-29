<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\ProductionMaterialUsage;
use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Support\FormatHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionMaterialService
{
    private const MATERIALS_ACCOUNT = '1-130';

    private const WIP_ACCOUNT = '1-140';

    private const FINISHED_GOODS_ACCOUNT = '1-150';

    private const OTHER_EXPENSE_ACCOUNT = '5-180';

    /** @param  array<int, array{raw_material_id: string, jumlah: float}>  $lines */
    public function apply(ProductionRecord $record, array $lines): int
    {
        return DB::transaction(function () use ($record, $lines) {
            $this->assertValidLines($lines);
            $this->assertStockAvailable($lines);

            $totalCost = 0;

            foreach ($lines as $line) {
                $material = RawMaterial::lockForUpdate()->findOrFail($line['raw_material_id']);
                $qty = (float) $line['jumlah'];
                $unitPrice = (int) $material->harga;
                $lineTotal = (int) round($qty * $unitPrice);

                ProductionMaterialUsage::create([
                    'production_record_id' => $record->id,
                    'raw_material_id' => $material->id,
                    'jumlah' => $qty,
                    'satuan' => $material->satuan,
                    'harga_satuan' => $unitPrice,
                    'total' => $lineTotal,
                ]);

                $material->jumlah = max(0, (float) $material->jumlah - $qty);
                $material->saveQuietly();

                $totalCost += $lineTotal;
            }

            $journalId = $this->createJournal($record, $totalCost);

            $record->update([
                'total_material_cost' => $totalCost,
                'journal_transaction_id' => $journalId,
            ]);

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
                    $material->jumlah = (float) $material->jumlah + (float) $usage->jumlah;
                    $material->saveQuietly();
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

    /** @param  array<int, array{raw_material_id?: string, jumlah?: mixed}>  $rawLines */
    public function normalizeLines(array $rawLines): array
    {
        $lines = [];

        foreach ($rawLines as $row) {
            $materialId = trim((string) ($row['raw_material_id'] ?? ''));
            $qtyRaw = $row['jumlah'] ?? null;

            if ($materialId === '' && ($qtyRaw === null || $qtyRaw === '')) {
                continue;
            }

            $lines[] = [
                'raw_material_id' => $materialId,
                'jumlah' => FormatHelper::normalizeQtyOne(
                    FormatHelper::formatQtyInput($qtyRaw)
                ),
            ];
        }

        return $lines;
    }

    /** @param  array<int, array{raw_material_id: string, jumlah: float|null}>  $lines */
    private function assertValidLines(array $lines): void
    {
        if (count($lines) === 0) {
            throw ValidationException::withMessages([
                'materials' => 'Minimal satu bahan baku wajib diisi.',
            ]);
        }

        $seen = [];

        foreach ($lines as $index => $line) {
            $key = "materials.{$index}";

            if (empty($line['raw_material_id'])) {
                throw ValidationException::withMessages([
                    "{$key}.raw_material_id" => 'Pilih bahan baku',
                ]);
            }

            if (isset($seen[$line['raw_material_id']])) {
                throw ValidationException::withMessages([
                    'materials' => 'Bahan baku tidak boleh duplikat dalam satu produksi.',
                ]);
            }

            $seen[$line['raw_material_id']] = true;

            $qty = $line['jumlah'];
            if ($qty === null || $qty <= 0) {
                throw ValidationException::withMessages([
                    "{$key}.jumlah" => 'Takaran harus lebih dari 0.',
                ]);
            }
        }
    }

    /** @param  array<int, array{raw_material_id: string, jumlah: float}>  $lines */
    private function assertStockAvailable(array $lines): void
    {
        $errors = [];

        foreach ($lines as $index => $line) {
            $material = RawMaterial::find($line['raw_material_id']);
            if (! $material) {
                continue;
            }

            if ((float) $line['jumlah'] > (float) $material->jumlah) {
                $errors["materials.{$index}.jumlah"] = 'Stok '.$material->nama.' tidak cukup (tersedia '.FormatHelper::formatQtyOne($material->jumlah).' '.$material->satuan.').';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function createJournal(ProductionRecord $record, int $totalCost): ?int
    {
        if ($totalCost <= 0) {
            return null;
        }

        $tx = JournalTransaction::create([
            'tanggal' => $record->tanggal,
            'deskripsi' => 'Produksi '.$record->product_name,
            'ref' => 'PROD-'.$record->id,
        ]);

        if ($record->status === 'Berhasil') {
            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::WIP_ACCOUNT,
                'debit' => $totalCost,
                'credit' => 0,
            ]);
            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::MATERIALS_ACCOUNT,
                'debit' => 0,
                'credit' => $totalCost,
            ]);
            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::FINISHED_GOODS_ACCOUNT,
                'debit' => $totalCost,
                'credit' => 0,
            ]);
            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::WIP_ACCOUNT,
                'debit' => 0,
                'credit' => $totalCost,
            ]);
        } else {
            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::OTHER_EXPENSE_ACCOUNT,
                'debit' => $totalCost,
                'credit' => 0,
            ]);
            JournalEntry::create([
                'journal_transaction_id' => $tx->id,
                'account_kode' => self::MATERIALS_ACCOUNT,
                'debit' => 0,
                'credit' => $totalCost,
            ]);
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
}
