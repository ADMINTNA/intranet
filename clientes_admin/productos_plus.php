<?php
	error_reporting(0);
	#es: Incluir el archivo de la libreria
	require_once('class/phpmydatagrid.class.php');
	#es: Crear el objeto contenedor
	$objGrid = new datagrid('productos_plus.php','1');
	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_enlaces", "P3rf3ct0.,", "tnasolut_enlaces");
	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	$objGrid-> tabla ("productos");
	#es: titulo
	$objGrid-> tituloGrid("Mantenedor de productos");
	#es: Definir campo(s) para búsquedas
	$objGrid-> searchby("id, nombre, dir_inst, estadoprod, proveedor, producto, clase_modelo, valor, tasa, codigo_servicio, n_pagador ");
	#es: Definir campo llave
	$objGrid-> keyfield ("rut");
	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("nombre","asc");
	#es: Definir la cantidad de registros a mostrar por pagina
	$objGrid-> datarows(1000);
	#es: Adicionar un boton a la Barra de botones
	$objGrid-> addButton("images/user.png", "clientes()", "Clientes");
	#es: Adicionar un separador a los botones
	$objGrid-> addSeparator();
	#es: Adicionar un boton a la Barra de botones
	/* Por ultimo realizamos un pequeño Hack para lograr que al momento de exportar el grid no nos genere código, en cambio nos devuelva los datos en arrays */
    if (isset($retCode)) $objGrid->retcode = $retCode;
	#es: Preparar el SQL personalizado, teniendo en cuenta de no incluir comandos como Where, Order o group, 
	#    ya que estos deben ser definidos desde su propio metodo.
	#en: Prepare the custom SQL query, having in mind to not to include statements like Where, Order or group, 
	#    because those statement must be included by their respective methods
    if (!empty($id_call)) $filtro = " WHERE p.id = $id_call"; else $filtro = "";
//echo "filtro = $filtro<br>";
	$sql = "SELECT * FROM productos AS p INNER JOIN clientes AS c ON p.rut=c.rut". $filtro;
	$objGrid-> sqlstatement ($sql);
	#es: Calcula próximo numero de factibilidad
	$next_fact = fnext_fac();
	
//////////////////////////////////////////////////////////////////////////
	#es: Especificar los campos a mostrar con sus respectivas caracter?sticas:
	$objGrid-> FormatColumn("id","ID", "12", "12", "0", "80", "right", "number");
	$objGrid -> FormatColumn("fac", "# FAC", 6, 6, 0, "20", "center", "number",$next_fact);
	$objGrid-> FormatColumn("rut","RUT", "13", "13", "0", "80", "right", "text");
	$objGrid -> FormatColumn("nombre", "Nombre Completo", 150, 150, 0, "250", "left");
	$objGrid -> FormatColumn("estadoprod", "Estado",  30, 30, 0, "80", "left", "select:En Instalacion_En Instalacion:Activo_Activo:Sin Cliente_Sin Cliente:De Baja_De Baja:En Traslado_En Traslado:Gasto_Gasto","Activo");
	$objGrid -> FormatColumn("proveedor", "Proveedor",  30, 30, 0, "150", "left", "select:Telefonica Empresas:Telefonica Chile:Claro:PIT Chile:Bynarya:TNA Solutions:GTD Teleductos:GTD Manquehue:Entel:Cmet","telefonica empresas");
	$objGrid -> FormatColumn("producto", "Producto",  25, 25, 0, "100", "center", "select:ADSL:Enlace Dedicado:Punto a Punto:Router:CCTV:Telefonia IP:Hosting:Soporte:Cloud:VPN:PACK IP:VPS:Colocate:DID Adicional:Equipo en Comodato:Equipo en Arriendo:MPLS:Reseller:Canje:Gasto:SMS:IPTV", "Enlace Dedicado");
	$objGrid -> FormatColumn("clase_modelo", "Tipo / Clase / Modelo",  50, 50, 0, "100", "right", "text");
	$objGrid -> FormatColumn("costo_UF", "Costo UF",  50, 50, 0, "100", "right");
	$objGrid -> FormatColumn("valor_UF", "Valor UF",  50, 50, 0, "100", "right");
//	$objGrid -> FormatColumn("Tipo_Pago", "Tipo de Pago",  20, 20, 0, "80", "right", "text");
	$objGrid -> FormatColumn("Tipo_Pago", "Tipo de Pago",  20, 20, 0, "80", "right", "select:Mensual_Mensual:Anual_Anual:Bienal_Bienal","Mensual");
	$objGrid -> FormatColumn("vendedor", "Vendedor",  20, 20, 0, "80", "right", "select:DAM:MAM:MAO:romina","romina");
	$objGrid -> FormatColumn("fecha_contrato", "Fecha Contrato",  10, 10, 0, "80", "center", "date:dmyy:-");
	//$objGrid -> FormatColumn("fecha_aprobacion", "Fecha aprobacion",  10, 10, 0, "80", "center", "date:dmyy:-");
	$objGrid -> FormatColumn("fecha_instalacion", "Fecha Instalacion",  10, 10, 0, "80", "center", "date:dmyy:-");
	$objGrid -> FormatColumn("dir_inst", "Direccion Instalacion", 200, 150, 0,"300", "left");
	$objGrid -> FormatColumn("codigo_servicio", "Codigo de Servicio",  50, 250, 0, "100", "right", "text");
	$objGrid -> FormatColumn("n_pagador", "Número Pagador",  50, 250, 0, "100", "right", "text");
	$objGrid -> FormatColumn("ip", "IP",  50, 50, 0, "110", "right", "text");
	$objGrid -> FormatColumn("gw", "IP GateWay",  50, 50, 0, "110", "right", "text");
	$objGrid -> FormatColumn("ip_router", "IP Router",  50, 50, 0, "110", "right", "text");
	$objGrid -> FormatColumn("modelo_router", "Modelo Router",  50, 50, 0, "100", "right", "text");
	$objGrid -> FormatColumn("comentarios", "Comentarios",  200, 200, 0, "400", "left", "text");
	$objGrid -> FormatColumn("direccion", "Direccion", 200, 150, 0,"300", "left");
	$objGrid -> FormatColumn("comuna", "Comuna", 50, 50, 0,"120", "right");
	$objGrid -> FormatColumn("fono", "Fono", 20, 20, 0,"100", "right");
	$objGrid -> FormatColumn("celular", "Otro Fono", 20, 20, 0,"100", "right");
	$objGrid -> FormatColumn("email", "eMail", 50, 50, 0,"100", "right");
	$objGrid -> FormatColumn("contacto_nombre", "Contacto Nombre", 150, 150, 0, "150", "right");
	$objGrid -> FormatColumn("contacto_cargo", "Contacto Cargo", 50, 50, 0,"180", "right");
	$objGrid -> FormatColumn("contacto_email", "Contacto eMail", 50, 50, 0,"150", "right");
	$objGrid -> FormatColumn("contacto_fono", "Contacto Fono", 20, 20, 0,"100", "right");
	$objGrid -> FormatColumn("contacto_celular", "Contacto Celular", 20, 20, 0,"120", "left");
	#es: Definir la condicion o condiciones y la clase CSS a usar si esta condición se cumple
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='en instalacion'", "colorrojo");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='activo'", "colorverde");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='sin cliente'", "colorrojo");
	$objGrid -> addCellStyle ("estadoprod", "['estadoprod']=='de baja'", "colorgris");
	$objGrid -> FormatColumn("portal", "Portal Factura", 150, 150, 0, "250", "left");

	$objGrid -> addRowStyle ("['estadoprod']=='de baja'", "sinfactibilidad");
/////////////////////////////////////////////////////////////////////////
	#es:agregamos variables_generales.php
	require_once('variables_generales.php');
	#es: Por ultimo, renderizar el Grid
	$objGrid-> grid();

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
