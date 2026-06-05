<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use App\Models\SalesTransaction;
use App\Services\RawMaterialRestockService;
use App\Services\SalesJournalService;
use Database\Seeders\Support\RecipeCatalog;
use Database\Seeders\Support\SeederHelpers;
use Illuminate\Database\Seeder;

/**
 * Data operasional asli Juni 2025 dari Excel Tandi's Bakery:
 * penjualan harian, biaya operasional, restock bahan baku, produksi historis.
 */
class June2025Seeder extends Seeder
{
    public function run(): void
    {
        $this->seedInitialStock();
        $this->seedRestocks();
        $this->seedSales();
        $this->call(OperationalCostSeeder::class);
        $this->seedJuneProduction();
    }

    private function seedInitialStock(): void
    {
        $service = app(RawMaterialRestockService::class);
        $tanggal = '2025-06-01';

        $openingStock = [
            'SBB001' => 60, 'SBB002' => 30, 'SBB003' => 12, 'SBB004' => 40, 'SBB005' => 10,
            'SBB006' => 4, 'SBB007' => 2, 'SBB008' => 5, 'SBB009' => 30, 'SBB010' => 180,
            'SBB011' => 60, 'SBB012' => 50, 'SBB013' => 15, 'SBB014' => 15, 'SBB015' => 8,
            'SBB016' => 0.5, 'SBB017' => 6, 'SBB018' => 20, 'SBB019' => 40, 'SBB020' => 25,
            'SBB021' => 4, 'SBB022' => 4, 'SBB023' => 2, 'SBB024' => 2, 'SBB025' => 2,
            'SBB026' => 12, 'SBB027' => 2, 'SBB028' => 8, 'SBB029' => 24, 'SBB030' => 4,
        ];

        foreach ($openingStock as $materialId => $qty) {
            $material = RawMaterial::find($materialId);

            if (! $material || $qty <= 0) {
                continue;
            }

            $service->record(
                $material,
                $tanggal,
                $qty,
                (int) $material->harga,
                'Stok awal Juni 2025',
            );
        }
    }

    private function seedRestocks(): void
    {
        $service = app(RawMaterialRestockService::class);

        foreach (RecipeCatalog::june2025Restocks() as $row) {
            $material = RawMaterial::find($row['material_id']);

            if (! $material) {
                continue;
            }

            $service->record(
                $material,
                $row['tanggal'],
                $row['qty'],
                $row['harga'],
                $row['catatan'],
            );
        }
    }

    private function seedSales(): void
    {
        $salesJournal = app(SalesJournalService::class);
        $seq = 1;

        foreach (RecipeCatalog::june2025DailySales() as $row) {
            $id = 'TRS'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);

            $sale = SalesTransaction::create([
                'id' => $id,
                'tanggal' => $row['date'],
                'total' => $row['total'],
                'metode' => $row['metode'],
                'jumlah' => $row['qty'],
            ]);

            $salesJournal->sync($sale);
            $seq++;
        }
    }

    private function seedJuneProduction(): void
    {
        SeederHelpers::createBahanDasarBatch('BD001', '2025-06-05', 'Batch adonan roti manis — Juni 2025');
        SeederHelpers::createBahanDasarBatch('BD001', '2025-06-18', 'Batch adonan roti manis — Juni 2025');
        SeederHelpers::createBahanDasarBatch('BD002', '2025-06-08', 'Batch kulit éclair — Juni 2025');
        SeederHelpers::createBahanDasarBatch('BD003', '2025-06-08', 'Batch vla éclair — Juni 2025');
        SeederHelpers::createBahanDasarBatch('BD004', '2025-06-12', 'Batch kulit kue sus — Juni 2025');
        SeederHelpers::createBahanDasarBatch('BD005', '2025-06-12', 'Batch vla vanilla — Juni 2025');
        SeederHelpers::createBahanDasarBatch('BD006', '2025-06-15', 'Batch kulit pisang molen — Juni 2025');

        foreach (RecipeCatalog::products() as $index => $product) {
            $day = 6 + ($index * 4);

            SeederHelpers::createProduction(
                id: 'PRD'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                tanggal: '2025-06-'.str_pad((string) min($day, 28), 2, '0', STR_PAD_LEFT),
                productName: $product['nama'],
                qty: $product['batch_qty'],
                satuan: $product['batch_unit'],
                bahanDasarUsages: $product['bahan_dasar'],
                rawLines: $product['raw_materials'],
            );
        }
    }
}
