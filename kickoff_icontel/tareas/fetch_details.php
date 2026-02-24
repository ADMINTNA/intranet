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
$url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26offset%3D1%26stamp%3D1644666990053569200%26return_module%3DCases%26action%3DDetailView%26record%3D";   
$url_caso = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26action%3DDetailView%26record%3D";      
$url_opor = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DOpportunities%26action%3DDetailView%26record%3D";  
$url_cuenta = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAccounts%26action%3DDetailView%26record%3D";
while($row = mysqli_fetch_array($result)){
	$ptr ++;    
	switch ($row["prioridad"]){    
	case "1 URGENTE ESCALADO":
		$tr = '<tr style="color: red" >';
		break;
	case "2 URGENTE":
		$tr = '<tr style="color: orangered" >';
		break;
	case "3 Alta":
		$tr .= '<tr style="color: orange" >';
		break;
	case "4 Baja":
		$tr = '<tr style="color: green" >';
		break;
	default:
		$tr .= '<tr>';                     
	}   
	?>
	<?PHP echo $tr; ?>
		<td><?php echo $ptr; ?></td>
		<td><?PHP echo $row["prioridad"]; ?></td>
		<td><?PHP echo $row["usuario"]; ?></td>
		<td><?PHP echo $row["estado"]; ?></td>
		<?PHP echo '<td><a target="_blank" href="'.$row["url"].'">'.$row["tarea"].'</a></td>'; 
		switch ($row["origen"]){ 
		case "Cases":
			$url ="";
			$td  = "<td>CASO</td>";
			$td .= '<td><a target="_blank" href="'.$url_caso.$row["origen_id"].'">'.$row["numero"].'</a></td>';                        
			break;
		case "Opportunities":
			$td  = "<td>OPORTUNIDAD</td>";
			$td .= '<td><a target="_blank" href="'.$url_opor.$row["origen_id"].'">'.$row["numero"].'</a></td>';                        
		   break;
		case "Accounts":
			$td  = "<td>CUENTA</td>";
			$td .= '<td><a target="_blank" href="'.$url_cuenta.$row["origen_id"].'">'.$row["numero"].'</a></td>';                        
			break;
		default:
			$td  = "<td> </td>";
			$td .= "<td> </td>";
		}
		echo $td;  
		?>
		<td><?PHP echo $row["f_creacion"]; ?></td>
		<td><?PHP echo $row["f_modifica"]; ?></td>
		<td align='right'><?PHP echo $row["dias"]; ?>&nbsp;&nbsp;</td>
	</tr>
<?php 
}







