<?php
	#es: Incluir el archivo de la libreria
	require_once('class/phpmydatagrid.class.php');

	#es: Crear el objeto contenedor
	$objGrid = new datagrid('productos_tna.php','1');

	#es: Defnir la relacion con la base de datos maestra y obtener el valor del id por defecto para nuevos registros
	#en: Define the relation with the master nase and get the default id for new records
	$rut = $objGrid -> setMasterRelation("producto");

	#es: Definir la cantidad de registros a mostrar por pagina
	$objGrid-> datarows(50);
	$objGrid -> liquidTable = true;	
	$objGrid -> width = "100%";
	$objGrid -> ButtonWidth = '60';


	#es: Realizar la conexión con la base de datos
	$objGrid -> conectadb("localhost", "tnasolut_enlaces", "P3rf3ct0.,", "tnasolut_enlaces");

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
	$objGrid-> searchby("producto");

	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	#en: Tell to phpMyDatagrid it must use the datepicker
	$objGrid-> useCalendar(true);

	#es: Definir campo llave
	#en: Define keyfield
	$objGrid-> keyfield ("producto");

	#es: Definir campo para ordenamiento
	$objGrid-> orderby ("producto","asc");

	#es: Definir un título para el grid
	$objGrid-> tituloGrid("Clientes por Producto");
	#es: Calcula próximo numero de factibilidad
	$orden  = " order by producto asc";
	$filtro = "";
	$sql = "SELECT distinct producto FROM tnasolut_enlaces.productos"; // . $filtro . $orden;
	$objGrid-> sqlstatement ($sql);

	//$next_fact = fnext_fac();

//////////////////////////////////////////////////////////////////////////
	#es: Especificar los campos a mostrar con sus respectivas caracter?sticas:
	//$objGrid -> FormatColumn("id","ID", "12", "12", "1", "50", "right", "number");
	$objGrid -> FormatColumn("producto", "Producto",  25, 25, 1, "100", "center", "select:ADSL_ADSL:Enlace Dedicado:Punto a Punto:Router:X conect:MB internacional:Boca Switch:Plan Telefonia IP:Hosting:Soporte:Cloud:VPN:PACK IP:VPS:Colocate:DID Adicional:Equipo en Comodato:Equipo en Arriendo:MPLS:Canje:Gasto:SMS:Siptrunk:Licencia:Otros telefonia ip:Publicacion ASN", "Enlace Dedicado");
	#es:agregamos variables_generales.php
	require_once('variables_generales.php');

	#es: Por ultimo, renderizar el Grid
	$objGrid-> grid();


?>



