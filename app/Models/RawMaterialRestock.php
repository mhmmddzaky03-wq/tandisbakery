<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Support\FormatHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialRestock extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'restock bahan baku';

    protected $fillable = [
        'raw_material_id',
        'tanggal',
        'kode_produksi',
        'expired',
        'jumlah',
        'sisa',
        'harga',
        'total',
        'catatan',
        'journal_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'expired' => 'date',
            'jumlah' => 'float',
            'sisa' => 'float',
        ];
    }

    public function displayLabel(?string $satuan = null): string
    {
        $unit = $satuan ?? $this->rawMaterial?->satuan ?? '';
        $parts = [];

        if ($this->kode_produksi) {
            $parts[] = $this->kode_produksi;
        } else {
            $parts[] = 'Restock '.FormatHelper::dateId($this->tanggal?->format('Y-m-d'));
        }

        if ($this->expired) {
            $parts[] = 'Exp '.FormatHelper::dateId($this->expired->format('Y-m-d'));
        }

        $parts[] = FormatHelper::formatQtyOne((float) $this->sisa).($unit !== '' ? ' '.$unit : '');

        return implode(' · ', $parts);
    }

    public function isExpired(): bool
    {
        return $this->expired !== null && $this->expired->isPast();
    }

    public function isExpiringSoon(int $withinDays = 7): bool
    {
        if ($this->expired === null || $this->isExpired()) {
            return false;
        }

        return $this->expired->lte(now()->addDays($withinDays)->startOfDay());
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
