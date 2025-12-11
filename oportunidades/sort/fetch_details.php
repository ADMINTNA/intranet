<?php

include "config.php";
//date_default_timezone_set("America/Santiago");
      // activo mostrar errores
     // error_reporting(E_ALL);
     // ini_set('display_errors', '1');

$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
session_start();
$select_query = $_SESSION["query_op"]." order by ".$columnName." ".$sort." ";
$conn = DbConnect("tnasolut_sweet"); 
$result = mysqli_query($conn,$select_query);
$html = '';
$ptr = 0;
$url = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DOpportunities%26action%3DDetailView%26record%3D";
                    while($row = mysqli_fetch_array($result)){
                        $ptr ++; 
                        $id             = $row["op_id"];
                        $numero         = $row["op_numero"];
                        $asunto         = $row['op_nombre'];
                        $cliente        = $row['op_cliente'];
                        $usuario        = $row['u_nombre'];
                        if(empty($usuario)) $usuario = $row['u_apellido']; else $usuario .= " ". $row['u_apellido'];
                        $estado         = $row['op_estado'];
                        $fecha          = horacl($row['op_fecha']);
                        ?>
                        <tr>
                            <td><?php echo $ptr; ?></td>
                            <td><a target="_blank" href="<?php echo $url.$id; ?>"><?php echo $numero; ?></a></td>
                            <td><?php echo $asunto; ?></td>
                            <td><?php echo $cliente; ?></td>
                            <td><?php echo $usuario; ?></td>
                            <td><?php echo $estado; ?></td>
                            <td><?php echo $fecha; ?></td>
                        </tr>
                <?php
                }
 