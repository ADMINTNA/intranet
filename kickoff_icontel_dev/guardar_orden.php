require_once __DIR__ . '/session_core.php';

// Validar entrada
$modulo = $_POST['modulo'] ?? ($_SESSION['modulo_activo'] ?? '');
$columna = $_POST['columna'] ?? '';
$direccion = $_POST['direccion'] ?? 'ASC';

if ($modulo && $columna) {
    $_SESSION['orden_' . $modulo] = [
        'columna' => $columna,
        'direccion' => strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC'
    ];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Datos insuficientes']);
}
?>
