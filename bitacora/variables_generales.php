<?php
	#es: Generar un código HTML amigable y legible
	$objGrid-> friendlyHTML();
	#es:Seleccionar idioma
	$objGrid -> language("es");
	#es: Definir propiedades para edicion sin capas
	$objGrid-> nowindow = false;
	#es: definir la codificación de caracteres para mostrar la página
	$objGrid -> charset = 'UTF-8';
	#es:Seleccionar set de caracteres para mysql
	$objGrid -> sqlcharset = "utf8";
	#es: Permitir Edición AJAX online
	$objGrid-> ajax('silent');
	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	$objGrid-> useCalendar(true);
	#es: Definir campo(s) para búsquedas
	$objGrid-> saveaddnew = true;
	#es: Definir opciones de exportacion
	$objGrid-> export(true,true,true,false,false);
	#es: Permitir selección de varios registros simultáneamente mediante el uso de cajas de chequeo (checkboxes)
	//$objGrid-> checkable();
	#es: Activar la barra de botones del datagrid
	$objGrid-> toolbar = true;
	#es: Adicionar un separador a los botones
	//$objGrid-> addSeparator();
	#Importante. Definir el uso de righclickmenu antes de definir las opciones
	$objGrid-> useRightClickMenu("class/phpMyMenu.inc.php");
	# Definir el menu
	$objGrid->objMenu->addmenu("add", 180, 22, 0, 1, 1, '#c0c0c0', '#fff', '#ddd', '', 'toolbar_menu_sample/bck-menuitems.gif' );
	#es: Definir el orden de presentaci?n de los botones de registro
	$objGrid-> btnOrder="[D][V][E]";
	#es: Definir acciones permitidas
	$objGrid-> buttons(true,true,true,true,0, "Botones");
	#es: Definir el caracter que desea utilizar como separador decimal
	$objGrid -> decimalPoint(',');
	#es: Definir el caracter que desea utilizar como separador CVS
	$objGrid -> csvSeparator = ';';
	#es: Activar el boton que permite Eliminar varios registros slmultaneamente
	//$objGrid-> delchkbtn = true;
	#es: Opciones de exportar en la barra de herramientas en vez de una ventana flotante
	$objGrid-> strExportInline = true;
	#es: Opciones de búsqueda en la barra de herramientas en vez de una ventana flotante
	$objGrid-> strSearchInline = false;
	#es: Activar icono de refrescar el DataGrid
	$objGrid-> reload = true;
	#es: Definir el orden de presentación de los botones de registro
	$objGrid-> btnOrder="[D][V][E]";
	#es: Definir acciones permitidas
	$objGrid-> buttons(true,true,true,true,0, "Botones");
	#es: Definir el tipo de paginacion
	$objGrid-> paginationmode ("input");
	#es: Eliminar las flechas de ordenamiento del campo active
	$objGrid-> chField("active","R");
/*
	#es: Interceptar el llamado AJAX 
	if ($objGrid->isAjaxRequest()){
		switch ($objGrid->getAjaxID()){
			case DG_IsInline: // case 4:	// editado Registro
				#es:agregamos notificador.php
				require_once('envia_notificaciones.php');
			break;
			#es: Validar si es "fa" (tal como se definio en el script), y procesar el campo de imagen
			case 'favorite': 
				$objGrid->changeImage(); 
			break;
		}
	}
*/    
?>