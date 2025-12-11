
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<head>
<meta charset="UTF-8">
<title>Datos iConTel</title>
</head>

<body>
    <table class="table_alarmas" align="center" border="0" width="80%">
      <tbody>
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <td colspan="9" rowspan="1" valign="top" style="border: none">
             <table class="table_alarmas" width="100%" style="vertical-align: top;" >
               <tbody>
                  <tr style="background-color: #1F1D3E;color: white;">
                    <td width="25%">Empresa</td>
                    <td width="10%">Ejecutiv@</td>
                    <td height="40" width="15%"> Contacto</td>
                    <td width="13%">Teléfono</td>
                    <td width="10%">eMail</td>
                    <td width="18%">Tipo</td>
                    <td align="center" width="22%">Rut Empresa</td>
                    <td align="right">Estado </td>
                  </tr>
                    <?php echo $datos_completos; ?> 
                  <tr style="background:#CFCFCF;">
                    <td height="22" colspan="8"></td>
                  </tr>
               </tbody>      
             </table> 
          </td>   
        </tr>
        <tr>
          <td height="40" colspan="9" align="center" bgcolor="#1F1D3E"  style="color: white;">
              <table width="100%" >
                  <tr >
                      <!--td align="left" bgcolor="#1F1D3E" style="color: white"><a href="https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26action%3DEditView" target="_blank" style="color: lightgray"><img src="../../images/esfera_roja.jpeg" height="20" alt=""/> Crear Caso </a></td-->
                      <td align="center" bgcolor="#1F1D3E" style="color: white">PORTAL DE PAGO</td>
                      <td align="right" bgcolor="#1F1D3E" style="color: white"><a href="https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DOpportunities%26action%3DEditView%26return_module%3DOpportunities%26return_action%3DDetailView" target="_blank" style="color: lightgray"><img src="../../images/esfera_azul.jpeg" height="20" alt=""/> Crear Oportunidad </a></td>
                 </tr>
              </table>
            </td>
        </tr>
        <tr>
          <td colspan="2" align="center" style="background-color:orange;color:white;">Por Vencer</td>
          <td colspan="2" align="center" style="background-color:orangered;color:white;border: none;">Vencido</td>
          <th colspan="5" rowspan="2" scope="row"><a href="<?php echo $dumit_portal; ?>" target="_blank"><?php
              if (!$endummit) echo "NO EN DUEMIT:<br>";
              echo $dumit_portal; ?></a></th>
        </tr>
        <tr>
          <td colspan="2" align="center" style="background-color:orange;color:white;border: none;"><?php echo number_format($dumit_por_vencer); ?></td>
          <td colspan="2" align="center" style="background-color:orangered;color:white;border: none;"><?php echo number_format($dumit_vencida); ?></td>
        </tr>
        <tr align="center" style="background: #CFCFCF;">
            <td colspan="9">
                <table class="table_alarmas" border="0" width="100%" style="vertical-align: top;" >
                    <tr>
                      <th>&nbsp;</th>    
                      <th bgcolor="#1F1D3E" style="color: white">Cant.</th>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Estado</th>                     
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Servicio</th>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Proveedor</th>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Cód.Servicio</th>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Detalles de instalación</h>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Contrato Cliente</h>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Plazo</h>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Meses</h>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">NV</h>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Coti_#</th>
                      <th bgcolor="#1F1D3E" style="color: white" align="left">Opor_#</th>
                      <th bgcolor="#1F1D3E" style="color: white" align="right">Valor</th>
                    </tr>
                     <?PHP 
                        include_once("busca_servicios_activos.php");
                      ?>
                </table>
            </td>
          </tr>
       </tbody>
    </table>
    <br><br>
</body>
</html>
