<?php
$host     = "localhost";
$user     = "data_studio";
$password = "1Ngr3s0.,";
$dbname   = "icontel_clientes";
date_default_timezone_set('UTC'); // hora de la base de datos
$userTimeZone = new DateTimeZone('America/Santiago');  // hora de chile
$hoy = date("d-m-Y H:i:s");


function DbConnect($dbname) {
    // Parámetros de conexión
    $server   = "localhost";
    $user     = "data_studio";
    $password = "1Ngr3s0.,";

    // Definir el charset explícitamente (ya que DB_CHARSET no existe)
    $DB_CHARSET = "utf8mb4";

    // Crear conexión MySQLi
    $conn = new mysqli($server, $user, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("❌ No me pude conectar al servidor localhost: " . $conn->connect_error);
    }

    // Forzar uso completo de UTF-8 en MySQL
    if (!$conn->set_charset($DB_CHARSET)) {
        die("❌ Error configurando charset: " . $conn->error);
    }

    $conn->query("SET NAMES 'utf8mb4'");
    $conn->query("SET CHARACTER SET utf8mb4");
    $conn->query("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");

    return $conn;
}



function horacl($date) {
    global $userTimeZone;
    $dateNeeded = new DateTime($date); 
    $dateNeeded->setTimeZone($userTimeZone);
    return($dateNeeded->format('d-m-Y H:i:s')) ;
}

// ---------------------------
// FUNCIONES DE FILTRO GLOBALES
// ---------------------------

// Filtro para números (NV, Factura, Cotización, Monto)
function numericCond($raw, $column){
    $raw = trim($raw);
    if ($raw === '') return '';
    if (preg_match('/^(>=|<=|>|<|=)\s*(-?\d+(?:\.\d+)?)$/', $raw, $m)) {
        return "$column {$m[1]} {$m[2]}";
    }
    if (preg_match('/^(-?\d+(?:\.\d+)?)\s*-\s*(-?\d+(?:\.\d+)?)$/', $raw, $m)) {
        $a=$m[1]; $b=$m[2];
        if ($a>$b) { $t=$a; $a=$b; $b=$t; }
        return "$column BETWEEN $a AND $b";
    }
    if (is_numeric($raw)) {
        return "$column = $raw";
    }
    return '';
}

// Filtro para fechas (YYYY-MM-DD, >, <, = o rango)
function parseDateToYmd($s){
    $s = trim($s);
    $s = str_replace('/', '-', $s);

    // YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
        return $s;
    }
    // DD-MM-YYYY
    if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $s, $m)) {
        return $m[3].'-'.$m[2].'-'.$m[1];
    }
    // fallback strtotime
    $ts = strtotime($s);
    return $ts ? date('Y-m-d', $ts) : '';
}

function dateCond($raw, $column){
    $raw = trim(strtolower($raw));
    if ($raw === '') return '';

    // normalizar separadores
    $raw = str_replace('/', '-', $raw);

    // mayor que
    if (preg_match('/^mayor que\s+(.+)$/', $raw, $m)) {
        $val = date('Y-m-d', strtotime($m[1]));
        return "$column > '$val'";
    }
    // menor que
    if (preg_match('/^menor que\s+(.+)$/', $raw, $m)) {
        $val = date('Y-m-d', strtotime($m[1]));
        return "$column < '$val'";
    }
    // igual
    if (preg_match('/^=\s*(.+)$/', $raw, $m)) {
        $val = date('Y-m-d', strtotime($m[1]));
        return "$column = '$val'";
    }
    // entre fecha1 y fecha2
    if (preg_match('/^entre\s+(.+)\s+y\s+(.+)$/', $raw, $m)) {
        $a = date('Y-m-d', strtotime($m[1]));
        $b = date('Y-m-d', strtotime($m[2]));
        if ($a > $b) { $t=$a; $a=$b; $b=$t; }
        return "$column BETWEEN '$a' AND '$b'";
    }

    // si solo pone una fecha → igualdad
    $val = date('Y-m-d', strtotime($raw));
    return "$column = '$val'";
}
