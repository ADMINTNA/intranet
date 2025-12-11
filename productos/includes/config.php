<?PHP // config.php datos de configuraciones y generales
   // activo mostrar errores
   // error_reporting(E_ALL);
   // ini_set('display_errors', '1');

    $db_sweet  = "tnasolut_sweet";
    date_default_timezone_set("America/Santiago");
    $hoy = date("d-m-Y H:i:s");
//////// Funciones //////////////
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
function get_aos_product_quotes($id) { // devuelve datos del aos_product_quotes buscado por id
    // global $account_id, $ani; $contacto;
    $conn = DbConnect("tnasolut_sweet");
    $sql = "CALL aos_products_quotes_by_id('$id')";       
    $result = $conn->query($sql);
    if($result->num_rows > 0)  { 
        $row = mysqli_fetch_array($result); 
        $datos =  array('nombre'=>trim($row['producto']), 'proveedor'=>trim($row['proveedor']), 'codigo_servicio'=> trim($row['service_code']) );
    } else $datos = "";
    $conn->close(); 
    return($datos);
}
function contactos_cuenta($id) { // devuelve select con los contactos de la cuenta
   // global $account_id, $ani; $contacto;
     // me conecto a la Base de Datos
    $conn = DbConnect("tnasolut_sweet");
    $sql = "CALL account_contacts_by_account_id('$id')";  
    $select = "<select name='contacto_id' id='contacto_id'>\n";
    $result = $conn->query($sql);
    if($result->num_rows > 0)  { 
        while($row = $result->fetch_assoc()) {
          $select .= "<option value='".$row['contact_id']."'>".$row['contact_nombre']." ".$row['contact_apellido']."\n";    
        }
    $select .= "<option value = ' '> </option>\n<select>\n";    
    } 
    $conn->close(); 
    return($select);
}

?>
