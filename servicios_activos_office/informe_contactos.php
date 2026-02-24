<?php   
	session_start();
    include_once("includes/config.php");
 
 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<?PHP include_once("../meta_data/meta_data.html"); ?>   
  <title>Servicios Activos iContel Telecomunicaciones</title>
   <style type="text/css">
   	html, body, div {
   		margin:0;
   		padding:0;
   		height:100%;
   		margin-left: 0px;
   		margin-top: 0px;
   		margin-right: 0px;
   		margin-bottom: 0px;
   	}
    table{
           border: none;
           color: #1F1D3E;
           color: white;
           font-size: 15px;
           border-collapse: collapse;
           background-color: #19173C;
           border-collapse: collapse;

       }   
   	iframe {
        border: none;
        border-collapse: collapse;
        padding: 0;
        margin: 0;
        display:block; 
        width:100%;  
        height: 90%;
    }
        footer {
          background-color: white;
          position: absolute;
          bottom: 0;
          width: 100%;
          height: 25px;
          color: gray;
          font-size: 12px;
        }
        /* unvisited link */
        a:link {
          color: darkslategrey;
        }

        /* visited link */
        a:visited {
          color: white;
        }

        /* mouse over link */
        a:hover {
          color: darkgrey;
          font-size: 20px;
          font-weight: bold;
        }

        /* selected link */
        a:active {
            color: blue;        
	    }		
  </style>
  </head>
  <body>
   <table align="center" border="0" width="100%" bgcolor="#1F1D3E">
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <th width="200"  valign="top" align="left"><img src="images/logo_icontel_azul.jpg"  height="60" alt=""/></th>
          <td>
              <table width="100%" height="100%" border="0" bgcolor="#1F1D3E">
                  <tr>
                      <th colspan="2" align="center" style="font-size: 20px;">Contactos por cliente con Servicios Activos</th>
                  </tr>
                  <tr style="color: white;background-color: #1F1D3E;">
                      <td align="center" style="font-size: 12px;">(click sobre los ti&aacute;tulos para ordenar)</td>
                      <td align="right" bgcolor="#1F1D3E"><a href="index.php"><img src="../images/volver_azul.png" width="30" height="30" alt=""/></a></td>
                  </tr>
              </table>
          </td>    
        </tr>
    </table>
  <iframe style="margin: 0 0 0 0;" src="sort_contactos/tabla.php"></iframe>
   </body>
    <?PHP echo $footer;?>   
</html>
