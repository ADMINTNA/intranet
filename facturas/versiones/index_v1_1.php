<?php
ini_set('log_errors', 'On');
ini_set('error_log', './error.log');

// ConfiguraciÃ³n de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'tnasolut_sweet');
define('DB_USER', 'data_studio');
define('DB_PASS', '1Ngr3s0.,');

// FunciÃ³n para conectar a la base de datos
function getConnection() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES 'utf8'");
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexiÃ³n: " . $e->getMessage());
    }
}

// Definir valores de fecha predeterminados
$start_date_default = date('Y-m-01'); // Primer dÃ­a del mes actual
$end_date_default = date('Y-m-d'); // Fecha actual

// Inicializar variables de filtro
$start_date = $start_date_default;
$end_date = $end_date_default;
$selected_user = '';
$selected_estado = '';
$excluir_op_gd = isset($_POST['excluir_op_gd']); // Variable para el checkbox de exclusiÃ³n

// Procesar el formulario si se envÃ­a
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : $start_date_default;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $end_date_default;
    $selected_user = $_POST['user'] ?? '';
    $selected_estado = $_POST['estado'] ?? '';
    $excluir_op_gd = isset($_POST['excluir_op_gd']);
}

// ConexiÃ³n a la base de datos
$pdo = getConnection();

// Preparar la llamada al procedimiento almacenado con los filtros aplicados
$sql = "CALL cotizaciones_entre_fechas(:start_date, :end_date)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrar resultados segÃºn usuario, estado y exclusiÃ³n en PHP
$quotes = array_filter($quotes, function($quote) use ($selected_user, $selected_estado, $excluir_op_gd) {
    if ($selected_user && $quote['Usuario'] !== $selected_user) return false;
    if ($selected_estado && $quote['Estado'] !== $selected_estado) return false;
    if ($excluir_op_gd && in_array($quote['Estado'], ['Guia Despacho', 'Orden de Compra'])) return false;
    return true;
});

// Generar el resumen y calcular los totales despuÃ©s de aplicar los filtros
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
    'Cerrado Ãnica' => ['suma' => 0, 'cantidad' => 0],
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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DistribuciÃ³n de Cotizaciones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; background-color: #f4f7fa; }
        h1, h2 { text-align: center; color: #2d2d2d; }
        form { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; font-size: 14px; }
        table { border-collapse: collapse; margin-top: 20px; font-size: 13px; background-color: #fff; box-shadow: 0 0 8px rgba(0, 0, 0, 0.1); }
        th, td { padding: 6px 10px; text-align: center; border: 1px solid #d0d7de; }
        th { background-color: #e1ebf5; color: #333; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #e2effb; }
        .principal-table {  margin: 30px auto; font-size: 13px; background-color: #fff; border: 1px solid #d0d7de; box-shadow: 0 0 6px rgba(0, 0, 0, 0.1); }
        .resumen-table { width: 60%; margin: 30px auto; font-size: 13px; background-color: #fff; border: 1px solid #d0d7de; box-shadow: 0 0 6px rgba(0, 0, 0, 0.1); }
        .resumen-title { background-color: #dae8f1; color: #333; font-weight: bold; }
        .total-row { background-color: #f0f5f9; font-weight: bold; color: #2d2d2d; }
        .chart-container { display: flex; justify-content: center; gap: 20px; margin-top: 30px; }
        .chart-wrapper { width: 500px; height: 500px; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Filtros de Cotizaciones</h1>
<form action="" method="POST" style="text-align: center; margin-bottom: 20px;">
    <label>Fecha Inicial: <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"></label>
    <label>Fecha Final: <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"></label>
    <label>Usuario:
        <select name="user">
            <option value="">Todos</option>
            <!-- Opciones de usuarios -->
        </select>
    </label>
    <label>Estado:
        <select name="estado">
            <option value="">Todos</option>
            <!-- Opciones de estados -->
        </select>
    </label>
    <label>
        <input type="checkbox" name="excluir_op_gd" value="1" <?= $excluir_op_gd ? 'checked' : '' ?> onchange="this.form.submit()">
        Excluir "OC/GD/GR"
    </label>
    <button type="submit">Filtrar</button>
</form>

<!-- Tabla Principal de Cotizaciones -->
<h2>Tabla Principal de Cotizaciones</h2>
<table class="principal-table">
    <tr>
        <th>#</th>
        <th>NÂº</th>
        <th class="text-left">TÃ­tulo</th>
        <th>Fecha de CreaciÃ³n</th>
        <th>Moneda</th>
        <th class="text-right">Monto</th>
        <th class="text-left">Estado</th>
        <th class="text-left">Usuario</th>
    </tr>
    <?php 
    $line_number = 1;
    foreach ($quotes as $quote): ?>
        <tr>
            <td><?= $line_number++; ?></td>
            <td><a href="<?= htmlspecialchars($quote['Link']); ?>" target="_blank"><?= htmlspecialchars($quote['numero']); ?></a></td>
            <td class="text-left"><?= htmlspecialchars($quote['titulo']); ?></td>
            <td><?= htmlspecialchars($quote['fecha']); ?></td>
            <td><?= htmlspecialchars($quote['Moneda']); ?></td>
            <td class="text-right"><?= number_format($quote['Monto'], 2); ?></td>
            <td class="text-left"><?= htmlspecialchars($quote['Estado']); ?></td>
            <td class="text-left"><?= htmlspecialchars($quote['Usuario']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Tabla Resumen de Cotizaciones -->
<h2>Resumen de Cotizaciones</h2>
<?php ksort($resumen); ?>
<table class="resumen-table">
    <tr>
        <th class="text-left">Estado</th>
        <th>Total de Cotizaciones</th>
        <th>% DistribuciÃ³n Cantidad</th>
        <th>Suma del Monto</th>
        <th>% DistribuciÃ³n Monto</th>
    </tr>
     <?php foreach ($resumen as $estado => $datos): ?>
        <tr>
            <td class="text-left"><?= htmlspecialchars($estado); ?></td>
            <td><?= $datos['total']; ?></td>
            <td><?= number_format($datos['porcentaje_cantidad'], 2); ?>%</td>
            <td class="text-right"><?= number_format($datos['monto'], 2); ?></td>
            <td class="text-right"><?= number_format($datos['porcentaje_monto'], 2); ?>%</td>
        </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td class="text-left">Total General</td>
        <td><?= $total_cotizaciones; ?></td>
        <td>100%</td>
        <td class="text-right"><?= number_format($total_monto, 2); ?></td>
        <td>100%</td>
    </tr>
</table>

<!-- Tabla ARPU -->
<h2>ARPU de Cierres en UF</h2>
<table style="margin: 0 auto; text-align: center; width: 50%; border-collapse: collapse; font-size: 16px; background-color: #fff; box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);">
    <tr>
        <?php foreach ($arpu as $estado => $valor): ?>
            <th>ARPU <?= htmlspecialchars($estado); ?></th>
        <?php endforeach; ?>
    </tr>
    <tr>
        <?php foreach ($arpu as $valor): ?>
            <td class="text-right"><?= number_format($valor, 2); ?></td>
        <?php endforeach; ?>
    </tr>
</table>

<!-- GrÃ¡ficos de DistribuciÃ³n -->
<h2>DistribuciÃ³n de Cotizaciones por Estado</h2>
<div class="chart-container">
    <div class="chart-wrapper">
        <canvas id="chartCotizacionesPorEstado"></canvas>
    </div>
    <div class="chart-wrapper">
        <canvas id="chartMontoPorEstado"></canvas>
    </div>
</div>

<script>
    const estadosLabels = <?= json_encode(array_keys($resumen)) ?>;
    const cantidadData = <?= json_encode(array_column($resumen, 'total')) ?>;
    const montoData = <?= json_encode(array_column($resumen, 'monto')) ?>;
    const colors = ['#4CAF50', '#FF9800', '#2196F3', '#FF5722', '#9C27B0', '#E91E63', '#00BCD4'];

    // GrÃ¡fico de DistribuciÃ³n de Cantidad de Cotizaciones por Estado
    const ctxCantidad = document.getElementById('chartCotizacionesPorEstado').getContext('2d');
    new Chart(ctxCantidad, {
        type: 'pie',
        data: {
            labels: estadosLabels,
            datasets: [{
                label: 'DistribuciÃ³n de Cantidad',
                data: cantidadData,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: true, text: 'DistribuciÃ³n de Cantidad de Cotizaciones por Estado' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            label += context.raw;
                            return label;
                        }
                    }
                }
            }
        }
    });

    // GrÃ¡fico de DistribuciÃ³n de Montos por Estado
    const ctxMonto = document.getElementById('chartMontoPorEstado').getContext('2d');
    new Chart(ctxMonto, {
        type: 'pie',
        data: {
            labels: estadosLabels,
            datasets: [{
                label: 'DistribuciÃ³n de Monto',
                data: montoData,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: true, text: 'DistribuciÃ³n de Montos por Estado' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            label += context.raw;
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>