<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\RawMaterial;
use App\Models\RawMaterialRestock;
use Illuminate\Support\Facades\DB;

class RawMaterialRestockService
{
    private const MATERIALS_ACCOUNT = '1-130';

    private const CASH_ACCOUNT = '1-110';

    public function record(
        RawMaterial $material,
        string $tanggal,
        float $jumlah,
        int $harga,
        ?string $catatan = null,
        bool $createJournal = true,
    ): RawMaterialRestock {
        return DB::transaction(function () use ($material, $tanggal, $jumlah, $harga, $catatan, $createJournal) {
            $total = (int) round($jumlah * $harga);

            $journalId = null;
            if ($createJournal && $total > 0) {
                $journalId = $this->createPurchaseJournal(
                    $tanggal,
                    $total,
                    $material->nama,
                    $material->id
                );
            }

            $restock = RawMaterialRestock::create([
                'raw_material_id' => $material->id,
                'tanggal' => $tanggal,
                'jumlah' => $jumlah,
                'harga' => $harga,
                'total' => $total,
                'catatan' => $catatan,
                'journal_transaction_id' => $journalId,
            ]);

            $this->applyStockIncrease($material, $jumlah, $harga);

            return $restock;
        });
    }

    public function applyStockIncrease(RawMaterial $material, float $addedQty, int $unitPrice): void
    {
        $oldQty = (float) $material->jumlah;
        $oldHarga = (int) $material->harga;
        $newQty = $oldQty + $addedQty;

        $avgHarga = $newQty <= 0
            ? $unitPrice
            : (int) round((($oldQty * $oldHarga) + ($addedQty * $unitPrice)) / $newQty);

        $material->jumlah = $newQty;
        $material->harga = $avgHarga;
        $material->saveQuietly();
    }

    private function createPurchaseJournal(string $tanggal, int $total, string $materialName, string $materialId): int
    {
        $tx = JournalTransaction::create([
            'tanggal' => $tanggal,
            'deskripsi' => 'Pembelian bahan baku '.$materialName,
            'ref' => 'RESTOCK-'.$materialId,
        ]);

        JournalEntry::create([
            'journal_transaction_id' => $tx->id,
            'account_kode' => self::MATERIALS_ACCOUNT,
            'debit' => $total,
            'credit' => 0,
        ]);

        JournalEntry::create([
            'journal_transaction_id' => $tx->id,
            'account_kode' => self::CASH_ACCOUNT,
            'debit' => 0,
            'credit' => $total,
        ]);

        return $tx->id;
    }
}
