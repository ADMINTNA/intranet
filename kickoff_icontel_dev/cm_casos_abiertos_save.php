<?php
//=====================================================
// Mantener sesi&oacute;n del KickOff
require_once __DIR__ . '/session_core.php';
// Guarda cambios realizados desde el Cuadro de Mando AJAX
// Autor: Mauricio Araneda (vía Antigravity)
// Fecha: 17-02-2026
//=====================================================

header('Content-Type: application/json; charset=utf-8');

// Bootstrap común AJAX (sesión unificada + config + auth)
require_once __DIR__ . "/ajax_bootstrap.php";

// El bootstrap ya tiene la validación de sesión básica.
// Mantenemos el bypass de desarrollador por si acaso, aunque ya debería estar cubierto
$is_developer = (strpos(basename(dirname(__FILE__)), 'kickoff_icontel') !== false && ($_SESSION['usuario'] ?? '') === 'Mauricio');

if (empty($_SESSION['loggedin']) && !$is_developer) {
    echo json_encode(["success" => false, "error" => "No autenticado"]);
    exit;
}

// Inicia la sesiÃ³n
require_once __DIR__ . '/session_core.php';

// Recibir datos
$id    = $_POST['id']    ?? '';
$campo = $_POST['campo'] ?? '';
$valor = $_POST['valor'] ?? '';

if (empty($id) || empty($campo)) {
    echo json_encode(["success" => false, "error" => "Parámetros insuficientes"]);
    exit;
}

// Mapeo de campos a base de datos de SuiteCRM
// cases -> tabla principal
// cases_cstm -> campos personalizados
$tabla = "cases";
$columna = "";

switch ($campo) {
    case 'priority':
        $columna = "priority";
        break;
    case 'status':
        $columna = "status";
        break;
    case 'category':
        $columna = "type"; // En SuiteCRM la categoría suele ser el campo 'type'
        break;
    case 'assigned_user_id':
        $columna = "assigned_user_id";
        break;
    case 'en_espera_de':
        $tabla = "cases_cstm";
        $columna = "en_espera_de_c";
        break;
    default:
        echo json_encode(["success" => false, "error" => "Campo no reconocido: $campo"]);
        exit;
}

$conn = DbConnect($db_sweet);
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Error de conexión a BD"]);
    exit;
}
$conn->set_charset("utf8mb4");

// 1. Actualizar el campo específico
if ($tabla === "cases") {
    $sql = "UPDATE cases SET $columna = ?, date_modified = NOW() WHERE id = ?";
} else {
    // Para cases_cstm, asumimos que el id es el mismo que el de cases
    $sql = "UPDATE cases_cstm SET $columna = ? WHERE id_c = ?";
    // Actualizar también date_modified en cases para marcar que hubo cambio
    $conn->query("UPDATE cases SET date_modified = NOW() WHERE id = '" . $conn->real_escape_string($id) . "'");
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Error en prepare: " . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $valor, $id);
$success = $stmt->execute();

if ($success) {
    echo json_encode(["success" => true, "msg" => "Cambio guardado en $campo"]);
} else {
    echo json_encode(["success" => false, "error" => "Error al ejecutar update: " . $stmt->error]);
}

$stmt->close();
$conn->close();
