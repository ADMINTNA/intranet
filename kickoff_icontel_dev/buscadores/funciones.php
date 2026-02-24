<?PHP
////// Funciones //////////////
function genera_condicion($opciones, $campo){
     if(isset($opciones)) {
        $condicion = "AND (";
        $ptr = 0;
        foreach($opciones as $opcion){
            if($ptr >0) $condicion .= " OR ";
            $condicion .= $campo . " = '".$opcion."'";
            $ptr ++;
        }    
        $condicion .= ") \n\n";
    } else $condicion = "";
    return($condicion);
}
function DbConnect($dbname){ // se conecta a base de datos y devuelve $conn
    $server   = "localhost";
    $user     = "tnasolut_data_studio";
    $password = "P3rf3ct0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    // Forzar UTF-8 completo en MySQL
    $conn->set_charset(DB_CHARSET);
    $conn->query("SET NAMES 'utf8mb4'");
    $conn->query("SET CHARACTER SET utf8mb4");
    $conn->query("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
}
function recrea_base_servicios_activos(){
    $conn = DbConnect("tnasolut_sweet");
    $result = $conn->query("CALL `recurrencias_vacia`()");
    $result = $conn->query("CALL `recurrencias_insert`()");
    $conn->close(); 
    return;        
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
function busca_categorias(){
    $sql = "CALL `searchcategories`()";
    $conn = DbConnect("tnasolut_sweet");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)   {  
        $categorias = Array();
        while($row = $result->fetch_assoc()) {
            if($row['categoria'] <> "Gasto" && $row['categoria'] <> "Varios" && $row['categoria'] <> "Televisión" && $row['categoria'] <> "Equipo Computacional" )  
            array_push($categorias, $row['categoria']); 
        } 
    } 
    $conn->close(); 
    return($categorias);
}
 function busca_servicios(){
    $sql = "CALL `searchservices`()";
    // me conecto a la Base de Datos
    $conn = DbConnect("tnasolut_sweet");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)   {  
        $servicios = Array();
        while($row = $result->fetch_assoc()) {
             array_push($servicios, $row['servicio']);
        } 
    } 
    $conn->close(); 
    return($servicios);
}
function crea_select_multiple($datos, $name){   
    ?><table>
        <tr>
            <td align="center" >Elija <?php echo $name; ?>: </td>
        </tr>
        <tr>
            <td></td><select style="width: 146px; background-color: lightgray; color: gray;" name='<?php echo $name; ?>[]'  multiple size = 11>
                <?php for ($i = 0; $i < count($datos); $i++) { ?>
                     <option value = '<?php echo $datos[$i]; ?>'><?php echo $datos[$i]; ?></option>
                <?php  } ?>   
            </select></td>
        </tr>
    </table><?php
}
function crea_select_sin_titulo($datos, $name){   
    ?><table>
        <tr>
            <td><select style="width: 146px; background-color: lightgray; color: gray;" name='<?php echo $name;?>[]' multiple size=6>
                <?php for ($i = 0; $i < count($datos); $i++) { ?>
                     <option value = '<?php echo $datos[$i]; ?>'><?php echo $datos[$i]; ?></option>
                <?php  } ?>   
            </select></td>
        </tr>
    </table><?php
}


function crea_select($datos, $name, $titulo, $size){   
    ?><table>
		<?PHP if ($titulo) { echo "<tr><td align='center'>Elija ". $name.": </td></tr>"; } ?>
        <tr>
            <td style="background-color: #1F1D3E;color: gray;">
				<!--select name="basic[]" multiple="multiple" class="3col active"-->
				<select style="width: 146px; background-color: lightgray; color: gray;" name='<?php echo $name; ?>[]'  multiple="multiple" class="3col active" size="3">
                <?php for ($i = 0; $i < count($datos); $i++) { ?>
                     <option value = '<?php echo $datos[$i]; ?>'><?php echo $datos[$i]; ?></option>
                <?php  } ?>   
            	</select>
					
			</td>
        </tr>
    </table><?php
}



?>