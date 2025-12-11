<?php
//  error_reporting(E_ALL);
//  ini_set('display_errors', '1');
	#es: Incluir el archivo de la libreria
	require_once('class/phpmydatagrid.class.php');
	#es: Crear el objeto contenedor
	$objGrid = new datagrid('bitacora_grid.php','1');
	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_data_studio", "P3rf3ct0.,", "tnasolut_bitacora");
	$objGrid -> friendlyHTML();
	$objGrid -> ButtonWidth = '30';
	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	$objGrid-> tabla ("bitacora");
	#es: titulo
	$objGrid-> tituloGrid("Bitacora iConTel");
	#es: Definir campo(s) para búsquedas
	$objGrid-> searchby("usuario, recurso, descripcion");
	#es: definir la codificación de caracteres para mostrar la página
	$objGrid -> charset = 'UTF-8';
	#es:Seleccionar set de caracteres para mysql
	$objGrid -> sqlcharset = "utf8";
	#es: Definir campo llave
	$objGrid-> keyfield ("id");
	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("id");
	#es: Definir la cantidad de registros a mostrar por pagina
	$objGrid-> datarows(1000);
	#es: Definir una altura fija para el DataGrid
	#en: Define a fixed height for the DataGrid
	//$objGrid-> height="500px";
	#es: Al tener activa la barra de botones (toolbar) hacer que el cuadro de exportacion sea desplegado en la barra
	$objGrid-> strExportInline = true;
	#es: Define una altura fija para el Grid sin importar cuantos registros contenga
	//$objGrid -> height = '570';
	#es: Generar un código HTML amigable y legible
	$objGrid-> friendlyHTML();
	$hoy = fhoy_str();
//////////////////////////////////////////////////////////////////////////
	#es: Especificar los campos a mostrar con sus respectivas caracter?sticas:
	// $objGrid-> FormatColumn("id","ID", "40", "50", "2", "93", "right", "text");
    //$objGrid -> FormatColumn("fecha", "Fecha / Hora",  10, 10, 0, "120", "center", "datetime:dmy:/:His");
   // $objGrid-> FormatColumn("fecha","Fecha", "5", "19", "0", "195", "left", "datetime:mdy:-:His,:",$hoy);
	$objGrid-> FormatColumn("fecha","Fecha", "5", "19", "0", "195", "left", "datetime:dmy:-:His,:",$hoy);

	$objGrid -> FormatColumn("usuario", "Usuario", 150, 150, 0, "150", "left");	
    $objGrid -> FormatColumn("recurso", "Recurso", 150, 150, 0, "150", "left");
	$objGrid-> FormatColumn("descripcion","Descripcion", "20", "200", "0", "350", "left", "textarea");
/////////////////////////////////////////////////////////////////////////
	#es:agregamos variables_generales.php
	require_once('variables_generales.php');
	#es: Por ultimo, renderizar el Grid
	$objGrid-> grid();

	function fhoy_str() { // convierte hoy en str
        date_default_timezone_set('America/Santiago');
		$date=new DateTime(); //fecha hora actual
        //$date = date('d-m-y h:i:s');
		$fecha_str = $date->format('d-m-Y H:i:s');
		//$tmp = explode('-',$fecha_str);
		//$fecha_str = implode("-",$tmp);
	    return $fecha_str;	
	} 


	

?>	
