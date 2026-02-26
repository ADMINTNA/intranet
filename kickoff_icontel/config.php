<?PHP 
// =======================================================
// kickoff/config.php
// funciones y declaraciones de variables para Kickoff
// 
// =======================================================

// =======================================================
// CONFIGURACIÓN DE AUTENTICACIÓN
// =======================================================
// Cambiar a true para usar autenticación de SuiteCRM
// Cambiar a false para usar autenticación tradicional
define('USE_SWEET_AUTH', false);  // Cambiar a true cuando esté listo para producción
// =======================================================

// declara variables a usar
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);
// ---------------------------------------------------------
// FUNCIÓN LOCAL: leer listas vía API Sweet
// ---------------------------------------------------------
function sweet_get_dropdown_api($lista, $ttl = 3600)
{
    // ── Caché en sesión (TTL = 1 hora por defecto) ────────────
    $ckey = '_dropdown_' . $lista;
    $tkey = '_dropdown_ts_' . $lista;

    if (
        !empty($_SESSION[$ckey]) &&
        !empty($_SESSION[$tkey]) &&
        (time() - $_SESSION[$tkey]) < $ttl
    ) {
        return $_SESSION[$ckey];
    }

    // ── Llamada a la API de SuiteCRM ──────────────────────────
    $url = "https://intranet.icontel.cl/sweet/custom/tools/api_dropdown.php?list="
         . urlencode($lista);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // máximo 5s por llamada

    $json = curl_exec($ch);
    curl_close($ch);

    if ($json === false) {
        return $_SESSION[$ckey] ?? []; // devolver caché expirada si existe
    }

    $arr = json_decode($json, true);
    if (!is_array($arr)) {
        return $_SESSION[$ckey] ?? [];
    }

    $final = [];
    foreach ($arr as $item) {
        if (isset($item['key'], $item['label'])) {
            $final[$item['key']] = $item['label'];
        }
    }

    // Guardar en caché de sesión
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION[$ckey]  = $final;
        $_SESSION[$tkey]  = time();
    }

    return $final;
}



// ── Lista de usuarios activos con caché de sesión ────────────
function sweet_get_usuarios_activos($conn, $ttl = 3600)
{
    $ckey = '_usuarios_activos';
    $tkey = '_usuarios_activos_ts';

    if (
        !empty($_SESSION[$ckey]) &&
        !empty($_SESSION[$tkey]) &&
        (time() - $_SESSION[$tkey]) < $ttl
    ) {
        return $_SESSION[$ckey];
    }

    $sql = "SELECT id, first_name, last_name
            FROM users
            WHERE deleted = 0 AND status = 'Active'
            ORDER BY first_name, last_name";

    $rs = $conn->query($sql);
    $lista = [];
    if ($rs) {
        while ($u = $rs->fetch_assoc()) {
            $lista[$u['id']] = trim($u['first_name'] . ' ' . $u['last_name']);
        }
        $rs->free();
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION[$ckey] = $lista;
        $_SESSION[$tkey] = time();
    }

    return $lista;
}

if (!isset($autoRefreshState)) {
    $autoRefreshState = 'off';
}
	$sweet 					= "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3D";
	$url_nuevo_lead 		= $sweet . "Leads%26action%3DEditView%26return_module%3DLeads%26return_action%3DDetailView";
	$url_nuevo_caso 		= $sweet . "Cases%26action%3DEditView%26return_module%3DCases%26return_action%3DDetailView";
	$url_nueva_oportunidad 	= $sweet . "Opportunities%26action%3DEditView%26return_module%3DOpportunities%26return_action%3DDetailView";
	$url_nueva_tarea 		= $sweet . "Tasks%26action%3DEditView%26return_module%3DTasks%26return_action%3DDetailView";
	$url_nueva_cotizacion	= $sweet . "AOS_Quotes%26action%3DEditView%26return_module%3DAOS_Quotes%26return_action%3DDetailView";
	$url_nuevo_contacto 	= $sweet . "Contacts%26action%3DEditView%26return_module%3DContacts%26return_action%3Dindex";
	$style_header			= 'style="color: white;background-color: midnightblue;"';
	$style_titulo			= 'style="color: white;background-color: #255154;"';
    $tmp      				= Valor_Diario_Moneda(6);  // Dolar = 2, EURO=3, UF=6
	$UF    	 				= $tmp[2];
    $UF_Fecha 				= date("d-m-Y", strtotime($tmp[1]));
	$tmp      				= Valor_Diario_Moneda(2);  // Dolar = 2, EURO=3, UF=6
	$USD   	  				= $tmp[2];
	$USD_Fecha				= date("d-m-Y", strtotime($tmp[1]));
	$db_sweet   			= "tnasolut_sweet";    
    $db_monedas  			= "icontel_monedas";
	$db_clientes 			= "icontel_clientes";
    date_default_timezone_set("America/Santiago");
    setlocale(LC_ALL, 'es_CL');
    $hoy 					= date("d-m-Y H:i:s");
//////// Funciones //////////////
function DbConnect($dbname){ // se conecta a base de datos y devuelve $conn
    $server   = "localhost";
    $user     = "data_studio";
    $password = "1Ngr3s0.,";
    
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) {
        die("No me pude conectar a servidor localhost: " . $conn->connect_error);
    }

    mysqli_set_charset($conn, "utf8");

    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) {
        die("No se puede usar la base de datos $dbname : " . mysqli_error($conn));
    }

    return $conn;
}
function DbConnect_icontel($dbname){ // se conecta a base de datos y devuelve $conn
    $server   = "localhost";
//    $user     = "icontel_data_studio";
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
    $select = "<select style='background-color: lightgray; vertical-align: bottom;' size='5' name='contacto_id' id='contacto_id'>\n";
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
function Valor_Diario_Moneda($cual){  // Dolar = 2, EURO=3, UF=6  
	$moneda = array();
    $conn = DbConnect("tnasolut_sweet");
	$sql = "CALL tnasolut_sweet.moneda_ultimo_valor({$cual})";                    
	$result = $conn->query($sql);
	$ptr=0;
	if($result->num_rows > 0)  { 
		while($row = $result->fetch_assoc()) {
		  $ptr ++; 
		  switch ($cual){    
		  case "2":
			$moneda[0] = "USD";
			break;
		  case "3":
			 $moneda[0] = "EURO";
			break;
		  case "6":
			 $moneda[0] = "UF";
			break;
		   default:
			 $moneda[0] = "Error Moneda desconocida.";                     
		  }  
		  $moneda[1] = $row["fecha"];				  
		  $moneda[2] = $row["valor"];
		}
	} 
	$conn->close();
	unset($result);
	unset($conn);
	return $moneda;
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

// ==================================================================
// FUNCIÓN: kickoff_send_mail()
// Envío de correo HTML estándar, UTF-8, compatible con PHP 7.x
// ==================================================================
function kickoff_send_mail($para, $asunto, $mensaje, $de = "no-reply@icontel.cl")
{
    // Forzar UTF-8 (muy importante con tildes y emojis)
    $asunto  = "=?UTF-8?B?" . base64_encode($asunto) . "?=";

    $headers = "From: $de\r\n";
    $headers .= "Reply-To: $de\r\n";    
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    return mail($para, $asunto, $mensaje, $headers);
}

// =============================================================
// Enviar mensaje a Telegram
// =============================================================
function kickoff_send_telegram($chat_id, $mensaje) {

    $token = "8380927188:AAGM-NuH-fNNkwI5Lvo2Dv0rJaaLpyUqV5Y"; // NO mostrar nunca en pantalla

    $url = "https://api.telegram.org/bot{$token}/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text'    => $mensaje,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

?>