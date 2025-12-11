<?php //include_once("../includes/session.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<?PHP include_once("../meta_data/meta_data.html"); ?>    
<title>Menú iConTel SpA</title>
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
	table { 
        width:100%; 
        border:0px solid lightgrey; 
    }
	iframe { 
        display:block; 
        width:100%;  
        border:none; 
    }
</style>
<script>
	parent.document.getElementById('caca').style.display = 'visible';	
</script>	
</head>
<body>
 <iframe src="menu.php" ></iframe>
</body>
</html>
