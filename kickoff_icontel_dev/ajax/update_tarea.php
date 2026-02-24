<?php
error_reporting(0);
// =====================================================================
// /intranet/kickoff/ajax/update_tarea.php
// Guarda TODOS los campos de una tarea al modificar cualquiera
// Envia correo cuando se reasigna
// Autor: mAo + ChatGPT
// =========================================================================

// Bootstrap AJAX seguro + sesión unificada + sg_id + sg_name
require_once __DIR__ . "/../ajax_bootstrap.php";
error_reporting(0); // Override config.php settings

// JSON limpio
header('Content-Type: application/json; charset=UTF-8');
// file_put_contents(__DIR__ . "/debug_update.log", date("Y-m-d H:i:s") . " START\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success"=>false,"error"=>"Método inválido"]);
    exit;
}

require_once "../config.php";
$conn = DbConnect("tnasolut_sweet");

// ---------------------------------------------------------
// Captura de variables POST
// ---------------------------------------------------------
$id         = trim($_POST['id'] ?? '');
$categoria  = trim($_POST['categoria'] ?? '');
$prioridad  = trim($_POST['prioridad'] ?? '');
$asignado   = trim($_POST['assigned_user_id'] ?? '');
$estado     = trim($_POST['estado'] ?? '');
$en_espera  = trim($_POST['en_espera'] ?? '');
$fecha_recibida = trim($_POST['date_due'] ?? '');

if ($id == "") {
    echo json_encode(["success"=>false,"error"=>"ID vacío"]);
    exit;
}

// ---------------------------------------------------------
// Obtener datos previos (para comparar asignación y enviar correo)
// ---------------------------------------------------------
$sqlPrev = "SELECT assigned_user_id, name, description FROM tasks WHERE id = ?";
$stmtPrev = $conn->prepare($sqlPrev);

if (!$stmtPrev) {
    echo json_encode(["success"=>false,"error"=>"ERROR SQL PREV: ".$conn->error]);
    exit;
}

$stmtPrev->bind_param("s", $id);
$stmtPrev->execute();
$resPrev = $stmtPrev->get_result();
$prev    = $resPrev->fetch_assoc() ?? [];

$asignado_anterior = $prev["assigned_user_id"] ?? "";
$tarea_titulo      = $prev["name"] ?? "(sin título)";
$tarea_desc        = $prev["description"] ?? "";
$usuario_asignador = $_SESSION["nombre_completo"] ?? "Sistema Kickoff";

// ---------------------------------------------------------
// Conversión de FECHA dd-mm-yyyy → yyyy-mm-dd
// ---------------------------------------------------------
$fecha_sql = null;

if ($fecha_recibida !== "") {
    $tmp = str_replace('/', '-', $fecha_recibida);
    $p   = explode('-', $tmp);

    if (count($p) == 3) {
        if (strlen($p[0]) == 4) {
            $fecha_sql = $tmp;  // YYYY-MM-DD
        } else {
            $fecha_sql = $p[2] . "-" . $p[1] . "-" . $p[0]; // DD-MM-YYYY
        }
    } else {
        echo json_encode(["success"=>false,"error"=>"Fecha inválida"]);
        exit;
    }
}

// =========================================================
// UPDATE tasks
// =========================================================
$sql1 = "
    UPDATE tasks SET
        priority = ?,
        assigned_user_id = ?,
        status = ?,
        date_due = ?,
        date_modified = NOW()
    WHERE id = ?
";

$stmt1 = $conn->prepare($sql1);
if (!$stmt1) {
    echo json_encode(["success"=>false,"error"=>"ERROR SQL1: ".$conn->error]);
    exit;
}

$stmt1->bind_param("sssss", $prioridad, $asignado, $estado, $fecha_sql, $id);

if (!$stmt1->execute()) {
    echo json_encode(["success"=>false,"error"=>$stmt1->error]);
    exit;
}

// =========================================================
// UPDATE tasks_cstm
// =========================================================
$sql2 = "
    UPDATE tasks_cstm SET
        categoria_c = ?,
        en_espera_de_c = ?
    WHERE id_c = ?
";

$stmt2 = $conn->prepare($sql2);
if (!$stmt2) {
    echo json_encode(["success"=>false,"error"=>"ERROR SQL2: ".$conn->error]);
    exit;
}

$stmt2->bind_param("sss", $categoria, $en_espera, $id);

if (!$stmt2->execute()) {
    echo json_encode(["success"=>false,"error"=>$stmt2->error]);
    exit;
}

// =====================================================================
// SI CAMBIÓ EL ASIGNADO → preparar envío de correo
// =====================================================================
if ($asignado !== $asignado_anterior) {

    // ---------------------------------------------------------
    // Obtener email del nuevo asignado
    // ---------------------------------------------------------
    $sqlU = "
        SELECT 
            u.first_name,
            u.last_name,
            ea.email_address
        FROM users u
        LEFT JOIN email_addr_bean_rel eb 
            ON eb.bean_id = u.id AND eb.deleted = 0
        LEFT JOIN email_addresses ea 
            ON ea.id = eb.email_address_id AND ea.deleted = 0
        WHERE u.id = ?
        LIMIT 1
    ";

    $stmtU = $conn->prepare($sqlU);

    if ($stmtU) {
        $stmtU->bind_param("s", $asignado);
        $stmtU->execute();
        $resU = $stmtU->get_result();

        if ($resU && $resU->num_rows > 0) {
            $u = $resU->fetch_assoc();

            $nuevo_nombre = trim(($u["first_name"] ?? "") . " " . ($u["last_name"] ?? ""));
            $nuevo_email  = trim($u["email_address"] ?? "");
        } else {
            $nuevo_nombre = "(sin nombre)";
            $nuevo_email  = "";
        }

    } else {
        $nuevo_nombre = "(error SQL)";
        $nuevo_email  = "";
    }

    // ---------------------------------------------------------
    // Construir correo HTML
    // ---------------------------------------------------------
    $url_tarea = "https://sweet.icontel.cl/index.php?action=DetailView&module=Tasks&record=$id";

    $html = "
    <h2>Se te ha asignado una tarea</h2>

    <p><b>{$usuario_asignador}</b> ha asignado la siguiente tarea a <b>{$nuevo_nombre}</b>.</p>

    <p><b>Título:</b> {$tarea_titulo}<br>
    <b>Estado:</b> {$estado}<br>
    <b>Prioridad:</b> {$prioridad}<br>
    <b>Fecha Vencimiento:</b> {$fecha_recibida}<br>
    <b>Descripción:</b><br>
    <pre style='font-size:13px;border:1px solid #ccc;padding:10px;border-radius:6px;background:#fafafa'>{$tarea_desc}</pre>
    </p>

    <p><a href='{$url_tarea}' target='_blank'>Haz clic aquí para ver la tarea</a></p>
    ";

    // ---------------------------------------------------------
    // Envío de correo DE PRUEBA SIEMPRE a MAURICIO
    // ---------------------------------------------------------
    // original: 
    kickoff_send_mail(
        $nuevo_email,
        "Se te ha asignado una tarea: {$tarea_titulo}",
        $html,
        "iContel Telecom <servicioalcliente@icontel.cl>"
    );
    
    // REDIRECCIONADO PARA PRUEBAS:
    /*
    kickoff_send_mail(
        "maraneda@tnagroup.cl", // <--- EMAIL DE PRUEBA
        "Se te ha asignado una tarea: {$tarea_titulo}",
        $html,
        "iContel Telecom <servicioalcliente@icontel.cl>"
    );
    */
}

// =============================================
// FIN OK
// =============================================
$response = ["success" => true];

if (isset($nuevo_nombre) && isset($nuevo_email) && $nuevo_email !== "") {
    $response["mail_info"] = 
        "El usuario {$nuevo_nombre} fue notificado por eMail ({$nuevo_email}) de la asignación de la tarea: \"{$tarea_titulo}\".";
}
echo json_encode($response);
exit;
?>