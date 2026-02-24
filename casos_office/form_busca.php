	<?PHP // buscador de casos horizontal ?>
	<form action="busqueda_session.php" method="post" target="_blank">
		<table border="0" style="border-style: solid solid solid solid; border-color: #ffffff; font-size: small;">
		  <tbody>
			<tr>
			  <td width="6%" align="center" style="background-color: #1F1D3E;color: white;">Nª<br /><input name="numero" type="text" id="numero" size="5" value="" /></td>
			  <td align="center" width="4%"style="background-color: #1F1D3E;color: white;"><input type="radio" name="mes" value="estemes" onclick="estemes();" /> <br />
		      Este <br />Mes</td>
			  <td align="center" width="7%" style="background-color: #1F1D3E;color: white;"><input type="radio" name="mes" value="mesant" onclick="mesanterior();"/>
			    <br />
		      Mes<br />Anterior </td>
			  <td width="22%" align="center" style="background-color: #1F1D3E;color: white;">Categorìa<br /><select name="categoria" id="categoria">
				<option value = ''>&nbsp;</option>
				  <option value = 'Cableado'>Cableado</option>
				  <option value = 'Enlace'>Enlace</option>
				  <option value = 'enlace_caido'>Enlace Caído</option>
				  <option value = 'facturacion'>Facturación</option>
				  <option value = 'Fuera_de_horario'>Fuera de Horario</option>
				  <option value = 'Hosting'>Hosting / Correos</option>
				  <!--option value = 'Nuevo_requerimiento'>Nuevo Requerimiento / Oportunidad</option-->
				  <option value = 'Otros'>Otros</option>
				  <option value = 'Soporte'>Soporte</option>
				  <option value = 'Soporte_contrato_mensual'>Soporte Contrato Mensual</option>
				  <option value = 'Sujeto_a_cobro'>Sujeto a Cobro</option>
				  <option value = 'Telefonia'>Telefonía</option>
				  <option value = 'termino_contrato'>Término de Contrato</option>
			  </select></td>
				<td width="11%" align="center" style="background-color: #1F1D3E;color: white;">Razón Social<br /><input name="empresa" type="text" id="empresa" size="10" value="" /></td>
				<td width="11%" align="center" style="background-color: #1F1D3E;color: white;">Usuario Asignado<br /><br /><input name="usuario" type="text" id="usuario" size="10" value="" /></td>
				<td width="10%" align="center" style="background-color: #1F1D3E;color: white;"><label><input type="radio" name="estado" value="cerrados" /> Cerrados </label><br />
                    <label><input type="radio" name="estado" value="abiertos" required="required" checked="checked" /> Abiertos </label><br />
                <label><input type="radio" name="estado" value="todos" /> Todos</label></td>
				<td width="12%" align="center" style="background-color: #1F1D3E;color: white;">Código de Servicio<br /><br /><input name="codservicio" type="text" id="codservicio" size="10" value="" /></td>
				<td width="17%" align="center" style="background-color: #1F1D3E;color: white;">
				  <input style="font-size: 12px;" type="submit" value="Buscar en Sistemas" /> <br />	
				  <input style="font-size: 10px;" type="reset" value="Limpiar" />
				</td>
			</tbody>
			</table>
    </form>                             
