<?php
//=========================================================
// /intranet/kickoff/update_comentario_sweet.php
// Actualiza comentario de estado de cobranza en Sweet
// Creado por Mauricio Araneda
// Fecha: 10/11/2025
// Actualizado: 2025-12-06 (Refactor)
//=========================================================

header('Content-Type: application/json; charset=utf-8');
require_once("config.php");

$rut        = $_POST['rut'] ?? '';
$comentario = $_POST['comentario'] ?? '';

if (empty($rut)) {
    echo json_encode(['success' => false, 'error' => 'RUT no recibido']);
    exit;
}

// Conexión usando config.php
$conn = DbConnect($db_sweet);
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la BD']);
    exit;
}
$conn->set_charset("utf8mb4");

// Usar Prepared Statements
$sql = "
    UPDATE accounts_cstm 
    SET comentario_estado_c = ? 
    WHERE REPLACE(REPLACE(TRIM(rut_c), '.', ''), ' ', '') = ?
    LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error preparando consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $comentario, $rut);
$success = $stmt->execute();

if ($success) {
    $affected = $stmt->affected_rows;
    echo json_encode([
        'success' => true, 
        'affected_rows' => $affected,
        'msg' => 'Comentario actualizado correctamente'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Error ejecutando update: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>