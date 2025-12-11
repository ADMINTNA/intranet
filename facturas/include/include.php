<?php
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '/../error.log');
ini_set('display_errors', 0);
error_reporting(E_ALL);

// --------- Config DB (ideal: mover a ./includes/config.php) ----------
define('DB_HOST', 'localhost');
define('DB_NAME', 'tnasolut_sweet');
define('DB_USER', 'data_studio');
define('DB_PASS', '1Ngr3s0.,');

// --------- Conexión ----------
function getConnection() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, array(
            PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::ATTR_EMULATE_PREPARES         => false,
            PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
        ));
        $pdo->exec("SET NAMES 'utf8mb4'");
        return $pdo;
    } catch (PDOException $e) {
        error_log('DB connect error: ' . $e->getMessage());
        http_response_code(500);
        die('Error de conexión a la base de datos');
    }
}

// --------- Defaults de filtros (visibles en contenido.php) ----------
$start_date = date('Y-m-01');
$end_date   = date('Y-m-d');

$selected_users   = array();
$selected_estados = array();

// Estos dos se usan en el formulario de contenido.php
$excluir_op_gd       = false; // NO aplica a facturas; se deja por compatibilidad visual
$excluir_op_perdida  = false; // excluye oportunidades "Perdida"
$excluir_op_reempl   = false; // excluye oportunidades "Reemplazada"

// --------- Lee POST del formulario ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['limpiar_filtros'])) {
        // Volvemos a defaults
        $selected_users   = array();
        $selected_estados = array();
        $start_date = date('Y-m-01');
        $end_date   = date('Y-m-d');
        $excluir_op_gd       = false;
        $excluir_op_perdida  = false;
        $excluir_op_reempl   = false;
    } else {
        // Fechas
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $start_date;
        $end_date   = isset($_POST['end_date'])   ? $_POST['end_date']   : $end_date;

        // Listas multi-select
        $selected_users   = isset($_POST['user'])   ? (array)$_POST['user']   : array();
        $selected_estados = isset($_POST['estado']) ? (array)$_POST['estado'] : array();

        // Checkboxes
        $excluir_op_gd       = !empty($_POST['excluir_op_gd']);      // visual, no se aplica a facturas
        $excluir_op_perdida  = !empty($_POST['excluir_op_perdida']); // si true, filtramos op_estado=Perdida
        $excluir_op_reempl   = !empty($_POST['excluir_op_reempl']);  // si true, filtramos op_estado=Reemplazada
    }
}

// --------- Ejecuta SP y arma dataset ----------
$facturas = array();
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("CALL facturas_entre_fechas(:start_date, :end_date)");
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date',   $end_date);
    $stmt->execute();

    // Primer resultset (datos)
    $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Drenar cualquier result set extra que devuelva MySQL tras CALL
    while ($stmt->nextRowset()) { /* no-op */ }

    $stmt->closeCursor();
} catch (PDOException $e) {
    error_log('SP error: ' . $e->getMessage());
    $facturas = array(); // Evita bloquear render
}

// --------- Filtro en PHP (según formulario) ----------
$facturas = array_values(array_filter($facturas, function($row) use ($selected_users, $selected_estados, $excluir_op_perdida, $excluir_op_reempl) {
    if (!empty($selected_users) && !in_array($row['Usuario'], $selected_users, true)) {
        return false;
    }
    if (!empty($selected_estados) && !in_array($row['Estado'], $selected_estados, true)) {
        return false;
    }
    if ($excluir_op_perdida && isset($row['op_estado']) && $row['op_estado'] === 'Perdida') {
        return false;
    }
    if ($excluir_op_reempl && isset($row['op_estado']) && $row['op_estado'] === 'Reemplazada') {
        return false;
    }
    return true;
}));

// --------- Listas únicas para filtros ----------
$usuarios = array_values(array_unique(array_map(function($r){
    return isset($r['Usuario']) ? $r['Usuario'] : '';
}, $facturas)));

$estados = array_values(array_unique(array_map(function($r){
    return isset($r['Estado']) ? $r['Estado'] : '';
}, $facturas)));

// --------- Resumen por Estado ----------
$resumen = array();
$total_facturas = 0;
$total_monto    = 0.0;

foreach ($facturas as $row) {
    $estado = isset($row['Estado']) ? $row['Estado'] : '';
    $monto  = isset($row['Monto']) ? (float)$row['Monto'] : 0.0;

    if (!isset($resumen[$estado])) {
        $resumen[$estado] = array('total' => 0, 'monto' => 0.0, 'porcentaje_cantidad' => 0, 'porcentaje_monto' => 0);
    }
    $resumen[$estado]['total']++;
    $resumen[$estado]['monto'] += $monto;

    $total_facturas++;
    $total_monto += $monto;
}

if ($total_facturas > 0 || $total_monto > 0) {
    foreach ($resumen as $est => &$datos) {
        $datos['porcentaje_cantidad'] = $total_facturas > 0 ? ($datos['total'] / $total_facturas) * 100 : 0;
        $datos['porcentaje_monto']    = $total_monto    > 0 ? ($datos['monto'] / $total_monto)       * 100 : 0;
    }
    unset($datos);
}

// --------- Helpers disponibles para contenido.php ----------
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function fmt_monto_uf($n) { return number_format((float)$n, 2, ',', '.'); }
function fmt_fecha($s)    { return date('Y-m-d', strtotime($s)); }
