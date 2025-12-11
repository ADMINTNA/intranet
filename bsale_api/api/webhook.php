<?php
// Configuración base de datos
$host = 'localhost';
$db   = 'icontel_bsale';
$user = 'data_studio';
$pass = '1Ngr3s0.,';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Captura del método (POST o PUT)
$accion = ($_SERVER['REQUEST_METHOD'] === 'POST') ? 'creacion' : 'actualizacion';

// Captura del body enviado por Bsale (JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Opcional: registrar log
file_put_contents('webhook_bsale.log', date('Y-m-d H:i:s') . ' - ' . $accion . ' - ' . $input . PHP_EOL, FILE_APPEND);

// Validación básica
if (!isset($data['id']) || !isset($data['number']) || !isset($data['documentTypeId'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios']);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Insertar notificación
    $stmt = $pdo->prepare("
        INSERT INTO notificaciones (doc_id, num_doc, tipo_doc_id, fecha_recepcion, accion)
        VALUES (:doc_id, :num_doc, :tipo_doc_id, NOW(), :accion)
    ");

    $stmt->execute([
        ':doc_id' => $data['id'],
        ':num_doc' => $data['number'],
        ':tipo_doc_id' => $data['documentTypeId'],
        ':accion' => $accion
    ]);

    echo json_encode(['status' => 'ok', 'message' => 'Notificación registrada']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}