<?php 
include_once("../session.php");
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<?PHP include_once("../meta_data/meta_data.html"); ?>        
<title>Eventos iConTel SpA</title>
   <style type="text/css">
   	html, body, div, iframe {
   		margin:0;
   		padding:0;
   		height:100%;
   		margin-left: 0px;
   		margin-top: 0px;
   		margin-right: 0px;
   		margin-bottom: 0px;
   	}
   	table { width:100%; border:0px solid lightgrey; }
   	iframe { display:block; width:100%;  border:none; }
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
  </head>
  <body>
		<iframe src="eventos.php"></iframe>';
  </body>
<footer align="center">
	℗®™&copy; Copyright <span id="Year"></span><b> TNA Solutions </b>- <a href="tel:228409988">☎+56 2 2840 9988</a> - <a href="mailto: contacto@tnasolutions.cl?subject=Contacto desde web Tarifas Telefonicas.">📧contacto@tnasolutions.cl</a> - 🏠Badajoz 45, piso 17, Las Condes, Santiago, Chile.<br> 
	<script type="text/javascript"	>
		var d = new Date(); 
		document.getElementById("Year").innerHTML = d.getFullYear();
	</script>	
</footer>	
	
</html>
