<?php
// ==========================================================
// KickOff AJAX – Casos Abiertos Sujeto a Cobro
// /kickoff_icontel/cm_casos_abiertos_sujeto_a_cobro.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// ------------------------------------------------------
// Bootstrap AJAX (sesión + $sg_id + $sg_name + DbConnect)
// ------------------------------------------------------
require_once __DIR__ . "/ajax_bootstrap.php";

// Seguridad
if ($sg_id === "" || $sg_name === "") {
    echo "<div style='padding:20px; color:red;'>❌ Error: sesión inválida.</div>";
    exit;
}

// ------------------------------------------------------
// Conexión SweetCRM
// ------------------------------------------------------
$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

// ------------------------------------------------------
// Listas via API SweetCRM (para dropdowns editables)
// ------------------------------------------------------
$lista_prioridad = sweet_get_dropdown_api("case_priority_dom");
$lista_estado    = sweet_get_dropdown_api("case_status_dom");
$lista_categoria = sweet_get_dropdown_api("case_type_dom");
$lista_tipo      = sweet_get_dropdown_api("case_category_dom");

// ------------------------------------------------------
// Usuarios activos (para dropdown Asignado a)
// ------------------------------------------------------
$sqlUsers = "
    SELECT id, first_name, last_name 
    FROM users
    WHERE deleted = 0 AND status = 'Active'
    ORDER BY first_name, last_name
";
$rsUsers = $conn->query($sqlUsers);

$lista_usuarios = [];
if ($rsUsers) {
    while ($u = $rsUsers->fetch_assoc()) {
        $lista_usuarios[$u["id"]] = trim($u["first_name"] . " " . $u["last_name"]);
    }
    $rsUsers->free();
}

// -------------------------------------------------------------
// Ejecutar SP (limpiando resultados previos del motor MySQL)
// -------------------------------------------------------------
while ($conn->more_results()) {
    $conn->next_result();
}

// Procedimiento almacenado
$sql = "CALL Kick_Off_Operaciones_Abiertos_sujeto_a_cobro()";
$result = $conn->query($sql);

// ------------------------------------------------------
// Construcción de datos
// ------------------------------------------------------
$ptr       = 0;
$contenido = "";
$muestra   = ($result && $result->num_rows > 0);

if ($muestra) {

    while ($row = $result->fetch_assoc()) {

        $ptr++;

        // Color por prioridad
        switch ($row["prioridad"]) {
            case "P1E": $color = "red";        break;
            case "P1":  $color = "orangered";  break;
            case "P2":  $color = "orange";     break;
            case "P3":  $color = "dimgray";    break;
            default:    $color = "inherit";
        }
        
        $caso_id = $row["id"] ?? "";

        // Usuario asignado: Usar ID directo del SP
        $usuario_id_sel = $row["id_usuario"] ?? "";

        $contenido .= "<tr data-id='{$caso_id}' style='color:{$color};'>";

        // Fila segura
        $contenido .= "<td>{$ptr}</td>";

        $contenido .= "<td><a target='_blank' href='" . 
                      htmlspecialchars($row["url_caso"]) . "'>" .
                      htmlspecialchars($row["numero"]) .
                      "</a></td>";

        $contenido .= "<td>" . htmlspecialchars($row["asunto"]) . "</td>";
        
        // PRIORIDAD - Dropdown editable
        $contenido .= "<td><select data-campo='priority'>";
        foreach ($lista_prioridad as $k => $v) {
            $sel = ($k == $row["prioridad"]) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        $contenido .= "</select></td>";

        // ESTADO - Dropdown editable (Lógica Robusta)
        $estado_db = trim($row["estado"]);
        $estado_selected = "";
        $estado_match_found = false;

        if (array_key_exists($estado_db, $lista_estado)) {
             $estado_selected = $estado_db;
             $estado_match_found = true;
        } else {
             $estado_db_lower = mb_strtolower($estado_db, 'UTF-8');
             foreach ($lista_estado as $k => $v) {
                 if (mb_strtolower($k, 'UTF-8') === $estado_db_lower) {
                     $estado_selected = $k;
                     $estado_match_found = true;
                     break;
                 }
                 if (mb_strtolower($v, 'UTF-8') === $estado_db_lower) {
                     $estado_selected = $k;
                     $estado_match_found = true;
                     break;
                 }
             }
        }

        if (!$estado_match_found && $estado_db !== "") {
            $estado_selected = $estado_db;
        }

        $contenido .= "<td><select data-campo='status'>";
        foreach ($lista_estado as $k => $v) {
            $sel = ((string)$k === (string)$estado_selected) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        if (!$estado_match_found && $estado_db !== "") {
            $contenido .= "<option value='{$estado_db}' selected>{$estado_db}</option>";
        }
        $contenido .= "</select></td>";

        // CATEGORÍA - Dropdown editable (Lógica Robusta)
        $cat_db = trim($row["categoria"]);
        $cat_selected = "";
        $cat_match_found = false;

        if (array_key_exists($cat_db, $lista_categoria)) {
             $cat_selected = $cat_db;
             $cat_match_found = true;
        } else {
             $cat_db_lower = mb_strtolower($cat_db, 'UTF-8');
             foreach ($lista_categoria as $k => $v) {
                 if (mb_strtolower($k, 'UTF-8') === $cat_db_lower) {
                     $cat_selected = $k;
                     $cat_match_found = true;
                     break;
                 }
                 if (mb_strtolower($v, 'UTF-8') === $cat_db_lower) {
                     $cat_selected = $k;
                     $cat_match_found = true;
                     break;
                 }
             }
        }
        
        if (!$cat_match_found && $cat_db !== "") {
            $cat_selected = $cat_db;
        }

        $contenido .= "<td><select data-campo='category'>";
        foreach ($lista_categoria as $k => $v) {
            $sel = ((string)$k === (string)$cat_selected) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        if (!$cat_match_found && $cat_db !== "") {
             $contenido .= "<option value='{$cat_db}' selected>{$cat_db}</option>";
        }
        $contenido .= "</select></td>";

        // TIPO - Dropdown editable (Lógica Robusta)
        $tipo_db = trim($row["tipo"]);
        $tipo_selected = "";
        $tipo_match_found = false;

        if (array_key_exists($tipo_db, $lista_tipo)) {
             $tipo_selected = $tipo_db;
             $tipo_match_found = true;
        } else {
             $tipo_db_lower = mb_strtolower($tipo_db, 'UTF-8');
             foreach ($lista_tipo as $k => $v) {
                 if (mb_strtolower($k, 'UTF-8') === $tipo_db_lower) {
                     $tipo_selected = $k;
                     $tipo_match_found = true;
                     break;
                 }
                 if (mb_strtolower($v, 'UTF-8') === $tipo_db_lower) {
                     $tipo_selected = $k;
                     $tipo_match_found = true;
                     break;
                 }
             }
        }
        
        if (!$tipo_match_found && $tipo_db !== "") {
            $tipo_selected = $tipo_db;
        }

        $contenido .= "<td><select data-campo='tipo_custom'>";
        foreach ($lista_tipo as $k => $v) {
            $sel = ((string)$k === (string)$tipo_selected) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        if (!$tipo_match_found && $tipo_db !== "") {
             $contenido .= "<option value='{$tipo_db}' selected>{$tipo_db}</option>";
        }
        $contenido .= "</select></td>";

        // ASIGNADO A - Dropdown editable
        $usuario_id_sel = $row["id_usuario"] ?? "";
        $user_name_db   = trim(($row["nombre"] ?? "") . " " . ($row["apellido"] ?? ""));
        
        $contenido .= "<td><select data-campo='assigned_user_id'>";
        $option_found = false;
        foreach ($lista_usuarios as $k => $v) {
            if ($k == $usuario_id_sel) {
                $sel = "selected";
                $option_found = true;
            } else {
                $sel = "";
            }
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        
        // Si el usuario asignado no está en la lista (ej: inactivo), agregarlo visualmente
        if (!$option_found && $usuario_id_sel) {
             $display_name = $user_name_db ?: "Usuario {$usuario_id_sel}";
             $contenido .= "<option value='{$usuario_id_sel}' selected>{$display_name} (Inactivo/Missing)</option>";
        }
        $contenido .= "</select></td>";

        $contenido .= "<td>" . htmlspecialchars($row["cliente"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["f_creacion"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["f_modifica"]) . "</td>";
        $contenido .= "<td align='right'>" . htmlspecialchars($row["dias"]) . "&nbsp;&nbsp;</td>";

        $contenido .= "</tr>";
    }

} else {

    $contenido = "
        <tr>
            <td colspan='12' style='padding:12px; text-align:center; color:#666;'>
                ⚠️ No se encontraron Casos Sujetos a Cobro.
            </td>
        </tr>";
}

$conn->close();
unset($result);
unset($conn);
?>
<link rel="stylesheet" href="css/kickoff.css">
<link rel="stylesheet" href="css/cm_tareas_pendientes.css?v=<?=time()?>">

<!-- ===================================== -->
<!--  TABLA HTML – Casos Sujeto a Cobro     -->
<!-- ===================================== -->

<style>
#casos_sujeto_a_cobro tr.subtit th { background:#512554 !important; color:#fff !important; }
</style>

<div class="tabla-scroll">
    <table id="casos_sujeto_a_cobro" width="100%" cellpadding="0" cellspacing="0" border="0">
    
        <tr style="background:#512554; color:white;">
            <td colspan="12" class="titulo" style="padding:8px;">
                &nbsp;&nbsp;💰 Casos Sujetos a Cobro
            </td>
        </tr>
    
        <tr class="subtit" style="background:#512554; color:white;">
            <th class="subtitulo">#</th>
            <th class="subtitulo">Nº</th>
            <th class="subtitulo" style="white-space:nowrap">
                Asunto&nbsp;<input id="filtro-sac-asunto"
                    type="text" placeholder="🔍"
                    oninput="sacFilterAsunto(this.value)"
                    style="width:80px!important;padding:2px 5px!important;border:1px solid rgba(255,255,255,0.6)!important;border-radius:4px;background:rgba(255,255,255,0.2)!important;color:#fff!important;font-size:11px;font-weight:400;outline:none;vertical-align:middle"><span
                    id="filtro-sac-asunto-x"
                    onclick="document.getElementById('filtro-sac-asunto').value='';sacFilterAsunto('')"
                    title="Quitar filtro"
                    style="display:none;cursor:pointer;color:#ffd600;font-weight:bold;font-size:13px;vertical-align:middle;margin-left:2px">✕</span>
                
            </th>
            <th class="subtitulo">Prioridad</th>
            <th class="subtitulo">Estado</th>
            <th class="subtitulo">Categoría</th>
            <th class="subtitulo">Tipo</th>
            <th class="subtitulo">Asignado a</th>
            <th class="subtitulo">Razón Social</th>
            <th class="subtitulo">F. Creación</th>
            <th class="subtitulo">F. Modifica.</th>
            <th class="subtitulo" align="right">Días&nbsp;&nbsp;</th>
        </tr>
    
        <?= $contenido ?>
    
    </table>
</div>

<script>
function sacFilterAsunto(q){
    q=q.toLowerCase();
    var x=document.getElementById('filtro-sac-asunto-x');
    if(x) x.style.display=q?'inline':'none';
    document.querySelectorAll('#casos_sujeto_a_cobro tr').forEach(function(r){
        if(!r.querySelector('td')) return;
        var tds=r.querySelectorAll('td');
        if(parseInt(tds[0].getAttribute('colspan')||'0')>5) return; // skip título
        var txt=tds[2]?tds[2].textContent.toLowerCase():'';
        r.style.display=(!q||txt.includes(q))?'':'none';
    });
}
</script>

<script src="js/cm_sort.js?v=<?=time()?>"></script>
<script src="js/cm_casos_abiertos.js?v=<?=time()?>"></script>