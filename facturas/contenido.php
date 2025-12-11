<?php include_once(__DIR__ . "/include/include.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Distribución de Facturas</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
        <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
        <script src='script.js' type='text/javascript'></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script type="text/javascript">
	function exportToExcel(tableId){
		let tableData = document.getElementById(tableId).outerHTML;
		tableData = tableData.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
		tableData = tableData.replace(/<input[^>]*>|<\/input>/gi, ""); //remove input params

		let a = document.createElement('a');
		a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`
		a.download = 'Facturas_' + getRandomNumbers() + '.xls'
		a.click()
	}
	function getRandomNumbers() {
		let dateObj = new Date()
		let dateTime = `${dateObj.getHours()}${dateObj.getMinutes()}${dateObj.getSeconds()}`

		return `${dateTime}${Math.floor((Math.random().toFixed(2)*100))}`
	}        

</script>
    </head>
<body>

<form action="" method="POST" style="text-align: center; margin-bottom: 10px;">
    <table>
        <tr><th colspan="5">Filtros de Facturas</th></tr>
        <tr>
            <td align="left">
                <label>Fecha Inicial:<br>
                    <input type="date" name="start_date" value="<?= h($start_date) ?>"></label><br>
                <label>Fecha Final:<br>
                    <input type="date" name="end_date" value="<?= h($end_date) ?>"></label>
            </td>
            <td>
                <label>Usuarios:</label>
                <select name="user[]" multiple>
                    <?php foreach ($usuarios as $usuario): if ($usuario==='') continue; ?>
                        <option value="<?= h($usuario) ?>" <?= in_array($usuario, $selected_users) ? 'selected' : '' ?>>
                            <?= h($usuario) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <label>Estados:</label>
                <select name="estado[]" multiple>
                    <?php foreach ($estados as $estado): if ($estado==='') continue; ?>
                        <option value="<?= h($estado) ?>" <?= in_array($estado, $selected_estados) ? 'selected' : '' ?>>
                            <?= h($estado) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <button type="submit" name="filtrar">Filtrar</button>
                <button style="background-color: #64C2C8" type="submit" name="limpiar_filtros">Limpiar Filtros</button>
            </td>
        </tr>
    </table>
</form>

<!-- Tabla Principal -->
<?php if (!empty($facturas)): ?>
    
    <div style="max-height: 400px; overflow-y: auto; margin: 0 auto; width: 95%;">
      <table id="empTable" style="border-collapse: collapse; width: 100%; text-align: center; border: 1px solid #ddd;">
          <thead>
              <tr style="background-color: #f2f2f2;">
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">#</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Fac</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Cot</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Opor</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">NV</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2; text-align:left;">Título</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Fecha_Fac</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Moneda</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Monto(UF)</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Estado</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Nmombre_Ejecutiv@</th>
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;"><input type="button" onClick="exportToExcel('empTable')" value="📊 Exportar" /><br>Estado OP</th>
              </tr>
          </thead>
          <tbody>
            <?php $line_number = 1; foreach ($facturas as $row): ?>
                <tr>
                    <td><?= $line_number++; ?></td>
                    <td>
                        <a href="<?= h($row['url_fac']); ?>" target="_blank" rel="noopener">
                            <?= h($row['numero_fac']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= h($row['url_cot']); ?>" target="_blank" rel="noopener">
                            <?= h($row['numero_cot']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= h($row['url_opor']); ?>" target="_blank" rel="noopener">
                            <?= h($row['numero_op']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= h($row['url_nv']); ?>" target="_blank" rel="noopener">
                            <?= h($row['numeo_nv']); ?>
                        </a>
                    </td>
                    <td style="text-align: left;"><?= h($row['titulo']); ?></td>
                    <td><?= fmt_fecha($row['fecha']); ?></td>
                    <td><?= h($row['Moneda']); ?></td>
                    <td style="text-align: right"><?= number_format((float)$row['Monto'], 2); ?></td>
                    <td style="text-align: left;"><?= h($row['Estado']); ?></td>
                    <td style="text-align: left"><?= h($row['Usuario']); ?></td>
                    <td><?= h($row['op_estado']); ?></td>
                </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
    </div>
<?php else: ?>
    <p style="text-align: center; color: red;">No se encontraron facturas con los filtros aplicados.</p>
<?php endif; ?>

<br>

<!-- Tabla Resumen -->
<?php ksort($resumen); ?>
<table style="margin: 0 auto; border-collapse: collapse; width: 60%; text-align: center; border: 1px solid #ddd;">
    <tr>
        <td colspan="5">Tabla Resumen de Facturas por Estado</td>
    </tr>
    <tr style="background-color: #f2f2f2;">
        <th>Estado</th>
        <th>Cantidad</th>
        <th>%</th>
        <th>Monto (UF)</th>
        <th>%</th>
    </tr>
    <?php 
    $total_cantidad = 0;
    $total_monto = 0.00;
    foreach ($resumen as $estado => $datos): 
        $total_cantidad += $datos['total'];
        $total_monto    += $datos['monto'];
    ?>
        <tr>
            <td><?= h($estado); ?></td>
            <td><?= (int)$datos['total']; ?></td>
            <td><?= number_format($datos['porcentaje_cantidad'], 2); ?>%</td>
            <td style="text-align: right;"><?= number_format($datos['monto'], 2); ?></td>
            <td><?= number_format($datos['porcentaje_monto'], 2); ?>%</td>
        </tr>
    <?php endforeach; ?>
    <tr style="background-color: #f2f2f2; font-weight: bold;">
        <td>Total</td>
        <td><?= $total_cantidad; ?></td>
        <td>100%</td>
        <td style="text-align: right;"><?= number_format($total_monto, 2); ?></td>
        <td>100%</td>
    </tr>
</table>

<!-- Gráfico -->
<div style="width: 800px; margin: 0 auto;">
    <canvas id="chartFacturasPorEstado"></canvas>
</div>

<script>
    var ctx = document.getElementById('chartFacturasPorEstado').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($resumen)); ?>,
            datasets: [{
                label: 'Cantidad',
                data: <?= json_encode(array_column($resumen, 'total')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Facturas por Estado',
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
