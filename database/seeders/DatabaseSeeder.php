<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Account;
use App\Models\JournalTransaction;
use App\Models\JournalEntry;
use App\Models\ProductionRecord;
use App\Models\SalesTransaction;
use App\Models\OperationalCost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        User::create([
            'name' => 'Haris',
            'username' => 'admin',
            'email' => 'admin@tandisbakery.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Karyawan Toko',
            'username' => 'karyawan',
            'email' => 'karyawan@tandisbakery.com',
            'password' => Hash::make('karyawan123'),
            'role' => 'karyawan',
        ]);

        User::create([
            'name' => 'Basket',
            'username' => 'basket',
            'email' => 'basket@tandisbakery.com',
            'password' => Hash::make('basket123'),
            'role' => 'basket',
        ]);

        // 2. Seed Accounts (Chart of Accounts)
        $accounts = [
            ['kode' => '1-110', 'nama' => "Cash in Tandi's Bank", 'posisi' => 'Debit', 'grup' => 'Current Asset'],
            ['kode' => '1-130', 'nama' => 'Direct Materials', 'posisi' => 'Debit', 'grup' => 'Current Asset'],
            ['kode' => '1-140', 'nama' => 'Work in Process', 'posisi' => 'Debit', 'grup' => 'Current Asset'],
            ['kode' => '1-150', 'nama' => 'Finished Goods', 'posisi' => 'Debit', 'grup' => 'Current Asset'],
            ['kode' => '1-160', 'nama' => 'Factory Supplies', 'posisi' => 'Debit', 'grup' => 'Current Asset'],
            ['kode' => '1-210', 'nama' => 'Factory Equipment', 'posisi' => 'Debit', 'grup' => 'Non-Current Asset'],
            ['kode' => '1-211', 'nama' => 'Accum. Depreciation - Equipment', 'posisi' => 'Credit', 'grup' => 'Non-Current Asset'],
            ['kode' => '1-220', 'nama' => 'Vehicle', 'posisi' => 'Debit', 'grup' => 'Non-Current Asset'],
            ['kode' => '1-221', 'nama' => 'Accum. Depreciation - Vehicle', 'posisi' => 'Credit', 'grup' => 'Non-Current Asset'],
            ['kode' => '2-110', 'nama' => 'Accounts Payable', 'posisi' => 'Credit', 'grup' => 'Liabilities'],
            ['kode' => '2-120', 'nama' => 'Tax Payable', 'posisi' => 'Credit', 'grup' => 'Liabilities'],
            ['kode' => '2-140', 'nama' => 'IPHONE Payable', 'posisi' => 'Credit', 'grup' => 'Liabilities'],
            ['kode' => '3-110', 'nama' => "Owner's Capital", 'posisi' => 'Credit', 'grup' => 'Equity'],
            ['kode' => '3-120', 'nama' => 'Retained Earnings', 'posisi' => 'Credit', 'grup' => 'Equity'],
            ['kode' => '4-110', 'nama' => 'Sales', 'posisi' => 'Credit', 'grup' => 'Revenue'],
            ['kode' => '4-120', 'nama' => 'Sales Discount', 'posisi' => 'Debit', 'grup' => 'Revenue'],
            ['kode' => '4-130', 'nama' => 'Sales Return', 'posisi' => 'Debit', 'grup' => 'Revenue'],
            ['kode' => '5-110', 'nama' => 'Cost of Goods Sold', 'posisi' => 'Debit', 'grup' => 'Expenses'],
            ['kode' => '5-120', 'nama' => 'Admin & Sales Salary Expense', 'posisi' => 'Debit', 'grup' => 'Expenses'],
            ['kode' => '5-130', 'nama' => 'Security Salary Expense', 'posisi' => 'Debit', 'grup' => 'Expenses'],
            ['kode' => '5-140', 'nama' => 'Vehicle Depreciation Expense', 'posisi' => 'Debit', 'grup' => 'Expenses'],
            ['kode' => '5-150', 'nama' => 'Salary Expense', 'posisi' => 'Debit', 'grup' => 'Expenses'],
            ['kode' => '5-160', 'nama' => 'Insurance Cost (BPJS)', 'posisi' => 'Debit', 'grup' => 'Expenses'],
            ['kode' => '5-170', 'nama' => 'Income Tax Expense', 'posisi' => 'Debit', 'grup' => 'Expenses'],
            ['kode' => '5-180', 'nama' => 'Other Expense', 'posisi' => 'Debit', 'grup' => 'Expenses'],
        ];

        foreach ($accounts as $acc) {
            Account::create($acc);
        }

        // 3. Seed Products
        $products = [
            ['id' => 'P001', 'nama' => 'Roti Tawar', 'satuan' => 'loyang', 'harga' => 25000, 'status' => 'Aktif'],
            ['id' => 'P002', 'nama' => 'Croissant', 'satuan' => 'pcs', 'harga' => 12000, 'status' => 'Aktif'],
            ['id' => 'P003', 'nama' => 'Kue Ulang Tahun', 'satuan' => 'pcs', 'harga' => 350000, 'status' => 'Aktif'],
        ];

        foreach ($products as $prod) {
            Product::create($prod);
        }

        // 4. Seed Raw Materials
        $materials = [
            ['id' => 'SBB001', 'nama' => 'Wincheez Custom (B) 8 x 2 kg', 'jumlah' => 48, 'min' => 16, 'harga' => 54323],
            ['id' => 'SBB002', 'nama' => 'Telur Ayam', 'jumlah' => 68.246, 'min' => 30, 'harga' => 28000],
            ['id' => 'SBB003', 'nama' => 'Endura Smoke Beef M 1kg', 'jumlah' => 2, 'min' => 1, 'harga' => 101410],
            ['id' => 'SBB006', 'nama' => 'UHT Milk Full Cream Sleeve 1kg', 'jumlah' => 1, 'min' => 1, 'harga' => 176904],
            ['id' => 'SBB010', 'nama' => 'Kismis Hitam 1kg USA Premium', 'jumlah' => 1, 'min' => 1, 'harga' => 50000],
        ];

        foreach ($materials as $mat) {
            RawMaterial::create($mat);
        }

        // 5. Seed Production Records
        $productions = [
            ['id' => 'PRD001', 'tanggal' => '2026-04-15', 'product_id' => 'P001', 'product_name' => 'Roti Tawar', 'jumlah' => 50, 'satuan' => 'loyang', 'status' => 'Berhasil', 'notes' => '-'],
            ['id' => 'PRD002', 'tanggal' => '2026-04-15', 'product_id' => 'P002', 'product_name' => 'Croissant', 'jumlah' => 100, 'satuan' => 'pcs', 'status' => 'Berhasil', 'notes' => '-'],
            ['id' => 'PRD003', 'tanggal' => '2026-04-14', 'product_id' => 'P003', 'product_name' => 'Kue Ulang Tahun', 'jumlah' => 5, 'satuan' => 'pcs', 'status' => 'Berhasil', 'notes' => '-'],
            ['id' => 'PRD004', 'tanggal' => '2026-04-14', 'product_id' => null, 'product_name' => 'Donat', 'jumlah' => 0, 'satuan' => 'pcs', 'status' => 'Gagal', 'notes' => 'Adonan tidak mengembang sempurna'],
        ];

        foreach ($productions as $p) {
            ProductionRecord::create($p);
        }

        // 6. Seed Sales Transactions (Rekap)
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

        // 7. Seed Operational Costs
        $costs = [
            ['id' => 'BO001', 'tanggal' => '2025-06-01', 'kat' => 'Kemasan', 'desk' => 'Pasar Tradisional - Bahan kemasan dan kebutuhan operasional', 'jumlah' => 420000, 'jenis' => 'Variable'],
            ['id' => 'BO002', 'tanggal' => '2025-06-30', 'kat' => 'Air', 'desk' => 'Refill Cleo - Air minum untuk operasional', 'jumlah' => 167000, 'jenis' => 'Variable'],
            ['id' => 'BO008', 'tanggal' => '2025-06-10', 'kat' => 'Lainnya', 'desk' => 'Cimbniaga Cicilan - Bayar HP Iphone (cicilan)', 'jumlah' => 2041500, 'jenis' => 'Fixed'],
            ['id' => 'BO014', 'tanggal' => '2025-06-30', 'kat' => 'Lainnya', 'desk' => 'Online - Pembelian kebutuhan online', 'jumlah' => 1000000, 'jenis' => 'Variable'],
        ];

        foreach ($costs as $c) {
            OperationalCost::create($c);
        }

        // 8. Seed Opening Balances & Historical Accounting Entries (Balanced)
        // Owner's Capital is adjusted to 277,054,484 to balance the opening ledger exactly.
        $tx = JournalTransaction::create([
            'tanggal' => '2025-06-01',
            'deskripsi' => 'Saldo Awal & Transaksi Historis',
            'ref' => 'Opening Balance',
        ]);

        $entries = [
            ['account_kode' => '1-110', 'debit' => 20678197, 'credit' => 0],
            ['account_kode' => '1-130', 'debit' => 22383272, 'credit' => 0],
            ['account_kode' => '1-140', 'debit' => 14588531, 'credit' => 0],
            ['account_kode' => '1-150', 'debit' => 5727000, 'credit' => 0],
            ['account_kode' => '1-160', 'debit' => 15000, 'credit' => 0],
            ['account_kode' => '1-210', 'debit' => 52967850, 'credit' => 0],
            ['account_kode' => '1-211', 'debit' => 0, 'credit' => 27500000],
            ['account_kode' => '1-220', 'debit' => 245000000, 'credit' => 0],
            ['account_kode' => '1-221', 'debit' => 0, 'credit' => 2041667],
            ['account_kode' => '2-120', 'debit' => 0, 'credit' => 125000],
            ['account_kode' => '2-140', 'debit' => 0, 'credit' => 2401000],
            ['account_kode' => '3-110', 'debit' => 0, 'credit' => 277054484],
            ['account_kode' => '3-120', 'debit' => 0, 'credit' => 29158652],
            ['account_kode' => '4-110', 'debit' => 0, 'credit' => 72285150],
            ['account_kode' => '4-120', 'debit' => 110300, 'credit' => 0],
            ['account_kode' => '4-130', 'debit' => 247000, 'credit' => 0],
            ['account_kode' => '5-110', 'debit' => 38548803, 'credit' => 0],
            ['account_kode' => '5-150', 'debit' => 10300000, 'credit' => 0],
        ];

        foreach ($entries as $ent) {
            JournalEntry::create(array_merge($ent, ['journal_transaction_id' => $tx->id]));
        }

        // 9. Seed the exact mockup entries for Jurnal Umum page view:
        // June 1, 2025 mock journal entries
        $tx1 = JournalTransaction::create([
            'tanggal' => '2025-06-01',
            'deskripsi' => 'Transaksi Operasional - Rincian Jun 1',
            'ref' => 'Restock bahan baku',
        ]);
        // Debit Materials 1-130 (Rp 500,000), Credit Cash 1-110 (Rp 500,000)
        JournalEntry::create(['journal_transaction_id' => $tx1->id, 'account_kode' => '1-130', 'debit' => 500000, 'credit' => 0]);
        JournalEntry::create(['journal_transaction_id' => $tx1->id, 'account_kode' => '1-110', 'debit' => 0, 'credit' => 500000]);

        // June 3, 2025 mock journal entries
        $tx2 = JournalTransaction::create([
            'tanggal' => '2025-06-03',
            'deskripsi' => 'Investasi PT Merging Nusantara',
            'ref' => 'PT Merging Nusantara',
        ]);
        JournalEntry::create(['journal_transaction_id' => $tx2->id, 'account_kode' => '1-110', 'debit' => 17232150, 'credit' => 0]);
        JournalEntry::create(['journal_transaction_id' => $tx2->id, 'account_kode' => '3-110', 'debit' => 0, 'credit' => 17232150]);

        // June 5, 2025 mock journal entries
        $tx3 = JournalTransaction::create([
            'tanggal' => '2025-06-05',
            'deskripsi' => 'Biaya Operasional Mandiri',
            'ref' => 'Bevelen Mandiri',
        ]);
        JournalEntry::create(['journal_transaction_id' => $tx3->id, 'account_kode' => '5-150', 'debit' => 137000, 'credit' => 0]);
        JournalEntry::create(['journal_transaction_id' => $tx3->id, 'account_kode' => '1-110', 'debit' => 0, 'credit' => 137000]);
    }
}
