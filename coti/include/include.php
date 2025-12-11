    <?php
ini_set('log_errors', 'On');
ini_set('error_log', './error.log');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'tnasolut_sweet');
define('DB_USER', 'data_studio');
define('DB_PASS', '1Ngr3s0.,');

// Función para conectar a la base de datos
function getConnection() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        $pdo->exec("SET NAMES 'utf8'");
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Inicializar filtros
$start_date = date('Y-m-01');
$end_date = date('Y-m-d');
$selected_users = [];
$selected_estados = [];
$excluir_op_gd = true;
$excluir_op_perdida = false; // Nueva opción para excluir OP Perdida

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['limpiar_filtros'])) {
        $selected_users = [];
        $selected_estados = [];
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        $excluir_op_gd = true;
        $excluir_op_perdida = false;
    } else {
        $start_date = $_POST['start_date'] ?? $start_date;
        $end_date = $_POST['end_date'] ?? $end_date;
        $selected_users = $_POST['user'] ?? [];
        $selected_estados = $_POST['estado'] ?? [];
        $excluir_op_gd = isset($_POST['excluir_op_gd']);
        $excluir_op_perdida = isset($_POST['excluir_op_perdida']); // Nuevo filtro
    }
}

// Obtener datos de la base
$pdo = getConnection();
$sql = "CALL cotizaciones_entre_fechas(:start_date, :end_date)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Filtrar resultados en PHP
$quotes = array_filter($quotes, function($quote) use ($selected_users, $selected_estados, $excluir_op_gd, $excluir_op_perdida) {
    if (!empty($selected_users) && !in_array($quote['Usuario'], $selected_users)) return false;
    if (!empty($selected_estados) && !in_array($quote['Estado'], $selected_estados)) return false;
    if ($excluir_op_gd && in_array($quote['Estado'], ['Guia Despacho', 'Orden de Compra', 'Reemplazada'])) return false;
    if ($excluir_op_perdida && $quote['op_estado'] === 'Perdida') return false; // Nueva condición
    return true;
});


// Obtener usuarios y estados únicos
$usuarios = array_unique(array_column($quotes, 'Usuario'));
$estados = array_unique(array_column($quotes, 'Estado'));

// Calcular el resumen
$resumen = [];
$total_cotizaciones = 0;
$total_monto = 0.00;

foreach ($quotes as $quote) {
    $estado = $quote['Estado'];
    $monto = $quote['Monto'];

    if (!isset($resumen[$estado])) {
        $resumen[$estado] = ['total' => 0, 'monto' => 0.00];
    }

    $resumen[$estado]['total']++;
    $resumen[$estado]['monto'] += $monto;
    $total_cotizaciones++;
    $total_monto += $monto;
}

// Calcular porcentajes
foreach ($resumen as $estado => &$datos) {
    $datos['porcentaje_cantidad'] = ($total_cotizaciones > 0) ? ($datos['total'] / $total_cotizaciones) * 100 : 0;
    $datos['porcentaje_monto'] = ($total_monto > 0) ? ($datos['monto'] / $total_monto) * 100 : 0;
}
unset($datos);
?>
