<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\ExpenseCategory;
use App\Models\OperationalCost;
use App\Models\Product;
use App\Models\ProductionRecord;
use App\Models\SalesTransaction;
use App\Models\User;
use App\Services\OperationalCostService;
use App\Services\SalesJournalService;
use Database\Seeders\Support\RecipeCatalog;
use Database\Seeders\Support\SeederHelpers;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DashboardDemoSeeder extends Seeder
{
    private const DEMO_PREFIX = 'DEMO';

    public function run(): void
    {
        $this->purgeDemoData();
        SeederHelpers::ensureStockForDemo();
        $this->seedRecentSales();
        $this->seedCurrentMonthCosts();
        $this->seedBahanDasarBatches();
        $this->seedRecentProduction();
        $this->seedActivityLogs();
    }

    private function purgeDemoData(): void
    {
        $salesJournal = app(SalesJournalService::class);

        SalesTransaction::query()
            ->where('id', 'like', self::DEMO_PREFIX.'%')
            ->each(function (SalesTransaction $sale) use ($salesJournal) {
                $salesJournal->deleteForSale($sale);
                $sale->delete();
            });

        OperationalCost::query()
            ->where('id', 'like', self::DEMO_PREFIX.'%')
            ->each(fn (OperationalCost $cost) => app(OperationalCostService::class)->delete($cost));

        ProductionRecord::query()
            ->where('id', 'like', self::DEMO_PREFIX.'%')
            ->each(function (ProductionRecord $record) {
                app(\App\Services\ProductionMaterialService::class)->reverse($record);
                app(\App\Services\ProductionBahanDasarService::class)->reverse($record);
                $record->delete();
            });

        Product::query()
            ->where('id', 'like', self::DEMO_PREFIX.'%')
            ->delete();

        ActivityLog::query()
            ->where('object', 'like', self::DEMO_PREFIX.'%')
            ->delete();
    }

    private function seedRecentSales(): void
    {
        $salesJournal = app(SalesJournalService::class);
        $metodes = ['Cash', 'Transfer', 'Mix'];

        $dailyMultipliers = [0.95, 1.02, 0.88, 1.08, 1.15, 1.05, 1.22, 1.28, 1.18, 1.35, 1.48, 1.32, 1.55, 1.72];
        $base = 3_400_000;

        foreach ($dailyMultipliers as $index => $multiplier) {
            $daysAgo = 13 - $index;
            $date = now()->subDays($daysAgo)->toDateString();
            $id = self::DEMO_PREFIX.'S'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $total = (int) round($base * $multiplier);
            $qty = 18 + $index + (int) ($multiplier * 4);

            $sale = SalesTransaction::create([
                'id' => $id,
                'tanggal' => $date,
                'total' => $total,
                'metode' => $metodes[$index % count($metodes)],
                'jumlah' => $qty,
            ]);

            $salesJournal->sync($sale);
        }

        for ($i = 15; $i <= 28; $i += 2) {
            $date = now()->subDays($i)->toDateString();
            $id = self::DEMO_PREFIX.'S'.str_pad((string) (20 + $i), 2, '0', STR_PAD_LEFT);
            $total = (int) round($base * (0.82 + ($i % 6) * 0.06));

            $sale = SalesTransaction::create([
                'id' => $id,
                'tanggal' => $date,
                'total' => $total,
                'metode' => 'Transfer',
                'jumlah' => 14 + ($i % 5),
            ]);

            $salesJournal->sync($sale);
        }
    }

    private function seedCurrentMonthCosts(): void
    {
        $service = app(OperationalCostService::class);
        $categories = ExpenseCategory::pluck('id', 'nama');
        $monthStart = now()->startOfMonth();
        $today = now()->toDateString();

        $costs = [
            ['id' => self::DEMO_PREFIX.'BF01', 'category' => 'Gaji Karyawan', 'desk' => 'Gaji tim produksi', 'jumlah' => 8_300_000, 'day' => 1],
            ['id' => self::DEMO_PREFIX.'BF02', 'category' => 'Internet / Wifi', 'desk' => 'Langganan bulan ini', 'jumlah' => 359_600, 'day' => 2],
            ['id' => self::DEMO_PREFIX.'BF03', 'category' => 'BPJS', 'desk' => 'Iuran BPJS karyawan', 'jumlah' => 315_549, 'day' => 3],
            ['id' => self::DEMO_PREFIX.'BV01', 'category' => 'Listrik', 'desk' => 'Tagihan PLN', 'jumlah' => 2_850_000, 'day' => 5],
            ['id' => self::DEMO_PREFIX.'BV02', 'category' => 'Kemasan', 'desk' => 'Plastik & box takeaway', 'jumlah' => 1_200_000, 'day' => 8],
            ['id' => self::DEMO_PREFIX.'BV03', 'category' => 'Gaji Overtime', 'desk' => 'Weekend shift', 'jumlah' => 1_800_000, 'day' => 12],
            ['id' => self::DEMO_PREFIX.'BV04', 'category' => 'PGN / Gas', 'desk' => 'Tagihan gas produksi', 'jumlah' => 680_000, 'day' => 14],
            ['id' => self::DEMO_PREFIX.'BV05', 'category' => 'Belanja Lainnya', 'desk' => 'Kebutuhan dapur', 'jumlah' => 950_000, 'day' => 18],
        ];

        foreach ($costs as $row) {
            $categoryId = $categories[$row['category']] ?? null;
            if (! $categoryId) {
                continue;
            }

            $dayOffset = min($row['day'], max(0, now()->day - 1));
            $tanggal = $monthStart->copy()->addDays($dayOffset)->toDateString();
            if ($tanggal > $today) {
                $tanggal = $today;
            }

            $service->record([
                'id' => $row['id'],
                'tanggal' => $tanggal,
                'expense_category_id' => $categoryId,
                'desk' => $row['desk'],
                'jumlah' => $row['jumlah'],
            ]);
        }
    }

    private function seedBahanDasarBatches(): void
    {
        $tanggal = now()->subDays(2)->toDateString();

        SeederHelpers::createBahanDasarBatch('BD001', $tanggal, 'Demo — adonan roti manis');
        SeederHelpers::createBahanDasarBatch('BD001', now()->subDay()->toDateString(), 'Demo — adonan roti manis batch 2');
        SeederHelpers::createBahanDasarBatch('BD002', $tanggal, 'Demo — kulit éclair');
        SeederHelpers::createBahanDasarBatch('BD003', $tanggal, 'Demo — vla cokelat éclair');
        SeederHelpers::createBahanDasarBatch('BD004', now()->subDay()->toDateString(), 'Demo — kulit kue sus');
        SeederHelpers::createBahanDasarBatch('BD005', now()->subDay()->toDateString(), 'Demo — vla vanilla kue sus');
        SeederHelpers::createBahanDasarBatch('BD006', $tanggal, 'Demo — kulit pisang molen');
    }

    private function seedRecentProduction(): void
    {
        $products = RecipeCatalog::products();

        $schedule = [
            ['id' => self::DEMO_PREFIX.'P01', 'days_ago' => 0, 'product_index' => 0, 'multiplier' => 3],
            ['id' => self::DEMO_PREFIX.'P02', 'days_ago' => 0, 'product_index' => 2, 'multiplier' => 2],
            ['id' => self::DEMO_PREFIX.'P03', 'days_ago' => 1, 'product_index' => 1, 'multiplier' => 2],
            ['id' => self::DEMO_PREFIX.'P04', 'days_ago' => 1, 'product_index' => 3, 'multiplier' => 2],
            ['id' => self::DEMO_PREFIX.'P05', 'days_ago' => 2, 'product_index' => 4, 'multiplier' => 1],
            ['id' => self::DEMO_PREFIX.'P06', 'days_ago' => 3, 'product_index' => 0, 'multiplier' => 2],
            ['id' => self::DEMO_PREFIX.'P07', 'days_ago' => 4, 'product_index' => 2, 'multiplier' => 3],
            ['id' => self::DEMO_PREFIX.'P08', 'days_ago' => 5, 'product_index' => 1, 'multiplier' => 1, 'status' => 'Gagal', 'notes' => 'Vla terlalu encer — batch dibuang'],
            ['id' => self::DEMO_PREFIX.'P09', 'days_ago' => 6, 'product_index' => 3, 'multiplier' => 3],
        ];

        foreach ($schedule as $row) {
            $product = $products[$row['product_index']];
            $status = $row['status'] ?? 'Berhasil';
            $multiplier = $row['multiplier'];
            $tanggal = now()->subDays($row['days_ago'])->toDateString();

            $bahanDasarUsages = array_map(
                fn (array $usage) => [
                    'bahan_dasar_id' => $usage['bahan_dasar_id'],
                    'qty' => $usage['qty'] * $multiplier,
                    'unit' => $usage['unit'],
                ],
                $product['bahan_dasar'],
            );

            $rawLines = array_map(
                fn (array $line) => [
                    'id' => $line['id'],
                    'qty' => $line['qty'] * $multiplier,
                    'unit' => $line['unit'],
                ],
                $product['raw_materials'],
            );

            SeederHelpers::createProduction(
                id: $row['id'],
                tanggal: $tanggal,
                productName: $product['nama'],
                qty: $product['batch_qty'] * $multiplier,
                satuan: $product['batch_unit'],
                bahanDasarUsages: $status === 'Berhasil' ? $bahanDasarUsages : [],
                rawLines: $status === 'Berhasil' ? $rawLines : [],
                status: $status,
                notes: $row['notes'] ?? '-',
            );
        }

        foreach ($products as $product) {
            Product::where('id', $product['product_id'])->update([
                'jumlah' => app(\App\Services\ProductStockService::class)->quantityForName($product['nama']),
            ]);
        }
    }

    private function seedActivityLogs(): void
    {
        $admin = User::where('username', 'admin')->first();

        $entries = [
            ['action' => 'tambah', 'menu' => 'transaksi penjualan', 'object' => self::DEMO_PREFIX.'S14 — Rp 5.848.000', 'hours_ago' => 1],
            ['action' => 'tambah', 'menu' => 'biaya operasional', 'object' => self::DEMO_PREFIX.'BV03 — Gaji Overtime', 'hours_ago' => 2],
            ['action' => 'tambah', 'menu' => 'produksi', 'object' => self::DEMO_PREFIX.'P01 — Roti Manis Rasa Cokelat 30 pcs', 'hours_ago' => 4],
            ['action' => 'tambah', 'menu' => 'bahan dasar', 'object' => 'BD001 Adonan Roti Manis — batch baru', 'hours_ago' => 5],
            ['action' => 'tambah', 'menu' => 'transaksi penjualan', 'object' => self::DEMO_PREFIX.'S12 — Rp 4.488.000', 'hours_ago' => 10],
            ['action' => 'tambah', 'menu' => 'biaya operasional', 'object' => self::DEMO_PREFIX.'BV01 — Listrik', 'hours_ago' => 14],
            ['action' => 'tambah', 'menu' => 'produksi', 'object' => self::DEMO_PREFIX.'P03 — Éclair Cokelat 30 pcs', 'hours_ago' => 20],
            ['action' => 'tambah', 'menu' => 'produksi', 'object' => self::DEMO_PREFIX.'P05 — Black Forest Cake 1 loyang', 'hours_ago' => 26],
            ['action' => 'tambah', 'menu' => 'restock bahan baku', 'object' => 'SBB001 Tepung Protein Tinggi +25 kg', 'hours_ago' => 32],
            ['action' => 'tambah', 'menu' => 'produksi', 'object' => self::DEMO_PREFIX.'P07 — Kue Sus Vla Vanilla 60 pcs', 'hours_ago' => 40],
        ];

        foreach ($entries as $entry) {
            $at = Carbon::now()->subHours($entry['hours_ago']);

            ActivityLog::create([
                'user_id' => $admin?->id,
                'user_name' => $admin?->name ?? 'Haris',
                'user_role' => $admin?->role ?? 'admin',
                'action' => $entry['action'],
                'object' => $entry['object'],
                'menu' => $entry['menu'],
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }
    }
}
