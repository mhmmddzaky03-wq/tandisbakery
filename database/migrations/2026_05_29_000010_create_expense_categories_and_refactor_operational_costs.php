<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis'); // Fixed | Variable
            $table->string('account_kode');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('account_kode')->references('kode')->on('accounts')->cascadeOnDelete();
        });

        Schema::table('operational_costs', function (Blueprint $table) {
            $table->foreignId('expense_category_id')->nullable()->after('id')->constrained('expense_categories')->nullOnDelete();
            $table->unsignedBigInteger('journal_transaction_id')->nullable()->after('jenis');
        });

        $now = now();
        $categories = [
            ['nama' => 'Gaji Karyawan', 'jenis' => 'Fixed', 'account_kode' => '5-150', 'sort_order' => 10],
            ['nama' => 'BPJS', 'jenis' => 'Fixed', 'account_kode' => '5-160', 'sort_order' => 20],
            ['nama' => 'Internet / Wifi', 'jenis' => 'Fixed', 'account_kode' => '5-180', 'sort_order' => 30],
            ['nama' => 'Angsuran Pinjaman', 'jenis' => 'Fixed', 'account_kode' => '5-180', 'sort_order' => 40],
            ['nama' => 'Satpam', 'jenis' => 'Fixed', 'account_kode' => '5-130', 'sort_order' => 50],
            ['nama' => 'Setrika', 'jenis' => 'Fixed', 'account_kode' => '5-180', 'sort_order' => 60],
            ['nama' => 'Lunch', 'jenis' => 'Fixed', 'account_kode' => '5-180', 'sort_order' => 70],
            ['nama' => 'Gaji Overtime', 'jenis' => 'Variable', 'account_kode' => '5-120', 'sort_order' => 110],
            ['nama' => 'Listrik', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 120],
            ['nama' => 'PGN / Gas', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 130],
            ['nama' => 'PDAM / Air', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 140],
            ['nama' => 'Maintenance', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 150],
            ['nama' => 'Kemasan', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 160],
            ['nama' => 'Pajak Kendaraan', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 170],
            ['nama' => 'Belanja Lainnya', 'jenis' => 'Variable', 'account_kode' => '5-180', 'sort_order' => 180],
        ];

        foreach ($categories as $category) {
            DB::table('expense_categories')->insert(array_merge($category, [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $categoryMap = DB::table('expense_categories')->pluck('id', 'nama');

        $legacyMap = [
            'Kemasan' => 'Kemasan',
            'Air' => 'PDAM / Air',
            'Gaji' => 'Gaji Karyawan',
            'Lainnya' => 'Belanja Lainnya',
        ];

        $fallbackId = $categoryMap['Belanja Lainnya'] ?? null;

        foreach (DB::table('operational_costs')->get() as $cost) {
            $targetName = $legacyMap[$cost->kat] ?? 'Belanja Lainnya';
            $categoryId = $categoryMap[$targetName] ?? $fallbackId;
            $category = DB::table('expense_categories')->where('id', $categoryId)->first();

            if (! $category) {
                continue;
            }

            DB::table('operational_costs')->where('id', $cost->id)->update([
                'expense_category_id' => $categoryId,
                'kat' => $category->nama,
                'jenis' => $category->jenis,
            ]);
        }

        DB::table('operational_costs')->whereNull('expense_category_id')->update([
            'expense_category_id' => $fallbackId,
        ]);
    }

    public function down(): void
    {
        Schema::table('operational_costs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expense_category_id');
            $table->dropColumn('journal_transaction_id');
        });

        Schema::dropIfExists('expense_categories');
    }
};
