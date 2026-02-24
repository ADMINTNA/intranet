<?PHP 
// ==========================================================
// intranet/kickoff/buscadores/productos.php
// Descripción: Buscador de productos en Sweet
// Autor: Mauricio Araneda
// Fecha: 2025-11-17
// Codificación: UTF-8 sin BOM
// ==========================================================
    session_name('icontel_intranet_sess');
    require_once __DIR__ . '/../session_core.php';
    header('Content-Type: text/html; charset=utf-8');    
    $categorias   = busca_columna("CALL `activos_categorias`()");
    $proveedores  = busca_columna("CALL `activos_proveedores`()");
    ?>
		<table height="100%"  style="background-color: #1F1D3E;color: white;  border-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px;  border-color: dimgrey;">
		  <tbody>
	 		<form action="../../productos/busqueda_session.php" method="post" target="_blank">
			<tr>
				<td colspan="2" align="center"><strong style="font-size: 20px;">Productos</strong></td>
			</tr>
			<tr>
			  <td width="90px" style="background-color: #1F1D3E;color: white;">Nombre</td>
			  <td style="background-color: #1F1D3E;color: white;">
				  <input style="background-color: lightgray" name="nombre" type="text" id="nombre" size="20" value="">
			  </td>
			</tr>
			<tr>
			  <td style="background-color: #1F1D3E;color: white;">Variante</td>
			  <td style="background-color: #1F1D3E;color: white;"><input style="background-color: lightgray" name="variante" type="text" id="variante" size="20" value=""></td>
			</tr>
			<tr>
			  <td style="background-color: #1F1D3E;color: white;">Descripción</td>
			  <td style="background-color: #1F1D3E;color: white;"><input style="background-color: lightgray" name="descripcion" type="text" id="descripcion" size="20" value=""></td>
			</tr>
			<tr>
			  	<td style="background-color: #1F1D3E;color: white;">Categoría<br></td>
			  	<td style="background-color: #1F1D3E;color: white;">
					<?php crea_select($categorias, "categoria", 0, 6); ?>
			  	</td>
		   	</tr>
			<tr>
			  	<td colspan="2" style="height: 30px; background-color: #1F1D3E;color: white;"></td>
		   	</tr>
			<tr>
				<td align="center" style="background-color: #1F1D3E;color: white;"> 
					<input style="background-color: lightgray; color: gray; font-size: 12px;" type="reset" value="Limpiar" />
				</td>
				<td align="center" style="background-color: #1F1D3E;color: white;">
					<input style="background-color: lightgray; font-size: 12px;" type="submit" value="Buscar Productos" />
				</td>
			</tr>
			</form>                             
		  </tbody>
		</table>
 