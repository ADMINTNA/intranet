<?php
	#es: Incluir el archivo de la libreria
	require_once('class/phpmydatagrid.class.php');
	#es: Crear el objeto contenedor
	$objGrid = new datagrid('clientes_new.php','1');
	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_enlaces", "P3rf3ct0.,", "tnasolut_enlaces");
	#es: Después de definir el objeto, definir el enlace al grid de detalles
	#en: Just after define the object, then define the link to details grid
	$objGrid-> setDetailsGrid("productos_new.php", "rut");

	$objGrid -> friendlyHTML();

//	$objGrid -> liquidTable = true;	
//	$objGrid -> width = "100%";
	$objGrid -> ButtonWidth = '40';

	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	$objGrid-> tabla ("clientes");
	#es: titulo
	$objGrid-> tituloGrid("Servicios por Cliente");
	#es: Definir campo(s) para búsquedas
	$objGrid-> searchby("nombre, rut, estado, direccion, comuna, ciudad, fono, celular, email, contacto_nombre, contacto_email");
	#es: Definir campo llave
	$objGrid-> keyfield ("rut");
	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("nombre","asc");
	#es: Definir la cantidad de registros a mostrar por pagina
	$objGrid-> datarows(1000);
	#es: Adicionar un boton a la Barra de botones
	//$objGrid-> addButton("images/productos.jpg", "productos()", "Productos");
	//#es: Adicionar un separador a los botones
	$objGrid-> addSeparator();
	#es: Adicionar un boton a la Barra de botones
//	$objGrid-> addButton("images/productos.jpg", "productos()", "Productos");
	#es: Adicionar un separador a los botones
//	$objGrid-> addSeparator();
	#es: Adicionar un boton a la Barra de botones
	//$objGrid-> addButton("images/tools.png", "meses_contrato()", "Meses Contrato");
	#es: Adicionar un separador a los botones
	$objGrid-> addSeparator();

	$objGrid-> strExportInline = false;
//////////////////////////////////////////////////////////////////////////
	#es: Especificar los campos a mostrar con sus respectivas caracter?sticas:
	//$objGrid-> FormatColumn("id","ID", "12", "12", "2", "80", "right", "number");
    $objGrid-> FormatColumn("homologado","Check", "13", "1", 0, "60", "center", "check:No:Si");
	$objGrid -> FormatColumn("rut", "R.U.T.", 13, 13, 1, "80", "right"," ");
	$objGrid -> FormatColumn("estado", "Estado",  30, 30, 1, "80", "left", "select:Activo:De Baja:Suspendido:Extra Judicial","Activo");
    $objGrid -> FormatColumn("nombre", "Nombre Completo", 150, 150, 1, "250", "left"," ");
	$objGrid -> FormatColumn("ejecutivo", "Ejecutiv@",  30, 30, 1, "150", "left", "select:DAM:MAM:MAO:Romina:Angel","DAM");
    $objGrid -> FormatColumn("direccion", "Direccion", 150, 150, 1,"300", "left"," ");
	$objGrid -> FormatColumn("comuna", "Comuna", 50, 50,1,"120", "right"," ");
	$objGrid -> FormatColumn("ciudad", "Ciudad", 50, 50, 2,"120", "right"," ");
	$objGrid -> FormatColumn("fono", "Fono", 20, 20, 1,"100", "right","+");
	$objGrid -> FormatColumn("celular", "Otro Fono", 20, 20, 1,"100", "right","+");
	$objGrid -> FormatColumn("email", "eMail", 50, 50, 1,"100", "right");
	$objGrid -> FormatColumn("contacto_nombre", "Contacto Nombre", 150, 150, 1, "150", "right");
	$objGrid -> FormatColumn("contacto_cargo", "Contacto Cargo", 50, 50, 1,"180", "right");
	$objGrid -> FormatColumn("contacto_email", "Contacto eMail", 50, 50, 1,"150", "right");
	$objGrid -> FormatColumn("contacto_fono", "Contacto Fono", 20, 20, 1,"100", "right");
	$objGrid -> FormatColumn("contacto_celular", "Contacto Celular", 20, 20, 1,"120", "left");
	$objGrid -> FormatColumn("portal", "Portal Factura", 150, 150, 0, "250", "left", "link:");
	
	#es: Definir la condicion o condiciones y la clase CSS a usar si esta condición se cumple
    $objGrid -> addCellStyle ("homologado", "['homologado']=='1'", "colorverde");	
    $objGrid -> addCellStyle ("homologado", "['homologado']=='0'", "colorrojo");	
	$objGrid -> addCellStyle ("estado", "['estado']=='activo'", "colorverde");	
	$objGrid -> addCellStyle ("estado", "['estado']=='de baja'", "colorgris");
	$objGrid -> addCellStyle ("estado", "['estado']=='extra judicial'", "colorazul");	
	$objGrid -> addCellStyle ("estado", "['estado']=='extra judicial'", "bold");
    $objGrid -> addCellStyle ("estado", "['estado']=='suspendido'", "colorrojo");
	$objGrid -> addRowStyle ("['estado']=='de baja'", "sinfactibilidad");
/////////////////////////////////////////////////////////////////////////
	#es:agregamos variables_generales.php
	require_once('variables_generales.php');
	#es: Por ultimo, renderizar el Grid
	$objGrid-> grid();
?>
