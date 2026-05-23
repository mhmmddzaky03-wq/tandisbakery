<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama',
        'satuan',
        'harga',
        'status',
    ];

    public function productionRecords(): HasMany
    {
        return $this->hasMany(ProductionRecord::class, 'product_id');
    }
}
