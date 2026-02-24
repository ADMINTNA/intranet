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
	switch ($row["estado"]){    
	case "1 Nuevo":
		$tr .= '<tr style="color: red" >';
		break;
	case "2 Asignado":
		$tr .= '<tr style="color: orange" >';
		break;
	case "3 En Proceso":
		$tr .= '<tr style="color: green" >';
		break;
	default:
		$tr .= '<tr>';                     
	}   
	?>
		<?PHP echo $tr; ?>
			<td><?php echo $ptr; ?></td>
			<?PHP echo '<td colspan="1"><a target="_blank" href="'.$url.$row["id"].'">'.$row["nombre"].'</a></td>'; ?>
			<td><?PHP echo $row["cuenta"]; ?></td>
			<td><?PHP echo $row["estado"]; ?></td>
			<td><?PHP echo $row["campana"]; ?></td>
			<td><?PHP echo $row["usuario"]; ?></td>
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







