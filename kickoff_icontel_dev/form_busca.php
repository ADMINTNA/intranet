	<?PHP // buscador de casos horizontal ?>
		<table align="left" border="0" id="busca_casos"  style="border-style: solid solid solid solid; border-color: dimgrey; font-size: small;">
			<tbody>
			<form  action="/casos/busqueda_session.php" method="post" target="_blank">
			<tr>
			  <td  align="center" style="background-color: #1F1D3E;color: white;"><input style="background-color: lightgray" placeholder="Caso NÂº" name="numero" type="text" id="numero" size="7" value="" /></td>
			  <td  align="center" style="background-color: #1F1D3E;color: white;"><select style="background-color: lightgray; color: gray;" name="categoria" id="categoria">
			  <option value = ''>&nbsp;</option>
				  <option value = "" selected>CategorÃ­a</option>
				  <option value = 'Cableado'>Cableado</option>
				  <option value = 'Enlace'>Enlace</option>
				  <option value = 'enlace_caido'>Enlace CaÃ­do</option>
				  <option value = 'facturacion'>FacturaciÃ³n</option>
				  <option value = 'Fuera_de_horario'>Fuera Horario</option>
				  <option value = 'Hosting'>Hosting/Correos</option>
				  <!--option value = 'Nuevo_requerimiento'>Nuevo Requerimiento / Oportunidad</option-->
				  <option value = 'Otros'>Otros</option>
				  <option value = 'Servicio_Tecnico'>Servicio TÃ©cnico</option>
				  <option value = 'Soporte'>Soporte</option>
				  <option value = 'Soporte_contrato_mensual'>Soporte Mensual</option>
				  <option value = 'Sujeto_a_cobro'>Sujeto a Cobro</option>
				  <option value = 'Telefonia'>TelefonÃ­a</option>
				  <option value = 'termino_contrato'>TÃ©rmino Contrato</option>
				  <option value = 'VPS'>VPS</option>
			    </select></td>
				<td  align="center" style="background-color: #1F1D3E;color: white;"><input name="empresa" type="text" id="empresa" placeholder="Empresa" style="background-color: lightgray" value="" size="10" /></td>
				<!--td align="center" style="background-color: #1F1D3E;color: white;"><input style="background-color: lightgray" placeholder="Usuario" name="usuario" type="text" id="usuario" size="10" value="" /></td-->
				<td align="center" style="background-color: #1F1D3E;color: white;"><input style="background-color: lightgray" placeholder="Cod.Servicio" name="codservicio" type="text" id="codservicio" size="10" value="" /></td>
				<td align="center" style="background-color: #1F1D3E;color: lightgray;">
					<label><input  style="background-color: lightgray" type="radio" name="estado" value="cerrados" />&nbsp; Cerrados </label>
				</td>
				<td align="center" style="background-color: #1F1D3E;color: lightgray;">
                	<label><input style="background-color: lightgray" type="radio" name="estado" value="abiertos" required="required" checked="checked" />&nbsp; Abiertos </label>
				</td>
				<td align="center" style="background-color: #1F1D3E;color: lightgray;">
                 	<label><input  style="color: lightgray;" type="radio" name="estado" value="todos" />&nbsp;Todos</label>
				</td>
			  	<td align="center" style="background-color: #1F1D3E;color: white;">
					<input style="background-color: lightgray; color: gray; font-size: 12px;" type="reset" value="Limpiar" />
				</td>
			  	<td align="center" style="background-color: #1F1D3E;color: white;">
			    	<input style="background-color: lightgray; font-size: 12px;" type="submit" value="Buscar Casos" />
			    </td>
			</tr>  
			</form>                          
			</tbody>
		</table>
 