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
        'harga',
        'status',
    ];

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
