<?php
	error_reporting(E_ALL);
	#es: Incluir el archivo de la libreria
	require_once('class/phpmydatagrid.class.php');
	#es: Crear el objeto contenedor
	$objGrid = new datagrid('productos.php','2');
	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_enlaces", "P3rf3ct0.,", "tnasolut_enlaces");
	#es: Especificar la tabla de trabajo
	$objGrid-> tabla ("productos");
	#es: Definir la cantidad de registros a mostrar por pagina
	$objGrid-> datarows(50);
	#es: Permitir Edición AJAX online
	$objGrid-> ajax('silent');
	#es: Definir acciones permitidas
	$objGrid-> buttons(true,true,true,true,0);
	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	$objGrid-> useCalendar(true);
	#es: Definir campo llave
	$objGrid-> keyfield ("id");
	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("producto");
	#es: Definir campo(s) para búsquedas
	$objGrid-> searchby("estado, dir_inst, codigo_servicio, proveedor, comentarios ");

	#es: Capturar el rut del cliente
	$rut = (isset($_GET['rut'])?$_GET['rut']:(isset($_POST['rut'])?$_POST['rut']:''));
	$rut = (isset($_GET['e_id'])?$_GET['e_id']:(isset($_POST['e_id'])?$_POST['e_id']:$rut));
	#es: Buscar los datos del enlace
	if (!empty($rut)){
	 	$strSQL = "SELECT * FROM clientes where rut=" . magic_quote($rut);
		$arrData = $objGrid->SQL_query($strSQL);
		$titulo = "Productos del RUT: # " .$arrData[0]['rut'];
	}else{
		$titulo = "No se ha seleccionado Detalle en la tabla superior.";
	}
	#es: Definir un título para el grid
	$objGrid-> tituloGrid($titulo);
	//$filtro="";
	//$sql = "SELECT p.*, c.nombre FROM productos AS p INNER JOIN clientes AS c ON p.rut=c.rut". $filtro;
	//$objGrid-> sqlstatement ("SELECT p.*, c.nombre FROM productos AS p INNER JOIN clientes AS c ON p.rut=c.rut");
	#es: Calcula próximo numero de factibilidad
	$next_fact = fnext_fac();
	$objGrid -> total('valor_UF,costo_UF');


//////////////////////////////////////////////////////////////////////////
	#es: Especificar los campos a mostrar con sus respectivas caracter?sticas:
	$objGrid-> FormatColumn("id","ID", "12", "12", "2", "80", "right", "number");
	$objGrid-> FormatColumn("fac", "# FAC", 6, 6, 0, "20", "center", "number",$next_fact);
	$objGrid-> FormatColumn("rut","RUT", "13", "13", "0", "80", "right", "text", $rut); // Definir ID con valor de tabla maestra predeterminado
	//$objGrid -> FormatColumn("nombre", "Nombre Completo", 150, 150, 0, "250", "left");
	$objGrid -> FormatColumn("estadoprod", "Estado",  30, 30, 0, "80", "left", "select:En Instalacion:Activo:Suspendido:Sin Cliente:De Baja:En Traslado:Gasto","Activo");
	$objGrid -> FormatColumn("proveedor", "Proveedor",  30, 30, 0, "150", "left", "select:Telefonica Empresas:Telefonica Chile:Telefonica Mayorista:Claro:PIT Chile:TNA Phone:Silica:Bynarya:TNA Solutions:GTD Teleductos:GTD Manquehue:Entel:Cmet:ifx:infomagia:3CX:TNA Office:DIDWW","telefonica empresas");
	$objGrid -> FormatColumn("producto", "Producto",  25, 25, 0, "100", "center", "select:ADSL_ADSL:Enlace Dedicado:Punto a Punto:Router:CCTV:X conect:MB internacional:Boca Switch:Plan Telefonia IP:Hosting:Soporte:Cloud:VPN:PACK IP:VPS:Colocate:DID Adicional:Equipo en Comodato:Equipo en Arriendo:MPLS:Reseller:Canje:Gasto:SMS:Siptrunk:Licencia:Otros telefonia ip:Publicacion ASN", "Enlace Dedicado");
	$objGrid -> FormatColumn("clase_modelo", "Tipo / Clase / Modelo",  50, 50, 0, "100", "right", "text");
	$objGrid -> FormatColumn("costo_UF", "Costo UF",  50, 50, 0, "80", "right","money:UF:0");
	$objGrid -> FormatColumn("valor_UF", "Valor UF",  50, 50, 0, "80", "right","money:UF:0");
	//$objGrid -> FormatColumn("Tipo_Pago", "Tipo de Pago",  20, 20, 0, "80", "right", "text");
	$objGrid -> FormatColumn("Tipo_Pago", "Tipo de Pago",  20, 20, 0, "80", "right", "select:Mensual_Mensual:Anual_Anual:Bienal_Bienal","Mensual");
	$objGrid -> FormatColumn("vendedor", "Vendedor",  20, 20, 0, "80", "right", "select:DAM:MAM:MAO:romina","romina");
	//$objGrid -> FormatColumn("fecha_factura", "Fecha facturacion",  10, 10, 0, "80", "center", "date:dmyy:-");
	$objGrid -> FormatColumn("fecha_inst_fac", "Fecha  FAC / Inst",  10, 10, 0, "80", "center", "date:dmyy:-");
	$objGrid -> FormatColumn("dir_inst", "Direccion Instalacion", 200, 150, 0,"300", "left");
	$objGrid -> FormatColumn("codigo_servicio", "Codigo de Servicio",  50, 250, 0, "80", "right", "text");
	$objGrid -> FormatColumn("n_pagador", "Número Pago",  50, 250, 0, "100", "right", "text");
	$objGrid -> FormatColumn("fecha_contrato", "Fecha Contrato",  10, 10, 0, "80", "center", "date:dmyy:-");
	$objGrid -> FormatColumn("meses_contrato", "Meses contrato",  50, 50, 0, "80", "right", "text");
	$objGrid -> FormatColumn("meses_renovacion", "Meses renovacion",  50, 50, 0, "80", "right", "text");
	$objGrid -> FormatColumn("ip", "IP",  50, 50, 0, "110", "right", "text");
	$objGrid -> FormatColumn("gw", "IP GateWay",  50, 50, 0, "110", "right", "text");
	$objGrid -> FormatColumn("ip_router", "IP Router",  50, 50, 0, "110", "right", "text");
	#$objGrid -> FormatColumn("modelo_router", "Modelo Router",  50, 50, 0, "100", "right", "text");
	$objGrid -> FormatColumn("comentarios", "Comentarios",  200, 200, 0, "400", "left", "text");
	#es: Definir la condicion o condiciones y la clase CSS a usar si esta condición se cumple
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='en instalacion'", "colorrojo");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='activo'", "colorverde");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='sin cliente'", "colorrojo");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='de baja'", "colorgris");
	$objGrid -> addRowStyle ("['estadoprod']=='de baja'", "sinfactibilidad");
	//////////////////////////////////////////////////////////////////////////
	#es: Crear la instruccion where
	$objGrid-> where("rut = '{$rut}'");
	#es: Pasar los parametros entre paginas
	$objGrid-> linkparam("rut=".$rut);

	#es:agregamos variables_generales.php
	require_once('variables_generales.php');
	$objGrid->processData = "fMeses";
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




?>



