<?php

//print_r($_POST);
//echo "<br> " . 
$producto = $_POST['dg_det_id'];


	#es: Incluir el archivo de la libreria
	#en: Include class file
	require_once('class/phpmydatagrid.class.php');
	
	#es: Crear el objeto contenedor
	#en: Create object container
	$objGrid = new datagrid('3.php','2');
	
	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_enlaces", "P3rf3ct0.,", "tnasolut_enlaces");


	#es:agregamos variables_generales.php
	require_once('variables_generales.php');

	$objGrid-> datarows(50);
	$objGrid -> liquidTable = true;	
	$objGrid -> width = "100%";
	$objGrid -> ButtonWidth = '100';



	#es: Especificar la tabla de trabajo
	$objGrid-> tabla ("productos");

	#es: Especificar campo clave para edición AJAX
	#en: Define key field to allow AJAX edition
	$objGrid-> keyfield("producto");
	
	#es: El manejo de tablas JOINed no acepta mantenimientos, por lo cual tenemos todos los botones disabled
	#en: JOINed tables do not allow maintenance, for that reason, all butons must be disabled
	$objGrid-> buttons(false,false,false,false);

	#es: Definir la cantidad de registros a mostrar por pagina
	#en: Define amount of records to display per page
	$objGrid-> datarows(500);

	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("Nombre","asc");

	#es: Definir un título para el grid
	$objGrid-> tituloGrid("Clientes del Producto " . strtoupper($producto));
	#es: Calcula próximo numero de factibilidad
	$orden  = " order by producto asc";
	$filtro = "";
	$sql = "SELECT * FROM tnasolut_enlaces.productos as prod INNER JOIN tnasolut_enlaces.clientes as cli ON prod.rut = cli.rut where prod.producto = '".$producto."'"; // . $filtro . $orden;
	$objGrid-> sqlstatement ($sql);
	
	#es: Especificar los campos a mostrar con sus respectivas características:
	#en: Specify each field to display with their own properties
	$objGrid-> FormatColumn("estadoprod","Estado", "150", "150", 0, "100","left");
	$objGrid-> FormatColumn("rut","RUT", "150", "150", 0, "100","left");
	$objGrid-> FormatColumn("nombre","Nombre", "150", "150", 0, "400","left");
	$objGrid-> FormatColumn("producto","Producto", "150", "150", 0, "150","left");
	$objGrid-> FormatColumn("clase_modelo","Detalle", "150", "150", 0, "100","left");
	$objGrid-> FormatColumn("proveedor","Proveedor", "150", "150", 0, "150","left");
	$objGrid-> FormatColumn("costo_UF","Costo UF", "150", "150", 0, "80","right");
	$objGrid-> FormatColumn("valor_UF","Valor UF", "150", "150", 0, "80","right");
	$objGrid-> FormatColumn("costo_vigente_uf","Costo Vigente UF", "150", "150", 0, "80","right");
	$objGrid-> FormatColumn("valor_vigente_uf","Valor UF", "150", "150", 0, "80","right");
	$objGrid-> FormatColumn("Tipo_Pago","Tipo de Pago", "150", "150", 0, "80","right");	
	$objGrid-> FormatColumn("dir_inst","Dirección de Instalación", "150", "150", 0, "150","right");

	$objGrid -> total('costo_vigente_uf, valor_vigente_uf');



	#es:agregamos variables_generales.php
	require_once('variables_generales.php');


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