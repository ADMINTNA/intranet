<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Eventos</title>
</head>
<body style="font-size: small">
<?php
	$db_host="localhost";
	$port=3306;
	$socket="";
	$db_user="tnasolut_data_studio";
	$db_pwd="P3rf3ct0.,";
	$db_name="tnasolut_eventos";
	$con = new mysqli($db_host, $db_user, $db_pwd);
	if (!$con) die("No se conecta a servidor");
	mysqli_select_db($con,$db_name);
	mysqli_set_charset($con, 'utf8');
	/* $query = "SELECT s.servicio, 
				   (select count(id_servicio)-1 FROM eventos as e3 where e3.id_servicio=e1.id_servicio) as eventos,      
				   e1.fecha_fin,
				   TIMEDIFF(e1.fecha_fin, e1.fecha_ini) as duracion,
				   e1.desc_evento,       
				   DATEDIFF(now(), e1.fecha_fin) as dias_up
			FROM eventos as e1 INNER JOIN ( SELECT id_servicio, MAX(fecha_fin) as fecha_fin FROM eventos GROUP BY id_servicio ) as e2
			ON (e1.fecha_fin = e2.fecha_fin AND e1.id_servicio = e2.id_servicio)
			INNER JOIN servicios as s ON e1.id_servicio = s.id_servicio
			ORDER BY e1.fecha_fin desc;";
	*/
	$query = "SELECT e1.servicio,
				   (select count(servicio)-1 FROM eventos as e3 where e3.servicio=e1.servicio) as eventos,      
				   e1.fecha_fin,
				   TIMEDIFF(e1.fecha_fin, e1.fecha_ini) as duracion,
				   e1.desc_evento,       
				   DATEDIFF(now(), e1.fecha_fin) as dias_up      
			FROM eventos as e1 INNER JOIN ( SELECT servicio, MAX(fecha_fin) as fecha_fin FROM eventos GROUP BY servicio ) as e2
			ON (e1.fecha_fin = e2.fecha_fin AND e1.servicio = e2.servicio)
			ORDER BY e1.fecha_fin desc; ";
	$result = mysqli_query($con,$query);
	$servicios = mysqli_num_rows($result);
	if (!$result) { die("Error para mostrar los datos."); }
	$fields_num = mysqli_num_fields($result);
	echo "<table border='1' style='border-collapse: collapse;'>\n
	<tr align='center'>\n
		<th><a href='mantencion.php'>#</a></th>\n
		<th>Servicio</th>\n
		<th>Cantidad<br>de eventos</th>\n
		<th align='center'>Fecha<br>Ultima Reposici&oacute;n</th>\n
		<th>Duraci&oacute;n</th>\n
		<th>Descripci&oacute;n</th>\n
		<th>D&iacute;as sin<br> Evento</th>\n
		<td rowspan='9' align='center' ><div id='grafico'>aqui<br>va<br>el<br>gráfico</div></td>
	</tr>\n"; 
	$linea = 0;	
	$data  = array(); // array a graficar
	while($row = mysqli_fetch_row($result)) {
		$linea ++;
		echo "<tr>\n";
		echo "<td align='right' style='width: 2%'>".$linea."</td>\n";	
		$col = 0;
		$servicio = $row[0];
		$data[$servicio] = $row[5]; // datos a graficar
		foreach($row as $cell) {
			$col ++;
			//echo "->col= {$col} cell= {$cell}<-";
			echo "<td align='center'>".$cell."</td>\n";
		} // fin de foreach
		echo "</tr>\n";
	}
	echo "<tr>
	<td colspan=7 align='center' >Nota: Datos de Monitoreo desde el 01/01/2021 00:00:00 Hrs.</td>
	</tr>
	</table>\n";
 	// agrego gráfico en google
	include( 'GoogChart.class.php' );
	$chart = new GoogChart();
	$color = array('#99C754','#54C7C5','#999999',);
	$color = array('#e0440e', '#e6693e', '#ec8f6e', '#f3b49f', '#f6c7b6');
	//$color = array ('#95b645', '#7498e9', '#999999', '#FFFF00', '#FF0000', '#00FFFF', '#996633', '#FF00FF', '#006633');
	$chart->setChartAttrs( array(
							'type' => 'pie',
							'title' => 'Estabilidad de Servicio',
							'data' => $data,
							'size' => array( 400, 224 ), // ancho, alto
							'color' => $color,
							'background' => '#F1F1F1',
							
							));
	echo  "<script>document.getElementById('grafico').innerHTML = '".$chart."'</script>";

	
	
	
	
	
	
	
	
	
	
	
	
?>
</body>
</html>