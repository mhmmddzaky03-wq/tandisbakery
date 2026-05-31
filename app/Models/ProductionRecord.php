<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductionRecord extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'produksi';
    protected $keyType = 'string';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total_material_cost' => 'integer',
        ];
    }

    protected $fillable = [
        'id',
        'tanggal',
        'product_name',
        'jumlah',
        'satuan',
        'status',
        'notes',
        'total_material_cost',
        'journal_transaction_id',
    ];

    public static function generateNextId(): string
    {
        $last = static::orderBy('id', 'desc')->first();
        $num  = $last ? (int) substr($last->id, 3) + 1 : 1;

        return 'PRD'.str_pad((string) $num, 3, '0', STR_PAD_LEFT);
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'production_record_id');
    }

    public function materialUsages(): HasMany
    {
        return $this->hasMany(ProductionMaterialUsage::class, 'production_record_id');
    }

    public function bahanDasarUsages(): HasMany
    {
        return $this->hasMany(PemakaianBahanDasarProduksi::class, 'production_record_id');
    }
}
