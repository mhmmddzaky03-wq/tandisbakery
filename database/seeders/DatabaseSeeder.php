<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Support\SeederHelpers;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kosongkan seluruh isi database
        SeederHelpers::truncateAll();

        foreach ([
            ['name' => 'Haris', 'username' => 'admin', 'email' => 'admin@tandisbakery.com', 'password' => 'admin123', 'role' => 'admin'],
            ['name' => 'Karyawan Toko', 'username' => 'karyawan', 'email' => 'karyawan@tandisbakery.com', 'password' => 'karyawan123', 'role' => 'karyawan'],
        ] as $user) {
            User::updateOrCreate(['username' => $user['username']], $user);
        }

        $this->call(AccountSeeder::class);
        $this->call(ExpenseCategorySeeder::class);

        // Master data: bahan baku, bahan dasar, katalog produk (5 resep)
        $this->call(MasterDataSeeder::class);

        // 2. Data asli Juni 2025 dari Excel
        $this->call(June2025Seeder::class);

        // Saldo neraca per 30-Jun-2025 (Trial Balance Excel)
        $this->call(OpeningBalanceSeeder::class);

        // 3. Data dummy bulan berjalan untuk dashboard presentasi
        $this->call(DashboardDemoSeeder::class);
    }
}
