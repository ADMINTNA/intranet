<?php
//=========================================================
// /intranet/kickoff/update_estado_sweet.php
// Actualiza el estado financiero de la cuenta en Sweet
// Creado por Mauricio Araneda
// Fecha: 10/11/2025
// Actualizado: 2025-12-06 (Refactor)
//=========================================================

header('Content-Type: application/json; charset=utf-8');
require_once("config.php");

// Validar conexión
$conn = DbConnect($db_sweet);
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Error de conexión a la BD"]);
    exit;
}
$conn->set_charset("utf8mb4");

$rut    = $_POST['rut'] ?? '';
$estado = $_POST['estado'] ?? '';

if ($rut === '' || $estado === '') {
    echo json_encode(["success" => false, "error" => "Faltan parámetros"]);
    exit;
}

$sql = "
UPDATE accounts ac
JOIN accounts_cstm acc ON acc.id_c = ac.id
SET 
    acc.estatusfinanciero_c = ?,
    ac.date_modified = NOW()
WHERE REPLACE(REPLACE(TRIM(acc.rut_c), '.', ''), ' ', '') = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Error preparando consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $estado, $rut);
$success = $stmt->execute();

if ($success) {
    $affected = $stmt->affected_rows;
    echo json_encode([
        "success" => true, 
        "affected_rows" => $affected,
        "msg" => "Estado actualizado correctamente"
    ]);
} else {
    echo json_encode([
        "success" => false, 
        "error" => "Error ejecutando consulta: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>