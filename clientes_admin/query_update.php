<!DOCTYPE html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="favicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
<link rel="manifest" href="favicon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="author" CONTENT="TNA Solutions">
<META NAME="subject" CONTENT="TNA SOlutions, Transportes">
<META NAME="Description" CONTENT="TNA SOlutions, Diseño, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
<META NAME="Classification" CONTENT="TNA Solutions, Diseño, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
<META NAME="Keywords" CONTENT="TNA Solutions, Diseño, Seguridad, Informatica, Desarrollo, Sistemas, Redes, Aplicaciones, Web, servidor, computacion, email">
<META NAME="Geography" CONTENT="Chile">
<META NAME="Language" CONTENT="Spanish">
<META HTTP-EQUIV="Expires" CONTENT="never">
<META NAME="Copyright" CONTENT="TNA Solutions">
<META NAME="Designer" CONTENT="TNA Solutions">
<META NAME="Publisher" CONTENT="TNA Solutions">
<META NAME="Revisit-After" CONTENT="7 days">
<META NAME="distribution" CONTENT="Global">
<META NAME="Robots" CONTENT="INDEX,FOLLOW">
<META NAME="city" CONTENT="Santiago">
<META NAME="country" CONTENT="Chile">
<title>Meses de Contrato</title>
</head>
<body>
<?php
// Motrar todos los errores de PHP
error_reporting(E_ALL);
 echo "Comienzo cálculo de meses de contrato. Favor espere ....<br>";	

	$db_host="cp2.tnasolutions.cl";
	$port=3306;
	$socket="";
	$db_user="tnasolut_enlaces";
	$db_pwd="P3rf3ct0.,";
	$db_name="tnasolut_enlaces";
	$con = mysql_connect($db_host, $db_user, $db_pwd);
	if (!$con) die("No se conecta a servidor");
	if (!mysql_select_db($db_name)) die("No selecciona base de datos");
	mysql_set_charset('utf8',$con);
	$sql = "SELECT	id,
			fecha_contrato,
			meses_contrato,
			meses_renovacion
			FROM tnasolut_enlaces.productos 
			order by id ASC;";
	$query = mysql_query($sql);
	while($row = mysql_fetch_row($query)) { 
/*		echo "Base datos: ";
			print_r($row);
		echo '<br>'; */
		$datos = array( "id"		 =>$row[0],
						"fcontrato"  =>$row[1],
					    "mcontrato"  =>$row[2],
            			"mrenovacion"=>"" );
/*		echo "Arra datos: ";
			print_r($datos);
		echo '<br>'; */
		$newdatos = fMesesContrato($datos);
/*		echo "fMes datos: ";
		print_r($newdatos);
		echo '<br><BR>'; */
		$sql = "update  tnasolut_enlaces.productos
			set fecha_contrato = '".$newdatos['fcontrato']."',
			meses_contrato     = '".$newdatos['mcontrato']."',
	 		meses_renovacion   = '".$newdatos['mrenovacion']."'
			where id=".$newdatos['id'].";";	
//		echo "<br>";
		mysql_query($sql) or die(mysql_error());
	}
	$sql = "select c.nombre,
			   p.rut,
			   p.proveedor,
			   p.producto,
			   p.costo_UF,
			   p.valor_UF,
			   p.fecha_contrato,
			   p.meses_contrato,
			   p.meses_renovacion
		   FROM  tnasolut_enlaces.productos p
		   INNER JOIN  tnasolut_enlaces.clientes c
		   ON p.rut = c.rut
		   where meses_renovacion != 0  
		   order by meses_renovacion ASC
		   limit 10";
	$query = mysql_query($sql);
	?>
	<table border="1" style="border: 1px solid silver" width="100%" cellpadding="5">
	<tr>
		<td colspan="9" align="center"><b>10 vencimientos más proximos</b></td>
	</tr>
	<tr style="border: 1px solid silver; background: orange; color: white ">
		<th>Meses renovación</th>
		<th>Nombre</th>
		<th>rut</th>
		<th>proveedor</th>
		<th>producto</th>
		<th>Costo UF</th>
		<th>Valor UF</th>
		<th>Fecha Contrato</th>
		<th>Meses Contrato</th>
	</tr>	
	<?php
	while($row = mysql_fetch_row($query)) { ?>
	<tr align="right">
		<td align="center"><?php echo $row[8]; ?></td>
		<td align="left"><?php echo $row[0]; ?></td>
		<td><?php echo $row[1]; ?></td>
		<td align="left"><?php echo $row[2]; ?></td>
		<td align="left"><?php echo $row[3]; ?></td>
		<td><?php echo $row[4]; ?></td>
		<td><?php echo $row[5]; ?></td>
		<td><?php echo $row[6]; ?></td>
		<td><?php echo $row[7]; ?></td>
	</tr>
	<?php }?>
	</table>
	<?php
	mysql_free_result($query);
	mysql_close($con);
echo "Meses de contrato actualizados correctamente.<br>";	
exit();


function fMesesContrato($datos){ //calcula meses de contrato
	$date_format = 'Y-m-d';
	$hoy = date($date_format);	
	$horafutura='00:00';
	$vacio = '';
	if (is_null($datos['fcontrato'])) {
		$datos['fcontrato']  = "";
		$datos['mrenovacion'] = "";	
	} else {
		if( $datos['fcontrato'] > "2001-1-1") {
		$d1    = DateTime::createFromFormat($date_format, $hoy);
		$d2    = DateTime::createFromFormat($date_format, $datos['fcontrato']);
		$diff  = $d1->diff($d2);
		$meses = ($diff->format('%y')*12) + $diff->format('%m');	
		$datos['mrenovacion'] =  $datos['mcontrato'] - $meses;		
		} else {
			$datos['fcontrato']  = "";
			$datos['mrenovacion'] = "";
		}
	}
	if ($datos['mcontrato'] < 1) {
		$datos['mcontrato'] = "";
		$datos['mrenovacion'] = "";
	}
	return $datos;
}


?>
	</body>
</html>	

