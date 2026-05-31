<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use LogsActivity;

    public const KATEGORI_KERING = 'kering';

    public const KATEGORI_BASAH = 'basah';

    public const KATEGORI_PADAT = 'padat';

    protected static string $activityMenu = 'bahan baku';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama',
        'kategori',
        'jumlah',
        'satuan',
        'min',
        'harga',
    ];

    /** @return array<string, string> */
    public static function kategoriOptions(): array
    {
        return [
            self::KATEGORI_KERING => 'Kering',
            self::KATEGORI_BASAH => 'Basah',
            self::KATEGORI_PADAT => 'Padat',
        ];
    }

    public function kategoriLabel(): string
    {
        return self::kategoriOptions()[$this->kategori] ?? 'Padat';
    }

    public function restocks(): HasMany
    {
        return $this->hasMany(RawMaterialRestock::class, 'raw_material_id')->latest('tanggal')->latest('id');
    }

    public function latestRestock(): ?RawMaterialRestock
    {
        if ($this->relationLoaded('restocks')) {
            return $this->restocks->first();
        }

        return $this->restocks()->first();
    }

    public function materialUsages(): HasMany
    {
        return $this->hasMany(ProductionMaterialUsage::class, 'raw_material_id');
    }

    public function adonanUsages(): HasMany
    {
        return $this->hasMany(PemakaianBahanBakuAdonan::class, 'raw_material_id');
    }

    public function isInUse(): bool
    {
        if (isset($this->material_usages_count) || isset($this->adonan_usages_count)) {
            return (int) ($this->material_usages_count ?? 0) > 0
                || (int) ($this->adonan_usages_count ?? 0) > 0;
        }

        return $this->materialUsages()->exists() || $this->adonanUsages()->exists();
    }

    public function canBeDeleted(): bool
    {
        return ! $this->isInUse();
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
