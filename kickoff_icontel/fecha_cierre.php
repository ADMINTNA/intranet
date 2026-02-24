   <?php
//////// Funciones //////////////
function DbConnect($dbname){ // se conecta a base de datos y devuelve $conn
    $server   = "localhost";
//    $user     = "tnasolut_data_studio";
//    $password = "P3rf3ct0.,";
    $user     = "data_studio";
    $password = "1Ngr3s0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    $dummy = mysqli_set_charset ($conn, "utf8");    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
}
			$db_sweet    = "tnasolut_sweet";    

            $conn = DbConnect($db_sweet);
            echo $sql = 'select aqa.date_created as date_created,
									 aqa.parent_id
						 FROM aos_quotes_audit As aqa 
						 where 1 && ( aqa.field_name = "etapa_cotizacion_c" 
								 && ( (aqa.after_value_string = "cerrado_aceptado_cot") 
								 OR (aqa.after_value_string = "Cerrado_Aceptado_anual_cot") 
								 OR (aqa.after_value_string = "cerrado_aceptado_cli")  
								 OR (aqa.after_value_string = "cerrado_aceptado") 
								 ) ) 
						group by aqa.parent_id 
						order by aqa.date_created DESC 
				';       
            $resultado = $conn->query($sql);
            $ptr=0;
            $contenido = "";
            if($resultado->num_rows > 0)  { 
                while($lin = $resultado->fetch_assoc()) {
				$ptr ++;
   echo "<br>" . $ptr . " "  ;             
echo $sql_update = "update aos_quotes_cstm as aqc set aqc.fechadecierre_c = '".$lin['date_created']."' where aqc.id_c = '".$lin['parent_id']."'";
  echo "<br><br>"   ;    
			$tmp = $conn->query($sql_update);


					
					
					
					
					
                 }
            } 
			$conn->close(); 
            unset($resultado);
            unset($conn);
?>