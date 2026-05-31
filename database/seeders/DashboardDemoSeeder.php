<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\ExpenseCategory;
use App\Models\OperationalCost;
use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Models\RawMaterialRestock;
use App\Models\SalesTransaction;
use App\Models\User;
use App\Services\OperationalCostService;
use App\Services\RawMaterialRestockService;
use App\Services\SalesJournalService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardDemoSeeder extends Seeder
{
    private const DEMO_PREFIX = 'DEMO';

    public function run(): void
    {
        $this->purgeDemoData();
        $this->seedRecentSales();
        $this->seedCurrentMonthCosts();
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

        RawMaterialRestock::query()
            ->where('catatan', 'Demo dashboard')
            ->delete();

        ProductionRecord::query()
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

        // 14 hari terakhir — pola naik untuk grafik tren yang menarik
        $dailyMultipliers = [0.85, 0.92, 0.78, 1.05, 1.12, 0.98, 1.18, 1.25, 1.15, 1.32, 1.45, 1.28, 1.52, 1.68];
        $base = 1_800_000;

        foreach ($dailyMultipliers as $index => $multiplier) {
            $daysAgo = 13 - $index;
            $date = now()->subDays($daysAgo)->toDateString();
            $id = self::DEMO_PREFIX.'S'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $total = (int) round($base * $multiplier);
            $qty = 12 + $index + (int) ($multiplier * 3);

            $sale = SalesTransaction::create([
                'id' => $id,
                'tanggal' => $date,
                'total' => $total,
                'metode' => $metodes[$index % count($metodes)],
                'jumlah' => $qty,
            ]);

            $salesJournal->sync($sale);
        }

        // Hari 15–28 lalu — agar KPI 30 hari & perbandingan 7 hari terisi
        for ($i = 15; $i <= 28; $i++) {
            if ($i % 3 !== 0) {
                continue;
            }

            $date = now()->subDays($i)->toDateString();
            $id = self::DEMO_PREFIX.'S'.str_pad((string) (20 + $i), 2, '0', STR_PAD_LEFT);
            $total = (int) round($base * (0.7 + ($i % 5) * 0.08));

            $sale = SalesTransaction::create([
                'id' => $id,
                'tanggal' => $date,
                'total' => $total,
                'metode' => 'Transfer',
                'jumlah' => 8 + ($i % 4),
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
            ['id' => self::DEMO_PREFIX.'BF01', 'category' => 'Gaji Karyawan', 'desk' => 'Gaji tim produksi — demo dashboard', 'jumlah' => 8_300_000, 'day' => 1],
            ['id' => self::DEMO_PREFIX.'BF02', 'category' => 'Internet / Wifi', 'desk' => 'Langganan bulan ini', 'jumlah' => 359_600, 'day' => 2],
            ['id' => self::DEMO_PREFIX.'BV01', 'category' => 'Listrik', 'desk' => 'Tagihan PLN', 'jumlah' => 2_850_000, 'day' => 5],
            ['id' => self::DEMO_PREFIX.'BV02', 'category' => 'Kemasan', 'desk' => 'Plastik & box takeaway', 'jumlah' => 680_000, 'day' => 8],
            ['id' => self::DEMO_PREFIX.'BV03', 'category' => 'Gaji Overtime', 'desk' => 'Weekend shift', 'jumlah' => 1_200_000, 'day' => 12],
            ['id' => self::DEMO_PREFIX.'BV04', 'category' => 'Belanja Lainnya', 'desk' => 'Kebutuhan dapur', 'jumlah' => 540_000, 'day' => 15],
        ];

        foreach ($costs as $row) {
            $categoryId = $categories[$row['category']] ?? null;
            if (! $categoryId) {
                continue;
            }

            $tanggal = $monthStart->copy()->addDays(min($row['day'], now()->day - 1))->toDateString();
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

        $material = RawMaterial::first();
        if ($material && ! RawMaterialRestock::where('catatan', 'Demo dashboard')->exists()) {
            app(RawMaterialRestockService::class)->record(
                $material,
                $today,
                5,
                (int) $material->harga,
                'Demo dashboard',
            );
        }
    }

    private function seedRecentProduction(): void
    {
        $records = [
            ['id' => self::DEMO_PREFIX.'P01', 'days_ago' => 0, 'product_name' => 'Croissant', 'jumlah' => 120, 'satuan' => 'pcs', 'status' => 'Berhasil'],
            ['id' => self::DEMO_PREFIX.'P02', 'days_ago' => 1, 'product_name' => 'Roti Tawar', 'jumlah' => 45, 'satuan' => 'loyang', 'status' => 'Berhasil'],
            ['id' => self::DEMO_PREFIX.'P03', 'days_ago' => 2, 'product_name' => 'Donat Glaze', 'jumlah' => 80, 'satuan' => 'pcs', 'status' => 'Berhasil'],
            ['id' => self::DEMO_PREFIX.'P04', 'days_ago' => 3, 'product_name' => 'Kue Basah', 'jumlah' => 0, 'satuan' => 'pcs', 'status' => 'Gagal', 'notes' => 'Adonan terlalu encer'],
            ['id' => self::DEMO_PREFIX.'P05', 'days_ago' => 5, 'product_name' => 'Pain au Chocolat', 'jumlah' => 60, 'satuan' => 'pcs', 'status' => 'Berhasil'],
        ];

        foreach ($records as $row) {
            ProductionRecord::updateOrCreate(
                ['id' => $row['id']],
                [
                    'tanggal' => now()->subDays($row['days_ago'])->toDateString(),
                    'product_name' => $row['product_name'],
                    'jumlah' => $row['jumlah'],
                    'satuan' => $row['satuan'],
                    'status' => $row['status'],
                    'notes' => $row['notes'] ?? '-',
                ]
            );
        }
    }

    private function seedActivityLogs(): void
    {
        $admin = User::where('username', 'admin')->first();

        $entries = [
            ['action' => 'tambah', 'menu' => 'transaksi penjualan', 'object' => self::DEMO_PREFIX.'S14 — Rp 3.024.000', 'hours_ago' => 1],
            ['action' => 'tambah', 'menu' => 'biaya operasional', 'object' => self::DEMO_PREFIX.'BV03 — Gaji Overtime', 'hours_ago' => 3],
            ['action' => 'tambah', 'menu' => 'produksi', 'object' => self::DEMO_PREFIX.'P01 — Croissant', 'hours_ago' => 5],
            ['action' => 'mengubah', 'menu' => 'stok bahan baku', 'object' => 'SBB002 Telur Ayam', 'hours_ago' => 8],
            ['action' => 'tambah', 'menu' => 'transaksi penjualan', 'object' => self::DEMO_PREFIX.'S12 — Rp 2.304.000', 'hours_ago' => 12],
            ['action' => 'tambah', 'menu' => 'biaya operasional', 'object' => self::DEMO_PREFIX.'BV01 — Listrik', 'hours_ago' => 18],
            ['action' => 'tambah', 'menu' => 'produksi', 'object' => self::DEMO_PREFIX.'P03 — Donat Glaze', 'hours_ago' => 24],
            ['action' => 'tambah', 'menu' => 'transaksi penjualan', 'object' => self::DEMO_PREFIX.'S08 — Rp 2.250.000', 'hours_ago' => 36],
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
