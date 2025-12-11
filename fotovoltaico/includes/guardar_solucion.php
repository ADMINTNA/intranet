<?php
// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Asegura que siempre se devuelva JSON como respuesta
header('Content-Type: application/json');
require_once 'config.php'; // Incluye la conexión y funciones de base de datos

// Verificar si config.php se cargó correctamente
if (!function_exists('guardarSolucion')) {
    file_put_contents('debug.log', "Error: La función guardarSolucion no está definida en config.php." . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: función guardarSolucion no encontrada.']);
    exit;
}

// Verificar si los datos están llegando correctamente
// Leer los datos crudos del JSON
$input = file_get_contents('php://input');
file_put_contents('debug.log', "Datos recibidos: " . $input . PHP_EOL, FILE_APPEND);

// Intentar decodificar el JSON
$datos = json_decode($input, true);

// Verificar si el JSON es válido
if (!$datos) {
    file_put_contents('debug.log', "Error: JSON inválido. Posible causa: JSON mal formado o incompleto." . PHP_EOL, FILE_APPEND);
    echo json_encode([
        'success' => false,
        'message' => 'Error al decodificar JSON.',
        'rawData' => $input // Muestra el JSON recibido en Postman para ver si está truncado
    ]);
    exit;
}

try {
    // Verifica que la solicitud sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Use POST.');
    }

    // Validar que cliente y dirección no estén vacíos
    if (empty($datos['cliente']) || empty($datos['direccion'])) {
        throw new Exception('El campo cliente y dirección son obligatorios.');
    }

    // Convertir estructura a decimal si es necesario
    if ($datos['estructura'] > 1) {
        $datos['estructura'] = $datos['estructura'] / 100; // Convierte 45 a 0.45
    }

    // **Usar `precioVenta` desde el JSON, sin recalcular en PHP**
    if (!isset($datos['precioVenta']) || !is_numeric($datos['precioVenta'])) {
        throw new Exception('Error: precioVenta no fue enviado correctamente.');
    }

    // Intentar guardar en la base de datos
    $resultado = guardarSolucion([
        'cliente' => $datos['cliente'],
        'direccion' => $datos['direccion'],
        'consumoMensual' => $datos['consumoMensual'],
        'facturacionMensual' => $datos['facturacionMensual'],
        'horasSol' => $datos['horasSol'],
        'potenciaPanel' => $datos['potenciaPanel'],
        'rendimientoPanel' => $datos['rendimientoPanel'],
        'tamanoPanel' => $datos['tamanoPanel'],
        'tramiteSec' => $datos['tramiteSec'],
        'sec' => $datos['sec'],
        'tipoCalculo' => $datos['tipoCalculo'],
        'cantidadInversores' => $datos['cantidadInversores'],
        'estructura' => $datos['estructura'],
        'costoInversor' => $datos['costoInversor'],
        'cantidadPaneles' => $datos['cantidadPaneles'],
        'kWhPlanta' => $datos['kWhPlanta'],
        'kWDia' => $datos['kWDia'],
        'cumplimiento' => $datos['cumplimiento'],
        'precioVenta' => $datos['precioVenta'] // ✅ Ahora tomamos el precio enviado desde JS
    ]);

    if (!$resultado) {
        throw new Exception('Error al guardar en la base de datos.');
    }

    // Enviar respuesta de éxito
    $response = ['success' => true, 'message' => 'Solución guardada con éxito.'];
    file_put_contents('debug.log', "Respuesta enviada: " . json_encode($response) . PHP_EOL, FILE_APPEND);
    echo json_encode($response);

} catch (Exception $e) {
    // Registrar error en debug.log
    file_put_contents('debug.log', "Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);

    // Enviar respuesta de error en JSON
    http_response_code(500); // Código de error 500
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>
