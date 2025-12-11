<?PHP // config.php datos de configuraciones y generales
   // activo mostrar errores
   //  error_reporting(E_ALL);
   //  ini_set('display_errors', '1');

    session_destroy();
	session_start();
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
 

function carga_inicial() {

   
	$_SESSION['query'] = "SELECT 
        a.id AS cliente_id, 
        UPPER(a.name) AS cliente, 
        IF(q.currency_id = -99, 'UF', cu.name) AS moneda, 
        q.name AS coti_nombre, 
        q.number AS coti_numero, 
        CONCAT(q.billing_address_street, ' ', q.billing_address_city, ' ', q.billing_address_country) AS coti_dir, 
        CONCAT('https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAOS_Quotes%26action%3DDetailView%26record%3D', q.id) AS coti_url, 
        p.name AS producto, 
        p.account_name AS proveedor, 
        p.codigo_servicio AS codigo_servicio, 
        p.part_number AS codigo, 
        p.product_qty AS produ_cantidad, 
        p.product_unit_price AS produ_precio, 
        (p.product_qty * p.product_unit_price) AS valor, 
        p.product_cost_price AS costo, 
        p.fecha_contrato AS produ_fecha_contrato, 
        p.duracion_contrato AS produ_vigencia, 
        p.description AS detalle_instalacion, 
        ca.name AS categoria, 
        CONCAT(u.first_name, ' ', u.last_name) AS vendedor, 
        (p.product_qty * p.product_unit_price) - p.product_cost_price AS produ_margen, 
        p.duracion_contrato - TIMESTAMPDIFF(MONTH, p.fecha_contrato, NOW()) AS produ_meses_vence, 
        CASE 
            WHEN c.etapa_cotizacion_c IN ('cerrado_aceptado_cot') THEN 'MENSUAL'
            WHEN c.etapa_cotizacion_c IN ('Cerrado_aceptado_anual_cot') THEN 'ANUAL'
            WHEN c.etapa_cotizacion_c IN ('cerrado_aceptado_cli') THEN 'BIENAL'
            WHEN c.etapa_cotizacion_c IN ('cerrado_aceptado') THEN 'UNICA'
            WHEN c.etapa_cotizacion_c IN ('gasto') THEN 'GASTO'
            WHEN c.etapa_cotizacion_c IN ('en_traslado') THEN 'EN TRASLADO'
            WHEN c.etapa_cotizacion_c IN ('posible_traslado') THEN 'POSIBLE TRASLADO'
            WHEN c.etapa_cotizacion_c IN ('suspendido') THEN 'SUSPENDIDO'
            ELSE c.etapa_cotizacion_c 
        END AS recurrencia 
        FROM 
        aos_quotes AS q FORCE INDEX(aos_quotes_stage_IDX)
        LEFT JOIN currencies 				AS cu ON q.currency_id = cu.id 
        LEFT JOIN accounts 					AS a ON a.id = q.billing_account_id 
        LEFT JOIN accounts_cstm 			AS ac ON ac.id_c = a.id 
        LEFT JOIN aos_products_quotes 		AS p ON p.parent_id = q.id 
        LEFT JOIN aos_quotes_cstm 			AS c ON q.id = c.id_c 
        LEFT JOIN aos_products 				AS pr ON p.product_id = pr.id 
        LEFT JOIN aos_product_categories 	AS ca ON pr.aos_product_category_id = ca.id 
        LEFT JOIN users 					AS u ON a.assigned_user_id = u.id 
        LEFT JOIN opportunities 			AS o	ON o.id = q.opportunity_id
        LEFT JOIN opportunities_cstm 		AS oc	ON oc.id_c = o.id
        ";
	  $_SESSION['where'] = " WHERE c.etapa_cotizacion_c != 'cerrado_perdido_cot' 
        AND ( q.stage = 'Closed Accepted' OR c.etapa_cotizacion_c = 'demo' 
              OR o.sales_stage IN ('Pre_Instalacion', 'Facturacion','Recepcion')
            )      
        AND p.parent_type = 'AOS_Quotes' 
        AND q.deleted = 0 
        AND a.deleted = 0 
        AND p.deleted = 0 
        AND o.deleted = 0 ";
    
        if(isset($_POST['producto']) && !empty($_POST['producto'])) {
            $_SESSION['where'] .= " && p.name like '%".$_POST['producto']."%'";
        }
        if(isset($_POST['codigo']) && !empty($_POST['codigo'])) {
            $_SESSION['where'] .= " && p.part_number  like '%".$_POST['codigo']."%'";
        }
        if(isset($_POST['codservicio']) && !empty($_POST['codservicio'])) {
            $_SESSION['where'] .= " && p.codigo_servicio  like '%".$_POST['codservicio']."%'";
        }
        if(isset($_POST['direccion']) && !empty($_POST['direccion'])) {
            $_SESSION['where'] .= " && ( q.billing_address_street  like '%".$_POST['direccion']."%' 
                                          OR q.billing_address_city    like '%".$_POST['direccion']."%' 
                                          OR q.billing_address_country like '%".$_POST['direccion']."%'
                                       ) ";
        }
        if(isset($_POST['proveedor']) && !empty($_POST['proveedor'])) {
            $proveedor = $_POST['proveedor'];
            $count = 1;
            $_SESSION['where'] .= " && (p.account_name like '".$proveedor[0]."'";
            for ($i=1;$i<count($proveedor);$i++) {     
                $_SESSION['where'] .= " OR p.account_name like '".$proveedor[$i]."'"; 
            }
            $_SESSION['where'] .= ")";
        }
        if(isset($_POST['vendedor']) && !empty($_POST['vendedor'])) {
            $_SESSION['where'] .= " && (u.first_name like '%".$_POST['vendedor']."%' OR
                                        u.last_name  like '%".$_POST['vendedor']."%')";
        }
        if(isset($_POST['recurrencia']) && !empty($_POST['recurrencia'])) {
            $recurrencia = $_POST['recurrencia'];
            $count = 1;
            $_SESSION['where'] .= " && (c.etapa_cotizacion_c like '".$recurrencia[0]."'";
            for ($i=1;$i<count($recurrencia);$i++) {     
                $_SESSION['where'] .= " OR c.etapa_cotizacion_c like '".$recurrencia[$i]."'"; 
            }
            $_SESSION['where'] .= ")";
        }
        if(isset($_POST['categoria']) && !empty($_POST['categoria'])) {
            $categoria = $_POST['categoria'];
            $count = 1;
            $_SESSION['where'] .= " && (ca.name like '".$categoria[0]."'";
            for ($i=1;$i<count($categoria);$i++) {     
                $_SESSION['where'] .= " OR ca.name  like '".$categoria[$i]."'";  
            }
            $_SESSION['where'] .= ")";
        }
      $_SESSION['orden'] = " ORDER BY `cliente` ASC"; 

 //     echo "la query=<br>".$_SESSION["query"].$_SESSION["where"].$_SESSION["orden"]. "<br><br>";
   //    exit();
    
    
}

$tmp = carga_inicial();
    
    
?>
