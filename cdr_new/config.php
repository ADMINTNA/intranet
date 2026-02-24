<?php
// ==========================================================
// /intranet/cdr_new/config.php
// Configuración y conexión a BD
// ==========================================================

session_name('icontel_intranet_sess');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la Base de Datos
$db_host = "cdr.tnasolutions.cl";
$db_user = "cdr";
$db_pwd  = "Pq63_10ad";
$db_name = "tnasolutions";

$con = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

$con->set_charset("utf8");

// Funciones auxiliares de fecha
function ultimo_dia_mes() { 
    $month = date('m');
    $year = date('Y');
    $day = date("d", mktime(0,0,0, $month+1, 0, $year));
    return date('Y-m-d H:i:s', mktime(23,59,59, $month, $day, $year));
}

function primer_dia_mes() {
    $month = date('m');
    $year = date('Y');
    return date('Y-m-d H:i:s', mktime(0,0,0, $month, 1, $year));
}

function ultimo_dia_mes_anterior() { 
    $month = date('m');
    $year = date('Y');
    $day = date("d", mktime(0,0,0, $month, 0, $year));
    return date('Y-m-d H:i:s', mktime(23,59,59, $month-1, $day, $year));
}

function primer_dia_mes_anterior() {
    $month = date('m');
    $year = date('Y');
    return date('Y-m-d H:i:s', mktime(0,0,0, $month-1, 1, $year));
}

function formatCurrency($value) {
    return '$' . number_format($value, 0, ',', '.');
}

function formatDecimal($value, $decimals = 2) {
    return number_format($value, $decimals, ',', '.');
}
?>
