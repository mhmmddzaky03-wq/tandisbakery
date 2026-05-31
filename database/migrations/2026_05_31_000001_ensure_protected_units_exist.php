<?php

use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Unit::PROTECTED_NAMES as $nama) {
            Unit::firstOrCreate(['nama' => $nama]);
        }
    }

    public function down(): void
    {
        // Satuan sistem tidak dihapus saat rollback.
    }
};
