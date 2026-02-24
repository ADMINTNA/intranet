<?php 
    include_once("config.php");  
	$nombre   = "";
	$apellido = "";
    date_default_timezone_set("America/Santiago");
    if (isset($_POST['nombre']))   { $nombre   = $_POST['nombre']; }
    if (isset( $_GET['nombre']))   { $nombre   =  $_GET['nombre']; }
    if (isset($_POST['apellido'])) { $apellido = $_POST['apellido']; }
    if (isset( $_GET['apellido'])) { $apellido =  $_GET['apellido']; }
    if(empty($nombre) && empty($apellido)) exit();
   //$contacto = strtolower($nombre)." ".strtolower($apellido);
    $ptr        = 0;
    $datos      = array();
    $id         = "";
    $no_mostrar = 0;
    $url        = 'https://sweet.tnaoffice.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAccounts%26offset%3D4%26stamp%3D1642464369091709700%26return_module%3DAccounts%26action%3DDetailView%26record%3D'; 
    // me conecto a la Base de Datos
    $conn = DbConnect($db_sweet);
    if(!empty($nombre) && !empty($apellido)) $clave = $nombre." ".$apellido; 
    else {
        if(!empty($nombre)) $clave = $nombre; else $clave = $apellido;
    }
    $sql = "CALL tnaoffice_suitecrm.searchbycontacto_por_clave('%".$clave."%')";  
    $result = $conn->query($sql);
    if($result->num_rows > 0)  { 
        while($row = $result->fetch_assoc()) {
          $ptr ++;
          if($tmp == $row["id"]) $no_mostrar = 1; else $no_mostrar = 0;
          if (!isset($account_id)) {
              $ani = $row['office_tel'];           
              $ani = str_replace(" ","", $ani);
              $ani = str_replace("+","", $ani);
              $account_id = $row["id"]; 
              $contacto_id = $row[" ct.id"];
              $limpia_rut =  array(' ','.'); 
              $rut = str_replace($limpia_rut, "", $row["rut"]);
          }
          $url_rut = 'https://intranet.tnaoffice.cl/calls/index.php?rut='.$row["rut"];              
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
          if(!$no_mostrar) $datos_completos .= "<td><a href='".$url_rut."' target=\"_blank\">".$row["rut"]."</a></td>".
                                  "<td>".$row["estado"]."</td></tr>"; else $datos_completos .= "<td></td><td></td></tr>";

          $tmp =  $row["id"];
        }
    } else {
      echo date("d-m-Y H:i:s").":Llamada desde número telefónico <a href='tel:{$ani}'><b>".formatPhoneNumber($ani)."</a></b> no encontrado en <a href='https://sweet.tnaoffice.cl\' target='_blank'>Sweet CRM</a>.</br>";
      exit();
    }

    $conn->close(); 
?>
