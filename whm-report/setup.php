<?php
/**
 * ============================================================
 * Setup - Descarga cacert.pem si no existe
 * ============================================================
 * Archivo    : setup.php
 * Path       : /home/icontel/public_html/intranet/whm-report/setup.php
 * Versión    : 1.0.1
 * Fecha      : 2026-02-25 22:20:00
 * Proyecto   : WHM Server Report - Icontel Intranet
 * Autor      : Icontel Dev Team
 * ============================================================
 * Ejecutar una sola vez después de instalar.
 * Acceder: https://intranet.icontel.cl/whm-report/setup.php
 * ELIMINAR DESPUÉS DE USAR
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== WHM Report - Setup ===\n\n";

$cacertPath = __DIR__ . '/cacert.pem';

// 1. Descargar cacert.pem
if (file_exists($cacertPath) && filesize($cacertPath) > 10000) {
    echo "[OK] cacert.pem ya existe (" . filesize($cacertPath) . " bytes)\n";
} else {
    echo "Descargando cacert.pem de curl.se...\n";
    $ch = curl_init('https://curl.se/ca/cacert.pem');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $pem = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    if ($pem && strlen($pem) > 10000) {
        file_put_contents($cacertPath, $pem);
        echo "[OK] cacert.pem descargado (" . strlen($pem) . " bytes)\n";
    } else {
        echo "[ERROR] No se pudo descargar: $err\n";
    }
}

// 2. Test de conexión WHM
echo "\nProbando conexión WHM...\n";
require_once __DIR__ . '/includes/WhmApi.php';
$whm = new WhmApi();
$test = $whm->testConnection();

if (isset($test['success']) && $test['success']) {
    echo "[OK] Conexión exitosa! WHM Version: " . $test['version'] . "\n";
    echo "\n=== INSTALACION COMPLETA ===\n";
    echo "Accede a: https://intranet.icontel.cl/whm-report/\n";
    echo "Recuerda ELIMINAR este archivo (setup.php) y test_whm.php\n";
} else {
    echo "[ERROR] " . ($test['message'] ?? 'No se pudo conectar') . "\n";
}
