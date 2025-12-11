<?php 
    include_once("config.php");    
    // me conecto a la Base de Datos
    $conn = DbConnect($db_sweet);
    $sql = "CALL searchopencasesbyhours('12')";       
    $result = $conn->query($sql);
    if ($result->num_rows > 0)   { 
        echo "entre". $result->num_rows;
        exit();
        while($row = $result->fetch_assoc()) {
          $ptr ++;
          if($tmp == $row["id"]) $no_mostrar = 1; else $no_mostrar = 0;
          if (!isset($account_id)) {
              $account_id = $row["id"]; 
              $limpia_rut =  array(' ','.'); 
              $rut = str_replace($limpia_rut, "", $row["rut"]);
          }
          switch($row["estado"]) {
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
          $datos_completos .= "<tr ".$style.">";
          if($ptr == 1) $datos_completos .= "<td>".$hoy."</td>".
                              "<td>".formatPhoneNumber($ani)."</td>";  else $datos_completos .= "<td></td><td></td>";
    
          if(!$no_mostrar) $datos_completos .= "<td ".$style."><a href='".$url.$row["id"]."' target=\"_blank\">".$row["razon_social"]."</a></td>"; else $datos_completos .= "<td></td>";
          $datos_completos .= "<td>".$row['nombre']." ".$row['apellido']."</td>".
                              "<td>".$row['celular']."</td>".
                              "<td>".$row['tipo_contacto']."</td>";
          if(!$no_mostrar) $datos_completos .= "<td>".$row["rut"]."</td>".
                                  "<td>".$row["estado"]."</td></tr>"; else $datos_completos .= "<td></td><td></td></tr>";

          $tmp =  $row["id"];
        }
     // echo $datos_completos;
    } else {
      echo date("d-m-Y H:i:s").":Llamada desde número telefónico <a href='tel:{$ani}'><b>".formatPhoneNumber($ani)."</a></b> no encontrado en <a href='https://sweet.icontel.cl\' target='_blank'>Sweet CRM</a>.</br>";
      exit();
    }

    $conn->close(); 
?>
