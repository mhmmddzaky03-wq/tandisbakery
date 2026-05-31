<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanDasar extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'bahan dasar';

    protected $table = 'bahan_dasar';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama',
        'jumlah',
        'satuan',
        'min',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'float',
            'min' => 'float',
            'harga' => 'integer',
        ];
    }

    public function batch(): HasMany
    {
        return $this->hasMany(BatchBahanDasar::class, 'bahan_dasar_id');
    }

    public function batches(): HasMany
    {
        return $this->batch();
    }

    public static function generateNextId(): string
    {
        $max = static::query()
            ->where('id', 'like', 'BD%')
            ->pluck('id')
            ->map(static fn (string $id): int => (int) substr($id, 2))
            ->max() ?? 0;

        return 'BD'.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    public function pemakaianProduksi(): HasMany
    {
        return $this->hasMany(PemakaianBahanDasarProduksi::class, 'bahan_dasar_id');
    }

    public function canBeDeleted(): bool
    {
        return ! $this->batches()->exists();
    }
}
