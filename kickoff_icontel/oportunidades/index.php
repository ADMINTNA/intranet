<?PHP 
	include_once("config.php");
    include_once("security_groups.php"); 
	$sg_id = $_GET['sg_id'];
 
	if (isset($_POST['sg']))   { 
		$sg_id   = $_POST['sg'];
		$_SESSION['sg_id'] = $sg_id;
	}
	if (isset( $_GET['sg']))   { 
		$sg_id   =  $_GET['sg']; 
		$_SESSION['sg_id'] = $sg_id;
	}

	if(!isset($sg_id)){
		if(isset($_SESSION['sg_id'])) {
			$sg_id = $_SESSION['sg_id'];
		} else {
			$sg_id   = "a03a40e8-bda8-0f1b-b447-58dcfb6f5c19"; // soporte
			$sg_name = "Soporte Soporte tecnico";
			$_SESSION['sg_id'] = $sg_id;                   
		}
	} 
	$i = 1;
	$cuantos = count($grupos);            
	while ($i <= $cuantos) {
		if($grupos[$i]['id'] == $sg_id) {
			$sg_name=$grupos[$i]['name'];
		}
		$i++;
	}
 	$tabla = "tabla.php?sg_id=".$sg_id;;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<?PHP include_once("../../meta_data/meta_data.html"); ?>   
  <title>Oportunidades iContel Telecomunicaciones</title>
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
	  color: gray;
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
	<script language="JavaScript">
		function autoSubmit() {
			var formObject = document.forms['form_select'];
			formObject.submit();
		}
    </script>  
  </head>
  <body>
   <table align="center" border="0" width="100%">
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <th width="200"  valign="top" align="left"><img src="../images/logo_icontel_azul.jpg"  height="60" alt=""/></th>
          <td>
              <table width="100%" height="100%">
                  <tr>
                      <th align="center" style="font-size: 20px;">Oportunidades en Curso de [<?PHP echo $sg_name; ?>]<br><span style="font-size: 12px;">(click sobre los t&iacute;tulos para ordenar)</span></th>
					  <td align="right" valign="top">
						  <form  action="" method="post" name="form_select" id="form_select">
								<?PHP echo $select; ?> <br>
								<!--input type="submit" value="Submit"-->
						  </form>
					  </td>
                  </tr>
              </table>
          </td>    
        </tr>
    </table>
  <iframe style="margin: 0 0 0 0;" src="<?PHP echo $tabla; ?>" ></iframe>
   </body>
<footer align="center">
âÂ®â¢&copy; Copyright <span id="Year"></span><b> iConTel </b>- <a href="tel:228409988">Ã¢ËÅ½+56 2 2840 9988</a> - <a href="mailto: contacto@tnasolutions.cl?subject=Contacto desde Intranet iConTel.">Ã°Å¸âÂ§contacto@tnasolutions.cl</a> - Ã°Å¸ÂÂ Badajoz 45, piso 17, Las Condes, Santiago, Chile.<br> 
	<script type="text/javascript"	>
		var d = new Date(); 
		document.getElementById("Year").innerHTML = d.getFullYear();
	</script>	
</footer>	
</html>
