<?php
$a = require __DIR__ . '/../config/trial_balance_snapshot.php';
$d = $c = 0;
foreach ($a['accounts'] as $x) { $d += $x['debit']; $c += $x['credit']; }
echo "D=$d C=$c target=352377811\n";
