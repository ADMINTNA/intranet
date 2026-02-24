<?php
//=====================================================
// /intranet/kickoff_icontel/cm_casos_abiertos.php
// Casos Abiertos – Versión AJAX
//=====================================================

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");

require_once __DIR__ . '/session_core.php';
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/security_groups.php";

$db_sweet       = $db_sweet ?? "tnasolut_sweet";
$sg_id          = $_SESSION['sg_id'] ?? "";
$url_nuevo_caso = "https://sweet.icontel.cl/index.php?module=Cases&action=EditView";

$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

$lista_prioridad = sweet_get_dropdown_api("case_priority_dom");
$lista_estado    = sweet_get_dropdown_api("case_status_dom");
$lista_categoria = sweet_get_dropdown_api("case_type_dom");

$sqlUsers = "SELECT id, first_name, last_name FROM users WHERE deleted = 0 AND status = 'Active' ORDER BY first_name, last_name";
$rsUsers = $conn->query($sqlUsers);
$lista_usuarios = [];
if ($rsUsers) {
    while ($u = $rsUsers->fetch_assoc()) {
        $lista_usuarios[$u["id"]] = trim($u["first_name"] . " " . $u["last_name"]);
    }
    $rsUsers->free();
}

$sql = "CALL Kick_Off_Operaciones_Abiertos('" . $conn->real_escape_string($sg_id) . "')";
$result = $conn->query($sql);

$ptr = 0;
$contenido = "";
$muestra = ($result && $result->num_rows > 0);

if ($muestra) {
    while ($row = $result->fetch_assoc()) {
        $ptr++;
        switch ($row["prioridad"]) {
            case "P1E": $color = "red"; break;
            case "P1":  $color = "orangered"; break;
            case "P2":  $color = "orange"; break;
            case "P3":  $color = "dimgray"; break;
            default:    $color = "inherit"; break;
        }

        $caso_id = $row["id"] ?? "";
        
        $usuario_id_sel = "";
        $nombre_completo = trim($row["nombre"] . " " . $row["apellido"]);
        foreach($lista_usuarios as $uid => $uname) {
            if ($uname === $nombre_completo) {
                $usuario_id_sel = $uid;
                break;
            }
        }

        $contenido .= "<tr data-id='{$caso_id}' style='color:{$color};'>";
        // COMBINAMOS COLUMNA # CON ESTADO AJAX
        $contenido .= "<td class='estado-ajax' style='text-align:center; min-width:30px;'>{$ptr}</td>";
        $contenido .= "<td><a target='_blank' href='" . htmlspecialchars($row["url_caso"]) . "'>" . htmlspecialchars($row["numero"]) . "</a></td>";
        
        $contenido .= "<td><select data-campo='priority'>";
        foreach ($lista_prioridad as $k => $v) {
            $sel = ($k == $row["prioridad"]) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        $contenido .= "</select></td>";

        $contenido .= "<td>" . htmlspecialchars($row["asunto"]) . "</td>";

        $estado_db = trim($row["estado"]);
        $contenido .= "<td><select data-campo='status'>";
        foreach ($lista_estado as $k => $v) {
             $sel = (mb_strtolower($k, 'UTF-8') === mb_strtolower($estado_db, 'UTF-8') || mb_strtolower($v, 'UTF-8') === mb_strtolower($estado_db, 'UTF-8')) ? "selected" : "";
             $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        $contenido .= "</select></td>";

        $contenido .= "<td><input type='text' data-campo='en_espera_de' value='" . htmlspecialchars($row["en_espera_de"]) . "'></td>";

        $cat_db = trim($row["categoria"]);
        $contenido .= "<td><select data-campo='category'>";
        foreach ($lista_categoria as $k => $v) {
            $sel = (mb_strtolower($k, 'UTF-8') === mb_strtolower($cat_db, 'UTF-8') || mb_strtolower($v, 'UTF-8') === mb_strtolower($cat_db, 'UTF-8')) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        $contenido .= "</select></td>";

        $contenido .= "<td><select data-campo='assigned_user_id'>";
        foreach ($lista_usuarios as $uid => $uname) {
            $sel = ($uid == $usuario_id_sel) ? "selected" : "";
            $contenido .= "<option value='{$uid}' {$sel}>{$uname}</option>";
        }
        $contenido .= "</select></td>";

        $contenido .= "<td>" . htmlspecialchars($row["cliente"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["f_creacion"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["f_modifica"]) . "</td>";
        $contenido .= "<td align='right'>" . htmlspecialchars($row["dias"]) . "</td>";
        $contenido .= "</tr>";
    }
} else {
    $contenido = "<tr><td colspan='12' style='padding:10px; text-align:center; color:#AAA;'>&#128683; No se encontraron casos abiertos</td></tr>";
}

$conn->close();
?>
<link rel="stylesheet" href="css/kickoff.css">
<link rel="stylesheet" href="css/cm_tareas_pendientes.css">

<div class="tabla-scroll">
    <table id="casos_abiertos" border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr style="color:white; background:#512554;">
            <td colspan="11" align="left" class="titulo" style="padding:8px 10px; font-size:16px;">
                 &#129534; Casos Abiertos
            </td>
            <td align="right" style="font-size:20px; background:#512554;">
                <a href="<?= $url_nuevo_caso ?>" target="_blank" style="color:white; text-decoration:none;"> + </a>&nbsp;&nbsp;
            </td>
        </tr>
        <tr class="subtit">
            <th class='subtitulo'>#</th>
            <th class='subtitulo'>N&deg;</th>
            <th class='subtitulo'>Prioridad</th>
            <th class='subtitulo'>Asunto</th>
            <th class='subtitulo'>Estado</th>
            <th class='subtitulo'>En Espera De</th>
            <th class='subtitulo'>Categor&iacute;a</th>
            <th class='subtitulo'>Asignado a</th>
            <th class='subtitulo'>Raz&oacute;n Social</th>
            <th class='subtitulo'>F. Creaci&oacute;n</th>
            <th class='subtitulo'>F. Modif.</th>
            <th class='subtitulo'>D&iacute;as</th>
        </tr>
        <?= $contenido ?>
    </table>
</div>

<script src="js/cm_sort.js?v=<?=time()?>"></script>
<script src="js/cm_casos_abiertos.js?v=<?=time()?>"></script>