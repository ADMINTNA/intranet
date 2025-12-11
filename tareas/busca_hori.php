<?PHP include_once("../oportunidades/includes/functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <?php include_once("../meta_data/meta_data.html"); ?>
<title>Buscador Tareas iContel</title>
<style type="text/css">
    .table_alarmas{
           border: none;
           color: #1F1D3E;
           color: white;
           font-size: 15px;
           border-collapse: collapse;
           background-color: #19173C;
           border-collapse: collapse;

       }   
      th, td {
          padding: 5px;
     }
     body{
        margin:0;
        padding:0;
        margin-left: 0px;
        margin-top: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
        font-size: 18px;
        background-color: #FFFFFF;
        color: #1F1D3E;
    }
    table {
      padding: 0;
      margin: 0;    
      border-collapse: collapse;
    }     
    
input[type='radio']:after {
        width: 15px;
        height: 15px;
        border-radius: 15px;
        top: -2px;
        left: -1px;
        position: relative;
        background-color: #1F1D3E;
        content: '';
        display: inline-block;
        visibility: visible;
        border: 2px solid white;
    }

    input[type='radio']:checked:after {
        width: 15px;
        height: 15px;
        border-radius: 15px;
        top: -2px;
        left: -1px;
        position: relative;
        background-color: white;
        content: '';
        display: inline-block;
        visibility: visible;
        border: 2px solid white;
    }    
    
</style>
</head>
<body>
	 <form action="../../tareas/busqueda_session.php" method="post" target="_blank">
		<table border="0" style="border-style: solid solid solid solid; border-color: dimgrey; font-size: small;">
		  <tbody>
			<tr>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Asunto" style="background-color: lightgray" name="asunto" type="text" id="asunto" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Cliente" style="background-color: lightgray" name="cliente" type="text" id="cliente" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><select style="background-color: lightgray; color: gray;" name="categoria" id="categoria">
				  	 <option value = "" selected>Categoría</option>
					 <option value = 'aprobación_hh'>Aprobación de HH</option> 
					 <option value = 'compras'>Compras</option>
					 <option value = 'general'>General</option>
					 <option value = 'levantamiento'>Levantamiento</option>
				</select>                                 
			  </td>
			  <td style="background-color: #1F1D3E;color: white;"><select style="background-color: lightgray; color: gray;" name="estado" id="estado">
				  	 <option value = "" selected>Estado</option>
					 <option value = 'Not Started'>No Iniciada</option> 
					 <option value = 'tarea_creada'>Mail Cliente-Tarea Creada</option>
					 <option value = 'In Progress'>En Progreso</option>
					 <option value = 'avance'>Mail Cliente-Avance</option>
					 <option value = 'retencion'>Cliente en Retención</option>
					 <option value = 'Reasignada'>Reasignada</option>
					 <option value = 'Aprobar_Hora_Extra'>Aprobación HH</option>
					 <option value = 'Rendicion'>Rendición</option>
					 <option value = 'movil_solicitado'>Móvil Solicitado</option>
					 <option value = 'gastoexterno'>Gasto Proveedor Externo</option>
					 <option value = 'validar'>Mail Cliente-Validar</option>
					 <option value = 'rechazo_comercial'>Rechazo Comercial</option>
					 <option value = 'Crear_Oportunidad'>Crear Oportunidad</option>
					 <option value = 'sujeta_cobro'>Sujeta a cobro $</option>
					 <option value = 'en_traslado'>En Traslado</option>
					 <option value = 'Completed'>Completada</option>
					 <option value = 'ATRASADA'>ATRASADA</option>
				</select>                                 
			  </td>
			  <td style="background-color: #1F1D3E;color: white;"><select style="background-color: lightgray; color: gray;" name="prioridad" id="prioridad">
				  	 <option value = "" selected>Prioridad</option>
					 <option value = 'URGENTE_E'>Urgente Escalado</option> 
					 <option value = 'URGENTE'>Urgente</option>
					 <option value = 'High'>Alta</option>
					 <option value = 'Low'>Baja</option>
				</select>                                 
			  </td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Ejecutiv@" style="background-color: lightgray" name="ejecutivo" type="text" id="ejecutivo" size="10" value=""></td>
			  <td align="center" style="background-color: #1F1D3E;color: white;">
				<input style="background-color: lightgray; color:  gray; font-size: 12px;" type="reset" value="Limpiar" />&nbsp;
			    <input style="background-color: lightgray; font-size: 12px;" type="submit" value="Buscar Tareas" /
			  </td>
			</tr>
		  </tbody>
		</table>
	</form>                             
</body>    
</html>
