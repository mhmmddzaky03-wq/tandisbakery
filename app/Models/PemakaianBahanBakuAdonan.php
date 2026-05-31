<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianBahanBakuAdonan extends Model
{
    protected $table = 'pemakaian_bahan_baku_adonan';

    protected $fillable = [
        'batch_bahan_dasar_id',
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
            'harga_satuan' => 'integer',
            'total' => 'integer',
        ];
    }

    public function batchBahanDasar(): BelongsTo
    {
        return $this->belongsTo(BatchBahanDasar::class, 'batch_bahan_dasar_id');
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function batchBahanBaku(): BelongsTo
    {
        return $this->belongsTo(RawMaterialRestock::class, 'raw_material_restock_id');
    }
}
