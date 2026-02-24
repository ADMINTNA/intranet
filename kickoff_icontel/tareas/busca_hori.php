		<table border="0" style="border-style: solid solid solid solid; border-color: dimgrey; font-size: small;">
		  <tbody>
			<tr><form action="../oportunidades/busqueda_session.php" method="post" target="_blank">
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Asunto" style="background-color: lightgray" name="asunto" type="text" id="asunto" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Cliente" style="background-color: lightgray" name="cliente" type="text" id="cliente" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><select style="background-color: lightgray" name="categoria" id="categoria">
				  	 <option value = "" selected>CategorÃ­a</option>
					 <option value = 'aprobaciÃ³n_hh'>AprobaciÃ³n de HH</option> 
					 <option value = 'compras'>Compras</option>
					 <option value = 'general'>General</option>
					 <option value = 'levantamiento'>Levantamiento</option>
				</select>                                 
			  </td>
			  <td style="background-color: #1F1D3E;color: white;"><select style="background-color: lightgray" name="estado" id="estado">
				  	 <option value = "" selected>Estado</option>
					 <option value = 'NotStarted'>No Iniciada</option> 
					 <option value = 'tarea_creada'>Mail Cliente-Tarea Creada</option>
					 <option value = 'InProgress'>En Progreso</option>
					 <option value = 'avance'>Mail Cliente-Avance</option>
					 <option value = 'retencion'>Cliente en RetenciÃ³n</option>
					 <option value = 'Reasignada'>Reasignada</option>
					 <option value = 'Aprobar_Hora_Extra'>AprobaciÃ³n HH</option>
					 <option value = 'Rendicion'>RendiciÃ³n</option>
					 <option value = 'movil_solicitado'>MÃ³vil Solicitado</option>
					 <option value = 'gastoexterno'>Gasto Proveedor Externo</option>
					 <option value = 'validar'>Mail Cliente-Validar</option>
					 <option value = 'rechazo_comercial'>Rechazo Comercial</option>
					 <option value = 'Crear_Oportunidad'>Crear Oportunidad</option>
					 <option value = 'tarea_sujeta_cobro'>Sujeta a cobro $</option>
					 <option value = 'en_traslado'>En Traslado</option>
					 <option value = 'Complete'>Completada</option>
					 <option value = 'ATRASADA'>ATRASADA</option>
				</select>                                 
			  </td>
			  <td style="background-color: #1F1D3E;color: white;"><select style="background-color: lightgray" name="prioridad" id="prioridad">
				  	 <option value = "" selected>Prioridad</option>
					 <option value = 'URGENTE_E'>Urgente Escalado</option> 
					 <option value = 'URGENTE'>Urgente</option>
					 <option value = 'High'>Alta</option>
					 <option value = 'Low'>Baja</option>
				</select>                                 
			  </td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Ejecutiv@" style="background-color: lightgray" name="ejecutivo" type="text" id="ejecutivo" size="10" value=""></td>
			  <td align="center" style="background-color: #1F1D3E;color: white;">
				<input style="background-color: lightgray; font-size: 10px;" type="reset" value="Limpiar" />&nbsp;
			    <input style="background-color: lightgray; font-size: 12px;" type="submit" value="Buscar Tareas" /
			  </td>
				</form>                             
			</tr>
		  </tbody>
		</table>
