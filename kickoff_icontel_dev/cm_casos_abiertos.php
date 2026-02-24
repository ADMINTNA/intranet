<?php
//=====================================================
// /intranet/kickoff_icontel/cm_casos_abiertos.php
// Casos Abiertos – Versi&oacute;n AJAX
// Autor: Mauricio Araneda
// Actualizado: 03-12-2025
// Codificaci&oacute;n: UTF-8 sin BOM
//=====================================================

// Forzar UTF-8 en el navegador
header('Content-Type: text/html; charset=utf-8');

// Mostrar errores durante desarrollo AJAX
error_reporting(E_ALL);
ini_set('display_errors', 1);

mb_internal_encoding("UTF-8");

// Mantener sesi&oacute;n del KickOff
require_once __DIR__ . '/session_core.php';

// Cargar contexto del KickOff
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/security_groups.php";

// Variables
$db_sweet       = $db_sweet ?? "tnasolut_sweet";
$sg_id          = $_SESSION['sg_id'] ?? "";
$url_nuevo_caso = "/intranet/casos/crear.php";

// ------------------------------------------------------
// Conexión a base de datos
// ------------------------------------------------------
$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

// ------------------------------------------------------
// Listas via API SweetCRM (para dropdowns editables)
// ------------------------------------------------------
$lista_prioridad = sweet_get_dropdown_api("case_priority_dom");
$lista_estado    = sweet_get_dropdown_api("case_status_dom");
$lista_categoria = sweet_get_dropdown_api("case_type_dom"); // Corregido de case_category_dom

// DEBUG: Verificar si las listas cargaron
echo "<script>";
echo "console.log('Cant. Prioridades:', " . count($lista_prioridad) . ");";
echo "console.log('Cant. Estados:', " . count($lista_estado) . ");";
echo "console.log('Cant. Categorías:', " . count($lista_categoria) . ");";
echo "</script>";

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

// ------------------------------------------------------
// Ejecutar Stored Procedure
// ------------------------------------------------------

$sql = "CALL Kick_Off_Operaciones_Abiertos('" . $conn->real_escape_string($sg_id) . "')";
$result = $conn->query($sql);

$ptr = 0;
$contenido = "";
$muestra = ($result && $result->num_rows > 0);

// ------------------------------------------------------
// Procesar resultados
// ------------------------------------------------------
if ($muestra) {

    while ($row = $result->fetch_assoc()) {

        $ptr++;

        // Color seg&uacute;n prioridad
        switch ($row["prioridad"]) {
            case "P1E": $color = "red"; break;
            case "P1":  $color = "orangered"; break;
            case "P2":  $color = "orange"; break;
            case "P3":  $color = "dimgray"; break;
            default:    $color = "inherit"; break;
        }

        // Obtener ID del caso
        $caso_id = $row["id"] ?? "";
        
        // Usuario asignado - buscar ID por nombre
        $usuario_id_sel = "";
        $nombre_completo = trim($row["nombre"] . " " . $row["apellido"]);
        $usuario_id_sel = array_search($nombre_completo, $lista_usuarios);
        if ($usuario_id_sel === false) $usuario_id_sel = "";

        $contenido .= "<tr data-id='{$caso_id}' style='color:{$color};'>";

        $contenido .= "<td>{$ptr}</td>";

        $contenido .= '<td><a target="_blank" href="' . htmlspecialchars($row["url_caso"]) . '">' .
                        htmlspecialchars($row["numero"]) .
                      '</a></td>';

        // PRIORIDAD - Dropdown editable
        $contenido .= "<td><select data-campo='priority'>";
        foreach ($lista_prioridad as $k => $v) {
            $sel = ($k == $row["prioridad"]) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        $contenido .= "</select></td>";

        $contenido .= "<td>" . htmlspecialchars($row["asunto"]) . "</td>";

        // ESTADO - Dropdown editable
        $estado_db = trim($row["estado"]);
        $estado_selected = "";
        $estado_match_found = false;

        // 1. Coincidencia Exacta Key
        if (array_key_exists($estado_db, $lista_estado)) {
             $estado_selected = $estado_db;
             $estado_match_found = true;
        } else {
             // 2. Coincidencia Insensible (Key o Value)
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

        // Si no se encontró, preservar el valor original
        if (!$estado_match_found && $estado_db !== "") {
            $estado_selected = $estado_db;
        }
        
        $contenido .= "<td><select data-campo='status'>";
        foreach ($lista_estado as $k => $v) {
            $sel = ((string)$k === (string)$estado_selected) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        // Si el valor seleccionado no estaba en la lista, agregarlo para que se vea
        if (!$estado_match_found && $estado_db !== "") {
            $contenido .= "<option value='{$estado_db}' selected>{$estado_db}</option>";
        }
        $contenido .= "</select></td>";

        // EN ESPERA DE - Input text editable
        $contenido .= "<td><input type='text' data-campo='en_espera_de' value='" . htmlspecialchars($row["en_espera_de"]) . "'></td>";

        // CATEGORÍA - Dropdown editable
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
        
        // Si no se encontró, preservar el valor original
        if (!$cat_match_found && $cat_db !== "") {
            $cat_selected = $cat_db;
        }

        $contenido .= "<td><select data-campo='category'>";
        foreach ($lista_categoria as $k => $v) {
            $sel = ((string)$k === (string)$cat_selected) ? "selected" : "";
            $contenido .= "<option value='{$k}' {$sel}>{$v}</option>";
        }
        // Si el valor seleccionado no estaba en la lista, agregarlo
        if (!$cat_match_found && $cat_db !== "") {
             $contenido .= "<option value='{$cat_db}' selected>{$cat_db}</option>";
        }
        $contenido .= "</select></td>";

        // ASIGNADO A - Dropdown editable
        $contenido .= "<td><select data-campo='assigned_user_id'>";
        foreach ($lista_usuarios as $uid => $uname) {
            $sel = ($uid == $usuario_id_sel) ? "selected" : "";
            $contenido .= "<option value='{$uid}' {$sel}>{$uname}</option>";
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
        <td colspan='12' style='padding:10px; text-align:center; color:#AAA;'>
            &#128683; No se encontraron casos abiertos
        </td>
    </tr>";
}

$conn->close();

$titulo = "&#129534; Casos Abiertos";
?>
<link rel="stylesheet" href="css/kickoff.css"   >
<link rel="stylesheet" href="css/cm_tareas_pendientes.css">

<!-- ===================================================== -->
<!-- TABLA HTML &#129534;  Casos Abiertos -->
<!-- ===================================================== -->

<div class="tabla-scroll">
    <table id="casos_abiertos" border="0" width="100%" cellpadding="0" cellspacing="0">

        <tr style="color:white; background:#512554;">
            <td colspan="11" align="left" class="titulo" style="padding:8px 10px; font-size:16px;">
                <?= $titulo ?>
            </td>

            <td align="right" style="font-size:20px; background:#512554;">
                <a href="<?= $url_nuevo_caso ?>" 
                   target="_blank" 
                   style="color:white; text-decoration:none;">
                   +
                </a>&nbsp;&nbsp;&nbsp;
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