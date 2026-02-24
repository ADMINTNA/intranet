<?php
// ==========================================================
// KickOff AJAX – Actualizar Cotización (Estado y N° DTE)
// /kickoff_icontel/update_cotizacion.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");
header("Content-Type: application/json; charset=UTF-8");

// Bootstrap AJAX
require_once __DIR__ . "/ajax_bootstrap.php";

// Validar sesión
if ($sg_id === "" || $sg_name === "") {
    echo json_encode(["success" => false, "msg" => "Sesión inválida"]);
    exit;
}

// Recibir parámetros
$cotiId  = isset($_POST['coti_id']) ? trim($_POST['coti_id']) : '';
$campo   = isset($_POST['campo']) ? trim($_POST['campo']) : '';
$valor   = isset($_POST['valor']) ? trim($_POST['valor']) : '';

// Validar
if (empty($cotiId)) {
    echo json_encode(["success" => false, "msg" => "ID de cotización vacío"]);
    exit;
}

if (!in_array($campo, ['invoice_status', 'num_dte__compra_c'])) {
    echo json_encode(["success" => false, "msg" => "Campo no permitido: {$campo}"]);
    exit;
}

// Conexión a SweetCRM
$conn = DbConnect($db_sweet);
if (!$conn) {
    echo json_encode(["success" => false, "msg" => "Error conectando a BD"]);
    exit;
}
$conn->set_charset("utf8mb4");

try {
    // Determinar tabla según el campo
    if ($campo === 'invoice_status') {
        // Campo en tabla principal aos_quotes
        $sql = "UPDATE aos_quotes SET invoice_status = ? WHERE id = ?";
    } else {
        // Campo num_dte__compra_c en aos_quotes_cstm
        $sql = "UPDATE aos_quotes_cstm SET {$campo} = ? WHERE id_c = ?";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparando query: " . $conn->error);
    }

    $stmt->bind_param("ss", $valor, $cotiId);
    
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando query: " . $stmt->error);
    }

    $affected = $stmt->affected_rows;
    $stmt->close();
    $conn->close();

    echo json_encode([
        "success" => true,
        "msg" => "Actualizado correctamente",
        "affected" => $affected
    ]);

} catch (Exception $e) {
    $conn->close();
    echo json_encode(["success" => false, "msg" => $e->getMessage()]);
}
