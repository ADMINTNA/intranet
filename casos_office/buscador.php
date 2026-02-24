<?PHP include_once("./includes/functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
     <?php include_once("../meta_data/meta_data.html"); ?>
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
    <?php  date_default_timezone_set("America/Santiago"); ?>     
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
                                <th align="center" style="font-size: 20px;">Buscador de Casos en Sweet</th>
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
                                  <td width="">Número de Caso</td>
                                  <td><input name="numero" type="text" id="numero" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">1</td>
                                  <td width="">Fecha Creación Desde</td>
                                  <td><input name="fechadesde" type="text" id="fechadesde" size="20" value=""></td>
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
                                  <td>
                                     <select name="categoria" id="categoria">
                                         <option value = ''>&nbsp;</option> 
                                         <option value = 'Cableado'>Cableado</option> 
                                         <option value = 'Enlace'>Enlace</option>
                                         <option value = 'enlace_caido'>Enlace Caído</option>
                                         <option value = 'facturacion'>Facturación</option>
                                         <option value = 'Fuera_de_horario'>Fuera de Horario</option>
                                         <option value = 'Hosting'>Hosting / Correos</option>
                                         <option value = 'Nuevo_requerimiento'>Nuevo Requerimiento / Oportunidad</option>
                                         <option value = 'Otros'>Otros</option>
                                         <option value = 'Soporte'>Soporte</option>
                                         <option value = 'Soporte_contrato_mensual'>Soporte Contrato Mensual</option>
                                         <option value = 'Sujeto_a_cobro'>Sujeto a Cobro</option>
                                         <option value = 'Telefonia'>Telefonía</option> 
                                         <option value = 'termino_contrato'>Término de Contrato</option>
                                    </select>                                 
                                </tr>
                                <tr>
                                  <td align="center">4</td>
                                  <td>Razón Social</td>
                                  <td><input name="empresa" type="text" id="empresa" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">5</td>
                                  <td>Usuario Asignado</td>
                                  <td><input name="usuario" type="text" id="usuario" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">6</td>
                                  <td>Creado por</td>
                                  <td><input name="creadopor" type="text" id="creadopor" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">7</td>
                                  <td>Proveedor</td>
                                  <td><input name="proveedor" type="text" id="proveedor" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">7</td>
                                  <td>Código de Servicio</td>
                                  <td><input name="codservicio" type="text" id="codservicio" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td align="center">8</td>
                                  <td>Estado</td>
                                  <td>
                                      <label><input type="radio" name="estado" value="cerrados"> Cerrados</label><br>
                                      <label><input type="radio" name="estado" value="abiertos" required checked> Abiertos</label><br>
                                      <label><input type="radio" name="estado" value="todos"> Todos</label>
                                  </td>
                                </tr>
                                  <td align="center">9</td>
                                  <td colspan="2">Antiguedad de Creación</td>
                                </tr>                                  
                                </tr>
                                  <td align="center">10</td>
                                  <td colspan="2">Horas Sin Servicio</td>
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
