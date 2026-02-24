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
while($row = mysqli_fetch_array($result)){  
	$ptr ++;    
	switch ($row["prioridad"]){    
	case "P1E":
		$tr = '<tr style="color: red" >';
		break;
	case "P1":
		$tr = '<tr style="color: orangered" >';
		break;
	case "P2":
		$tr = '<tr style="color: orange" >';
		break;
	case "P3":
		$tr = '<tr style="color: green" >';
		break;
	default:
		$tr = '<tr>';                     
	}   
	?>
		<?PHP echo $tr; ?>
			<td><?php echo $ptr; ?></td>
			<td><?PHP echo $row["prioridad_descr"]; ?></td>
			<?PHP echo '<td><a target="_blank" href="'.$url.$row["id"].'">'.$row["numero"].'</a></td>'; ?>
			<td><?PHP echo $row["asunto"]; ?></td>
			<td><?PHP echo $row["estado"]; ?></td>
			<td><?PHP echo $row["categoria"]; ?></td>
			<td><?PHP echo $row["usuario"]; ?></td>
			<td><?PHP echo $row["cliente"]; ?></td>
			<td><?PHP echo $row["f_creacion"]; ?></td>
			<td><?PHP echo $row["f_modifica"]; ?></td>
			<td align='right'><?PHP echo $row["dias"]; ?>&nbsp;&nbsp;</td>
		</tr>
	<?php 




	
	
	
	
	
	
    $html .= "<tr>
    <td>".$ptr."</td>
    <td><a target='_blank' href='".$url.$id."'>".$producto."</a></td>
    <td>".$variante."</td>
    <td align='right'>".number_format($valor,2)."</td>
    <td width='45%'>".$descripcion."</td>
    <td>".$categoria."</td>
    <td>".$tipo."</td>
    </tr>";
	// echo $html;
}







