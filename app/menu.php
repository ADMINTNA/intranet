<?php
//=========================================================
// /intranet/app/menu.php
// Index principal del módulo app
//=========================================================

session_name('icontel_intranet_sess');
session_start();

/// ... tu código de la página ...
error_reporting(E_ALL);
if(!$_SESSION['loggedin']) {
	$_SESSION['url_origen'] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	header("location: http://intranet.tnasolutions.cl/login/logout.php");
	echo "UPs, ha ocurrido un error. La página que busca no se encuentra.";
	exit();
}
$update = 0;
if($_POST['check'] and $_POST['check'] > 1) {
	$update = 1;
	$query_update = 'UPDATE `app_x_cliente` SET `checked` = 0 WHERE `app_x_cliente`.`id_clie` = '.$_POST['id_clie'].' and  `app_x_cliente`.`id_app` = '.$_POST['check'];
}
//if(!$_SESSION['TNA']) header("location: http://intranet.tnasolutions.cl/cdr/");
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?PHP include_once("../meta_data/meta_data.html"); ?>       
<title>App en iConTel</title>
<LINK href="css/app.css" rel="stylesheet" type="text/css">
<style>	
div.scroll {
	height: 530px;
	}
a:linkx, a:visitedx {
	-moz-box-shadow:inset 0px 1px 0px 0px #fff6af;
	-webkit-box-shadow:inset 0px 1px 0px 0px #fff6af;
	box-shadow:inset 0px 1px 0px 0px #fff6af;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ffec64), color-stop(1, #ffab23));
	background:-moz-linear-gradient(top, #ffec64 5%, #ffab23 100%);
	background:-webkit-linear-gradient(top, #ffec64 5%, #ffab23 100%);
	background:-o-linear-gradient(top, #ffec64 5%, #ffab23 100%);
	background:-ms-linear-gradient(top, #ffec64 5%, #ffab23 100%);
	background:linear-gradient(to bottom, #ffec64 5%, #ffab23 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffec64', endColorstr='#ffab23',GradientType=0);
	background-color:#ffec64;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #ffaa22;
	display:inline-block;
	cursor:pointer;
	color:#333333;
    font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
	font-size:12px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	text-shadow:0px 1px 0px #ffee66;
}
body {
    margin-left: 0px;
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
    padding: 0px;
}
a:hover, a:active {
	background-color: lightblue;
}
.pseudolink { 
   color:blue; 
   text-decoration:underline; 
   cursor:pointer; 
 }
	
</style>
<script type="text/javascript" src="../js/IP_generalLib.js"></script>
</head>
<body>
<form action="?" method="post">		

<?php	
	require('includes/config.php');
	require('includes/functions.php');	
	//$id = 1;
	$id = $_SESSION['id'];
	if($_SESSION['TNA']) { 
		$query = "SELECT * FROM app order by nombre ASC"; 
	} else{
		$query = "select app.* from clientes cli ";
		$query .= "join app_x_cliente cli_app on (cli.id = cli_app.id_clie) ";
		$query .= "join app on (cli_app.id_app = app.id_app) ";
		$query .= "where cli.id = " . $id . " and cli_app.checked = 1 order by app.nombre ASC ";

	}
	if($update)	{
		$update=0;
		$result = mysqli_query($conn, $query_update);
	} 
	$result = mysqli_query($conn, $query);
?>
<div class="scrollx">
	<table class="xredondo">
	<tr>
	    <td height="5" colspan="5" align="left" style="font-size: 12px; text-align: left;"><b><?php echo "Usuario: " . $_SESSION["name"] ?></b> [<a href="../login/logout.php">Salir</a>]</td>
	    <td colspan="5" align="left" style="font-size: 12px;"><a href="menu_no_visibles.php" alt="Aplicaciones No Visibles"> 
	        <img src="images/not-visible.png"
            alt="Aplicaciones No Visibles"
            width="20"
            height="20">
        </td>
	</tr>	
	<tr>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>		
		<th></th>
	</tr>
	<?php
	if (mysqli_num_rows($result) > 0) {
	
	$ptr = 0;
	while($fila = mysqli_fetch_assoc($result)){ 
		$ptr ++;
		$app = $fila['id_app'];
		if ($ptr == 1) echo "<tr>"; 
		if($fila['id_app']==1) $self = ",'_self'"; else $self = "";
		echo '<td><table class="xbox_app"><div>
				<tr>
					<td width="3px" align="left">
					<input type="checkbox" name="check" value="'.$app.'"  onchange="this.form.submit()">
					<input type="hidden" value="'.$id.'" name="id_clie" />
					</td>
					<td>
					    <div align="center">'.$fila['nombre'].'</div>
					</td>
				</tr>
				<tr>
					<td colspan = "2"><div align="center" class="pseudolink" onclick="window.open(\''.$fila['url'].'\''.$self.')"><img class="Redonda"  src="data:image/jpeg;base64,'.base64_encode( $fila['icono'] ).'" /></div></td>
				</tr>	
		</table></div></td>';
		if($ptr == 10 ) {
			$ptr = 0;
			echo "</tr>";
		} 
	} 
	?>
<?php
if($_SESSION['TNA']){ ?>	
	<a href="formulario.php">Administración de Usuarios</a>
<?php } ?>		
<?php
} else { ?>
	<tr><td colspan="2"><h1> No hay Aplicaciones en este estado.</h1></td>
		<td colspan="8"> </td>
	</tr>
</tbody>
</table>
</div>	
<?php
	   
}
mysqli_free_result($result);
mysqli_close($conn); ?>
</form>		
</body>
</html>