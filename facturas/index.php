<?php
// index.php
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '/error.log');
ini_set('display_errors', 0);
error_reporting(E_ALL);

ob_start();
session_start();

// Marca de entrada para debug
error_log('[index] entry uri=' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''));

// RUTA BASE
define('APP_ROOT', __DIR__);

// Carga datos/variables (SP, filtros, $facturas, $usuarios, $estados, $resumen, etc.)
require_once APP_ROOT . '/include/include.php';

// Render directo del contenido (sin redirects)
require_once APP_ROOT . '/contenido.php';

ob_end_flush();
