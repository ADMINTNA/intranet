<?php 
    if (isset($_POST['estserv'])) { $estserv = $_POST['estserv']; }
    if (isset( $_GET['estserv'])) { $estserv =  $_GET['estserv']; }
    if(empty($estserv)) exit();
    include_once("config.php");    
    // me conecto a la Base de Datos
    $conn = DbConnect($db_sweet);
     $sql = "CALL tnaoffice_suitecrm.searchbyservicestate('%".$estserv."%')";
    $result = $conn->query($sql);
    if($result->num_rows > 0)  { 
        $count = 1;
        while($row = $result->fetch_assoc()) {
            if ($tmp <> $row["rut"]) {
                $datos_completos .= busca_datos($row["rut"],$count);
                $count++;
            }
 
            $tmp = $row["rut"];
            if(!isset($rut)) {
                $rut = $tmp;
            }
         }
    } else {
       exit();
    }
    $conn->close(); 
?>
