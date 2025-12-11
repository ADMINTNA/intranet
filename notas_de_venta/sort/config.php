<?php
$host     = "localhost";
$user     = "tnasolut_data_studio";
$password = "P3rf3ct0.,";
$dbname   = "tnasolut_sweet";
date_default_timezone_set('UTC'); // hora de la base de datos
$userTimeZone = new DateTimeZone('America/Santiago');  // hora de chile
$hoy = date("d-m-Y H:i:s");


function DbConnect($dbname){
    $server   = "localhost";
    $user     = "tnasolut_data_studio";
    $password = "P3rf3ct0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    $dummy = mysqli_set_charset ($conn, "utf8");    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
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
