<?PHP  // include para buscar datos de deuda en duemint

function DbConnecta($dbname){
    $server   = "localhost";
    $user     = "icontel_data_studio";
    $password = "P3rf3ct0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    $dummy = mysqli_set_charset ($conn, "utf8");    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
}
     $conn = DbConnecta("icontel_clientes");
     $limpia_rut =  array(' ','.'); 
     $rut = str_replace($limpia_rut, "", $rut);
     // [SEGURIDAD] Escapar rut para prevenir inyección SQL
     $rutEscaped = $conn->real_escape_string($rut);
     $sql = "CALL icontel_clientes.searchbyrut('{$rutEscaped}')";
	/* $sql ="SELECT cron_duemint_clients.url AS url_cliente
		 		FROM cron_duemint_clients
				where cron_duemint_clients.taxId ='".$rut."'"; */
     $dumit = $conn->query($sql);
     if ($dumit->num_rows > 0) { 
            $endummit=1;
            while($col = $dumit->fetch_assoc()) {
               switch($col["estado"]) {
               case 1:
                $dumit_pagada = $col["monto"];
                break;
               case 2:
                $dumit_por_vencer = $col["monto"];
                break;
               case 3:
                $dumit_vencida =  $col["monto"];
                break;
               default:
               } 
               $dumit_portal = $col["url_cliente"];
            }
       } else {
         $dumit_pagada     = "0";  
         $dumit_por_vencer = "0"; 
         $dumit_vencida    = "0";
         $dumit_portal     = "https://www.duemint.com";
         $endummit = 0;
       }
       $conn->close(); 
?>
