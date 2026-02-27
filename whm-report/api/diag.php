<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/index.php';
} catch (Throwable $e) {
    echo "Caught: " . $e->getMessage();
}
