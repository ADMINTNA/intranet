<?php
// ==========================================================
// KickOff Office V2 - Actualizar sg_id en sesión
// /kickoff_office_v2/update_sg_id.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2026-01-06
// ==========================================================

header('Content-Type: application/json; charset=utf-8');

// Iniciar sesión
require_once __DIR__ . '/session_core.php';

// Obtener nuevo sg_id
$new_sg_id = $_POST['sg_id'] ?? '';

// Actualizar sesión
if ($new_sg_id !== '') {
    $_SESSION['sec_id_office'] = $new_sg_id;
    
    // También actualizar sg_id para compatibilidad
    $_SESSION['sg_id'] = $new_sg_id;
    
    echo json_encode(['success' => true, 'sg_id' => $new_sg_id]);
} else {
    echo json_encode(['success' => false, 'error' => 'sg_id vacío']);
}
?>
