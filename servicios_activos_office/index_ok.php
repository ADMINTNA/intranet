<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?PHP include_once('../meta_data/meta_data.html'); ?>
		<title>Servicios Activos</title>
		<link rel="stylesheet" href="css/style.css"> 	
		<script type="text/javascript" src="./js/script.js"></script>  
	</head>
	<body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" onLoad="myFunction()" style="margin:0;" >
		<div id="loader"></div>
		<div style="display:none;" id="myDiv" class="animate-bottom">
			<?PHP include_once("config.php"); ?>
			<?php include_once("contenido.php") ; ?>
		</div>
		<?php include_once("../footer/footer.php");?>
	</body>    
	<script>
		var myVar;
		function myFunction() {
		  myVar = setTimeout(showPage, 500);
		}
		function showPage() {
		  document.getElementById("loader").style.display = "none";
		  document.getElementById("myDiv").style.display = "block";
		}
	</script>		
</html>
