<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionMaterialUsage extends Model
{
    protected $fillable = [
        'production_record_id',
        'raw_material_id',
        'raw_material_restock_id',
        'jumlah',
        'satuan',
        'harga_satuan',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'float',
        ];
    }

    public function productionRecord(): BelongsTo
    {
        return $this->belongsTo(ProductionRecord::class, 'production_record_id');
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function restockBatch(): BelongsTo
    {
        return $this->belongsTo(RawMaterialRestock::class, 'raw_material_restock_id');
    }
}
