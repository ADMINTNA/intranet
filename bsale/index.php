<?PHP 
//=====================================================
// /intranet/bsale/index.php
// Gateway de acceso: redirige según sesión
// Autor: Mauricio Araneda
// Actualizado: 08-11-2025
//=====================================================

include_once(__DIR__ . '/../includes/security_check.php');

    include_once("./includes/config.php");    
    $sql = "call categorias";
    $conn = DbConnect("tnasolut_sweet");
    $result = $conn->query($sql);
    $select = "<select name='categoria' id='categoria'>\n
             <option value = ''>&nbsp;</option>\n";
    if($result->num_rows > 0)  { 
        while($row = $result->fetch_assoc()) {
           $select .= "<option value = '".$row['id']."'>".$row['categoria']."</option> \n";
        }
        $select .= "</select>\n";
    }
    $conn->close(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <?php include_once("../meta_data/meta_data.html"); ?>
<title>Buscador de Productos iContel</title>
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
                                <th align="center" style="font-size: 20px;">Buscador de Productos en Sweet</th>
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
                                  <td width="">Nombre</td>
                                  <td><input name="nombre" type="text" id="nombre" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td>Variante</td>
                                  <td><input name="variante" type="text" id="variante" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td>Descripción</td>
                                  <td><input name="descripcion" type="text" id="descripcion" size="20" value=""></td>
                                </tr>
                                <tr>
                                  <td>Categoría<br></td>
                                  <td><?php echo $select; ?></td>
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
