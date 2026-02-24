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
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexiÃ³n: " . $e->getMessage());
    }
}

// Definir valores de fecha predeterminados
$start_date_default = date('Y-m-01'); // Primer dÃ­a del mes actual
$end_date_default = date('Y-m-d'); // Fecha actual

// Inicializar variables para evitar errores
$start_date = $start_date_default;
$end_date = $end_date_default;
$selected_user = '';
$selected_estado = '';

// Procesar el formulario de filtro si se envÃ­a
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : $start_date_default;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $end_date_default;
    $selected_user = $_POST['user'] ?? '';
    $selected_estado = $_POST['estado'] ?? '';
}

// Obtener la conexiÃ³n a la base de datos
$pdo = getConnection();

// Obtener los valores de dolar y uf
$dolar_stmt = $pdo->query("SELECT valor_moneda FROM tnasolut_monedas.valor_moneda USE INDEX (id_moneda) WHERE DATE(fecha_moneda) = CURDATE() AND id_moneda = 2");
$dolar = $dolar_stmt->fetchColumn();

$uf_stmt = $pdo->query("SELECT valor_moneda FROM tnasolut_monedas.valor_moneda USE INDEX (id_moneda) WHERE DATE(fecha_moneda) = CURDATE() AND id_moneda = 6");
$uf = $uf_stmt->fetchColumn();

// Filtros a aplicar
$query_conditions = "WHERE !aq.deleted AND aq.date_entered BETWEEN :start_date AND :end_date AND aqc.etapa_cotizacion_c NOT IN ('remplazada_cot', 'guia_oc_cli', 'orden_compra')";
if ($selected_user) $query_conditions .= " AND aq.assigned_user_id = :selected_user";
if ($selected_estado) $query_conditions .= " AND aqc.etapa_cotizacion_c = :selected_estado";

// Consulta principal para obtener las cotizaciones
$sql = "
    SELECT 
        aq.number AS numero,
        CONCAT('https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAOS_Quotes%26action%3DDetailView%26record%3D', aq.id) AS Link,
        aq.date_entered AS fecha,
        CASE 
            WHEN aqc.etapa_cotizacion_c = 'entregada_cot' THEN 'Entregada'
            WHEN aqc.etapa_cotizacion_c = 'espera_cliente_cot' THEN 'En espera Cliente'
            WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado' THEN 'Cerrado Ãnica'
            WHEN aqc.etapa_cotizacion_c = 'cerrado_perdido_cot' THEN 'Perdida'
            WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_cot' THEN 'Cerrado Mensual'
            WHEN aqc.etapa_cotizacion_c = 'Cerrado_aceptado_anual_cot' THEN 'Cerrado Anual'
            WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_cli' THEN 'Cerrado Bienall'
            WHEN aqc.etapa_cotizacion_c = 'Borrador_cot' THEN 'Borrador'
            WHEN aqc.etapa_cotizacion_c = 'negociacion_cot' THEN 'En NegociaciÃ³n'
            WHEN aqc.etapa_cotizacion_c = 'demo' THEN 'En Demo'
            WHEN aqc.etapa_cotizacion_c = 'de_baja' THEN 'De Baja'
            WHEN aqc.etapa_cotizacion_c = 'en_traslado' THEN 'En Traslado'
            ELSE aqc.etapa_cotizacion_c
        END AS Estado,
        CASE 
            WHEN cu.symbol = 'USD' THEN 'USD a UF'
            WHEN cu.symbol = '$' THEN '$ a UF'
            ELSE IF(cu.symbol > '', cu.symbol, 'UF')
        END AS Moneda,
        CASE 
            WHEN cu.symbol = 'USD' THEN aq.total_amount * " . $dolar . "/" . $uf . "
            WHEN cu.symbol = '$' THEN aq.total_amount/" . $uf . "
            ELSE aq.total_amount
        END AS Monto,
        CONCAT(us.first_name, ' ', us.last_name) AS Usuario
    FROM tnasolut_sweet.aos_quotes AS aq
    LEFT JOIN tnasolut_sweet.users AS us ON aq.assigned_user_id = us.id
    LEFT JOIN tnasolut_sweet.currencies AS cu ON aq.currency_id = cu.id 
    LEFT JOIN tnasolut_sweet.aos_quotes_cstm AS aqc ON aq.id = aqc.id_c
    $query_conditions
    ORDER BY aq.date_entered ASC
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
if ($selected_user) $stmt->bindParam(':selected_user', $selected_user);
if ($selected_estado) $stmt->bindParam(':selected_estado', $selected_estado);
$stmt->execute();
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consultar los usuarios para el select de usuario segÃºn los registros encontrados
$usuarios_stmt = $pdo->prepare("SELECT DISTINCT us.id, CONCAT(us.first_name, ' ', us.last_name) AS nombre FROM tnasolut_sweet.aos_quotes AS aq LEFT JOIN tnasolut_sweet.users AS us ON aq.assigned_user_id = us.id LEFT JOIN tnasolut_sweet.aos_quotes_cstm AS aqc ON aq.id = aqc.id_c $query_conditions");
$usuarios_stmt->bindParam(':start_date', $start_date);
$usuarios_stmt->bindParam(':end_date', $end_date);
if ($selected_user) $usuarios_stmt->bindParam(':selected_user', $selected_user);
if ($selected_estado) $usuarios_stmt->bindParam(':selected_estado', $selected_estado);
$usuarios_stmt->execute();
$usuarios = $usuarios_stmt->fetchAll(PDO::FETCH_ASSOC);

// Consultar los estados Ãºnicos para el select de estado segÃºn los registros encontrados
$estados_stmt = $pdo->prepare("SELECT DISTINCT aqc.etapa_cotizacion_c FROM tnasolut_sweet.aos_quotes AS aq LEFT JOIN tnasolut_sweet.aos_quotes_cstm AS aqc ON aq.id = aqc.id_c $query_conditions");
$estados_stmt->bindParam(':start_date', $start_date);
$estados_stmt->bindParam(':end_date', $end_date);
if ($selected_user) $estados_stmt->bindParam(':selected_user', $selected_user);
if ($selected_estado) $estados_stmt->bindParam(':selected_estado', $selected_estado);
$estados_stmt->execute();
$estados = [];
foreach ($estados_stmt as $row) {
    $estados[] = $row['etapa_cotizacion_c'];
}

// Consulta para obtener el resumen
$resumen_sql = "
    SELECT 
        COUNT(aq.id) AS total_cotizaciones,
        CASE 
            WHEN aqc.etapa_cotizacion_c = 'entregada_cot' THEN 'Entregada'
            WHEN aqc.etapa_cotizacion_c = 'espera_cliente_cot' THEN 'En espera Cliente'
            WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado' THEN 'Cerrado Ãnica'
            WHEN aqc.etapa_cotizacion_c = 'cerrado_perdido_cot' THEN 'Perdida'
            WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_cot' THEN 'Cerrado Mensual'
            WHEN aqc.etapa_cotizacion_c = 'Cerrado_aceptado_anual_cot' THEN 'Cerrado Anual'
            WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_cli' THEN 'Cerrado Bienall'
            WHEN aqc.etapa_cotizacion_c = 'Borrador_cot' THEN 'Borrador'
            WHEN aqc.etapa_cotizacion_c = 'negociacion_cot' THEN 'En NegociaciÃ³n'
            WHEN aqc.etapa_cotizacion_c = 'demo' THEN 'En Demo'
            WHEN aqc.etapa_cotizacion_c = 'de_baja' THEN 'De Baja'
            WHEN aqc.etapa_cotizacion_c = 'en_traslado' THEN 'En Traslado'
            ELSE aqc.etapa_cotizacion_c
        END AS Estado,
        CASE 
            WHEN cu.symbol = 'USD' THEN SUM(aq.total_amount * " . $dolar . "/" . $uf . ") 
            WHEN cu.symbol = '$' THEN SUM(aq.total_amount/" . $uf . ") 
            ELSE SUM(aq.total_amount)
        END AS suma_monto
    FROM tnasolut_sweet.aos_quotes AS aq
    LEFT JOIN tnasolut_sweet.aos_quotes_cstm AS aqc ON aq.id = aqc.id_c
    LEFT JOIN tnasolut_sweet.currencies AS cu ON aq.currency_id = cu.id 

    $query_conditions
    GROUP BY Estado
";

$resumen_stmt = $pdo->prepare($resumen_sql);
$resumen_stmt->bindParam(':start_date', $start_date);
$resumen_stmt->bindParam(':end_date', $end_date);
if ($selected_user) $resumen_stmt->bindParam(':selected_user', $selected_user);
if ($selected_estado) $resumen_stmt->bindParam(':selected_estado', $selected_estado);
$resumen_stmt->execute();
$resumen = $resumen_stmt->fetchAll(PDO::FETCH_ASSOC);

// CÃ¡lculo de ARPU por estado usando un bucle general para simplificar la lÃ³gica
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
    <title>AnÃ¡lisis de Cotizaciones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; background-color: #f4f7fa; }
        h1, h2 { text-align: center; color: #2d2d2d; }
        form { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; font-size: 14px; }
        table { border-collapse: collapse; margin-top: 20px; font-size: 13px; background-color: #fff; box-shadow: 0 0 8px rgba(0, 0, 0, 0.1); }
        th, td { padding: 6px 10px; text-align: center; border: 1px solid #d0d7de; }
        th { background-color: #e1ebf5; color: #333; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #e2effb; }
        .resumen-table { width: 60%; margin: 30px auto; font-size: 13px; background-color: #fff; border: 1px solid #d0d7de; box-shadow: 0 0 6px rgba(0, 0, 0, 0.1); }
        .resumen-title { background-color: #dae8f1; color: #333; font-weight: bold; }
        .total-row { background-color: #f0f5f9; font-weight: bold; color: #2d2d2d; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>AnÃ¡lisis de Cotizaciones</h1>

<form action="" method="POST">
    <label for="start_date">Fecha Inicial:</label>
    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date); ?>" required onchange="this.form.submit()">

    <label for="end_date">Fecha Final:</label>
    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date); ?>" required onchange="this.form.submit()">

    <label for="user">Usuario:</label>
    <select name="user" id="user" onchange="this.form.submit()">
        <option value="">Todos</option>
        <?php foreach ($usuarios as $usuario): ?>
            <option value="<?= htmlspecialchars($usuario['id']); ?>" <?= $selected_user == $usuario['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($usuario['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="estado">Estado:</label>
    <select name="estado" id="estado" onchange="this.form.submit()">
        <option value="">Todos</option>
        <?php foreach ($estados as $estado): ?>
            <option value="<?= htmlspecialchars($estado); ?>" <?= $selected_estado == $estado ? 'selected' : '' ?>>
                <?= htmlspecialchars($estado); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filtrar</button>
</form>

<?php if (!empty($quotes)): ?>
    <table align="center">
        <tr>
            <th>#</th>
            <th>NÂº</th>
            <th>Fecha de CreaciÃ³n</th>
            <th>Estado</th>
            <th>Moneda</th>
            <th>Monto</th>
            <th>Usuario</th>
        </tr>
        <?php 
        $line_number = 1;
        foreach ($quotes as $quote): ?>
            <tr>
                <td><?= $line_number++; ?></td>
                <td><a href="<?= htmlspecialchars($quote['Link']); ?>" target="_blank"><?= htmlspecialchars($quote['numero']); ?></a></td>
                <td><?= htmlspecialchars($quote['fecha']); ?></td>
                <td><?= htmlspecialchars($quote['Estado']); ?></td>
                <td><?= htmlspecialchars($quote['Moneda']); ?></td>
                <td align="right" class="monto"><?= number_format($quote['Monto'], 2); ?></td>
                <td><?= htmlspecialchars($quote['Usuario']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <p>No se encontraron cotizaciones en el rango de fechas seleccionado.</p>
<?php endif; ?>

<!-- Tabla de Resumen -->
<?php if (!empty($resumen)): ?>
    <h2 style="text-align: center; margin-top: 30px;">Resumen de Cotizaciones</h2>
    <table class="resumen-table">
        <tr>
            <th class="resumen-title">Estado</th>
            <th class="resumen-title">Total de Cotizaciones</th>
            <th class="resumen-title">% DistribuciÃ³n Cantidad</th>
            <th class="resumen-title">Suma del Monto</th>
            <th class="resumen-title">% DistribuciÃ³n Monto</th>
        </tr>
        <?php 
        $total_cotizaciones = 0;
        $total_monto = 0.00;
        
        // Calcular totales generales
        foreach ($resumen as $row) {
            $total_cotizaciones += $row['total_cotizaciones'];
            $total_monto += $row['suma_monto'];
        }
        
        // Mostrar cada fila con porcentaje
        foreach ($resumen as $row): 
            $distribucion_cantidad = ($row['total_cotizaciones'] / $total_cotizaciones) * 100;
            $distribucion_monto = ($row['suma_monto'] / $total_monto) * 100;
        ?>
            <tr>
                <td><?= htmlspecialchars($row['Estado']); ?></td>
                <td><?= htmlspecialchars($row['total_cotizaciones']); ?></td>
                <td><?= number_format($distribucion_cantidad, 2); ?>%</td>
                <td class="monto"><?= number_format($row['suma_monto'], 2); ?></td>
                <td class="monto"><?= number_format($distribucion_monto, 2); ?>%</td>
            </tr>
        <?php endforeach; ?>
        
        <tr class="total-row">
            <td>Total General</td>
            <td><?= $total_cotizaciones; ?></td>
            <td>100%</td>
            <td class="monto"><?= number_format($total_monto, 2); ?></td>
            <td class="monto">100%</td>
        </tr>
    </table>
<?php else: ?>
    <p style="text-align: center; color: red;">No se encontraron cotizaciones para el rango de fechas seleccionado.</p>
<?php endif; ?>

<!-- VisualizaciÃ³n de ARPU por estado en una tabla horizontal -->
<h2 style="text-align: center; margin-top: 20px;">ARPU por Estado</h2>
<table style="margin: 0 auto; text-align: center; width: 50%; border-collapse: collapse; font-size: 16px; background-color: #fff; box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);">
    <tr>
        <?php foreach ($arpu as $estado => $valor): ?>
            <th style="padding: 10px; border: 1px solid #d0d7de; background-color: #e1ebf5; color: #333;">
                ARPU <?= htmlspecialchars($estado); ?>
            </th>
        <?php endforeach; ?>
    </tr>
    <tr>
        <?php foreach ($arpu as $valor): ?>
            <td style="padding: 10px; border: 1px solid #d0d7de;">
                <?= number_format($valor, 2); ?>
            </td>
        <?php endforeach; ?>
    </tr>
</table>

<!-- GrÃ¡ficos de DistribuciÃ³n -->
<div style="display: flex; justify-content: center; gap: 20px; margin-top: 30px;">
    <div style="max-width: 300px;">
        <canvas id="chartCotizacionesPorEstado"></canvas>
    </div>
    <div style="max-width: 300px;">
        <canvas id="chartMontoPorEstado"></canvas>
    </div>
</div>

<?php
$estados_labels = [];
$cantidad_data = [];
$monto_data = [];
$total_cotizaciones = 0;
$total_monto = 0.00;

// Extraer datos del resumen para los grÃ¡ficos
foreach ($resumen as $row) {
    $estados_labels[] = $row['Estado'];
    $cantidad_data[] = $row['total_cotizaciones'];
    $monto_data[] = $row['suma_monto'];
    $total_cotizaciones += $row['total_cotizaciones'];
    $total_monto += $row['suma_monto'];
}
?>

<script>
    const ctxCotizaciones = document.getElementById('chartCotizacionesPorEstado').getContext('2d');
    new Chart(ctxCotizaciones, {
        type: 'pie',
        data: {
            labels: <?= json_encode($estados_labels) ?>,
            datasets: [{
                label: 'DistribuciÃ³n de Cotizaciones',
                data: <?= json_encode($cantidad_data) ?>,
                backgroundColor: ['#4CAF50', '#FF9800', '#2196F3', '#FF5722', '#9C27B0', '#E91E63', '#00BCD4'],
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
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw;
                            return label;
                        }
                    }
                }
            }
        }
    });

    const ctxMonto = document.getElementById('chartMontoPorEstado').getContext('2d');
    new Chart(ctxMonto, {
        type: 'pie',
        data: {
            labels: <?= json_encode($estados_labels) ?>,
            datasets: [{
                label: 'DistribuciÃ³n de Monto',
                data: <?= json_encode($monto_data) ?>,
                backgroundColor: ['#4CAF50', '#FF9800', '#2196F3', '#FF5722', '#9C27B0', '#E91E63', '#00BCD4'],
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
                            if (label) {
                                label += ': ';
                            }
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
