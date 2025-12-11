<?php 
    if (isset($_POST['estserv'])) { $estserv = $_POST['estserv']; }
    if (isset( $_GET['estserv'])) { $estserv =  $_GET['estserv']; }
    if(empty($estserv)) exit();
    include_once("config.php");    
    // me conecto a la Base de Datos
    $conn = DbConnect($db_sweet);
    $sql = "CALL tnasolut_sweet.searchbyservicestate('%".$estserv."%')";
    $result = $conn->query($sql);
    if($result->num_rows > 0)  { 
        while($row = $result->fetch_assoc()) {
            if ($tmp <> $row["rut"]) $datos_completos .= busca_datos($row["rut"]);
            $tmp = $row["rut"];
            if(!isset($rut)) $rut = $tmp;
         }
    } else {
      echo date("d-m-Y H:i:s").":Llamada desde número telefónico <a href='tel:{$ani}'><b>".formatPhoneNumber($ani)."</a></b> no encontrado en <a href='https://sweet.icontel.cl\' target='_blank'>Sweet CRM</a>.</br>";
      exit();
    }
    $conn->close(); 
?>
