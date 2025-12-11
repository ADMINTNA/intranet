<?php
require_once __DIR__ . '/session_config.php';
// ... tu código de la página ...
error_reporting(0);
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
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<link rel="apple-touch-icon" sizes="57x57" href="../favicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="../favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="../favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="../favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="../favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="../favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="../favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="../favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="../favicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="../favicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="../favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="../favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="../favicon/favicon-16x16.png">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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
<META NAME="Publisher" CONTENT="TNA Solutions">
<META NAME="Revisit-After" CONTENT="7 days">
<META NAME="distribution" CONTENT="Global">
<META NAME="Robots" CONTENT="INDEX,FOLLOW">
<META NAME="city" CONTENT="Santiago">
<META NAME="country" CONTENT="Chile">
<meta http-equiv="refresh" content="1800"> 
<title>App en TNA Solutions</title>
<LINK href="css/app.css" rel="stylesheet" type="text/css">
<style type="text/css">	
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
	font-family:Arial;
	font-size:10px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	text-shadow:0px 1px 0px #ffee66;
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
<script type="text/javascript">
function invisibles() {
  window.location = "https://google.cl";
}

</script>
<script type="text/javascript" src="../js/IP_generalLib.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
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
	    <td colspan="2" align="left" style="font-size: 14px; text-align: left;"><b><?php echo "Usuario: " . $_SESSION["name"] ?></b></td>
	    <td align="right" style="font-size: 14px;">
			
<div class="w3-bar w3-black">
	<button style="background-color: lightgray" class="w3-bar-item w3-button" onclick=""><img src="images/not-visible.png"
		alt="Aplicaciones Visibles"
		width="32"
		height="32">
	</button>
	<button  class="w3-bar-item w3-button" onclick=""><img src="images/not-visible.png"
		alt="Aplicaciones No Visibles"
		width="32"
		height="32">
	</button>
</div>			
			
			</td>	    
		<td colspan="7" align="left" style="font-size: 14px;"><a href="menu_no_visibles.php" alt="Aplicaciones No Visibles">
	        <img src="images/not-visible.png"
            alt="Aplicaciones No Visibles"
            width="32"
			height="32"></a>
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
					    <div align="center"><a href="'.$fila['url'].' "target="_self">'.$fila['nombre'].'</a></div>
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