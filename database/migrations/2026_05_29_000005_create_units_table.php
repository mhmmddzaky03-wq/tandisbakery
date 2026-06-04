<?php

use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        Unit::withoutEvents(function () {
            foreach (Unit::PROTECTED_NAMES as $nama) {
                Unit::firstOrCreate(['nama' => $nama]);
            }

            foreach (['g', 'gram'] as $nama) {
                Unit::firstOrCreate(['nama' => $nama]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
