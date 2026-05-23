<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRecord extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    protected $fillable = [
        'id',
        'tanggal',
        'product_id',
        'product_name',
        'jumlah',
        'satuan',
        'status',
        'notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
