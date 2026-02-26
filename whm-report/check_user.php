<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/WhmApi.php';
$whm = new WhmApi();
echo "=== RESUMEN CUENTA icontel ===\n";
$sum = $whm->accountSummary('icontel');
print_r($sum);
echo "\n=== FIN ===\n";
