<?php
//=====================================================
// Mantener sesión del KickOff
require_once __DIR__ . '/session_core.php';
// Guarda cambios realizados desde el Cuadro de Mando AJAX
// Ahora incluye notificaciones de correo al reasignar
// Autor: Mauricio Araneda (vía Antigravity)
// Fecha: 18-02-2026
//=====================================================

header('Content-Type: application/json; charset=utf-8');

// Bootstrap común AJAX (sesión unificada + config + auth)
require_once __DIR__ . "/ajax_bootstrap.php";

$is_developer = (basename(dirname(__FILE__)) === 'kickoff_icontel' && ($_SESSION['usuario'] ?? '') === 'Mauricio');

if (empty($_SESSION['loggedin']) && !$is_developer) {
    echo json_encode(["success" => false, "error" => "No autenticado"]);
    exit;
}

// Recibir datos
$id    = $_POST['id']    ?? '';
$campo = $_POST['campo'] ?? '';
$valor = $_POST['valor'] ?? '';

// Debug Log
$log_file = __DIR__ . "/debug_save_casos.log";
$log_msg = date("[Y-m-d H:i:s]") . " SAVE: id=$id, campo=$campo, valor=$valor\n";

if (empty($id) || empty($campo)) {
    file_put_contents($log_file, $log_msg . " ERROR: Parámetros insuficientes\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "Parámetros insuficientes"]);
    exit;
}

// Mapeo de campos a base de datos de SuiteCRM
$tabla = "cases";
$columna = "";

switch ($campo) {
    case 'priority':
        $columna = "priority";
        break;
    case 'status':
        $columna = "status";
        break;
    case 'category':
        $columna = "type";
        break;
    case 'assigned_user_id':
        $columna = "assigned_user_id";
        break;
    case 'en_espera_de':
        $tabla = "cases_cstm";
        $columna = "en_espera_de_c";
        break;
    default:
        file_put_contents($log_file, $log_msg . " ERROR: Campo no reconocido $campo\n", FILE_APPEND);
        echo json_encode(["success" => false, "error" => "Campo no reconocido: $campo"]);
        exit;
}

$conn = DbConnect($db_sweet);
if (!$conn) {
    file_put_contents($log_file, $log_msg . " ERROR: Conexión BD fallida\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "Error de conexión a BD"]);
    exit;
}
$conn->set_charset("utf8mb4");

// ---------------------------------------------------------
// REASIGNACIÓN: Obtener datos previos para el mail
// ---------------------------------------------------------
$asignado_cambio = false;
$msg_mail = "";

if ($campo === 'assigned_user_id') {
    $sqlPrev = "SELECT assigned_user_id, case_number, name FROM cases WHERE id = ?";
    $stmtPrev = $conn->prepare($sqlPrev);
    $stmtPrev->bind_param("s", $id);
    $stmtPrev->execute();
    $resPrev = $stmtPrev->get_result();
    if ($p = $resPrev->fetch_assoc()) {
        if ($p['assigned_user_id'] !== $valor) {
            $asignado_cambio = true;
            $case_num = $p['case_number'];
            $case_name = $p['name'];
        }
    }
    $stmtPrev->close();
}

// ---------------------------------------------------------
// EJECUTAR UPDATE
// ---------------------------------------------------------
if ($tabla === "cases") {
    $sql = "UPDATE cases SET $columna = ?, date_modified = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $valor, $id);
    $success = $stmt->execute();
    $error_db = $stmt->error;
    $stmt->close();
} else {
    $sql_upd = "UPDATE cases_cstm SET $columna = ? WHERE id_c = ?";
    $stmt = $conn->prepare($sql_upd);
    $stmt->bind_param("ss", $valor, $id);
    $success = $stmt->execute();
    $affected = $stmt->affected_rows;
    $error_db = $stmt->error;
    $stmt->close();

    if ($success && $affected === 0) {
        $check = $conn->query("SELECT 1 FROM cases_cstm WHERE id_c = '" . $conn->real_escape_string($id) . "'");
        if ($check && $check->num_rows === 0) {
            $sql_ins = "INSERT INTO cases_cstm (id_c, $columna) VALUES (?, ?)";
            $stmt_ins = $conn->prepare($sql_ins);
            $stmt_ins->bind_param("ss", $id, $valor);
            $success = $stmt_ins->execute();
            $error_db = $stmt_ins->error;
            $stmt_ins->close();
        }
    }
    $conn->query("UPDATE cases SET date_modified = NOW() WHERE id = '" . $conn->real_escape_string($id) . "'");
}

// ---------------------------------------------------------
// ENVIAR NOTIFICACIÓN SI CORRESPONDE
// ---------------------------------------------------------
if ($success && $asignado_cambio) {
    // Obtener mail del nuevo asignado
    $sqlU = "
        SELECT u.first_name, u.last_name, ea.email_address
        FROM users u
        LEFT JOIN email_addr_bean_rel eb ON eb.bean_id = u.id AND eb.deleted = 0
        LEFT JOIN email_addresses ea ON ea.id = eb.email_address_id AND ea.deleted = 0
        WHERE u.id = ? LIMIT 1
    ";
    $stmtU = $conn->prepare($sqlU);
    $stmtU->bind_param("s", $valor);
    $stmtU->execute();
    $resU = $stmtU->get_result();
    
    if ($u = $resU->fetch_assoc()) {
        $nuevo_nombre = trim($u["first_name"] . " " . $u["last_name"]);
        $nuevo_email  = trim($u["email_address"]);
        $asignador    = $_SESSION["nombre_completo"] ?? "Sistema Kickoff";

        if ($nuevo_email) {
            $url_sweet = "https://sweet.icontel.cl/index.php?module=Cases&action=DetailView&record=$id";
            $asunto = "Se te ha asignado el caso #$case_num";
            $cuerpo = "
                <h2 style='color:#512554;'>Asignación de Caso</h2>
                <p>Estimado/a <b>$nuevo_nombre</b>,</p>
                <p><b>$asignador</b> te ha asignado el siguiente caso en SuiteCRM:</p>
                <div style='background:#f4f4f4; padding:15px; border-radius:8px; border-left: 4px solid #512554;'>
                    <b>Número:</b> $case_num<br>
                    <b>Asunto:</b> $case_name<br>
                </div>
                <p><a href='$url_sweet' style='display:inline-block; padding:10px 20px; background:#512554; color:white; text-decoration:none; border-radius:5px;'>Ver Caso en SuiteCRM</a></p>
                <p><small>Este es un mensaje automático del sistema KickOff.</small></p>
            ";
            
            $enviado = kickoff_send_mail($nuevo_email, $asunto, $cuerpo, "iContel Telecom <servicioalcliente@icontel.cl>");
            if ($enviado) {
                $msg_mail = "Notificación enviada a $nuevo_nombre ($nuevo_email)";
                file_put_contents($log_file, $log_msg . " MAIL SENT: to $nuevo_email\n", FILE_APPEND);
            }
        }
    }
    $stmtU->close();
}

if ($success) {
    file_put_contents($log_file, $log_msg . " SUCCESS\n", FILE_APPEND);
    $resp = ["success" => true, "msg" => "Cambio guardado en $campo"];
    if ($msg_mail) $resp["mail"] = $msg_mail;
    echo json_encode($resp);
} else {
    file_put_contents($log_file, $log_msg . " ERROR DB: $error_db\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "Error al ejecutar query: " . $error_db]);
}

$conn->close();
?>
