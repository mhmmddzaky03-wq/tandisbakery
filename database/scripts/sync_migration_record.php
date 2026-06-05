<?php

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$migration = '2026_05_29_000004_create_activity_logs_table';

if (! Schema::hasTable('activity_logs')) {
    fwrite(STDERR, "Tabel activity_logs belum ada.\n");
    exit(1);
}

if (DB::table('migrations')->where('migration', $migration)->exists()) {
    echo "Migrasi sudah tercatat.\n";
    exit(0);
}

DB::table('migrations')->insert([
    'migration' => $migration,
    'batch' => (int) DB::table('migrations')->max('batch') + 1,
]);

echo "Migrasi {$migration} ditandai sebagai Ran.\n";
