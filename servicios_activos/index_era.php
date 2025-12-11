<?php include_once("config.php");?>
<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 	<?PHP include_once('../meta_data/meta_data.html'); ?>
    <title>iContel Servicios Activos por Cliente</title>
    <link href='./css/style.css' rel='stylesheet' type='text/css'>
    <script type="text/javascript">
     function clickMe(){
        var result ="<?php recrea_base_servicios_activos(); ?>"
        window.location.reload();
     }
     </script>
    </head>
    <!--body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" -->
    <body>
	<?php include_once("contenido.php");?>
	<?php include_once("../footer/footer.php");?>
    </body>   
</html>

