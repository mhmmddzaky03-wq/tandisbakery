<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianBahanDasarProduksi extends Model
{
    protected $table = 'pemakaian_bahan_dasar_produksi';

    protected $fillable = [
        'production_record_id',
        'batch_bahan_dasar_id',
        'bahan_dasar_id',
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

    public function productionRecord(): BelongsTo
    {
        return $this->belongsTo(ProductionRecord::class, 'production_record_id');
    }

    public function batchBahanDasar(): BelongsTo
    {
        return $this->belongsTo(BatchBahanDasar::class, 'batch_bahan_dasar_id');
    }

    public function bahanDasar(): BelongsTo
    {
        return $this->belongsTo(BahanDasar::class, 'bahan_dasar_id');
    }
}
