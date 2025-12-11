<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Verificar si la solicitud es POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["success" => false, "message" => "Método no permitido. Use POST."]);
        exit;
    }

    // Verificar que se haya recibido un ID válido
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(["success" => false, "message" => "ID inválido."]);
        exit;
    }

    $id = intval($_POST['id']); // Convertir a número entero

    // Llamar a la función eliminarSolucion() de config.php
    $resultado = eliminarSolucion($id);

    if ($resultado) {
        echo json_encode(["success" => true, "message" => "Solución eliminada correctamente."]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo eliminar la solución o no existe."]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error en el servidor: " . $e->getMessage()]);
    exit;
}
?>
