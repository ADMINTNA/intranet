<?php

//Rutinas para actualizar los días de vencimiento de legal_documentos.
// uso: $tmp = dias_vencimiento(); // actualiza dias_vencimiento de la tabla legal_documentos

function dias_vencimiento(){ //calcula y actualiza dias de vencimiento
	$datos  =  array();
	$dbhost = 'localhost';
	$dbuser = 'tnasolut_legal';
	$dbpass = '1Ngr3s0.,';
	$dbbase = 'tnasolut_legal';
	$conn   = mysqli_connect($dbhost, $dbuser, $dbpass, $dbbase);
	if(! $conn ) { die('Could not connect: ' . mysql_error()); }
	mysqli_set_charset($con, 'utf8');
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
    mysql_close($conn);
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
	mysqli_set_charset($con, 'utf8');
	if (mysqli_query($conn, $sql)) {
		//echo "Record updated successfully";
	} else {
		die('Could not update data: ' . mysql_error());	
	}
   mysql_close($conn);
}





?>