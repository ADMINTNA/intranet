<?PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//echo "Hola Mauricio";

// File: /api_sweet/test_token.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/utils.php';

header('Content-Type: application/json');

try {
    $token = get_crm_token();

    if (!$token) {
        throw new Exception("No se pudo obtener token desde SuiteCRM.");
    }

    echo json_encode([
        'status' => 'OK',
        'token' => $token
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Error interno: ' . $e->getMessage()
    ]);
}


?>