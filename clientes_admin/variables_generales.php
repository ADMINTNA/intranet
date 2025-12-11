<?php
	#es: Generar un c�digo HTML amigable y legible
	$objGrid-> friendlyHTML();
	#es:Seleccionar idioma
	$objGrid -> language("es");
	#es: Definir propiedades para edicion sin capas
	$objGrid-> nowindow = false;
	#es: definir la codificaci�n de caracteres para mostrar la p�gina
	$objGrid -> charset = 'UTF-8';
	#es:Seleccionar set de caracteres para mysql
	$objGrid -> sqlcharset = "utf8";
	#es: Permitir Edici�n AJAX online
	$objGrid-> ajax('silent');
	#es: Decirle al datagrid que va a usar el calendario (datepicker)
	$objGrid-> useCalendar(true);
	#es: Define boton Gravar & Nuevo
	$objGrid-> saveaddnew = true;
	#es: Definir opciones de exportacion
	$objGrid-> export(true,true,true,false,false);
	#es: Permitir selecci�n de varios registros simult�neamente mediante el uso de cajas de chequeo (checkboxes)
	//$objGrid-> checkable();
	#es: Activar la barra de botones del datagrid
	$objGrid-> toolbar = true;
	#Importante. Definir el uso de righclickmenu antes de definir las opciones
	$objGrid-> useRightClickMenu("class/phpMyMenu.inc.php");
	# Definir el menu
	#es: Definir el caracter que desea utilizar como separador decimal
	$objGrid -> decimalPoint(',');
	#es: Definir el caracter que desea utilizar como separador CVS
	$objGrid -> csvSeparator = ';';
	#es: Activar el boton que permite Eliminar varios registros slmultaneamente
	//$objGrid-> delchkbtn = true;
	#es: Opciones de exportar en la barra de herramientas en vez de una ventana flotante
	$objGrid-> strExportInline = true;
	#es: Opciones de b�squeda en la barra de herramientas en vez de una ventana flotante
	$objGrid-> strSearchInline = false;
	#es: Activar icono de refrescar el DataGrid
	$objGrid-> reload = true;
	#es: Definir el orden de presentaci�n de los botones de registro
	$objGrid-> btnOrder="[D][V][E]";
	#es: Definir acciones permitidas
	$objGrid-> buttons(true,true,true,true,0, "Botones");
	#es: Definir el tipo de paginacion
	$objGrid-> paginationmode ("input");
	#es: Eliminar las flechas de ordenamiento del campo active
	$objGrid-> chField("active","R");
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
?>
