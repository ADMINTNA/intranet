<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/WhmApi.php';

try {
    $whm = new WhmApi();
    echo "WHM Api Instantiated\n";
    $report = $whm->getFullReport();
    echo "Report generated successfully\n";
} catch (Throwable $e) {
    echo "FATAL ERROR CAUGHT: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
}
