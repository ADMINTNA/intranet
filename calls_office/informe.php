<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<head>
<meta charset="UTF-8">
<title>Documento sin título</title>
</head>

<body>
    <table class="table_alarmas" align="center" border="2">
      <tbody>
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <td valign="top" rowspan="2"><img src="../images/logo_icontel_azul.jpg"  height="115" alt=""/></td>
          <td colspan="8" rowspan="1" valign="top" style="border: none">
             <table class="table_alarmas" width="100%" style="vertical-align: top;" >
               <tbody>
                  <tr style="background-color: #1F1D3E;color: white;">
                    <td>Fecha / Hora</td>
                    <td height="40"> Llamada desde</td>
                    <td>Empresa</td>
                    <td height="40"> Contacto</td>
                    <td>Teléfono</td>
                    <td>Tipo</td>
                    <td align="right">Rut Empresa</td>
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
          <td height="40" colspan="7" align="center" bgcolor="#1F1D3E"  style="color: white;">PORTAL DE PAGO</td>
        </tr>
        <tr>
          <td colspan="2" align="center" style="background-color:orange;color:white;">Por Vencer</td>
          <td colspan="2" align="center" style="background-color:orangered;color:white;border: none;">Vencido</td>
          <th colspan="5" rowspan="2" scope="row"><a href="<?php echo $dumit_portal; ?>" target="_blank"><?php echo  $dumit_portal; ?>c</a></th>
        </tr>
        <tr>
          <td colspan="2" align="center" style="background-color:orange;color:white;border: none;"><?php echo number_format($dumit_por_vencer); ?></td>
          <td colspan="2" align="center" style="background-color:orangered;color:white;border: none;"><?php echo number_format($dumit_vencida); ?></td>
        </tr>
        <tr align="center" style="background: #CFCFCF;">
          <td colspan="1">Cant.</td>
          <td colspan="1" align="left">Servicio</td>
          <td colspan="1" align="left">Proveedor</td>
          <td colspan="1" align="left" width="40">Cód.Servicio</td>
          <td colspan="2" align="left">dirección de Instalación</td>
        </tr>
         <?PHP include_once("busca_servicios_activos.php"); ?>
       </tbody>
    </table>
    <br><br>
</body>
</html>
