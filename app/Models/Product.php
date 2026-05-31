<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'produk';
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'production_record_id',
        'nama',
        'satuan',
        'jumlah',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga' => 'integer',
        ];
    }

    public static function normalizeName(string $name): string
    {
        return mb_strtolower(trim($name));
    }

    public static function findByName(string $name): ?self
    {
        return static::query()
            ->whereRaw('LOWER(TRIM(nama)) = ?', [static::normalizeName($name)])
            ->first();
    }

    public static function existsForName(string $name): bool
    {
        return static::findByName($name) !== null;
    }

    public static function generateNextId(): string
    {
        $last = static::orderBy('id', 'desc')->first();
        $num  = $last ? (int) substr($last->id, 1) + 1 : 1;

        return 'P'.str_pad((string) $num, 3, '0', STR_PAD_LEFT);
    }

    public function productionRecord(): BelongsTo
    {
        return $this->belongsTo(ProductionRecord::class, 'production_record_id');
    }
}
