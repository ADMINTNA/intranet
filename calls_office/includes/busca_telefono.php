<?php 
    include_once("config.php");    
    date_default_timezone_set("America/Santiago");
    if (isset($_POST['ani'])){ $ani = $_POST['ani']; }
    if (isset($_GET['ani'])) { $ani = $_GET['ani']; }
    if(empty($ani)) exit();
    $ani        = intval(preg_replace('/[^0-9]+/', '', $ani), 10); ;
    $ptr        = 0;
    $datos      = array();
    $id         = "";
    $no_mostrar = 0;
    $url        = 'https://sweet.tnaoffice.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAccounts%26offset%3D4%26stamp%3D1642464369091709700%26return_module%3DAccounts%26action%3DDetailView%26record%3D'; 
    // me conecto a la Base de Datos
    $conn = DbConnect($db_sweet);
    $sql = "CALL tnaoffice_suitecrm.searchbyphone('%".$ani."%')";       
    $result = $conn->query($sql);
    if ( ($result->num_rows > 0)  && (strlen($ani)>8) ) { 
        $contacto = Array();
        while($row = $result->fetch_assoc()) {
          $ptr ++;
          $contacto[$ptr]["id" ]    = $row["ct.id"];          
          $contacto[$ptr]["nombre"] = $row['contacto'];            
          // $contacto[$ptr]["nombre"] = $row['nombre']." ".$row['apellido'];
            
          if($tmp == $row["id"]) $no_mostrar = 1; else $no_mostrar = 0;
          if (!isset($account_id)) {
              $account_id = $row["id"]; 
              $contacto_id = $row[" ct.id"];
              $limpia_rut =  array(' ','.'); 
              $rut = str_replace($limpia_rut, "", $row["rut"]);
          }
          $url_rut = 'https://intranet.icontel.cl/calls_office/index.php?rut='.$row["rut"];                          
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

            $datos_completos .= "<tr ".$style.">";                

        if (!$no_mostrar) {
            $datos_completos .= "<td ".$style.">".$ptr.".- <a href='".$url_empresa.$row["id"]."' target=\"_blank\">".$row["razon_social"]."</a><br>".
            "<div style='display: flex; justify-content: space-between;'><span>".$row["tamano_empresa"]."</span>"."<span><a href='".$url_rut."' target=\"_blank\">".$row["rut"]."</a></span></div></td>";
            $datos_completos .= "<td colspan='6'>".$row["descripcion"]."</td></tr>
            <tr ".$style."><td></td><td>".$row["ejecutivo"]."</td>";            
        } else {
            $datos_completos .= "<td></td><td></td>";
        }
 
            $datos_completos .= "<td>".$contacto[$ptr]["nombre"]."</td>".
                              "<td>".$row['celular']."</td>".
                              "<td>".$row['email']."</td>".
                              "<td>".$row['tipo_contacto']."</td>";
        if (!$no_mostrar) {
            $datos_completos .= "<td>".$row["estado"]."</td></tr>";
        } else {
            $datos_completos .= "<td></td></tr>";
        }
         
            
            
            

          $tmp =  $row["id"];
        }
     // echo $datos_completos;
    } else {
        $quien = formatPhoneNumber($ani);
         if(strlen($ani)==3) {
            switch($ani){
                case '201': $quien = "MAM - Mauricio Araneda"; break;
                case '301': $quien = "DAM - Daniela Araneda"; break;
                case '302': $quien = "María José Rincón"; break;
                case '401': $quien = "MAO - Mauricio Araneda"; break;
                case '402': $quien = "MAM - Mauricio Araneda"; break;
                case '403': $quien = "DAM - Daniela Araneda"; break;
                case '409': $quien = "Alex Roustom"; break;
                case '501': $quien = "Ivan Mera"; break;
                case '507': $quien = "Vicente Acevedo"; break;
                case '508': $quien = "Daniel Tapia"; break;
                case '601': $quien = "MAO - Mauricio Araneda"; break;
                case '602': $quien = "Ghislaine Rivera"; break;
                case '603': $quien = "Mónica Rojas"; break;
                case '603': $quien = "Natalia Diaz"; break;
                case '701': $quien = "Alex Routom"; break;
                case '703': $quien = "Bryan Farias"; break;
                default:    $quien = formatPhoneNumber($ani);
            }
            $quien = " anexo de " . $quien;
          	$mensaje =  date("d-m-Y H:i:s").":Llamada desde <a href='tel:{$ani}'><b>".$quien."</a></b>, no encontrado en <a href='https://sweet.icontel.cl\' target='_blank'>Sweet CRM</a>.</br>";
        } else {
			$mensaje = "Ha recibido una llamada de un número Telefónico no identificado en nuestra base de datos: ". formatPhoneNumber($ani);
		}
         die($mensaje);
     }
    $conn->close(); 
?>
