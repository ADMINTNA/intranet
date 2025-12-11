<?php
	#es: Incluir el archivo de la libreria
	#en: Include class file
	require_once('class/phpmydatagrid.class.php');
	
	#es: Crear el objeto contenedor
	#en: Create object container
	$objGrid = new datagrid('prod_servi.php','1');
	
	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_enlaces", "P3rf3ct0.,", "tnasolut_enlaces");

	$objGrid-> setDetailsGrid("3.php", "producto");

	$objGrid -> friendlyHTML();
//	$objGrid-> datarows(50);
	$objGrid -> liquidTable = true;	
//	$objGrid -> width = "100%";
//	$objGrid -> ButtonWidth = '100';





	#es: Especificar la tabla de trabajo
	$objGrid-> tabla ("productos");
	
	#es: Especificar campo clave para edición AJAX
	#en: Define key field to allow AJAX edition
	$objGrid-> keyfield("producto");
	
	#es: El manejo de tablas JOINed no acepta mantenimientos, por lo cual tenemos todos los botones disabled
	#en: JOINed tables do not allow maintenance, for that reason, all butons must be disabled
//	$objGrid-> buttons(false,false,false,false);

	#es: Definir la cantidad de registros a mostrar por pagina
	#en: Define amount of records to display per page
	$objGrid-> datarows(100);

	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("producto","asc");

	#es: Definir un título para el grid
	$objGrid-> tituloGrid("Clientes por Producto");

	#es: Calcula próximo numero de factibilidad
	$orden  = " order by producto asc";
	$filtro = "";
	$sql = "SELECT distinct producto FROM tnasolut_enlaces.productos"; // . $filtro . $orden;
	$objGrid-> sqlstatement ($sql);
	
	#es: Especificar los campos a mostrar con sus respectivas características:
	#en: Specify each field to display with their own properties
	$objGrid-> FormatColumn("producto","Producto", "150", "150", 0, "500","left");

	#es:agregamos variables_generales.php
//	require_once('variables_generales.php');


	#es: Generar una barra de botones
	#en: Add a toolbar
	$objGrid-> toolbar = true;
	
	#es: Definir el tipo de paginacion
	#en: Define pagination mode
	$objGrid-> paginationmode ("input");

	#es: Por ultimo, renderizar el Grid
	#en: Finally, render the grid.
	$objGrid-> grid();
?>