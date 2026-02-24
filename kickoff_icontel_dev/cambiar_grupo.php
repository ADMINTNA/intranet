<?php
// ==========================================================
// KickOff – Cambiar Grupo de Seguridad (AJAX)
// ==========================================================

require_once __DIR__ . '/session_core.php';

// Mapeo de variables de sesión
if (isset($_SESSION['name']) && !isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = $_SESSION['name'];
}

header('Content-Type: application/json');

// Verificar sesión (con bypass para mAo)
$is_authenticated = (!empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true);
$is_developer = (basename(dirname(__FILE__)) === 'kickoff_icontel' && ($_SESSION['usuario'] ?? '') === 'Mauricio');

if (!$is_authenticated && !$is_developer) {
    echo json_encode([
        'success' => false, 
        'error' => 'No autenticado'
    ]);
    exit;
}

// Verificar que llegó el parámetro
if (!isset($_POST['sg']) || empty($_POST['sg'])) {
    echo json_encode(['success' => false, 'error' => 'Grupo no especificado']);
    exit;
}

// Actualizar sesión
$sg_id = $_POST['sg'];
$_SESSION['sg_id'] = $sg_id;

// Persistir en Cookie por 30 días
setcookie('icontel_last_sg_id', $sg_id, time() + (86400 * 30), '/', '.icontel.cl', false, true);

// Obtener nombre del grupo
include_once("config.php");
$conn = DbConnect($db_sweet);
$sql = "SELECT name FROM securitygroups WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_POST['sg']);
$stmt->execute();
$result = $stmt->get_result();

$sg_name = '';
if ($row = $result->fetch_assoc()) {
    $sg_name = $row['name'];
}

$_SESSION['sg_name'] = $sg_name;
$conn->close();

echo json_encode([
    'success' => true,
    'sg_id' => $_SESSION['sg_id'],
    'sg_name' => $sg_name
]);
?>