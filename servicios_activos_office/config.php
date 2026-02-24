<?php  
     error_reporting(E_ALL);
     ini_set('display_errors', '1');


 function DbConnect($dbname){ // se conecta a base de datos y devuelve $conn
    $server   = "localhost";
    $user     = "data_studio";
    $password = "1Ngr3s0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { 
        die("No me pude conectar a servidor localhost: " . $conn->connect_error); 
    }
    
    // Check if charset set successfully
    if (!$conn->set_charset("utf8")) {
        // error_log("Error loading character set utf8: " . $conn->error);
    }
    
    $bd_seleccionada = $conn->select_db($dbname);
    if (!$bd_seleccionada) { 
        die ('No se puede usar '.$dbname.' : ' . $conn->error); 
    }
    return($conn);
}

function busca_columna($sql){
    $datos = Array();
    $conn = DbConnect("tnaoffice_suitecrm");
    
    // Debug: Print the query being executed
    // echo "Executing: $sql <br>";
    
    $result = $conn->query($sql);
    
    // Check for query failure
    if ($result === false) {
        die("Error en query: " . $sql . " - Error MySQL: " . $conn->error);
    }
    
    if ($result->num_rows > 0)   {  
         while($row = $result->fetch_assoc()) {
             // Check if 'dato' key exists
             if (isset($row['dato'])) {
                array_push($datos, $row['dato']); 
             } else {
                 // Fallback if 'dato' doesn't exist, assume first column
                 $row_values = array_values($row);
                 if (count($row_values) > 0) {
                     array_push($datos, $row_values[0]);
                 }
             }
        } 
    } 
    $conn->close(); 
    return($datos);
}
function crea_select($datos, $name){   
    ?><table>
        <tr>
            <td align="center" >Elija <?php echo $name; ?>: </td>
        </tr>
        <tr>
            <td><select name='<?php echo $name; ?>[]'  multiple size = 11>
                <?php for ($i = 0; $i < count($datos); $i++) { ?>
                     <option value = '<?php echo $datos[$i]; ?>'><?php echo $datos[$i]; ?></option>
                <?php  } ?>   
            </select></td>
        </tr>
    </table><?php
}
function recrea_base_servicios_activos(){
    $conn   = DbConnect("tnaoffice_suitecrm");
    $result = $conn->query("CALL `recurrencias_vacia`()");
    $result = $conn->query("CALL `recurrencias_insert`()");
    $conn->close(); 
    return;        
}

$recurrencias = busca_columna("CALL `recurrencias_recurrencia`()");    
$categorias   = busca_columna("CALL `recurrencias_categorias`()");    
$productos    = busca_columna("CALL `recurrencias_productos`()");    
$codigo    	  = busca_columna("CALL `recurrencias_codigo`()");   
$tmp 		  = recrea_base_servicios_activos();

?>
