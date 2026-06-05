<?php

namespace Database\Seeders;

use App\Models\BahanDasar;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Unit;
use Database\Seeders\Support\RecipeCatalog;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['kg', 'pcs', 'L', 'loyang', 'gram', 'ml'] as $unitName) {
            Unit::firstOrCreate(['nama' => $unitName]);
        }

        foreach (RecipeCatalog::rawMaterials() as $mat) {
            RawMaterial::create([
                'id' => $mat['id'],
                'nama' => $mat['nama'],
                'kategori' => $mat['kategori'],
                'jumlah' => 0,
                'satuan' => $mat['satuan'],
                'min' => $mat['min'],
                'harga' => $mat['harga'],
            ]);
        }

        foreach (RecipeCatalog::bahanDasar() as $bd) {
            BahanDasar::create([
                'id' => $bd['id'],
                'nama' => $bd['nama'],
                'jumlah' => 0,
                'satuan' => $bd['satuan'],
                'min' => $bd['min'],
                'harga' => 0,
            ]);
        }

        foreach (RecipeCatalog::products() as $product) {
            Product::create([
                'id' => $product['product_id'],
                'production_record_id' => null,
                'nama' => $product['nama'],
                'satuan' => $product['batch_unit'],
                'jumlah' => 0,
                'harga' => $product['harga'],
            ]);
        }
    }
}
