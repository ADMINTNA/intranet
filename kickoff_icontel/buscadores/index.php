<?PHP 
//=====================================================
// /intranet/kickoff/buscadores/index.php
// index de buscadores
// Autor: Mauricio Araneda
// Actualizado: 08-11-2025
//=====================================================
require_once __DIR__ . '/../session_core.php';
include_once("funciones.php"); 
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Buscadores iContel</title>
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
    <link href="./jquery.multiselect.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="./jquery.multiselect.js"></script>	
    <script src="./buscadores.js"></script>			
<style type="text/css">
body,td,th {
	color: #FFFFFF;
}
body {
	background-color: #1F1D3E;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
</head>
<body>
<p style="font-size: 10px" > </p>
	<table align="center" border="0" style="background-color: #512554; color: white; border-style: solid;">
		<tbody>
			<tr>
				<td valign="top"><?PHP include_once("clientes.html"); ?></td>
				<td valign="top"><?PHP include_once("cotizaciones.html"); ?></td>
				<td valign="top"><?PHP include_once("casos.html"); ?></td>
				<td valign="top"><?PHP include_once("activos.php"); ?></td>
				<td valign="top"><?PHP include_once("../../duemint/buscador_duemint.php"); ?></td>
			</tr>
			<tr>
				<td valign="top"><?PHP include_once("tareas.html"); ?></td>
				<td valign="top"><?PHP include_once("oportunidades.html"); ?></td>
				<td valign="top"><?PHP include_once("productos.php"); ?></td>
				<td valign="top"><?PHP include_once("nota_venta.php"); ?></td>
				<td valign="top"><?PHP include_once("nv_fac_bsale.php"); ?></td>
                
			</tr>
		</tbody>
	</table>
</body>
</html>
