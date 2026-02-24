<?php

include "config.php";
//date_default_timezone_set("America/Santiago");
      // activo mostrar errores
     // error_reporting(E_ALL);
     // ini_set('display_errors', '1');

$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
require_once __DIR__ . '/../session_core.php';
echo $select_query =  $_SESSION["query"]." order by ".$columnName." ".$sort." ";
$conn = DbConnect("tnasolut_sweet"); 
$result = mysqli_query($conn,$select_query);
$html = '';
$ptr = 0;
$url = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DOpportunities%26action%3DDetailView%26record%3D"; 
while($row = mysqli_fetch_array($result)){
	$ptr ++;    
	  switch ($row["estado"]){    
	  case "1 Escalado Urgente":
		$tr = '<tr style="color: red" >';
		break;
	  case "2 Aceptadado, listo para Instalar":
		$tr = '<tr style="color: orangered" >';
		break;
	  case "3 Generar NV":
		$tr = '<tr style="color: orange" >';
		break;
	  case "Cotizar":
		$tr = '<tr style="color: green" >';
		break;
	  default:
		$tr .= '<tr>';                     
	  }   
	?>
	<?PHP echo $tr; ?>
		<td><?php echo $ptr; ?></td>
		<td><?PHP echo $row["numero"]; ?></td>
		<td><?PHP echo $row["cuenta"]; ?></td>
		<?PHP echo '<td><a target="_blank" href="'.$url.$row["id"].'">'.$row["nombre"].'</a></td>'; ?>
		<td><?PHP echo $row["estado"]; ?></td>
		<td><?PHP echo $row["asignado"]; ?></td>
		<td><?PHP echo $row["f_creacion"]; ?></td>
		<td><?PHP echo $row["f_modifica"]; ?></td>
		<td align='right'><?PHP echo $row["dias"]; ?>&nbsp;&nbsp;</td>
	</tr>
<?php 
}







