<?php
require_once __DIR__ . '/sb_config.php';

header('Content-Type: application/json');

try {
    $uf_value = getUFValue();
    $usd_value = getUSDValue();
    $date = date('d-m-Y');
    
    echo json_encode([
        'success' => true,
        'uf_value' => $uf_value,
        'usd_value' => $usd_value,
        'date' => $date
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
