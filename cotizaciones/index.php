<?PHP include_once("./includes/functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <?php include_once("../meta_data/meta_data.html"); ?>
<title>Cotizaciones iContel</title>
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
<div align="center">
   <table>
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <td valign="top" rowspan="2"><img src="./images/logo_icontel_azul.jpg"  height="115" alt=""/></td>
          <td width="" colspan="1" rowspan="1" valign="top" style="border: none">
             <table align="center" width="100%" style="vertical-align: top;" border="0" >
                  <!-- Titulo del menú o informe -->
                  <tr style="background-color: #1F1D3E;color: white;">  
                      <td>
                          <table width="100%">
                              <tr>
                                <th align="center" style="font-size: 20px;">Buscador de Cotizaciones en Sweet</th>
                              </tr>
                          </table>
                      </td>
                  </tr>
                  <!-- FIN Titulo del menú o informe -->  
                  <tr align="center">
                     <td >
                     <!-- Contenido Principal del menú o informe -->     
	 <form action="./busqueda_session.php" method="post" target="_blank">
		<table border="0" style="border-style: solid solid solid solid; border-color: dimgrey; font-size: small;">
		  <tbody>
			<tr>
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
			  </td>
			</tr>
		  </tbody>
		</table>
	</form>                             
                     <!-- FINContenido Principal del menú o informe -->                                            
                     </td> 
                  </tr>
             </table> 
          </td>   
        </tr>
        <tr>
          <td height="20" colspan="2" align="right" bgcolor="#1F1D3E"  style="color: white; font-size: 12px;"> Selección Múltiple excepto N°</td>
        </tr>
        <tr style="background:#CFCFCF;">
          <td height="10" colspan="2"></td>
        </tr>
    </table> 
   </div>
   </body>    
</html>
