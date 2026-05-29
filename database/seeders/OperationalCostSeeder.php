<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\OperationalCost;
use App\Services\OperationalCostService;
use Illuminate\Database\Seeder;

class OperationalCostSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ExpenseCategory::pluck('id', 'nama');
        $service = app(OperationalCostService::class);

        $records = [
            // Fixed Cost — Juni 2025 (mirror Excel Fixed Cost)
            ['id' => 'BO101', 'tanggal' => '2025-06-30', 'category' => 'Gaji Karyawan', 'desk' => 'Gaji karyawan 4 orang', 'jumlah' => 8_300_000],
            ['id' => 'BO102', 'tanggal' => '2025-06-30', 'category' => 'BPJS', 'desk' => '', 'jumlah' => 315_549],
            ['id' => 'BO103', 'tanggal' => '2025-06-30', 'category' => 'Internet / Wifi', 'desk' => '', 'jumlah' => 359_600],
            ['id' => 'BO104', 'tanggal' => '2025-06-10', 'category' => 'Angsuran Pinjaman', 'desk' => 'Angsuran BRI KUR', 'jumlah' => 11_750_000],
            ['id' => 'BO105', 'tanggal' => '2025-06-30', 'category' => 'Satpam', 'desk' => '', 'jumlah' => 350_000],
            ['id' => 'BO106', 'tanggal' => '2025-06-30', 'category' => 'Setrika', 'desk' => '', 'jumlah' => 800_000],
            ['id' => 'BO107', 'tanggal' => '2025-06-30', 'category' => 'Lunch', 'desk' => '', 'jumlah' => 900_000],

            // Variable Cost — transaksi manual
            ['id' => 'BO001', 'tanggal' => '2025-06-01', 'category' => 'Kemasan', 'desk' => 'Pasar Tradisional - Bahan kemasan dan kebutuhan operasional', 'jumlah' => 420_000],
            ['id' => 'BO201', 'tanggal' => '2025-06-30', 'category' => 'Gaji Overtime', 'desk' => 'Putri Maulidina', 'jumlah' => 200_000],
            ['id' => 'BO202', 'tanggal' => '2025-06-30', 'category' => 'Gaji Overtime', 'desk' => 'Zizi', 'jumlah' => 300_000],
            ['id' => 'BO203', 'tanggal' => '2025-06-30', 'category' => 'Gaji Overtime', 'desk' => 'Mang Ahmad', 'jumlah' => 300_000],
            ['id' => 'BO204', 'tanggal' => '2025-06-30', 'category' => 'Gaji Overtime', 'desk' => 'Pak Endang', 'jumlah' => 600_000],
            ['id' => 'BO205', 'tanggal' => '2025-06-30', 'category' => 'Gaji Overtime', 'desk' => 'Eka dan Teman-teman', 'jumlah' => 600_000],
            ['id' => 'BO206', 'tanggal' => '2025-06-30', 'category' => 'Listrik', 'desk' => 'M1:538215069406', 'jumlah' => 3_043_106],
            ['id' => 'BO207', 'tanggal' => '2025-06-30', 'category' => 'PGN / Gas', 'desk' => 'M1:11441045/1002', 'jumlah' => 475_400],
            ['id' => 'BO208', 'tanggal' => '2025-06-30', 'category' => 'PDAM / Air', 'desk' => 'M1:150019669/2532', 'jumlah' => 721_200],
            ['id' => 'BO209', 'tanggal' => '2025-06-30', 'category' => 'Maintenance', 'desk' => '', 'jumlah' => 150_000],
            ['id' => 'BO002', 'tanggal' => '2025-06-30', 'category' => 'PDAM / Air', 'desk' => 'Refill Cleo - Air minum untuk operasional', 'jumlah' => 167_000],
            ['id' => 'BO014', 'tanggal' => '2025-06-30', 'category' => 'Belanja Lainnya', 'desk' => 'Online - Pembelian kebutuhan online', 'jumlah' => 1_000_000],
        ];

        foreach ($records as $row) {
            $categoryId = $categories[$row['category']] ?? null;
            if (! $categoryId) {
                continue;
            }

            $payload = [
                'tanggal' => $row['tanggal'],
                'expense_category_id' => $categoryId,
                'desk' => $row['desk'],
                'jumlah' => $row['jumlah'],
            ];

            $existing = OperationalCost::find($row['id']);
            if ($existing) {
                $service->update($existing, $payload);

                continue;
            }

            $service->record(array_merge($payload, ['id' => $row['id']]));
        }

        // Legacy miscategorized row — reclassify to Fixed (angsuran)
        $legacy = OperationalCost::find('BO008');
        if ($legacy && ($categories['Angsuran Pinjaman'] ?? null)) {
            $service->update($legacy, [
                'tanggal' => '2025-06-10',
                'expense_category_id' => $categories['Angsuran Pinjaman'],
                'desk' => 'Cimbniaga Cicilan - Bayar HP Iphone (cicilan)',
                'jumlah' => 2_041_500,
            ]);
        }

        $service->backfillMissingJournals();
    }
}
