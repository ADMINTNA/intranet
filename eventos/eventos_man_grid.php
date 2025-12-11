<?php
#es: Incluir el archivo de la libreria
	require_once('class/phpmydatagrid.class.php');
	#es: Crear el objeto contenedor
	$objGrid = new datagrid('eventos_man_grid.php','1');
	#es: Realizar la conexión con la base de datos
	$objGrid-> conectadb("127.0.0.1", "tnasolut_data_studio", "P3rf3ct0.,", "tnasolut_eventos");

	$objGrid -> friendlyHTML();

//	$objGrid -> liquidTable = true;	
	$objGrid -> width = "100%";
	$objGrid -> ButtonWidth = '40';

	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	$objGrid-> tabla ("eventos");
	
	#es: titulo
	$objGrid-> tituloGrid("Mantenedor de Eventos");

	#es: Definir campo(s) para búsquedas
	$objGrid-> searchby ("id_servicio, desc_evento, fecha_ini");
	
	#es: Definir campo llave
	$objGrid-> keyfield ("id_evento");
	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("id_evento","desc");
	#es: Definir la cantidad de registros a mostrar por pagina
	$objGrid-> datarows(1000);
	#es: Adicionar un boton a la Barra de botones
	$objGrid-> addButton("images/calendar.gif", "eventos()", "Eventos");
	#es: Adicionar un separador a los botones
	$objGrid-> addSeparator();

	$objGrid-> strExportInline = true;
//////////////////////////////////////////////////////////////////////////
	#es: Especificar los campos a mostrar con sus respectivas características:
	#en: Specify each field to display with their own properties
	$objGrid-> FormatColumn("id_evento","ID", "40", "50", "1", "30", "left", "text");
	$objGrid-> FormatColumn("fecha_ini","Fecha Inicio", "5", "19", "0", "195", "left", "datetime:dmy:-:His,:");
	$objGrid-> FormatColumn("fecha_fin","Fecha Fin", "5", "19", "0", "195", "left", "datetime:dmy:-:His,:");
   // $objGrid -> FormatColumn("servicio","Servicios",  40, 30, 0, "200", "left", "select:telefonia:enlaces nacional:enlace internacional:hosting y/o cpanel:correos:vps:dns:collocate:Todos los Servicios:POP GTD:POP IFX","enlaces");

	$objGrid-> FormatColumn("servicio","Servicios", "5", "0", "0", "150", "center", "select:telefonia:enlace nacional:enlace internacional:hosting y/o cpanel:correos:vps:dns:collocate:Todos los Servicios:POP GTD:POP IFX","enlaces");
	$objGrid-> FormatColumn("desc_evento","Descripcion", "5", "100", 0, "400", "left");
/////////////////////////////////////////////////////////////////////////
	#es:agregamos variables_generales.php
	require_once('variables_generales.php');
	#es: Por ultimo, renderizar el Grid
	$objGrid-> grid();
?>

