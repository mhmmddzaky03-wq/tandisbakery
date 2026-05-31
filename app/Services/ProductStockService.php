<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductionRecord;

class ProductStockService
{
    public function quantityForName(string $productName): int
    {
        return (int) ProductionRecord::query()
            ->where('status', 'Berhasil')
            ->whereRaw('LOWER(TRIM(product_name)) = ?', [Product::normalizeName($productName)])
            ->sum('jumlah');
    }

    public function syncForName(?string $productName): void
    {
        if ($productName === null || trim($productName) === '') {
            return;
        }

        $product = Product::findByName($productName);

        if (! $product) {
            return;
        }

        $product->update([
            'jumlah' => $this->quantityForName($productName),
        ]);
    }

    /** @param  array<int, string|null>  $names */
    public function syncForNames(array $names): void
    {
        $normalized = [];

        foreach ($names as $name) {
            if ($name === null || trim($name) === '') {
                continue;
            }

            $normalized[Product::normalizeName($name)] = $name;
        }

        foreach ($normalized as $name) {
            $this->syncForName($name);
        }
    }

    public function afterProductionSaved(ProductionRecord $record, ?ProductionRecord $before = null): void
    {
        $this->syncForNames([
            $record->product_name,
            $before?->product_name,
        ]);
    }

    public function afterProductionDeleted(ProductionRecord $record): void
    {
        $this->syncForName($record->product_name);
    }
}
