<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama' => 'Gaji Karyawan', 'jenis' => 'Fixed', 'account_kode' => '5-150', 'sort_order' => 10],
            ['nama' => 'BPJS', 'jenis' => 'Fixed', 'account_kode' => '5-140', 'sort_order' => 20],
            ['nama' => 'Internet / Wifi', 'jenis' => 'Fixed', 'account_kode' => '5-161', 'sort_order' => 30],
            ['nama' => 'Angsuran Pinjaman', 'jenis' => 'Fixed', 'account_kode' => '5-180', 'sort_order' => 40],
            ['nama' => 'Satpam', 'jenis' => 'Fixed', 'account_kode' => '5-152', 'sort_order' => 50],
            ['nama' => 'Setrika', 'jenis' => 'Fixed', 'account_kode' => '5-180', 'sort_order' => 60],
            ['nama' => 'Lunch', 'jenis' => 'Fixed', 'account_kode' => '5-180', 'sort_order' => 70],
            ['nama' => 'Gaji Overtime', 'jenis' => 'Variable', 'account_kode' => '5-151', 'sort_order' => 110],
            ['nama' => 'Listrik', 'jenis' => 'Variable', 'account_kode' => '5-160', 'sort_order' => 120],
            ['nama' => 'PGN / Gas', 'jenis' => 'Variable', 'account_kode' => '5-160', 'sort_order' => 130],
            ['nama' => 'PDAM / Air', 'jenis' => 'Variable', 'account_kode' => '5-160', 'sort_order' => 140],
            ['nama' => 'Maintenance', 'jenis' => 'Variable', 'account_kode' => '6-600', 'sort_order' => 150],
            ['nama' => 'Kemasan', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 160],
            ['nama' => 'Pajak Kendaraan', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 170],
            ['nama' => 'Belanja Lainnya', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 180],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['nama' => $category['nama']],
                array_merge($category, ['is_active' => true])
            );
        }
    }
}
