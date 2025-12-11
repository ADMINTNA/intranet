<?php include_once("./include/include.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Distribución de Cotizaciones</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<form action="" method="POST" style="text-align: center; margin-bottom: 10px;">
    <table>
        <tr><th>Filtros de Cotizaciones</th></tr>
        <tr>
            <td>
                <label>Fecha Inicial: <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"></label>
                <label>Fecha Final: <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"></label>

                <label>Usuarios:</label>
                <select name="user[]" multiple>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= htmlspecialchars($usuario) ?>" <?= in_array($usuario, $selected_users) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Estados:</label>
                <select name="estado[]" multiple>
                    <?php foreach ($estados as $estado): ?>
                        <option value="<?= htmlspecialchars($estado) ?>" <?= in_array($estado, $selected_estados) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($estado) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>
                    <input type="checkbox" name="excluir_op_gd" value="1" <?= $excluir_op_gd ? 'checked' : '' ?>>
                    Excluir "OC/GD/GR y Reemplazada"
                </label>
                &nbsp;&nbsp;<button type="submit" name="filtrar">Filtrar</button>
                &nbsp;&nbsp;&nbsp;&nbsp;<button style="background-color: #64C2C8" type="submit" name="limpiar_filtros">Limpiar Filtros</button>
            </td>
        </tr>
    </table>
</form>
 <!-- Tabla Principal -->   
<h2 style="text-align: center;">Tabla Principal de Cotizaciones</h2>

<?php if (!empty($quotes)): ?>
    <table style="margin: 0 auto; border-collapse: collapse; width: 90%; text-align: center; border: 1px solid #ddd;">
        <tr style="background-color: #f2f2f2;">
            <th>#</th><th>Nº</th><th style="text-align: left;">Título</th><th>Creación</th>
            <th>Moneda</th><th>Monto</th><th>Estado</th><th>Usuario</th><th>Op Nº</th><th>Estado OP</th>
        </tr>
        <?php $line_number = 1; foreach ($quotes as $quote): ?>
            <tr>
                <td><?= $line_number++; ?></td>
                <td><a href="<?= htmlspecialchars($quote['url_cot']); ?>" target="_blank"><?= htmlspecialchars($quote['numero']); ?></a></td>
                <td style="text-align: left;"><?= htmlspecialchars($quote['titulo']); ?></td>
                 <td><?= date('Y-m-d', strtotime($quote['fecha'])); ?></td> 
                <td><?= htmlspecialchars($quote['Moneda']); ?></td>
                <td><?= number_format($quote['Monto'], 2); ?></td>
                <td><?= htmlspecialchars($quote['Estado']); ?></td>
                <td><?= htmlspecialchars($quote['Usuario']); ?></td>
                <td><a href="<?= htmlspecialchars($quote['url_opor']); ?>" target="_blank"><?= htmlspecialchars($quote['numero_op']); ?></a></td>
                <td><?= htmlspecialchars($quote['op_estado']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p style="text-align: center; color: red;">No se encontraron cotizaciones con los filtros aplicados.</p>
<?php endif; ?>
    
<!-- Tabla Resumen -->
<h2 style="text-align: center;">Tabla Resumen</h2>
<?php ksort($resumen); // Ordenar la tabla por estado (clave del array) en orden ascendente ?>    
<table style="margin: 0 auto; border-collapse: collapse; width: 60%; text-align: center; border: 1px solid #ddd;">
    <tr style="background-color: #f2f2f2;">
        <th>Estado</th>
        <th>Cantidad</th>
        <th>%</th>
        <th>Monto</th>
        <th>%</th>
    </tr>
    <?php 
    $total_cantidad = 0;
    $total_monto = 0.00;

    foreach ($resumen as $estado => $datos): 
        $total_cantidad += $datos['total'];
        $total_monto += $datos['monto'];
    ?>
        <tr>
            <td><?= htmlspecialchars($estado); ?></td>
            <td><?= $datos['total']; ?></td>
            <td><?= number_format($datos['porcentaje_cantidad'], 2); ?>%</td>
            <td><?= number_format($datos['monto'], 2); ?></td>
            <td><?= number_format($datos['porcentaje_monto'], 2); ?>%</td>
        </tr>
    <?php endforeach; ?>

    <!-- Línea de Totales -->
    <tr style="background-color: #f2f2f2; font-weight: bold;">
        <td>Total</td>
        <td><?= $total_cantidad; ?></td>
        <td>100%</td>
        <td><?= number_format($total_monto, 2); ?></td>
        <td>100%</td>
    </tr>
</table><!-- Tabla Principal -->   
<h2 style="text-align: center;">Gráfico de Cotizaciones Por Estado</h2>    
<!-- Gráfico -->
<!-- Contenedor para el gráfico con ancho de 800px -->
<div style="width: 800px; margin: 0 auto;">
    <canvas id="chartCotizacionesPorEstado"></canvas>
</div>

<script>
    var ctx = document.getElementById('chartCotizacionesPorEstado').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($resumen)); ?>,
            datasets: [{
                label: 'Cantidad',
                data: <?= json_encode(array_column($resumen, 'total')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Cotizaciones por Estado',
                    font: { size: 18 }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
</body>
</html>
