<?PHP include_once("../oportunidades/includes/functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <?php include_once("../meta_data/meta_data.html"); ?>
<title>Buscador Oportunidades iContel</title>
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
	 <form action="../oportunidades/busqueda_session.php" method="post" target="_blank">
		<!--table border="0" style="background-color: #1F1D3E;color: white; border-style: solid solid solid solid; border-color: dimgrey; font-size: small;"-->	
				<table border="0" style="border-style: solid solid solid solid; border-color: dimgrey; font-size: small;">
 
		  <tbody>
			<tr>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Oportunidad Nº" style="background-color: lightgray" name="numero" type="text" id="numero" size="14" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Asunto" style="background-color: lightgray" name="asunto" type="text" id="asunto" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Cliente" style="background-color: lightgray" name="cliente" type="text" id="cliente" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><input placeholder="Ejecutiv@" style="background-color: lightgray" name="ejecutivo" type="text" id="ejecutivo" size="10" value=""></td>
			  <td style="background-color: #1F1D3E;color: white;"><select style="width:140px; background-color: lightgray; color: gray; name="estado" id="estado" >
				  	 <option value = "" selected>Etapa de Ventas</option>
					 <option value = 'Prospecting'>00 Prospecto</option> 
					 <option value = 'Levantamiento'>01 Levantamiento</option>
					 <option value = 'proyectodemo'>02 Proyecto DEMO</option>
					 <option value = 'esperafact'>02 Esperando Factibilidad</option>
					 <option value = 'Value Proposition'>03 Cotizar</option>
					 <option value = 'ReCotizar'>03 Recotizar</option>
					 <option value = 'Proposal/Price Quote'>04 Seguimiento</option>
					 <option value = 'Facturarprepago'>05 Facturar Abono/Prepago</option>
					 <option value = 'Estatusfinanciero'>05 Estatus Financiero</option> 
					 <option value = 'Firmar_Contrato'>05 Firmar Contratoo</option>
					 <option value = 'AceptadoCliente'>05 Aceptado Cliente</option>
					 <option value = 'Escalado'>05 ESCALADO URGENTE</option>
					 <option value=  'Pre_Instalacion'>06 Pre-Instalación</option>
					 <option value=  'pendiente_enlace'>06 Pendiente Enlace</option> 
					 <option value=  'Proyecto'>07 Instalación</option>
					 <option value=  'Renovacion'>07 Renovación</option>
					 <option value=  'Recepcion'>07 Solicitar recepción</option> 
					 <option value=  'Cerrar_a_Fin_de_Mes'>08 Cerrar a Fin de Mes</option>
					 <option value=  'Facturacion'>09 :) Instalado - Generar NV</option>
					 <option value=  'facturar'>10 Listo para Factuar</option>
					 <option value=  'Facturado_bienvenida'>11 Facturado + Bienvenida</option>
					 <option value=  'Facturado'>11 Facturado/Cerrado</option>
					 <option value=  'Waiting'>12 PAUSA/En Espera</option>
					 <option value=  'duplicada_reemplazada'>13 Duplicada / Reemplazada</option> 
					 <option value=  'Closed Lost'>13 Perdido/Descartado</option>
					 <option value=  'Dado_de_Baja'>14 Dado de Baja</option>
				</select>                                 
			  </td>
			  <td align="center" style="background-color: #1F1D3E;color: white;">
				<input style="background-color: lightgray; color: gray; font-size: 12px;" type="reset" value="Limpiar" />&nbsp;
			    <input style="background-color: lightgray; font-size: 12px;" type="submit" value="Buscar Oportunidades" /
			  </td>
			</tr>
		  </tbody>
		</table>
	</form>                             
</body>    
</html>
