<?PHP
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
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true); // Habilitar el almacenamiento en búfer de consultas
        $pdo->exec("SET NAMES 'utf8'");
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Definir valores de fecha predeterminados
$start_date_default = date('Y-m-01'); // Primer día del mes actual
$end_date_default = date('Y-m-d'); // Fecha actual

// Inicializar variables de filtro
$start_date = $start_date_default;
$end_date = $end_date_default;
$selected_user = '';
$selected_estado = '';
$excluir_op_gd = true; // Variable para el checkbox de exclusión

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : $start_date_default;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $end_date_default;
    $selected_user = $_POST['user'] ?? '';
    $selected_estado = $_POST['estado'] ?? '';
    $excluir_op_gd = isset($_POST['excluir_op_gd']);
}

// Conexión a la base de datos
$pdo = getConnection();

// Preparar la llamada al procedimiento almacenado con los filtros aplicados
$sql = "CALL cotizaciones_entre_fechas(:start_date, :end_date)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor(); // Cerrar el cursor para liberar la conexión

// Filtrar resultados según usuario, estado y exclusión en PHP
$quotes = array_filter($quotes, function($quote) use ($selected_user, $selected_estado, $excluir_op_gd) {
    if ($selected_user && $quote['Usuario'] !== $selected_user) return false;
    if ($selected_estado && $quote['Estado'] !== $selected_estado) return false;
    if ($excluir_op_gd && in_array($quote['Estado'], ['Guia Despacho', 'Orden de Compra', 'Reemplazada'])) return false;
    return true;
});

// Obtener lista de usuarios y estados únicos del recorset filtrado
$usuarios = array_unique(array_column($quotes, 'Usuario'));
$estados = array_unique(array_column($quotes, 'Estado'));

// Generar la tabla de cotizaciones por vendedor
$cotizaciones_por_vendedor = [];
foreach ($quotes as $quote) {
    $usuario = $quote['Usuario'];
    $estado = $quote['Estado'];
    $monto = $quote['Monto'];
    
    if (!isset($cotizaciones_por_vendedor[$usuario])) {
        $cotizaciones_por_vendedor[$usuario] = [];
    }
    if (!isset($cotizaciones_por_vendedor[$usuario][$estado])) {
        $cotizaciones_por_vendedor[$usuario][$estado] = ['cantidad' => 0, 'monto' => 0.00];
    }
    
    $cotizaciones_por_vendedor[$usuario][$estado]['cantidad'] += 1;
    $cotizaciones_por_vendedor[$usuario][$estado]['monto'] += $monto;
}

// Generar el resumen y calcular los totales después de aplicar los filtros
$resumen = [];
$total_cotizaciones = 0;
$total_monto = 0.00;

foreach ($quotes as $quote) {
    if (!isset($quote['Estado'], $quote['Monto'])) continue;

    $estado = $quote['Estado'];
    $monto = $quote['Monto'];
    
    if (!isset($resumen[$estado])) $resumen[$estado] = ['total' => 0, 'monto' => 0.00];
    $resumen[$estado]['total'] += 1;
    $resumen[$estado]['monto'] += $monto;
    $total_cotizaciones += 1;
    $total_monto += $monto;
}

// Calcular porcentajes para el resumen
foreach ($resumen as $estado => &$datos) {
    $datos['porcentaje_cantidad'] = ($datos['total'] / $total_cotizaciones) * 100;
    $datos['porcentaje_monto'] = ($datos['monto'] / $total_monto) * 100;
}
unset($datos);

// Calcular el ARPU de acuerdo con los filtros
$estado_montos = [
    'Cerrado Mensual' => ['suma' => 0, 'cantidad' => 0],
    'Cerrado Única' => ['suma' => 0, 'cantidad' => 0],
    'Cerrado Anual' => ['suma' => 0, 'cantidad' => 0],
    'Cerrado Bienal' => ['suma' => 0, 'cantidad' => 0]
];

foreach ($quotes as $quote) {
    $estado = $quote['Estado'];
    $monto = $quote['Monto'];
    if (isset($estado_montos[$estado])) {
        $estado_montos[$estado]['suma'] += $monto;
        $estado_montos[$estado]['cantidad'] += 1;
    }
}

$arpu = [];
foreach ($estado_montos as $estado => $datos) {
    $arpu[$estado] = ($datos['cantidad'] > 0) ? ($datos['suma'] / $datos['cantidad']) : 0;
}
?>