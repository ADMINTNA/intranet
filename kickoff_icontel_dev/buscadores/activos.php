	<?PHP $categorias   = busca_columna("CALL `activos_categorias`()");?>
	<?PHP $proveedores  = busca_columna("CALL `activos_proveedores`()");?>
	<table height="100%"  style="background-color: #1F1D3E;color: white;  border-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px;  border-color: dimgrey;">
		  <tbody>
			<form action="/servicios_activos/informe_activos.php" method="post" target="_blank" name="clientes" id="clientes">
				<tr>
					<td colspan="2" align="center"><strong style="font-size: 20px;">Clientes Activos</strong></td>
				</tr>
				<tr>
					<td width="90px" style="background-color: #1F1D3E;color: white;"> 
						<label>Producto:</label></td>
		   			<td style="background-color: #1F1D3E;color: white;">
						<input style="background-color: lightgray" name="producto" type="text" id="producto"  value="">
					</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"> 
						<label>Variante:</label></td>
					<td style="background-color: #1F1D3E;color: white;">
						<input style="background-color: lightgray" name="codigo" type="text" id="codigo" value="">
					</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"> 
						<label>Dirección:</label>
					</td>
					<td style="background-color: #1F1D3E;color: white;">
						<input style="background-color: lightgray" name="direccion" type="text" id="direccion" value="">
					</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"> 
						<label>Cod.Servicio:</label>
					</td>
					<td align="left" style="background-color: #1F1D3E;color: white;">
						<input style="background-color: lightgray" name="codservicio" type="text" id="codservicio" value="" />
					</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"><label>Proveedor</label>:</td>
					<td>
						<?php crea_select($proveedores, "proveedor", 0, 6); ?>
					</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"> 
						<label>Ejecutiv@:</label>
					</td>
					<td style="background-color: #1F1D3E;color: white;">
						<input style="background-color: lightgray" name="vendedor" type="text" id="vendedor" value="">
					</td>
				</tr>
			  <tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"> 						
						<label>Recurrencia:</label>
					</td>
					<td style="background-color: #1F1D3E;color: gray;">
						<select name="recurrencia[]" multiple="multiple" id="recurrencia" class="3col active" style="background-color: lightgray; color: gray;" >
						  <option value="cerrado_aceptado_cot">Mensual</option>
						  <option value="Cerrado_aceptado_anual_cot">Anual</option>
						  <option value="cerrado_aceptado_cli">Bienal</option>
						  <option value="de_baja">De Baja</option>
						  <option value="en_traslado">En Traslado</option>
						  <option value="posible_traslado">Posible Traslado</option>
						  <option value="suspendido">Suspendido</option>
						  <option value="demo">En Demo</option>
						</select>						
				</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"><label>Categor&iacute;a</label>:</td>
					<td>
						<?php crea_select($categorias, "categoria", 0, 6); ?>
					</td>
				</tr>
				<tr>
					<td align="center" style="background-color: #1F1D3E;color: white;">
						
				<button type="button" onclick="resetForm('clientes')" style="background-color: lightgray; color: gray; font-size: 12px;">Limpiar</button>						
					</td>
					<td align="center" >
						<input style="background-color: lightgray; font-size: 12px;" name="submit" type="submit" value="Buscar Clientes Activos" />
					</td>
				</tr>
			</form>
		  </tbody>
		</table>
	                             
