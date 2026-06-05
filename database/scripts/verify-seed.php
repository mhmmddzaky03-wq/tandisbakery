<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BahanDasar;
use App\Models\BatchBahanDasar;
use App\Models\OperationalCost;
use App\Models\ProductionRecord;
use App\Models\RawMaterial;
use App\Models\SalesTransaction;

echo "Bahan Baku      : ".RawMaterial::count()."\n";
echo "Bahan Dasar     : ".BahanDasar::count()."\n";
echo "Batch Adonan    : ".BatchBahanDasar::count()."\n";
echo "Produksi        : ".ProductionRecord::count()."\n";
echo "Penjualan       : ".SalesTransaction::count()."\n";
echo "Biaya Operasional: ".OperationalCost::count()."\n\n";

foreach (BahanDasar::orderBy('id')->get() as $bd) {
    echo "{$bd->id} {$bd->nama} — stok {$bd->jumlah} {$bd->satuan}\n";
}
