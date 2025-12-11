	 
		<table border="0" style="border-style: solid solid solid solid; border-color: dimgrey; font-size: small;">
		  <tbody>
			<tr><form action="../cotizaciones/busqueda_session.php" method="post" target="_blank">
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Cotización Nº" style="background-color: lightgray" name="numero" type="text" id="numero" size="14" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Titulo" style="background-color: lightgray" name="asunto" type="text" id="asunto" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Cliente" style="background-color: lightgray" name="cliente" type="text" id="cliente" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Ejecutiv@" style="background-color: lightgray" name="ejecutivo" type="text" id="ejecutivo" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><select style="width:140px; background-color: lightgray; color: gray"  name="etapa" id="etapa">
				  	 <option value = "" selected>Etapa de Cotización</option>
					 <option value = 'Borrador_cot'>Borrador</option> 
					 <option value = 'negociacion_cot'>Negociación</option>
					 <option value = 'esperando_prov_cot'>Esperando Proveedor</option>
					 <option value = 'entregada_cot'>Cotización Entregada</option>
					 <option value = 'espera_cliente_cot'>En Espera de Cliente</option>
					 <option value = 'reemplazada_cot'>Reemplazada</option>
					 <option value = 'cerrado_aceptado_cot'>Cerrado Recurrente Mensual</option>
					 <option value = 'Cerrado_aceptado_anual_cot'>Cerrado Recurrente Anual</option>
					 <option value = 'cerrado_aceptado_cli'>Cerrado Recurrente Bienal</option> 
					 <option value = 'cerrado_aceptado'>Cerrado Aceptado Unica</option>
					 <option value = 'posible_traslado'>Posible Traslado</option>
					 <option value = 'en_traslado'>En Traslado</option>
					 <option value=  'Suspender'>Suspender</option>
					 <option value=  'gasto'>Gasto</option> 
					 <option value=  'generar_baja'>Generar Baja</option>
					 <option value=  'cambio_razon_social'>Cambio Razón Social</option>
					 <option value=  'no_renovado'>No Renovado</option> 
					 <option value=  'cerrado_perdido_cot'>Cerrado Perdido/Descartado</option>
					 <option value=  'suspendido'>Suspendido</option>
					 <option value=  'de_baja'>De Baja</option>
					 <option value=  'guia_oc_cli'>Guia de Despacho/Recepción</option>
					 <option value=  'orden_compra'>Orden de Compra</option>
				</select>                                 
			  </td>
			  <td align="center" style="background-color: #1F1D3E;color: white;">
				<input style="background-color: lightgray; color: gray; font-size: 12px;" type="reset" value="Limpiar" />&nbsp;
			    <input style="background-color: lightgray; font-size: 12px;" type="submit" value="Buscar Cotizaciones" /
			  </td></form>
			</tr>
		  </tbody>
		</table>
	                             
