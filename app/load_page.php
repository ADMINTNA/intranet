<?php 

// include_once("session.php");
if (isset($_POST['pagina'])){
   $url = $_POST['pagina'];
} else {
    echo "ERROR desconocido. Favor comunicarse con Administrador de Sistemas.";
}    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="favicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="author" CONTENT="TNA Solutions">
<META NAME="subject" CONTENT="TNA SOlutions, Transportes">
<META NAME="Description" CONTENT="TNA SOlutions, Diseño, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
<META NAME="Classification" CONTENT="TNA Solutions, Diseño, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
<META NAME="Keywords" CONTENT="TNA Solutions, Diseño, Seguridad, Informatica, Desarrollo, Sistemas, Redes, Aplicaciones, Web, servidor, computacion, email">
<META NAME="Geography" CONTENT="Chile">
<META NAME="Language" CONTENT="Spanish">
<META HTTP-EQUIV="Expires" CONTENT="never">
<META NAME="Copyright" CONTENT="TNA Solutions">
<META NAME="Designer" CONTENT="TNA Solutions">
<META NAME="Publisher" CONTENT="SecureOne">
<META NAME="Revisit-After" CONTENT="7 days">
<META NAME="distribution" CONTENT="Global">
<META NAME="Robots" CONTENT="INDEX,FOLLOW">
<META NAME="city" CONTENT="Santiago">
<META NAME="country" CONTENT="Chile">
<title><?php echo $_POST['titulo']; ?></title>
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
	iframe {
		margin:0;
		padding:0;
		height:55%;
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
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
	
	table { width:100%; border:0px solid lightgrey; }
	iframe { display:block; width:100%;  height: 100%; border:none; }
</style>
</head>
<body>
 	
<?PHP //include_once("eventos/eventos.php"); ?> 	
 <iframe src=<?php echo $url; ?> ></iframe>
<!--footer align="center">
	℗®™&copy; Copyright <span id="Year"></span><b> iConTel </b>- <a href="tel:228409988">☎+56 2 2840 9988</a> - <a href="mailto: contacto@tnasolutions.cl?subject=Contacto desde Intranet de iContel.">📧contacto@tnasolutions.cl</a> - 🏠Badajoz 45, piso 17, Las Condes, Santiago, Chile.<br> 
	<script type="text/javascript"	>
		var d = new Date(); 
		document.getElementById("Year").innerHTML = d.getFullYear();
	</script>	
</footer-->	
	
</body>
</html>
