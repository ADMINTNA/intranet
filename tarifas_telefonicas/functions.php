<?php
// Funciones de uso general para 

function totales($id){ //calcula totales
	$datos  =  array();
	$dbhost = 'localhost';
	$dbuser = 'tnasolut_legal';
	$dbpass = '1Ngr3s0.,';
	$dbbase = 'tnasolut_legal';
	$conn   = mysqli_connect($dbhost, $dbuser, $dbpass, $dbbase);
	if (mysqli_connect_errno()) { die("Sin Conección con DB tnasolut_legal en MySQL: " . mysqli_connect_error()); }	
	mysqli_set_charset($conn, 'utf8');
	$sql = "SELECT SUM(`monto`) AS enproceso FROM legal_documentos where (`estado` = 'publicado(dicom)' or `estado`= 'extrajudicial') && (id_cli = ".$id.")";
		$arraytmp = mysqli_query($conn, $sql);
		//$arraytmp = mysql_query( $sql, $conn );
		if(! $arraytmp ) { die('No obtengo datos de acumulador enproceso: ' . mysql_error());}
		//$fila = mysql_fetch_row($arraytmp);	
		$fila = mysqli_fetch_row($arraytmp);	
		$datos['enproceso'] = $fila[0];
	$sql = "SELECT SUM(`monto`) AS recaudado FROM legal_documentos where `estado` = 'recaudado' && id_cli = ".$id;
		$arraytmp = mysql_query( $sql, $conn );
		if(! $arraytmp ) { die('No obtengo datos de acumulador recaudado: ' . mysql_error());}		
		$fila = mysqli_fetch_row($arraytmp);	
		$datos['recaudado'] = $fila[0];
	$sql = "SELECT SUM(`monto`) AS judicial FROM legal_documentos where `estado` = 'judicial' && id_cli = ".$id;
		$arraytmp = mysql_query( $sql, $conn );
	   if(! $arraytmp ) { die('No obtengo datos de acumulador judicial: ' . mysql_error());}
		$fila = mysqli_fetch_row($arraytmp);	
		$datos['judicial'] = $fila[0];
	//echo "Updated data successfully\n";
	mysqli_free_result($arraytmp);
	mysqli_close($conn);
	#es: Siempre se debe retornar un array con la nueva informacion
	return $datos;
}
	
function dias_vencimiento(){ //calcula y actualiza dias de vencimiento
	$datos  =  array();
	$dbhost = 'localhost';
	$dbuser = 'tnasolut_legal';
	$dbpass = '1Ngr3s0.,';
	$dbbase = 'tnasolut_legal';
	$conn   = mysqli_connect($dbhost, $dbuser, $dbpass, $dbbase);
	if (mysqli_connect_errno()) { die("Sin Conección con DB tnasolut_legal en MySQL: " . mysqli_connect_error()); }	
	mysqli_set_charset($conn, 'utf8');
	$sql = "SELECT id_doc, fecha_vencimiento FROM legal_documentos where `estado` != 'judicial cerrado' ";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$hoy = fhoy_str(); 
		while($fila = mysqli_fetch_assoc($result)){
			$dias = DiasEntreFechas($fila['fecha_vencimiento'], $hoy);
			$sql = "UPDATE `legal_documentos` SET `dias_vencimiento` = '".$dias."' WHERE `id_doc` = ". $fila['id_doc'];
			$tmp = actualiza_datos($sql);			
		}   
	} else {
		die("Error: No hay datos en la tabla seleccionada");
	}   
   mysqli_close($conn);
   return;
}

function fhoy_str() { // convierte hoy en str
	$date      = new DateTime(); //fecha hora actual
	$fecha_str = $date->format('d-m-Y');
	$tmp       = explode('-',$fecha_str);
	$fecha_str = implode("-",$tmp);
	return $fecha_str;	
} 

function DiasEntreFechas($inicio, $fin) { // calcula dias entre fechas
    $inicio   = strtotime($inicio);
    $fin      = strtotime($fin);
    $dif      = $fin - $inicio;
    $diasFalt = (( ( $dif / 60 ) / 60 ) / 24);
    return ceil($diasFalt);
}

function actualiza_datos($sql) {
	$datos  =  array();
	$dbhost = 'localhost';
	$dbuser = 'tnasolut_legal';
	$dbpass = '1Ngr3s0.,';
	$dbbase = 'tnasolut_legal';
	$conn   = mysqli_connect($dbhost, $dbuser, $dbpass, $dbbase);
	if(! $conn ) { die('Could not connect: ' . mysql_error()); }
	mysqli_set_charset($conn, 'utf8');
	if (mysqli_query($conn, $sql)) {
		//echo "Record updated successfully";
	} else {
		die('Could not update data: ' . mysql_error());	
	}
   mysqli_close($conn);
}


?>