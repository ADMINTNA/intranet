<?php 
    $produ_id   = $_GET["produ_id"];
    $account_id = $_GET["account_id"];
    $servicio   = sweet_query_product($produ_id);
    $select     = sweet_query_account_contact($account_id);
    $servicio["proveedor"]  = normaliza_proveedor($servicio["proveedor"]); // normaliza proveedor caso con proveedor cotizaciones
?>    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <?php include_once("../meta_data/meta_data.html"); ?>
<title>Crea nuevo Caso iContel</title>
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
    .input_read_only {
        background-color: #1F1D3E;
        color: white;
        border: none
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
   <table border="1">
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <td valign="top" rowspan="2"><img src="https://intranet.icontel.cl/calls/images/logo_icontel_azul.jpg"  height="115" alt=""/></td>
          <td width="" colspan="1" rowspan="1" valign="top" style="border: none">
             <table align="center" width="100%" style="vertical-align: top;" border="1" >
                  <!-- Titulo del menú o informe -->
                  <tr style="background-color: #1F1D3E;color: white;">  
                      <td>
                          <table width="100%">
                              <tr>
                                <th align="center" style="font-size: 20px;">Crea Caso en Sweet</th>
                              </tr>
                          </table>
                      </td>
                  </tr>
                  <tr align="center">
                     <td >
                          <form method="post" target="_self" onsubmit='disableSubmitButton();'>
                             <!--------------   variables POST invisibles ---------------->
                             <input type="hidden" id="account_id" name="account_id" 
                                    value="<?PHP echo $account_id; ?>">
                             <input type="hidden" id="servicio_afectado" name="servicio_afectado" 
                                    value="<?PHP echo $servicio['nombre']; ?>">                             
                             <input type="hidden" name="proveedor" id="proveedor" size="30" 
                                    value="<?PHP echo $servicio['proveedor']; ?>">
                             <input type="hidden" name="codigo_servicio" id="codigo_servicio" size="30" 
                                    value="<?PHP echo $servicio['codigo_servicio']; ?>">
                             <input type="hidden" name="fecharesol" id="fecharesol" size="30" 
                                    value="<?PHP echo $fecha_est_Resolucion; ?>">
                             <input type="hidden" name="estado" id="estado" size="30" value="Open">
                             <input type="hidden" name="responsable" id="responsable" size="30" value="validando"> 
                             <input type="hidden" name="numero_ticket" id="numero_ticket" size="30" value="N/A"> 
                             <input type="hidden" name="h_sin_servicio" id="h_sin_servicio" size="30" value="0"> 
                             <!------------------ formulario -------------------------------->
                            <table border="1" align="center">
                              <tbody>
                                <tr>
                                <tr>
                                  <td width="">Asunto</td>
                                  <td colspan="3"><input  name="asunto" type="text" id="asunto" size="88"></td>
                                </tr>
                                <tr>
                                <tr>
                                  <td width="">Contacto</td>
                                  <td><?PHP echo $select; ?></td>
                                 <td>Prioridad<br></td>
                                  <td>
                                    <select name="prioridad" id="prioridad">
                                         <option value = 'P1'>Alta</option>
                                         <option value = 'P2'>Media</option> 
                                         <option value = 'P3' selected>Baja</option>
                                    </select>   
                                  </td>
                                </tr>
                                <tr>
                                  <td>Horario<br></td>                                    
                                  <td> <input type="radio" id="horario" name="horario" value="horario_habil" 
                                              <?PHP if($habil) echo "checked"; ?> > Habil 
                                       <input type="radio" id="horario" name="horario" value="fuera_horario"
                                              <?PHP if(!$habil) echo "checked"; ?> > Fuera de Horario
                                  </td>
                                  <td>Caso Tipo<br></td>
                                  <td>
                                    <select name="casotipo" id="casotipo">
                                         <option value = 'continuidad_operacional' selected>Continuidad Operacional</option>
                                         <option value = 'sujeto_cobro'>Sujeto a Cobro</option> 
                                         <option value = 'termino_servicio'>Término de Servicio</option>
                                    </select>                                 
                                  </td>
                                </tr>
                                <tr>
                                  <td>Categoría<br></td>
                                  <td>
                                    <select name="categoria" id="categoria">
                                        <option value = 'Cableado'>Cableado</option>
                                        <option value = 'Enlace'>Enlace</option> 
                                        <option value = 'enlace_caido'selected>Caida de Enlace</option>
                                        <option value = 'facturacion'>Facturacion</option>
                                        <option value = 'Fuera_de_Horario'>Fuera de Horario</option>
                                        <option value = 'Hosting'>Hosting / Correos</option>
                                        <option value = 'Nuevo_requerimiento'>Nuevo requerimiento / oportunidad</option>
                                        <option value = 'Otros'>Otros</option>
                                        <option value = 'Soporte'>Soporte</option>
                                        <option value = 'Soporte_contrato_mensual'>Soporte contrato mensual</option>
                                        <option value = 'Sujeto_a_cobro'>Sujeto a cobro</option>
                                        <option value = 'Telefonia'>Telefonia</option>                                         
                                        <option value = 'termino_contrato'>Termino de contrato</option>
                                    </select>
                                  </td>
                                  <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr style="background-color: #1F1D3E;color: white;">  
                                  <td colspan="4" align="left">Descripción:<br>
                                      <textarea id="descripcion" name="descripcion" rows="5" cols="80"><?PHP echo $hoy." ".$usuario.": "; ?></textarea></td>
                                </tr>
                                <tr style="background-color: #1F1D3E;color: white;">  
                                  <td colspan="2" align="center"><input style="font-size: 10px;" type="reset" value="Limpiar" /></td>
                                  <td colspan="2" align="center"><input style="font-size: 12px;" type="submit" id="crear_caso" name="crear_caso" value="Crear Caso" onClick="this.form.submit(); this.disabled=true; this.value='Creando Caso... '; " /></td>     
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
        <tr style="background:#CFCFCF;">
          <td height="10" colspan="2"></td>
        </tr>
    </table> 
   </div>
   </body>    
</html>