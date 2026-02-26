<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/WhmApi.php';
$whm = new WhmApi();
echo "=== LISTADO DE CUENTAS ===\n";
$accts = $whm->listAccounts();
if (isset($accts['error'])) {
    print_r($accts);
} else {
    foreach ($accts as $a) {
        echo "User: " . $a['user'] . " | Domain: " . $a['domain'] . "\n";
    }
}
echo "\n=== FIN ===\n";
