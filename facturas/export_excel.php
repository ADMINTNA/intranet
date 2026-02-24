<?php
// Para que Excel lo reconozca
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=facturacion_export.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Inicia el buffer
ob_start();

// Incluye el contenido de tu tabla
include("contenido.php");

// ObtÃ©n todo el HTML generado
$html = ob_get_clean();

// Imprime solo la tabla, Excel lo interpretarÃ¡ correctamente
echo $html;
