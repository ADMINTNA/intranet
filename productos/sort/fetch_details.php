<?php

include "config.php";
//date_default_timezone_set("America/Santiago");
      // activo mostrar errores
     // error_reporting(E_ALL);
     // ini_set('display_errors', '1');

$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
session_start();
$select_query =  $_SESSION["query"]." order by ".$columnName." ".$sort." ";
$conn = DbConnect("tnasolut_sweet"); 
$result = mysqli_query($conn,$select_query);
$html = '';
$ptr = 0;
$url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAOS_Products%26offset%3D1%26stamp%3D1647912995043611800%26return_module%3DAOS_Products%26action%3DDetailView%26record%3D";      
while($row = mysqli_fetch_array($result)){
    $ptr ++;    
    $producto       = $row["producto"];
    $variante       = $row['numero_parte'];
    $valor          = $row['valor'];
    $descripcion    = $row['descripcion'];
    $categoria      = $row['categoria'];
    $tipo           = $row["tipo"];
    $id             = $row["id"];

    $html .= "<tr>
    <td>".$ptr."</td>
    <td><a target='_blank' href='".$url.$id."'>".$producto."</a></td>
    <td>".$variante."</td>
    <td align='right'>".number_format($valor,2)."</td>
    <td width='45%'>".$descripcion."</td>
    <td>".$categoria."</td>
    <td>".$tipo."</td>
    </tr>";
}
echo $html;