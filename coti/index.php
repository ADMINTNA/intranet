<?PHP 
	ini_set('log_errors', 'On');
	ini_set('error_log', './error.log');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<?PHP include_once("../meta_data/meta_data.html"); ?>   
	 
<title>Distribución de Cotizaciones</title>
<style type="text/css">
	html, body, div, iframe {
		padding:0;
		height:100%;
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
		background-color: white;
	}
	iframe{
		padding:0;
		height:84%;
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
		display:block; 
		width:100%;  
		border:none;
	}
	footer{
		background-color: #1F1D3E; 
		color: #5D6D7E; 
		font-size: 12px; 
		font-family: Gotham, "Helvetica Neue", Helvetica, Arial, "sans-serif";
	}
 	footerx {
		background-color: #1F1D3E; 
		color: #5D6D7E; 
		font-size: 12px; 
		font-family: Gotham, Helvetica Neue, Helvetica, Arial, sans-serif;
	  position: absolute;
	  bottom: 0px;
	  width: 100%;
	  height: 30px;
	}	
	table { 
		width:100%; 
		border:0px solid lightgrey; 
	}
</style>
</head>
<body>
	 <?PHP include_once("./include/header.php"); ?>
	 <iframe src="rebote.php"></iframe>
     <footer align="center"><br>
			&#9786; &#169;&#174;&#8482; Copyright <span id='Year'></span><b> iConTel </b>- &#9742; +56 2 2840 9988 - &#9993; contacto@icontel.cl - &#x1F3E0; Badajoz 45, piso 17, Las Condes, Santiago, Chile.
     </footer>	    	
	 <script type="text/javascript"	>
		var d = new Date(); 
		document.getElementById("Year").innerHTML = d.getFullYear();
	 </script>		 
</body>
</html>
