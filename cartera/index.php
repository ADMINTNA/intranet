<?PHP include_once("functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <?php include_once("../meta_data/meta_data.html"); ?>
<title>Buscador Cartera iContel</title>
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
                                <th align="center" style="font-size: 20px;">Buscador de Cartera en Sweet</th>
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
                                  <td width="">Cliente</td>
                                  <td><input name="cliente" type="text" id="numero" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td width="">Tipo</td>
                                  <td><input name="tipo" type="text" id="numero" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td width="">Estado</td>
                                  <td><input name="estado" type="text" id="asunto" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td>Dirección</td>
                                  <td><input name="direccion" type="text" id="cliente" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td>Ciudad<br></td>
                                  <td><input name="ciudad" type="text" id="usuario" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td>Ejecutivo</td>
                                  <!--td><input name="ejecutivo" type="text" id="usuario" size="20" value=""></td-->
                                  <td><?PHP generarSelectVendedores(); ?></td>
                                </tr>
                                <tr>
                                  <td>Contacto</td>
                                  <td><input name="contacto" type="text" id="usuario" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td>Solo Empresas <br>(y un contacto)</td>
                                  <td><input name="sincontactos" type="checkbox" id="sincontactos" checked /></td>
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
