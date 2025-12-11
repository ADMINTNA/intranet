<?php
	error_reporting(0);
	#es: Incluir el archivo de la libreria
	require_once('class/phpmydatagrid.class.php');
	#es: Crear el objeto contenedor
	$objGrid = new datagrid('clientes.php','1');
	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_enlaces", "P3rf3ct0.,", "tnasolut_enlaces");
	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	$objGrid-> tabla ("clientes");
	#es: titulo
	$objGrid-> tituloGrid("Mantenedor de Clientes");
	#es: Definir campo(s) para búsquedas
	$objGrid-> searchby("nombre, rut, estado, direccion, comuna, ciudad, fono, celular, email, contacto_nombre, contacto_email");
	#es: Definir campo llave
	$objGrid-> keyfield ("rut");
	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("nombre","asc");
	#es: Definir la cantidad de registros a mostrar por pagina
	$objGrid-> datarows(1000);
	#es: Adicionar un boton a la Barra de botones
	$objGrid-> addButton("images/productos.jpg", "productos()", "Productos");
	#es: Adicionar un separador a los botones
	$objGrid-> addSeparator();
	#es: Adicionar un boton a la Barra de botones
//	$objGrid-> addButton("images/productos.jpg", "productos()", "Productos");
	#es: Adicionar un separador a los botones
//	$objGrid-> addSeparator();
	#es: Adicionar un boton a la Barra de botones
	$objGrid-> addButton("images/tools.png", "meses_contrato()", "Meses Contrato");
	#es: Adicionar un separador a los botones
	$objGrid-> addSeparator();

	// Inicialmente le decimos que queremos que la exportación HTML interactue con otro archivo y a la vez definimos el archivo que procesara la salida de datos.
//	$objGrid->exportMagma = "_magma_exporta.php";
	// Definimos los datos de cada una de las opciones de detalle a imprimir
	// los sigguientes parametros:
	// sql: este key contiene el SQL necesario para mostrar la salida detalle,
	// parameters: la lista de parametros usados anteriormente de la tabla maestra, separados por coma
	// menu: Define el valor que mostrará en el menu desplegable al usuario
	$objGrid->exportDetails['producto'] = array("sql"=>"SELECT * FROM `productos` WHERE rut = '['rut']' ORDER BY `producto` ASC",
										"parameters"=>"rut",
										"menu"=>"producto");
	/* Por ultimo realizamos un pequeño Hack para lograr que al momento de exportar el grid no nos genere código, en cambio nos devuelva los datos en arrays */
    if (isset($retCode)) $objGrid->retcode = $retCode;
	#es: Al tener activa la barra de botones (toolbar) hacer que el cuadro de exportacion sea desplegado en la barra
	$objGrid-> strExportInline = true;
//////////////////////////////////////////////////////////////////////////
	#es: Especificar los campos a mostrar con sus respectivas caracter?sticas:
	//$objGrid-> FormatColumn("id","ID", "12", "12", "0", "80", "right", "number");
	$objGrid-> FormatColumn("dummy_field_1","Detalle", "5", "30", 4, "40", "center", "imagelink:images/selected_rows.gif:viewDetails(%s),rut");
	$objGrid -> FormatColumn("rut", "R.U.T.", 13, 13, 0, "80", "right");
	$objGrid -> FormatColumn("estado", "Estado",  30, 30, 0, "80", "left", "select:Activo_Activo:De Baja_De Baja:Suspendido_suspendido ","1");
	$objGrid -> FormatColumn("nombre", "Nombre Completo", 150, 150, 0, "250", "left");
	$objGrid -> FormatColumn("direccion", "Direccion", 150, 150, 0,"300", "left");
	$objGrid -> FormatColumn("comuna", "Comuna", 50, 50, 0,"120", "right");
	$objGrid -> FormatColumn("fono", "Fono", 20, 20, 0,"100", "right");
	$objGrid -> FormatColumn("celular", "Otro Fono", 20, 20, 0,"100", "right");
	$objGrid -> FormatColumn("email", "eMail", 50, 50, 0,"100", "right");
	$objGrid -> FormatColumn("contacto_nombre", "Contacto Nombre", 150, 150, 0, "150", "right");
	$objGrid -> FormatColumn("contacto_cargo", "Contacto Cargo", 50, 50, 0,"180", "right");
	$objGrid -> FormatColumn("contacto_email", "Contacto eMail", 50, 50, 0,"150", "right");
	$objGrid -> FormatColumn("contacto_fono", "Contacto Fono", 20, 20, 0,"100", "right");
	$objGrid -> FormatColumn("contacto_celular", "Contacto Celular", 20, 20, 0,"120", "left");
	$objGrid -> FormatColumn("portal", "Portal Factura", 150, 150, 0, "250", "left");
	
	#es: Definir la condicion o condiciones y la clase CSS a usar si esta condición se cumple
	$objGrid -> addCellStyle ("estado", "['estado']=='activo'", "colorverde");
	$objGrid -> addCellStyle ("estado", "['estado']=='de baja'", "colorgris");
	$objGrid -> addCellStyle ("estado", "['estado']=='suspendido'", "colorrojo");
	$objGrid -> addRowStyle ("['estado']=='de baja'", "sinfactibilidad");
/////////////////////////////////////////////////////////////////////////
	#es:agregamos variables_generales.php
	require_once('variables_generales.php');
	#es: Por ultimo, renderizar el Grid
	$objGrid-> grid();
?>
