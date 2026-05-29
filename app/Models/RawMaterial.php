<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'bahan baku';
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

    public function restocks(): HasMany
    {
        return $this->hasMany(RawMaterialRestock::class, 'raw_material_id')->latest('tanggal')->latest('id');
    }

    public static function generateNextId(): string
    {
        $max = static::query()
            ->where('id', 'like', 'SBB%')
            ->pluck('id')
            ->map(static fn (string $id): int => (int) substr($id, 3))
            ->max() ?? 0;

        return 'SBB'.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }
}
