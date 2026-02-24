<?php
// /home/icontel/public_html/intranet/calls/bsale/includes/config.php
// Configuración general + conexión MySQL + funciones auxiliares

// --- CONFIGURACIÓN GLOBAL ---
date_default_timezone_set("America/Santiago");

// Forzar codificación UTF-8 en todas las capas
ini_set('default_charset', 'UTF-8');
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");

// --- CONFIG BD ---
define('DB_HOST', 'localhost');
define('DB_USER', 'data_studio');
define('DB_PASS', '1Ngr3s0.,');
define('DB_CHARSET', 'utf8mb4');

// --- FUNCIÓN DE CONEXIÓN ---
function DbConnect($schema = "icontel_clientes") {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, $schema);
    if ($conn->connect_error) {
        die("❌ Error conexión MySQL: " . $conn->connect_error);
    }
    // Forzar UTF-8 completo en MySQL
    $conn->set_charset(DB_CHARSET);
    $conn->query("SET NAMES 'utf8mb4'");
    $conn->query("SET CHARACTER SET utf8mb4");
    $conn->query("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
    return $conn;
}

// --- FUNCIONES AUXILIARES ---
function q($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

function fechaCorta($f) {
    if (!$f) return '';
    return date("d-m-Y", strtotime($f));
}

// --- LLAMADA AL PROCEDIMIENTO ALMACENADO ---
function callGetBsaleDocumento($num_doc = null, $tipo_key = null) {
    $conn = DbConnect("icontel_clientes");

    $stmt = $conn->prepare("CALL get_bsale_documento(?, ?)");
    $stmt->bind_param("is", $num_doc, $tipo_key);
    $stmt->execute();

    $result = $stmt->get_result();
    $rows = [];
    while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }

    $stmt->close();
    $conn->close();
    return $rows;
}
?>
