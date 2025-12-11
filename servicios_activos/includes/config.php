<?PHP // config.php datos de configuraciones y generales
   // activo mostrar errores
//    error_reporting(E_ALL);
 //   ini_set('display_errors', '1');
    date_default_timezone_set("America/Santiago");
    $hoy = date("d-m-Y H:i:s");
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
    $dummy = mysqli_set_charset ($conn, "utf8");    
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
            if($row['categoria'] <> "Gasto" AND $row['categoria'] <> "Varios" AND $row['categoria'] <> "Televisión" AND $row['categoria'] <> "Equipo Computacional" )  
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
function carga_incial(){
    $Recurrencia = "";
    $Categoria = "";
    $Producto = "";
    $Codigo = "";
    // $sql ="Select * from recurrencias where 1 ";
        if(isset($_POST["Recurrencia"])) $Recurrencia = genera_condicion($_POST["Recurrencia"], "recurrencia");
        if(isset($_POST["Categoria"])) $Categoria = genera_condicion($_POST["Categoria"], "categoria"); else 
        if(isset($_POST["Producto"])) $Producto = genera_condicion($_POST["Producto"], "producto"); 
        if(isset($_POST["Codigo"])) $Codigo = genera_condicion($_POST["Codigo"], "codigo"); 
    
        $_SESSION['filtro'] = $Recurrencia . $Categoria . $Producto . $Codigo;
      
        $_SESSION['agrupar'] = " GROUP BY coti_numero ";
        $_SESSION['agrupar_contactos']= " GROUP BY empresa, contacto";
        $_SESSION['orden'] = " ORDER BY cliente ASC, coti_numero ASC";
        $_SESSION['orden_contactos'] = " ORDER BY re.cliente ASC, contacto ASC";
    
        $_SESSION['query'] = "Select * from recurrencias where 1 " .  
            $_SESSION['filtro'] . 
            $_SESSION['agrupar'] .
            $_SESSION['orden'] ;
    
        $_SESSION['query_contactos'] = "Select
            a.name 									as empresa, 
            a.phone_office 							as office_tel,
            ac.estatusfinanciero_c 					as estado,
            CONCAT(ct.first_name,' ',ct.last_name)	as contacto,
            ct.title 								as cargo,
            ct.lead_source 							as tipo_contacto,
            ct.phone_mobile 						as celular,
            ct.phone_work 							as telefono,
            e.email_address 						as email
        from recurrencias 				as re
        LEFT JOIN accounts 				as a  ON a.id	= re.cliente_id
        LEFT JOIN accounts_contacts 	as co ON a.id	= co.account_id
        LEFT JOIN contacts				as ct ON co.contact_id = ct.id
        LEFT JOIN accounts_cstm 		as ac ON ac.id_c = a.id
        LEFT JOIN email_addr_bean_rel 	as ea ON ea.bean_id = ct.id
        LEFT JOIN email_addresses		as e  ON e.id = ea.email_address_id 
        where 1
        AND ! (ac.estatusfinanciero_c = 'Baja')
        AND co.deleted = 0
        AND ct.deleted = 0
        AND ea.deleted = 0 
        AND e.deleted  = 0 " . $_SESSION['filtro'] . $_SESSION["agrupar_contactos"] . $_SESSION['orden_contactos'];
}
?>
