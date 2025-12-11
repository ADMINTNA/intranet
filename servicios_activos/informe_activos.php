<?php
	if(!isset($_POST["submit"])) { die("Ha ocurrido un error inesperado. Comuníquese con el Administrador de Sistemas."); }
	session_start();
	include_once("includes/config_activos.php");
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<?PHP include_once("../meta_data/meta_data.html"); ?>   
  <title>Servicios Activos iContel Telecomunicaciones</title>
  <link rel="stylesheet" href="css/informe.css"> 		
  </head>
  <body>
   <table align="center" border="0" width="100%" bgcolor="#1F1D3E">
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <th width="200"  valign="top" align="left"><img src="images/logo_icontel_azul.jpg"  height="60" alt=""/></th>
          <td>
              <table width="100%" height="100%" border="0" bgcolor="#1F1D3E">
                  <tr>
                      <th colspan="2" align="center" style="font-size: 20px;">Informe de Servicios Activos por Cliente</th>
                  </tr>
                  <tr style="color: white;background-color: #1F1D3E;">
                      <td align="center" style="font-size: 12px;">(click sobre los ti&aacute;tulos para ordenar)</td>
                    </tr>
              </table>
          </td>    
        </tr>
    </table>
  <iframe style="margin: 0 0 0 0;" src="./sort _activos/tabla.php" ></iframe>
  <?php include_once("../footer/footer.php");?>
  </body>
   <?PHP echo $footer;?>   
</html>
