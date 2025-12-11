<?php
	#es: Incluir el archivo de la libreria
	require_once('class/phpmydatagrid.class.php');

	#es: Crear el objeto contenedor
	$objGrid = new datagrid('productos_new.php','2');

	#es: Defnir la relacion con la base de datos maestra y obtener el valor del id por defecto para nuevos registros
	#en: Define the relation with the master nase and get the default id for new records
	$rut = $objGrid -> setMasterRelation("rut");

	#es: Definir la cantidad de registros a mostrar por pagina
	$objGrid-> datarows(50);
	$objGrid -> liquidTable = true;	
	$objGrid -> width = "100%";
	$objGrid -> ButtonWidth = '60';


	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_enlaces", "P3rf3ct0.,", "tnasolut_enlaces");

	#es:agregamos variables_generales.php
	require_once('variables_generales.php');


	#es: Especificar la tabla de trabajo
	$objGrid-> tabla ("productos");

	#es: Definir la cantidad de registros a mostrar por pagina
	#en: Define amount of records to display per page
	$objGrid-> datarows(100);

	#es: Definir acciones permitidas
//	$objGrid-> buttons(true,true,true,true,0);
	#es: Decirle al datagrid que va a usar el calendario (datepicker)

	#es: Definir campo llave
	#es: Definir campo(s) para búsquedas
	$objGrid-> searchby("estadoprod, dir_inst, codigo_servicio, proveedor, comentarios ");

	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	#en: Tell to phpMyDatagrid it must use the datepicker
	$objGrid-> useCalendar(true);

	#es: Definir campo llave
	#en: Define keyfield
	$objGrid-> keyfield ("id");

	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("estadoprod","asc");

	#es: Definir un título para el grid
	$objGrid-> tituloGrid("Detalle de Servicios por cliente");
	#es: Calcula próximo numero de factibilidad

	//$next_fact = fnext_fac();

//////////////////////////////////////////////////////////////////////////
	#es: Especificar los campos a mostrar con sus respectivas caracter?sticas:
	//$objGrid -> FormatColumn("id","ID", "12", "12", "1", "50", "right", "number");
	$objGrid -> FormatColumn("rut","RUT", "13", "13", 2, "90", "right", "text", $rut); 
	$objGrid -> FormatColumn("estadoprod", "Estado",  30, 30, 1, "80", "left", "select:En Instalacion:Activo:Suspendido:Sin Cliente:De Baja:En Traslado:Gasto","Activo");
	$objGrid -> FormatColumn("proveedor", "Proveedor",  30, 30, 1, "150", "left", "select:Telefonica Empresas:Telefonica Chile:Telefonica Mayorista:Claro:PIT Chile:TNA Phone:Silica:Bynarya:TNA Solutions:GTD Teleductos:GTD Manquehue:Entel:Cmet:ifx:infomagia:3CX:TNA Office:DIDWW","telefonica empresas");
	$objGrid -> FormatColumn("producto", "Producto",  25, 25, 1, "100", "center", "select:ADSL_ADSL:Enlace Dedicado:Punto a Punto:Router:X conect:MB internacional:Boca Switch:Plan Telefonia IP:Hosting:Soporte:Cloud:VPN:PACK IP:VPS:Colocate:DID Adicional:Equipo en Comodato:Equipo en Arriendo:MPLS:Canje:Gasto:SMS:Siptrunk:Licencia:Otros telefonia ip:Publicacion ASN", "Enlace Dedicado");
	$objGrid -> FormatColumn("clase_modelo", "Tipo / Clase / Modelo",  50, 50, 1, "100", "right", "text");
	$objGrid -> FormatColumn("costo_UF", "Costo UF",  50, 50, 2, "80", "right","money:UF:0");
	$objGrid -> FormatColumn("valor_UF", "Valor UF",  50, 50, 2, "80", "right","money:UF:0");
	$objGrid -> FormatColumn("costo_vigente_uf", "Costo Vigente",  50, 50, 1, "80", "right","money:UF:0");
	$objGrid -> FormatColumn("valor_vigente_uf", "Valor Vigente",  50, 50, 1, "80", "right","money:UF:0");
	$objGrid -> FormatColumn("Tipo_Pago", "Tipo de Pago",  20, 20, 1, "80", "right", "select:Mensual_Mensual:Anual_Anual:Bienal_Bienal","Mensual");
	$objGrid -> FormatColumn("vendedor", "Vendedor",  20, 20, 1, "80", "right", "select:DAM:MAM:MAO:romina","romina");
	$objGrid -> FormatColumn("fecha_inst_fac", "Fecha  FAC / Inst",  10, 10, 1, "80", "center", "date:dmyy:-");
	$objGrid -> FormatColumn("dir_inst", "Direccion Instalacion", 200, 150, 1,"300", "left");
	$objGrid -> FormatColumn("codigo_servicio", "Codigo de Servicio",  50, 250, 1, "80", "right", "text");
	$objGrid -> FormatColumn("n_pagador", "Número Pago",  50, 250, 1, "100", "right", "text");
	$objGrid -> FormatColumn("fecha_contrato", "Fecha Contrato",  10, 10, 1, "80", "center", "date:dmyy:-");
	$objGrid -> FormatColumn("meses_contrato", "Meses contrato",  50, 50, 1, "80", "right", "text");
	$objGrid -> FormatColumn("meses_renovacion", "Meses renovacion",  50, 50, 1, "80", "right", "text");
	$objGrid -> FormatColumn("ip", "IP",  50, 50, 2, "110", "right", "text");
	$objGrid -> FormatColumn("gw", "IP GateWay",  50, 50, 2, "110", "right", "text");
	$objGrid -> FormatColumn("ip_router", "IP Router",  50, 50, 2, "110", "right", "text");
	$objGrid -> FormatColumn("comentarios", "Comentarios",  200, 200, 1, "400", "left", "text");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='en instalacion'", "colorrojo");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='activo'", "colorverde");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='sin cliente'", "colorrojo");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='de baja'", "colorgris");
	$objGrid -> addRowStyle ("['estadoprod']=='de baja'", "sinfactibilidad");
	//////////////////////////////////////////////////////////////////////////
	$objGrid -> total('costo_vigente_uf, valor_vigente_uf');
	#es:agregamos variables_generales.php
	require_once('variables_generales.php');
	$objGrid->processData = "fMeses";
	$objGrid->processData = "valores_actuales";
	#es: Por ultimo, renderizar el Grid
	$objGrid-> grid();

  function fecha_meses($f, $val){ // suma meses a una fecha
        $fecha= strtotime($val.' month',strtotime($f));
        $fecha= date('Y-m-d',$fecha);
        return $fecha;
    }



function fMeses($arrData=array()){ //calcula meses de contrato
	$date_format = 'Y-m-d';
	$hoy = date($date_format);	
	$horafutura='00:00';
	foreach($arrData as $key=>$row){
		$fecha  = $row['fecha_contrato'];
		if ($fecha > "2001-1-1") {
			$d1    = DateTime::createFromFormat($date_format, $hoy);
			$d2    = DateTime::createFromFormat($date_format, $fecha);
			$diff  = $d1->diff($d2);
			$meses = ($diff->format('%y')*12) + $diff->format('%m');	
			$row['meses_renovacion'] =  $row['meses_contrato'] - $meses;		
		} 
		$arrTmpData[$key] = $row;
	}
	#es: Siempre se debe retornar un array con la nueva informacion
	return $arrTmpData;

}

	function fnext_fac() { // recupera la última fac utilizada y le suma 1
		// Crea conexion a mlysq
		$conn = new mysqli('localhost', 'tnasolut_enlaces', 'P3rf3ct0.,', 'tnasolut_enlaces');
		// Checkea conexion
		if ($conn->connect_error) die("Error de Conexion: " . $conn->connect_error);
		mysqli_set_charset($conn, 'utf8');
		$sql = "SELECT `fac` FROM `productos` order by `fac`DESC LIMIT 1";
		$result = mysqli_query($conn, $sql);
		if ($result->num_rows > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$fnext_fac = $row['fac'];
			}
		}
		mysqli_free_result($result);
		$conn->close();	
		return ($fnext_fac + 1);
	}

function valores_actuales($arrData=array()){

	foreach($arrData as $key=>$row){		
		if($row['estadoprod'] == "activo") {
			$row['costo_vigente_uf'] =  $row['costo_UF'];			
			$row['valor_vigente_uf'] =  $row['valor_UF'];			
		} else {
			$row['costo_vigente_uf'] =  0;			
			$row['valor_vigente_uf'] =  0;			
		}
		
		$datos = array( "id"		 =>$row['id'],
						"fcontrato"  =>$row['fecha_contrato'],
					    "mcontrato"  =>$row['meses_contrato'],
            			"mrenovacion"=>"" );
		
		$newdatos = fMesesContrato($datos);
		$row['fecha_contrato'] = $newdatos['fcontrato'];
		$row['meses_contrato'] = $newdatos['mcontrato'];
		$row['meses_renovacion'] = $newdatos['mrenovacion'];
		$arrTmpData[$key] = $row;
		$query = "UPDATE `productos` 
				 SET `costo_vigente_uf` = '{$row['costo_vigente_uf']}',
				     `valor_vigente_uf` = '{$row['valor_vigente_uf']}',	
					     fecha_contrato = '{$row['fecha_contrato']}',
			             meses_contrato = '{$row['meses_contrato']}',
			           meses_renovacion = '{$row['meses_renovacion']}'
					 WHERE `id` = {$row['id']}";		
		$tmp = actualiza_datos($query );
	}
	#es: Siempre se debe retornar un array con la nueva informacion
	return $arrTmpData;
}

function actualiza_datos($sql) {
   $dbhost = 'localhost';
   $dbuser = 'tnasolut_enlaces';
   $dbpass = 'P3rf3ct0.,';
   $conn = new mysqli('localhost', 'tnasolut_enlaces', 'P3rf3ct0.,', 'tnasolut_enlaces');	
  // $conn = mysql_connect($dbhost, $dbuser, $dbpass);
	if (mysqli_connect_errno()) { die('Error de conexión: ' . $mysqli->connect_error); }   mysqli_set_charset($conn, 'utf8');
   //if(! $conn ) { die('Could not connect: ' . mysql_error()); }
   //mysql_select_db('tnasolut_enlaces');
   $retval = mysqli_query($conn, $sql);
  // $retval = mysql_query( $sql, $conn );
   if(! $retval ) { die('Error al actualizar Query: '.$sql."error Nº" . mysqli_error($conn));}
 
  // if(! $retval ) { die('Could not update data: ' . mysql_error());}
   //echo "Updated data successfully\n";
  // mysql_close($conn);
	//mysqli_free_result($retval);
	mysqli_close($conn);
	
}

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



