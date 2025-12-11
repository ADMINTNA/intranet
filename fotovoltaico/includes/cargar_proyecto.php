<?php
require 'config.php';

// Verificar si se proporcionó un ID en la solicitud
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID del proyecto no proporcionado']);
    exit;
}

$id = intval($_GET['id']);
$proyecto = cargarProyecto($id);

if ($proyecto) {
    echo json_encode(['success' => true, 'data' => $proyecto]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se encontró el proyecto con ID ' . $id
    ]);
}
