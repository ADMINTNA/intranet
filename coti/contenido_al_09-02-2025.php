<?php include_once("./include/include.php"); ?>
<?php 
	sort($estados); 
	sort($usuarios); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Distribución de Cotizaciones</title>
	<link rel="stylesheet" type="text/css" href="./css/style.css" />
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body leftmargin="O" topmargin="0" marginwidth="0" marginheight="0">	
	<form  action="" method="POST" style="text-align: center; margin-bottom: 10px;">
		<table>
		<tr>
			<th>Filtros de Cotizaciones</th>
		</tr>
		<tr>
			<td>
				<label>Fecha Inicial: <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" onchange="this.form.submit()"></label>
				<label>Fecha Final: <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" onchange="this.form.submit()"></label>
				<label>Usuario:
					<select name="user" onchange="this.form.submit()">
						<option value="">Todos</option>
						<?php foreach ($usuarios as $usuario): ?>
							<option value="<?= htmlspecialchars($usuario) ?>" <?= $selected_user == $usuario ? 'selected' : '' ?>>
								<?= htmlspecialchars($usuario) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>Estado:
					<select name="estado" onchange="this.form.submit()">
						<option value="">Todos</option>
						<?php foreach ($estados as $estado): ?>
							<option value="<?= htmlspecialchars($estado) ?>" <?= $selected_estado == $estado ? 'selected' : '' ?>>
								<?= htmlspecialchars($estado) ?>
							</option>
					<?php endforeach; ?>
				</select>
				</label>
				<label>
					<input type="checkbox" name="excluir_op_gd" value="1" <?= $excluir_op_gd ? 'checked' : '' ?> onchange="this.form.submit()">
					Excluir "OC/GD/GR y Reemplazada"
				</label>
				<!--button type="submit">Filtrar</button-->
			</td>
		</tr>
		</table>
	</form>

<!-- Tabla Principal de Cotizaciones -->
<table class="" style="margin: 0 auto; text-align: center;">
	<tr>
		<th colspan="10">Tabla Principal de Cotizaciones</th>
	</tr>
	<tr>
        <th>#</th>
        <th>Nº</th>
        <th class="text-left">Título</th>
        <th>Fecha de Creación</th>
        <th>Moneda</th>
        <th class="text-right">Monto</th>
        <th class="text-left">Estado</th>
        <th class="text-left">Usuario</th>
        <th>Op Nº</th>
        <th class="text-left">Estado OP</th>
    </tr>
    <?php 
    $line_number = 1;
    foreach ($quotes as $quote): ?>
        <tr>
            <td><?= $line_number++; ?></td>
            <td><a href="<?= htmlspecialchars($quote['url_cot']); ?>" target="_blank"><?= htmlspecialchars($quote['numero']); ?></a></td>
            <td class="text-left"><?= htmlspecialchars($quote['titulo']); ?></td>
            <td><?= htmlspecialchars($quote['fecha']); ?></td>
            <td><?= htmlspecialchars($quote['Moneda']); ?></td>
            <td class="text-right"><?= number_format($quote['Monto'], 2); ?></td>
            <td class="text-left"><?= htmlspecialchars($quote['Estado']); ?></td>
            <td class="text-left"><?= htmlspecialchars($quote['Usuario']); ?></td>
            <td><a href="<?= htmlspecialchars($quote['url_opor']); ?>" target="_blank"><?= htmlspecialchars($quote['numero_op']); ?></a></td>
            <td class="text-left"><?= htmlspecialchars($quote['op_estado']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<br>
<!-- Tabla Cantidad y monto por vendedor -->	
<table id="tabla_cantidad_monto" align="center">
	<tr>
		<td style="padding: 0px">
			<!-- Tabla Cantidad -->
			<table  id="tabla_cantidad" align="center">
				<tr>
					<th colspan="<?= count($usuarios) + 1 ?>">Cantidad de Cotizaciones</th>
				</tr>
				<tr>
					<th>Estado</th>
					<?php foreach ($usuarios as $usuario): ?>
						<th><?= htmlspecialchars($usuario) ?></th>
					<?php endforeach; ?>
				</tr>
				<?php foreach ($estados as $estado): ?>
					<tr>
						<td style="text-align: left"><?= htmlspecialchars($estado) ?></td>
						<?php foreach ($usuarios as $usuario): ?>
							<td><?= isset($cotizaciones_por_vendedor[$usuario][$estado]) ? $cotizaciones_por_vendedor[$usuario][$estado]['cantidad'] : 0 ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th style="text-align: right">Total</th>
					<?php
					foreach ($usuarios as $usuario) {
						$total_usuario = 0;
						foreach ($estados as $estado) {
							if (isset($cotizaciones_por_vendedor[$usuario][$estado])) {
								$total_usuario += $cotizaciones_por_vendedor[$usuario][$estado]['cantidad'];
							}
						}
						echo "<th>$total_usuario</th>";
					}
					?>
				</tr>
			</table>			
		</td>
		<td style="padding: 0px">
			<!-- Tabla Monto -->
			<table id="tabla_monto" align="center">
				<tr>
					<th colspan="<?= count($usuarios) + 1 ?>"> Monto UF de Cotizaciones</th>
				</tr>
				<tr>
					<th>Estado</th>
					<?php foreach ($usuarios as $usuario): ?>
						<th><?= htmlspecialchars($usuario) ?></th>
					<?php endforeach; ?>
				</tr>
				<?php foreach ($estados as $estado): ?>
					<tr>
						<td style="text-align: left;"><?= htmlspecialchars($estado) ?></td>
						<?php foreach ($usuarios as $usuario): ?>
							<td class="text-right">
								<?= isset($cotizaciones_por_vendedor[$usuario][$estado]) ? number_format($cotizaciones_por_vendedor[$usuario][$estado]['monto'], 2) : '0.00' ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th style="text-align: right">Total</th>
					<?php
					foreach ($usuarios as $usuario) {
						$total_monto_usuario = 0;
						foreach ($estados as $estado) {
							if (isset($cotizaciones_por_vendedor[$usuario][$estado])) {
								$total_monto_usuario += $cotizaciones_por_vendedor[$usuario][$estado]['monto'];
							}
						}
						echo "<th class='text-right'>" . number_format($total_monto_usuario, 2) . "</th>";
					}
					?>
				</tr>
			</table>
		</td>
	</tr>
	
</table>	
<!-- Tabla Resumen de Cotizaciones -->
<?php ksort($resumen); ?>
<table class="resumen-table">
	<tr>
		<th colspan="8">Resumen de Cotizaciones</th>
	</tr>
    <tr>
        <th class="text-left">Estado</th>
        <th>Cantidad</th>
        <th> % </th>
        <th>Monto</th>
        <th> % </th>
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
        <th class="text-left">Total General</td>
        <th><?= $total_cotizaciones; ?></td>
        <th>100%</td>
        <th class="text-right"><?= number_format($total_monto, 2); ?></td>
        <th>100%</td>
    </tr>
</table>

<!-- Tabla ARPU -->
<table style="margin: 0 auto; text-align: center; width: 50%; border-collapse: collapse; font-size: 16px; background-color: #fff; box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);">
	<tr>
		<th colspan="8">ARPU de Cierres en U</th>
	</tr>
    <tr>
        <?php foreach ($arpu as $estado => $valor): ?>
            <th><?= htmlspecialchars($estado); ?></th>
        <?php endforeach; ?>
    </tr>
    <tr>
        <?php foreach ($arpu as $valor): ?>
            <td class="text-right"><b><?= number_format($valor, 2); ?></b></td>
        <?php endforeach; ?>
    </tr>
</table>

<!-- Gráficos de Distribución -->
<h2>Distribución de Cotizaciones por Estado</h2>
<div class="chart-container">
    <div class="chart-wrapper">
        <canvas id="chartCotizacionesPorEstado"></canvas>
    </div>
    <div class="chart-wrapper">
        <canvas id="chartMontoPorEstado"></canvas>
    </div>
</div>
    <!-- incluyo rutinas de java script -->
	<?PHP include_once("./include/js.php"); ?>
</body>
</html>
