<?php 
    if (isset($_POST['estserv'])) { $estserv = $_POST['estserv']; }
    if (isset( $_GET['estserv'])) { $estserv =  $_GET['estserv']; }
    if(empty($estserv)) exit();
    include_once("config.php");    
    // me conecto a la Base de Datos
    $conn = DbConnect($db_sweet);
    $sql_accounts = "CALL tnasolut_sweet.searchbyaccountestate('".$estserv."')";
    $resultado = $conn->query($sql_accounts);
    if($resultado->num_rows > 0)  { 
        while($row = $resultado->fetch_assoc()) {
            if ($tmp <> $row["rut"]) $datos_completos .= busca_datos($row["rut"]);
            $tmp = $row["rut"];
            if(!isset($rut)) $rut = $tmp;
         }
    } else {
      echo date("d-m-Y H:i:s").":Nose encontraron Cuentas en ese estado ";
      exit();
    }
    $conn->close(); 
?>
