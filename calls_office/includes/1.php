<?php
    include_once("config.php");    
    date_default_timezone_set("America/Santiago");
    if (isset($_POST['ani'])){ $ani = $_POST['ani']; }
    if (isset($_GET['ani'])) { $ani = $_GET['ani']; }
    if(empty($ani)) exit(); 
    $anix = $ani;    
    $pos = strpos($ani,":");
    if($pos) $anix = substr($anix,$pos+1,strlen($anix)-$pos);
    $anix = str_replace(" ","", $anix);
    $anix = str_replace("+","", $anix);
    $ani_final = "%".$anix."%";
    $limpia_rut =  array(' ','.'); 
    // me conecto a la Base de Datos
    $conn = DbConnect($db_sweet);
    $sql = "CALL tnasolut_sweet.searchbyphone('{$ani}')";       
    $result = $conn->query($sql);
    if ( ($result->num_rows > 0)  && (strlen($ani)>8) ) { 
        while($row = $result->fetch_assoc()) {
          $rut_empresa = $row["rut"];       
          $rut = str_replace($limpia_rut, "", $row["rut"]);
          $empresa = $row["razon_social"];    
          if(!isset($account_id)) {
              if (!isset($account_id)) {$account_id = $row["id"];} // guardo el id de la primera empresa
              $estado       =  $row["estado"]; 
              $razon_social .= $row["razon_social"]."<br>";    
              $url= 'https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAccounts%26offset%3D4%26stamp%3D1642464369091709700%26return_module%3DAccounts%26action%3DDetailView%26record%3D'.$row["id"]; 
              switch($estado) {
                  case "Baja":
                  $style = "style=\"background-color:orange;color:yellow;\""; 
                      break;
                  case "Suspendido":
                  $style = "style=\"background-color:orange;color:yellow;\""; 
                      break;
                  case "Extrajudicial":
                  $style = "style=\"background-color:orange;color:yellow;\""; 
                      break;
                  default:
                  $style = "style=\"background-color:white;color:black;\""; 
              } 
             $sweet = "<td ".$style."><a href='".$url."' target=\"_blank\">".$row["razon_social"]."</a></td>";
          }
         $contacto .= "<tr><td>".$row["nombre"]." ".$row["apellido"]."</td><td>".$row["celular"]."<td>".$row["tipo_contacto"]."</td></tr>";            
       }
    } else {
      echo date("d-m-Y H:i:s").":Llamada desde número telefónico <a href='tel:{$ani}'><b>".formatPhoneNumber($ani)."</a></b> no encontrado en <a href='https://sweet.icontel.cl\' target='_blank'>Sweet CRM</a>.</br>";
      exit();
    }

    $conn->close(); 
?>
