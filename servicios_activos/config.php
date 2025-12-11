<?php  
     //error_reporting(E_ALL);
     //ini_set('display_errors', '1');
 function DbConnect($dbname){ // se conecta a base de datos y devuelve $conn
    $server   = "localhost";
    $user     = "tnasolut_data_studio";
    $password = "P3rf3ct0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    $dummy = mysqli_set_charset ($conn, "utf8");    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
}

function busca_columna($sql){
    $datos = Array();
    $conn = DbConnect("tnasolut_sweet");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)   {  
         while($row = $result->fetch_assoc()) {
            array_push($datos, $row['dato']); 
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
    $conn   = DbConnect("tnasolut_sweet");
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
