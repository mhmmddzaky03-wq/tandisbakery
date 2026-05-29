<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_restocks', function (Blueprint $table) {
            $table->id();
            $table->string('raw_material_id');
            $table->date('tanggal');
            $table->decimal('jumlah', 12, 1);
            $table->unsignedInteger('harga');
            $table->unsignedBigInteger('total');
            $table->string('catatan')->nullable();
            $table->foreignId('journal_transaction_id')->nullable()->constrained('journal_transactions')->nullOnDelete();
            $table->timestamps();

            $table->foreign('raw_material_id')
                ->references('id')
                ->on('raw_materials')
                ->cascadeOnDelete();
        });

        $now = now();
        foreach (DB::table('raw_materials')->orderBy('id')->get() as $material) {
            if ((float) $material->jumlah <= 0) {
                continue;
            }

            DB::table('raw_material_restocks')->insert([
                'raw_material_id' => $material->id,
                'tanggal' => $material->created_at
                    ? \Carbon\Carbon::parse($material->created_at)->toDateString()
                    : $now->toDateString(),
                'jumlah' => $material->jumlah,
                'harga' => $material->harga,
                'total' => (int) round((float) $material->jumlah * (int) $material->harga),
                'catatan' => 'Stok awal',
                'journal_transaction_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_restocks');
    }
};
