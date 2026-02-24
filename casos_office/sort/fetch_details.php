<?php
<?php
require_once __DIR__ . '/session_config.php';
// ... tu código de la página ...
include "config.php";
//date_default_timezone_set("America/Santiago");
      // activo mostrar errores
     // error_reporting(E_ALL);
     // ini_set('display_errors', '1');

$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
$select_query =  $_SESSION["query"]." order by ".$columnName." ".$sort." ";
$conn = DbConnect("tnasolut_sweet"); 
$result = mysqli_query($conn,$select_query);
$html = '';
$ptr = 0;
$url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26offset%3D1%26stamp%3D1644666990053569200%26return_module%3DCases%26action%3DDetailView%26record%3D";      
while($row = mysqli_fetch_array($result)){
    $ptr ++;
    $numero         = $row["numero"];
    $asunto         = $row['asunto'];
    $estado         = $row['estado'];
    $responsable    = $row['responsable'];
    $cliente        = $row['cliente'];
    $usuario        = $row['usuario'];
    $categoria      = $row['categoria'];
    $f_creacion     = horacl($row['f_creacion']);
    $creado_por     = $row["creado_por"];
    $f_modificacion = horacl($row["f_modifica"]);
    $antiguedad     = $row["antiguedad"];
    $horas          = $row["horas"];
    $proveedor      = $row["proveedor"];
    $codigo_servicio = $row["codigo_servicio"];
    $id             = $row["id"];

    $html .= "<tr>
    <td>".$ptr."</td>
    <td><a target='_blank' href='".$url.$id."'>".$numero."</a></td>
    <td>".$asunto."</td>
    <td>".$estado."</td>
    <td>".$responsable."</td>
    <td>".$cliente."</td>
    <td>".$usuario."</td>
    <td>".$categoria."</td>
    <td>".$f_creacion."</td>
    <td>".$creado_por."</td>
    <td>".$f_modificacion."</td>
    <td align='center'>".$antiguedad."</td>
    <td align='center'>".$horas."</td>   
    <td>".$proveedor."</td>    
    <td>".$codigo_servicio."</td>
    </tr>";
}
echo $html;