<?php
require_once __DIR__ . '/session_config.php';
// ... tu código de la página ...
if(!$_SESSION['loggedin']) {
	$_SESSION['url_origen'] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	header("location: http://intranet.tnasolutions.cl/login/logout.php");
	echo "UPs, ha ocurrido un error. La p叩gina que busca no se encuentra.";
	exit();
}

//if(!$_SESSION['TNA']) header("location: http://intranet.tnasolutions.cl/cdr/");
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
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

<META NAME="author" CONTENT="TNA Solutions">
<META NAME="subject" CONTENT="TNA SOlutions, Transportes">
<META NAME="Description" CONTENT="TNA SOlutions, Dise単o, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
<META NAME="Classification" CONTENT="TNA Solutions, Dise単o, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
<META NAME="Keywords" CONTENT="TNA Solutions, Dise単o, Seguridad, Informatica, Desarrollo, Sistemas, Redes, Aplicaciones, Web, servidor, computacion, email">
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
<style>
div.scroll {
	height: 530px;
	}
a:hover, a:active {
	background-color: red;
}
.pseudolink { 
   color:blue; 
   text-decoration:underline; 
   cursor:pointer; 
 }
</style>
<!--script type="text/javascript" src="../js/IP_generalLib.js"></script-->

</head>
<body onload="selectUser()">
<?php
	require('includes/config.php');
	require('includes/functions.php');
	$id = $_GET['id'];
	if($id > 1) {
		$query = "select app.* from clientes cli ";
		$query .= "join app_x_cliente cli_app on (cli.id = cli_app.id_clie) ";
		$query .= "join app on (cli_app.id_app = app.id_app)";
		$query .= "where cli.id = " . $id;
	} 
	$result = mysqli_query($conn, $query);
	if (mysqli_num_rows($result) > 0) {

	if(empty($_GET["save"])){
		$save_user = "";
	}else{
		$save_user = $_GET["save"];
	}
	if ($save_user  == "true"){
		
		echo '<script type="text/javascript">
			alert("Usuario actualizado satisfactoriamente.");
			//var old_url = window.location.href;
			//var new_url = old_url.substring(0, old_url.indexOf("?"));
			//window.location.href = new_url;
		</script>';


	}
?>
<div class="scrollxx">
<form action="actualiza_app_x_cliente_v.php" method="post" id="formUsersV">

	<table class="xredondo">
		<tr>
			
			<td style="text-align: left; font-size: 16px;">Usuario: 	<?php 
				echo $_GET["userName"];
            ?>
			<td style="text-align: left; font-size: 16px;"></td>
			</td>
			<td></td>	
			<td></td>	
			<td></td>		
		</tr>
		<tr>
			<td style="text-align: left; font-size: 14px;">Selecciona Todas las App: <input name="todas" type="checkbox" onclick="toggle(this)" /></td>
			<td>&nbsp;
					
		    </td>
			
			<td></td>
			<td></td>
			<td></td>	
		</tr>
	</table>

	<table class="xredondo">

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
	$ptr = 0;
	$ptr2 = 0;
	$seleccionado = Array();
	while($fila = mysqli_fetch_assoc($result)){ 
		$ptr ++;
		$ptr2 ++;
		$seleccionado[$ptr2] = $fila['id_app'];
		if ($ptr == 1) echo "<tr>"; 
		if($fila['id_app']==1) $self = ",'_self'"; else $self = "";
		//echo "<td><input type=\"checkbox\" value=\"{$seleccionado[$ptr2]}\" name=\"apps[]\"> </td>";
		echo '<td><table class="xbox_app"><div>
				<tr>
					<td><div align="left"><input type="checkbox" id="app_'.$seleccionado[$ptr2].'" value="'.$seleccionado[$ptr2].'" name="apps[]"></div> </td>
					<td><div align="right"><a href="'.$fila['url'].'" target="_self">'.$fila['nombre'].'</a></div></td>
				</tr>
				<tr>
					<td colspan="2"><div align="center" class="pseudolink" onclick="window.open(\''.$fila['url'].'\''.$self.')"><img class="Redonda"  src="data:image/jpeg;base64,'.base64_encode( $fila['icono'] ).'" /></div></td>
				</tr>	
		</table></div></td>';
		if($ptr == 10 ) {
			$ptr = 0;
			echo "</tr>";
		} 
	}
	
	echo "<input type='hidden' name='apps_id' value=' ". base64_encode(serialize($seleccionado)) . "'>";
	echo "<input type='hidden' name='id_clie' id='id_clie' value=' ". $id  . "'>";
	echo "<input type='hidden' name='userName' id='userName' value=' ". $_GET['userName']  . "'>";
	//echo "<input type='hidden' name='array' value='" .htmlspecialchars(json_encode($seleccionado)). "'>";
	?>
	</tbody>
	</table>

	<table class="xredondo">
		<tr>
			<td style="text-align: left;">				
				<!--input type="submit" name="submit" value="Guardar"  style="width: 325px;"/-->
				<button type="button" style="background: #ffec64; width: 325px;" onclick="validate();">Guardar</button>
			</td>
			<td>
				<button type="button" style="background: #ffec64; width: 325px;" onclick="back();">Regresar</button>
		    </td>
			<td>&nbsp;
				
		    </td>
			<td>&nbsp;
				
		    </td>
			<td>&nbsp;
				
		    </td>
			
		</tr>
	</table>
		
	</form>
</div>
<?php
} else { die("Error: No hay datos en la tabla {$dbtable}."); }
mysqli_free_result($result);
mysqli_close($conn);

?>

<script language="JavaScript">
function toggle(source) {
  console.log('entre');
  var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
            checkboxes[i].checked = source.checked;
    }
}

function clearApps(){
	var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;
    }
}

function getParamURL(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
}


function selectUser(){

    
	var id_clie = getParamURL("id");

	if (id_clie < 2){
		return;
	}
	clearApps();
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {

			try {

				var objJson = JSON.parse(this.responseText);
				var strJson = this.responseText;

				console.log(objJson);
				console.log(typeof(objJson));
				for (var i in objJson.id_apps){
					//console.log(objJson.id_apps[i]);
					document.getElementById("app_"+ objJson.id_apps[i]+ "").checked = true;
				}
			}
			catch (e) {
				console.log(e);
			}
		}
	};
	xhttp.open("POST", "api/get_apps_v.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("id_clie=" + id_clie);

}

function validate(){	
	//alert("Revisar \"angular autovalidate\" ");
	document.getElementById("formUsersV").submit();
	//return true;
}

function back(){
	window.location = "formulario.php";
}

</script>
</body>
</html>