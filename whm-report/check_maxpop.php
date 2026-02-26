<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/WhmApi.php';
$whm = new WhmApi();
echo "=== COUNT POPS bioel ===\n";
$count = $whm->callUapi('bioel', 'Email', 'count_pops', []);
echo "Type: " . gettype($count) . "\n";
print_r($count);
echo "\n=== FIN ===\n";
