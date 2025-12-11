<?PHP include_once("./includes/functions.php"); ?>
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
                                <th align="center" style="font-size: 20px;">Buscador de Oportunidades en Sweet</th>
                              </tr>
                          </table>
                      </td>
                  </tr>
                  <!-- FIN Titulo del menú o informe -->  
                  <tr align="center">
                     <td >
                     <!-- Contenido Principal del menú o informe -->     
                         <form action="busqueda_session.php" method="post" target="_blank">
                            <table border="0" align="center">
                              <tbody>
                                <tr>
                                  <td align="center">Sólo</td>
                                  <td width="">Oportunidad Número</td>
                                  <td><input name="numero" type="text" id="numero" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">1</td>
                                  <td width="">Asunto</td>
                                  <td><input name="asunto" type="text" id="asunto" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">2</td>
                                  <td>Cliente</td>
                                  <td><input name="cliente" type="text" id="cliente" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">3</td>
                                  <td>Usuario<br></td>
                                  <td><input name="usuario" type="text" id="usuario" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">3</td>
                                  <td>Estapa Ventas<br></td>
                                  <td>
                                    <select name="estado" id="estado">
                                         <option value = ''>&nbsp;</option>                                         
                                         <option value = 'Prospecting'>00 Prospecto</option> 
                                         <option value = 'Levantamiento'>01 Levantamiento</option>
                                         <option value = 'proyectodemo'>02 Proyecto DEMO</option>
                                         <option value = 'esperafact'>02 Esperando Factibilidad</option>
                                         <option value = 'Value Proposition'>03 Cotizar</option>
                                         <option value = 'ReCotizar'>03 Recotizar</option>
                                         <option value = 'Proposal/Price Quote'>04 Seguimiento</option>
                                         <option value = 'Facturarprepago'>05 Facturar Abono/Prepago</option>
                                         <option value = 'Estatusfinanciero'>05 Verificar Estatus Financiero</option> 
                                         <option value = 'Firmar_Contrato'>05 Firmar Contratoo Anexo Contrato</option>
                                         <option value = 'AceptadoCliente'>05 Aceptado Cliente</option>
                                         <option value = 'Escalado'>05 ESCALADO URGENTE</option>
                                         <option value=  'Pre_Instalacion'>06 Pre-Instalación</option>
                                         <option value=  'pendiente_enlace'>06 Pendiente Enlace / Proveedor</option> 
                                         <option value=  'Proyecto'>07 Instalación</option>
                                         <option value=  'Renovacion'>07 Renovación</option>
                                         <option value=  'Recepcion'>07 Solicitar recepción conforme</option> 
                                         <option value=  'Cerrar_a_Fin_de_Mes'>08 Cerrar a Fin de Mes</option>
                                         <option value=  'Facturacion'>09 :) Instalado - Generar NV</option>
                                         <option value=  'facturar'>10 Listo para Factuar</option>
                                         <option value=  'Facturado_bienvenida'>11 Facturado + Bienvenida</option>
                                         <option value=  'Facturado'>11 Facturado/Cerrado</option>
                                         <option value=  'Waiting'>12 PAUSA En Espera de Cliente</option>
                                         <option value=  'Archivado_Ventas'>12 Archivado Ventas</option>
                                         <option value=  'Needs Analysis'>12 Pausa en Análisis</option>
                                         <option value=  'duplicada_reemplazada'>13 Duplicada / Reemplazada</option> 
                                         <option value=  'Closed Lost'>13 :( Perdido :(  o Descartado Cliente</option>
                                         <option value=  'Dado_de_Baja'>14 :( Servicio Dado de Baja</option>
                                    </select>                                 
                                    </td>
                                </tr>
                                <tr style="background-color: #1F1D3E;color: white;">  
                                  <td colspan="" align="left"><input style="font-size: 10px;" type="reset" value="Limpiar" /></td>
                                  <td align="center"><input style="font-size: 12px;" type="submit" value="Buscar en Sistemas" /></td>
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
          <td height="20" colspan="2" align="right" bgcolor="#1F1D3E"  style="color: white; font-size: 12px;"> Selección Múltiple</td>
        </tr>
        <tr style="background:#CFCFCF;">
          <td height="10" colspan="2"></td>
        </tr>
    </table> 
   </div>
   </body>    
</html>
