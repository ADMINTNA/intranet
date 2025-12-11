<?php

	#es: Incluir el archivo de la libreria
	#en: Include class file
	require_once('class/phpmydatagrid.class.php');

	#es: Crear el objeto contenedor
	#en: Create object container
	$objGrid = new datagrid('tarifas.php', '1');
	

	#es: Realizar la conexión con la base de datos
	#en: Connect with database
	$objGrid -> conectadb("localhost", "tnasolut_data_studio", "P3rf3ct0.,", "tnasolut_tarifas_telefonicas");

	#es: Especificar la tabla de trabajo
	#en: Define Tablename
	$objGrid-> tabla ("tarifas");
	
	#es: Definir acciones permitidas
	#en: Define allowed actions
	//$objGrid-> buttons(true,0,0,0,true,"Accion");
	$objGrid-> buttons(true,true,true,true,0, "Acci&oacute;n");


	#es: Definir campo llave
	#en: Define keyfield
	$objGrid-> keyfield ("id");
	
	#es: Definir campo para ordenamiento
	#en: Define order field
	$objGrid-> orderby ("location_name","asc");

	#es: Definir campo(s) para búsquedas
	$objGrid-> searchby("location_name, dial_code, precio_venta");
	#es: definir la codificación de caracteres para mostrar la página
	$objGrid -> charset = 'UTF-8';
	#es:Seleccionar set de caracteres para mysql
	$objGrid -> sqlcharset = "utf8";


#es: Definir la cantidad de registros a mostrar por pagina
	#en: Define amount of records to display per page
	$objGrid-> datarows(20);

	#es: Al tener activa la barra de botones (toolbar) hacer que el cuadro de exportacion sea desplegado en la barra
	#en: As we have the toolbar active, allow that the export option to be linked to the toolbar
	$objGrid-> strExportInline = true;

	#es: Preparar el SQL personalizado, teniendo en cuenta de no incluir comandos como Where, Order o group, 
	#    ya que estos deben ser definidos desde su propio metodo.
	#en: Prepare the custom SQL query, having in mind to not to include statements like Where, Order or group, 
	#    because those statement must be included by their respective methods
	//$objGrid-> sqlstatement ("SELECT * FROM `tarifas` WHERE 1 GROUP BY `location_name`, `precio_venta`");

	#es: Crear la instruccion where
	//$objGrid-> where("rut = '{$rut}'");

	$titulo = "<b>Tarifas Telef&oacute;nicas agrupadas por destino y precio (en pesos x minuto, m&aacute;s IVA)</b>";

	$objGrid-> tituloGrid($titulo);
	#es: Especificar los campos a mostrar con sus respectivas caracter?sticas:
	$objGrid-> FormatColumn("id","ID", "40", "50", "1", "40", "left", "text");
	$objGrid-> FormatColumn("dial_code","Dial Code", "13", "20", 0, "50","left");
	$objGrid-> FormatColumn("location_name","Location Name", "5", "30", 0, "150", "left");
	$objGrid-> FormatColumn("precio_venta","Precio Neto $", "5", "10", "0", "50", "right", "money:$");	
/////////////////////////////////////////////////////////////////////////
	#es: Instrucciones para el usuario
	#en: User instructions
	//$objGrid-> fldComment("birth_date", "Write the employee bith date (YYYY-MM-DD)");
	#es:agregamos variables_generales.php
	require('variables_generales.php');
	#es: Por ultimo, renderizar el Grid
	#en: Finally, render the grid.
	$objGrid-> grid();
	
	
	


	
	
	
?>