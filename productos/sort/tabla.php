<?php 
// ==========================================================
// /intranet/productos/sort/tabla.php
// Muestra el resultado de la busqueda de productos
// Autor: Mauricio Araneda
// Fecha: 2025-11-18
// Codificación: UTF-8 sin BOM
// ==========================================================

// ⚠️ IMPORTANTE: NADA DE HTML ANTES DE ESTO
session_name('icontel_intranet_sess');
session_start();

header('Content-Type: text/html; charset=utf-8');


include "config.php";
// activo mostrar errores
     // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
 //   session_start();
?>
<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <title>Buscador Casos iContel</title>
	 <link href='style.css' rel='stylesheet' type='text/css'>
	 <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
	 <script src='script.js' type='text/javascript'></script>
</head>
<body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
	<div class='container'>
		<input type='hidden' id='sort' value='asc'>
		<table width='100%' id='empTable' border='1' cellpadding='10'>
			<tr>
				<th>#</span></th>
				<th><span onclick='sortTable("producto");'>Producto</span></th>
				<th><span onclick='sortTable("numero_parte");'>Variante</span></th>
				<th><span onclick='sortTable("valor");'>Valor</span></th>
				<th><span onclick='sortTable("descripcion");'>Descripción</span></th>
				<th><span onclick='sortTable("categoria");'>Categoría</a></th>
				<th><span onclick='sortTable("tipo");'>Tipo</a></th>
			</tr>
			<?php 
				$query =  $_SESSION["query"]." ORDER BY producto DESC";
				$conn = DbConnect("tnasolut_sweet");
				$result = mysqli_query($conn,$query);
				$ptr = 0;
				$url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAOS_Products%26offset%3D1%26stamp%3D1647912995043611800%26return_module%3DAOS_Products%26action%3DDetailView%26record%3D";      
				while($row = mysqli_fetch_array($result)){
					$ptr ++;    
					$producto       = $row["producto"];
					$variante       = $row['numero_parte'];
					$valor          = $row['valor'];
					$descripcion    = $row['descripcion'];
					$categoria      = $row['categoria'];
					$tipo           = $row["tipo"];
					$id             = $row["id"];
					?>
					<tr>
						<td><?php echo $ptr; ?></td>
						<td><a target="_blank" href="<?php echo $url.$id; ?>"><?php echo $producto; ?></a></td>
						<td><?php echo $variante; ?></td>
						<td align="right"><?php echo number_format($valor,2); ?></td>
						<td width="45%"><?php echo $descripcion; ?></td>
						<td><?php echo $categoria; ?></td>
						<td><?php echo $tipo; ?></td>
					</tr>
			<?php
			}
			?>
		</table><br><br>
	</div>
</body>
</html>