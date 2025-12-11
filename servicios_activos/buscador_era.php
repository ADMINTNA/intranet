<?PHP include_once("./includes/functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta name="Keywords" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cablead">
    <meta name="author" content="iConTel S.p.A.">
    <meta name="subject" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado">
    <meta NAME="Classification" content="TNA Solutions, Enlaces, Internet, ISP, WISP, Diseño, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
    <meta name="Geography" content="Chile">
    <meta name="Language" content="Spanish">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/favicon/favicon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta http-equiv="content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Expires" content="never">
    <meta name="Copyright" content="iConTel S.p.A.">
    <meta name="Designer" content="iConTel S.p.A.">
    <meta name="Publisher" content="iConTel S.p.A.">
    <meta name="Revisit-After" content="7 days">
    <meta name="distribution" content="Global">
    <meta name="city" content="Santiago">
    <meta name="country" content="Chile">
    <!-- index para los robots-->
    <meta name="robots" content="index,follow" />
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <!-- OpenGraph metadata-->
    <meta property="og:locale" content="es_LA" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="iContel Telecomunicaciones" />
    <meta property="og:description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta property="og:url" content="https://www.icontel.cl/index.php" />
    <meta property="og:site_name" content="Icontel Telecomunicaciones" />
    <meta property="og:image" content="https://www.icontel.cl/favicon/logo.png" />
    <meta property="fb:admins" content="FB-AppID"/>
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta name="twitter:title" content="iConTel Telecomunicaciones"/>
    <meta name="twitter:site" content="iContel S.p.A."/>
    <meta name="twitter:creator" content="iConTel Telecomunicaciones"/>
    <link rel="canonical" href="https://www.icontel.cl/index.php" />
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.icontel.cl/favicon/favicon-16x16.png">    
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.icontel.cl/favicon/favicon-32x32.png">    
    <link rel="icon" type="image/png" sizes="57x57" href="https://www.icontel.cl/favicon/favicon-57x57.png">
    <link rel="icon" type="image/png" sizes="60x60" href="https://www.icontel.cl/favicon/favicon-60x60.png">
    <link rel="icon" type="image/png" sizes="72x72" href="https://www.icontel.cl/favicon/favicon-72x72.png">
    <link rel="icon" type="image/png" sizes="76x76" href="https://www.icontel.cl/favicon/favicon-76x76.png">
    <link rel="icon" type="image/png" sizes="96x96" href="https://www.icontel.cl/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="114x114" href="https://www.icontel.cl/favicon/favicon-114x114.png">
    <link rel="icon" type="image/png" sizes="120x120" href="https://www.icontel.cl/favicon/favicon-120x120.png">
    <link rel="icon" type="image/png" sizes="144x144" href="https://www.icontel.cl/favicon/favicon-144x144.png">
    <link rel="icon" type="image/png" sizes="152x152" href="https://www.icontel.cl/favicon/favicon-152x152.png">
    <link rel="icon" type="image/png" sizes="180x180" href="https://www.icontel.cl/favicon/favicon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="https://www.icontel.cl/favicon/favicon-192x192.png">
    <link rel="manifest" href="https://www.icontel.cl/favicon/manifest.json">
<title>Buscador Casos iContel</title>
<script type="text/javascript">
function estemes() {
    document.getElementById('fechadesde').value = "<?php echo $estemes_desde; ?>"; 
    document.getElementById('fechahasta').value = "<?php echo $estemes_hasta; ?>"; 
}
function mesanterior() {
    document.getElementById('fechadesde').value = "<?php echo $mesanterior_desde; ?>"; 
    document.getElementById('fechahasta').value = "<?php echo $mesanterior_hasta; ?>"; 
}

</script>     
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
   <table class="table_alarmas" align="center">
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <td valign="top" rowspan="2"><img src="./images/logo_icontel_azul.jpg"  height="115" alt=""/></td>
          <td width="" colspan="1" rowspan="1" valign="top" style="border: none">
             <table class="table_alarmas" width="100%" style="vertical-align: top;" border="0" >
                  <!-- Titulo del menú o informe -->
                  <tr style="background-color: #1F1D3E;color: white;">  
                      <td>
                          <table width="100%">
                              <tr>
                                <th align="center" style="font-size: 20px;">Buscador de Casos en Sweet</th>
                                <th align="right">Orden </th>  
                              </tr>
                          </table>
                      </td>
                  </tr>
                  <!-- FIN Titulo del menú o informe -->  
                  <tr align="center">
                     <td >
                     <!-- Contenido Principal del menú o informe -->     
                         <form action="busqueda_session.php" method="post" target="_blank">
                            <table class="table_alarmas" border="0" align="center">
                              <tbody>
                                <tr>
                                  <td align="center">1</td>
                                  <td width="">Fecha Creación Desde</td>
                                  <td><input name="fechadesde" type="text" id="fechadesde" size="20" value=""></td>
                                  <td rowspan="2"><label><input type="radio" name="ordenar" value="fechacreacion" checked> </label></td>    
                                </tr>
                                <tr>
                                  <td align="center">2</td>
                                  <td>Fecha Creación Hasta</td>
                                  <td><input name="fechahasta" type="text" id="fechahasta" size="20" value=""></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan="1">
                                      <label><input type="radio" name="mes" value="estemes" onclick="estemes();">&nbsp;&nbsp;Este Mes </label>&nbsp;
                                    </td>
                                    <td colspan="1">
                                      <label><input type="radio" name="mes" value="mesant" onclick="mesanterior();">&nbsp;&nbsp;Mes Anterior</label>&nbsp;
                                    </td>
                                    <td></td>
                                </tr>  
                                <tr>
                                  <td align="center">3</td>
                                  <td>Categoría<br></td>
                                  <td><input name="categoria" type="text" id="categoria" size="20" value""></td>
                                  <td><label><input type="radio" name="ordenar" value="categoria"> </label></td>    
                                </tr>
                                <tr>
                                  <td align="center">4</td>
                                  <td>Razón Social</td>
                                  <td><input name="empresa" type="text" id="empresa" size="20" value=""></td>
                                  <td><label><input type="radio" name="ordenar" value="cliente"> </label></td>    
                                </tr>
                                <tr>
                                  <td align="center">5</td>
                                  <td>Usuario Asignado</td>
                                  <td><input name="usuario" type="text" id="usuario" size="20" value=""></td>
                                   <td><label><input type="radio" name="ordenar" value="usuario"> </label></td>    
                                </tr>
                                <tr>
                                  <td align="center">6</td>
                                  <td>Creado por</td>
                                  <td><input name="creadopor" type="text" id="creadopor" size="20" value=""></td>
                                   <td><label><input type="radio" name="ordenar" value="creadopor"> </label></td>    
                                </tr>
                                <tr>
                                  <td align="center">7</td>
                                  <td>Proveedor</td>
                                  <td><input name="proveedor" type="text" id="proveedor" size="20" value=""></td>
                                   <td><label><input type="radio" name="ordenar" value="proveedor"> </label></td>    
                                </tr>
                                <tr>
                                  <td align="center">8</td>
                                  <td>Estado</td>
                                  <td>
                                      <label><input type="radio" name="estado" value="cerrados"> Cerrados</label><br>
                                      <label><input type="radio" name="estado" value="abiertos" required checked> Abiertos</label><br>
                                      <label><input type="radio" name="estado" value="todos"> Todos</label>
                                  </td>
                                    <td><label><input type="radio" name="ordenar" value="estado"> </label></td>    
                                </tr>
                                  <td align="center">9</td>
                                  <td colspan="2">Antiguedad de Creación</td>
                                   <td><label><input type="radio" name="ordenar" value="antiguedad"> </label></td>    
                                </tr>                                  
                                </tr>
                                  <td align="center">10</td>
                                  <td colspan="2">Horas Sin Servicio</td>
                                   <td><label><input type="radio" name="ordenar" value="horas"> </label></td>    
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
   </body>    
</html>
