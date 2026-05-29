<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialRestock extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'restock bahan baku';

    protected $fillable = [
        'raw_material_id',
        'tanggal',
        'jumlah',
        'harga',
        'total',
        'catatan',
        'journal_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'float',
        ];
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function journalTransaction(): BelongsTo
    {
        return $this->belongsTo(JournalTransaction::class);
    }

    public function activityObjectName(): string
    {
        return $this->rawMaterial?->nama ?? $this->raw_material_id;
    }
}
