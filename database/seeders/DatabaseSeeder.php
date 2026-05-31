<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Models\ProductionRecord;
use App\Models\SalesTransaction;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (plain password — model casts to hashed)
        foreach ([
            ['name' => 'Haris', 'username' => 'admin', 'email' => 'admin@tandisbakery.com', 'password' => 'admin123', 'role' => 'admin'],
            ['name' => 'Karyawan Toko', 'username' => 'karyawan', 'email' => 'karyawan@tandisbakery.com', 'password' => 'karyawan123', 'role' => 'karyawan'],
        ] as $user) {
            User::updateOrCreate(['username' => $user['username']], $user);
        }

        // 2. Seed Chart of Accounts (COA perusahaan)
        $this->call(AccountSeeder::class);

        $this->call(ExpenseCategorySeeder::class);
        $this->call(OperationalCostSeeder::class);

        // 4. Seed Production Records (products are registered from production)
        $productions = [
            ['id' => 'PRD001', 'tanggal' => '2026-04-15', 'product_name' => 'Roti Tawar', 'jumlah' => 50, 'satuan' => 'loyang', 'status' => 'Berhasil', 'notes' => '-'],
            ['id' => 'PRD002', 'tanggal' => '2026-04-15', 'product_name' => 'Croissant', 'jumlah' => 100, 'satuan' => 'pcs', 'status' => 'Berhasil', 'notes' => '-'],
            ['id' => 'PRD003', 'tanggal' => '2026-04-14', 'product_name' => 'Kue Ulang Tahun', 'jumlah' => 5, 'satuan' => 'pcs', 'status' => 'Berhasil', 'notes' => '-'],
            ['id' => 'PRD004', 'tanggal' => '2026-04-14', 'product_name' => 'Donat', 'jumlah' => 0, 'satuan' => 'pcs', 'status' => 'Gagal', 'notes' => 'Adonan tidak mengembang sempurna'],
        ];

        foreach ($productions as $p) {
            ProductionRecord::create($p);
        }

        Product::create(['id' => 'P001', 'production_record_id' => 'PRD001', 'nama' => 'Roti Tawar', 'satuan' => 'loyang', 'jumlah' => 10, 'harga' => 25000]);
        Product::create(['id' => 'P002', 'production_record_id' => 'PRD002', 'nama' => 'Croissant', 'satuan' => 'pcs', 'jumlah' => 48, 'harga' => 12000]);
        Product::create(['id' => 'P003', 'production_record_id' => 'PRD003', 'nama' => 'Kue Ulang Tahun', 'satuan' => 'pcs', 'jumlah' => 1, 'harga' => 350000]);

        // 4. Seed Raw Materials
        $materials = [
            ['id' => 'SBB001', 'nama' => 'Wincheez Custom (B) 8 x 2 kg', 'kategori' => 'kering', 'jumlah' => 48, 'satuan' => 'kg', 'min' => 16, 'harga' => 54323],
            ['id' => 'SBB002', 'nama' => 'Telur Ayam', 'kategori' => 'basah', 'jumlah' => 100, 'satuan' => 'pcs', 'min' => 50, 'harga' => 28000],
            ['id' => 'SBB003', 'nama' => 'Endura Smoke Beef M 1kg', 'kategori' => 'padat', 'jumlah' => 2, 'satuan' => 'kg', 'min' => 1, 'harga' => 101410],
            ['id' => 'SBB006', 'nama' => 'UHT Milk Full Cream Sleeve 1kg', 'kategori' => 'basah', 'jumlah' => 1, 'satuan' => 'kg', 'min' => 1, 'harga' => 176904],
            ['id' => 'SBB010', 'nama' => 'Kismis Hitam 1kg USA Premium', 'kategori' => 'kering', 'jumlah' => 1, 'satuan' => 'kg', 'min' => 1, 'harga' => 50000],
            ['id' => 'SBB011', 'nama' => 'Pastel Besar', 'kategori' => 'padat', 'jumlah' => 200, 'satuan' => 'L', 'min' => 80, 'harga' => 15000],
        ];

        foreach ($materials as $mat) {
            RawMaterial::create($mat);
        }

        foreach (['kg', 'pcs', 'L', 'loyang', 'gram', 'ml'] as $unitName) {
            Unit::firstOrCreate(['nama' => $unitName]);
        }

        // 5. Seed Sales Transactions (Rekap)
        for ($i = 1; $i <= 10; $i++) {
            $totalSales = 1800000 + ($i * 135000);
            SalesTransaction::create([
                'id' => 'TRX' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'tanggal' => '2025-06-' . str_pad((string) (6 + $i), 2, '0', STR_PAD_LEFT),
                'total' => $totalSales,
                'metode' => 'Mix',
                'jumlah' => 15 + $i,
            ]);
        }

        // 8. Saldo awal (Excel) — jurnal operasional lain tidak dihapus
        $this->call(OpeningBalanceSeeder::class);

        // 9. Contoh jurnal manual (berkesinambungan dengan modul Jurnal Umum)
        $tx1 = JournalTransaction::create([
            'tanggal' => '2025-06-01',
            'deskripsi' => 'Transaksi Operasional - Rincian Jun 1',
            'ref' => 'Restock bahan baku',
        ]);
        JournalEntry::create(['journal_transaction_id' => $tx1->id, 'account_kode' => '1-130', 'debit' => 500000, 'credit' => 0]);
        JournalEntry::create(['journal_transaction_id' => $tx1->id, 'account_kode' => '1-110', 'debit' => 0, 'credit' => 500000]);

        $tx2 = JournalTransaction::create([
            'tanggal' => '2025-06-03',
            'deskripsi' => 'Investasi PT Merging Nusantara',
            'ref' => 'PT Merging Nusantara',
        ]);
        JournalEntry::create(['journal_transaction_id' => $tx2->id, 'account_kode' => '1-110', 'debit' => 17232150, 'credit' => 0]);
        JournalEntry::create(['journal_transaction_id' => $tx2->id, 'account_kode' => '3-110', 'debit' => 0, 'credit' => 17232150]);

        $tx3 = JournalTransaction::create([
            'tanggal' => '2025-06-05',
            'deskripsi' => 'Biaya Operasional Mandiri',
            'ref' => 'Bevelen Mandiri',
        ]);
        JournalEntry::create(['journal_transaction_id' => $tx3->id, 'account_kode' => '5-150', 'debit' => 137000, 'credit' => 0]);
        JournalEntry::create(['journal_transaction_id' => $tx3->id, 'account_kode' => '1-110', 'debit' => 0, 'credit' => 137000]);

        // Contoh buku besar akun 6-100 Direct Labor (sesuai template Excel)
        $txAdj = JournalTransaction::create([
            'tanggal' => '2025-06-30',
            'deskripsi' => 'Penyesuaian tenaga kerja langsung',
            'ref' => 'ADJ',
        ]);
        JournalEntry::create(['journal_transaction_id' => $txAdj->id, 'account_kode' => '6-100', 'debit' => 0, 'credit' => 8_300_000]);
        JournalEntry::create(['journal_transaction_id' => $txAdj->id, 'account_kode' => '5-180', 'debit' => 8_300_000, 'credit' => 0]);

        $txAlloc = JournalTransaction::create([
            'tanggal' => '2025-06-30',
            'deskripsi' => 'Alokasi biaya umum ke tenaga kerja langsung',
            'ref' => 'ALLOCATING GA',
        ]);
        JournalEntry::create(['journal_transaction_id' => $txAlloc->id, 'account_kode' => '6-100', 'debit' => 8_300_000, 'credit' => 0]);
        JournalEntry::create(['journal_transaction_id' => $txAlloc->id, 'account_kode' => '5-180', 'debit' => 0, 'credit' => 8_300_000]);

        // Data demo dashboard (grafik tren penjualan, komposisi biaya, dll.) — relatif ke tanggal hari ini
        $this->call(DashboardDemoSeeder::class);
    }
}
