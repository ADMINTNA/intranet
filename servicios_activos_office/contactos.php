<?php
   include_once("includes/config.php");
   if(isset($_POST["submit"])) {
        $sql ="Select * from recurrencias where 1 ";
        if(isset($_POST["Recurrencia"])) $Recurrencia = genera_condicion($_POST["Recurrencia"], "recurrencia");
        if(isset($_POST["Categoria"])) $Categoria = genera_condicion($_POST["Categoria"], "categoria");
        if(isset($_POST["Producto"])) $Producto = genera_condicion($_POST["Producto"], "producto");
        session_start();
        $_SESSION["query"] = "Select * from recurrencias where 1 " . $Recurrencia . $Categoria . $Producto;
        $_SESSION["query_contactos"] = "Select 		
                    a.name 	                                as empresa, 
                    CONCAT(ct.first_name,' ',ct.last_name)	as contacto,
                    ct.title 								as cargo,
                    ct.lead_source 							as tipo_contacto,
                    ct.phone_mobile 						as celular,
                    ct.phone_work 							as telefono,
                    e.email_address 						as email
                    from recurrencias 				as re
                    LEFT JOIN accounts 				as a  ON a.id	= re.cliente_id
                    LEFT JOIN accounts_contacts 	as co ON a.id	= co.account_id
                    LEFT JOIN contacts				as ct ON co.contact_id = ct.id
                    LEFT JOIN accounts_cstm 		as ac ON ac.id_c = a.id
                    LEFT JOIN email_addr_bean_rel 	as ea ON ea.bean_id = ct.id
                    LEFT JOIN email_addresses		as e  ON e.id = ea.email_address_id 
                    where 1 
                    && !co.deleted 
                    && !ct.deleted 
                    && !ea.deleted 
                    && !e.deleted " . $Recurrencia . $Categoria . $Producto ;
        $_SESSION["goupby"]= " GROUP BY co.id ";
        $_SESSION['orden'] = " ORDER BY `cliente` ASC";
  } else {
      exit();
   };
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
        height: 80%;
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
	  color: white;
	}
	/* visited link */
	a:visited {
	  color: gray;
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
          <th width="200" height="130" valign="top" align="left"><img src="images/logo_icontel_azul.jpg"  height="115" alt=""/></th>
          <td>
              <table width="100%" height="100%" border="0" bgcolor="#1F1D3E">
                  <tr height="90">
                      <th colspan="2" align="center" style="font-size: 20px;">Informe de Servicios Activos por Cliente</th>
                  </tr>
                  <tr style="color: white;background-color: #1F1D3E;">
                      <td align="center" style="font-size: 12px;">(click sobre los ti&aacute;tulos para ordenar)</td>
                      <td align="right" bgcolor="#1F1D3E"><a href="javascript:history.back()"><img src="../images/volver_azul.png" width="30" height="30" alt=""/></a></td>
                  </tr>
              </table>
          </td>    
        </tr>
    </table>
  <iframe style="margin: 0 0 0 0;" src="sort/tabla.php" ></iframe>
   </body>
    <footer align="center">â„—Â®â„¢Â© Copyright <span id="Year"></span><b> iConTel </b>- <a href="tel:228409988">-â˜Ž+56 2 2840 9988</a> - <a href="mailto: contacto@icontel.cl?subject=Contacto desde Intranet iConTel.">- ðŸ“§contacto@icontel.cl</a> - ðŸ Badajoz 45, piso 17, Las Condes, Santiago, Chile.<br> 
	    <script type="text/javascript"	>
            var d = new Date(); 
            document.getElementById("Year").innerHTML = d.getFullYear();
	   </script>	
    </footer>	     
</html>
