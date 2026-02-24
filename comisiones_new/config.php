<?php
// ==========================================================
// /intranet/comisiones_new/config.php
// Configuración y funciones base - Modern Comisiones
// ==========================================================

session_name('icontel_intranet_sess');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("America/Santiago");

// Configuración de Base de Datos (Tomada de la versión original)
define('DB_HOST', 'localhost');
define('DB_USER', 'tnasolut_data_studio');
define('DB_PASS', 'P3rf3ct0.,');
define('DB_NAME', 'tnasolut_sweet');

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}
$con->set_charset("utf8");

// Funciones de utilidad para fechas
function primer_dia_mes() {
    return date('Y-m-01') . " 00:00:00";
}

function ultimo_dia_mes() {
    return date('Y-m-t') . " 23:59:59";
}

function primer_dia_mes_anterior() {
    return date('Y-m-01', strtotime('first day of last month')) . " 00:00:00";
}

function ultimo_dia_mes_anterior() {
    return date('Y-m-t', strtotime('last month')) . " 23:59:59";
}

// Funciones de formato
function formatCurrency($value) {
    return '$' . number_format($value, 0, ',', '.');
}

function formatDecimal($value, $decimals = 2) {
    return number_format($value, $decimals, ',', '.');
}

// Lógica original de comisiones (Adaptada)
function genera_condicion($opciones, $campo) {
    if(!empty($opciones)) {
        if (in_array("TODOS", $opciones)) return "";
        
        $condicion = " AND (";
        $ptr = 0;
        foreach($opciones as $opcion){
            if($ptr > 0) $condicion .= " OR ";
            $condicion .= $campo . " = '" . $opcion . "'";
            $ptr ++;
        }    
        $condicion .= ") ";
        return $condicion;
    }
    return "";
}

function recrea_base_comisiones($sql) {
    $temp_con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($temp_con->connect_error) return false;
    $temp_con->set_charset("utf8");
    
    $temp_con->query("DROP TABLE IF EXISTS ventas_comisiones;");
    $res = $temp_con->query($sql);
    
    // Consumir posibles resultados extras de procedimientos almacenados
    while($temp_con->next_result()) { if($result = $temp_con->store_result()) $result->free(); }
    
    $temp_con->close();
    return $res;
}

function busca_columna($sql, $columna = 'dato') {
    // Usamos una conexión aislada para evitar "Commands out of sync" con procedimientos almacenados
    $temp_con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($temp_con->connect_error) return [];
    $temp_con->set_charset("utf8");
    
    $datos = [];
    $result = $temp_con->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $datos[] = $row[$columna];
        }
    }
    $temp_con->close();
    return $datos;
}
?>
