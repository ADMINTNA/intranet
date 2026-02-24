<?php
// ==========================================================
// KickOff AJAX – Tareas ASIGNADAS A USTED
// /intranet/kickoff_icontel/cm_tareas_pendientes.php
// Autor: Mauricio Araneda (mAo)
// Versión AJAX – Editables + Correcciones
// Codificación: UTF-8 sin BOM
// ==========================================================
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
// Bootstrap común AJAX (sesión + config + $sg_id + $sg_name + DbConnect)
require_once __DIR__ . "/ajax_bootstrap.php";

$conn = DbConnect($db_sweet);

// ---------------------------------------------------------
// Listas via API SweetCRM (para los selects editables)
// ---------------------------------------------------------
$lista_categoria = sweet_get_dropdown_api("categoria_list");
$lista_prioridad = sweet_get_dropdown_api("task_priority_dom");
$lista_estado    = sweet_get_dropdown_api("task_status_dom");

// ---------------------------------------------------------
// Usuarios activos (assigned_user_id)
// ---------------------------------------------------------
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

$sql = "CALL Kick_Off_Operaciones_Tareas_Abiertas('" . $conn->real_escape_string($sg_id) . "')";
$resultado = $conn->query($sql);

$datos = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $datos[] = $row;
    }
}

// -------------------------------------------------------------
// Ordenar por fecha de vencimiento (ASC)
// -------------------------------------------------------------
usort($datos, function ($a, $b) {
    $fa = strtotime(str_replace('/', '-', $a['f_vencimiento']));
    $fb = strtotime(str_replace('/', '-', $b['f_vencimiento']));
    if ($fa === false) return 1;
    if ($fb === false) return -1;
    return $fa <=> $fb;
});

// URLs base SweetCRM
$url_caso      = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26action%3DDetailView%26record%3D";
$url_opor      = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DOpportunities%26action%3DDetailView%26record%3D";
$url_cuenta    = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAccounts%26action%3DDetailView%26record%3D";
$url_insidente = "https://sweet.icontel.cl/index.php?module=Bugs&action=DetailView&record=";

?>
<link rel="stylesheet" href="css/kickoff.css?v=<?=time()?>">

<link rel="stylesheet" href="css/cm_tareas_pendientes.css">


<div class="tabla-scroll">
<table id="tareas" width="100%">

<tr>
    <td colspan="13" class="titulo" align="left">
        &nbsp;&nbsp;⏳ Tareas Abiertas ASIGNADAS A USTED
    </td>
    <td align="right" style="font-size:20px; background:#512554; color:white;">
        <a href="<?=$url_nueva_tarea?>" target="new" title="Crear Nueva Tarea"
           style="color:white; text-decoration:none;"><b>+</b></a>&nbsp;&nbsp;&nbsp;
    </td>
</tr>
<tr class="subtit">
    <th class="subtitulo">&nbsp;</th>
    <th class="subtitulo">#</th>
    <th class="subtitulo">Asunto</th>
    <th class="subtitulo">Categoría</th>
    <th class="subtitulo">Prioridad</th>
    <th class="subtitulo">Asignado a</th>
    <th class="subtitulo">Estado</th>
    <th class="subtitulo">En Espera de</th>
    <th class="subtitulo">Origen Tipo</th>
    <th class="subtitulo">N°</th>
    <th class="subtitulo">Creación</th>
    <th class="subtitulo">Modificación</th>
    <th class="subtitulo">Vencimiento</th>
    <th class="subtitulo" align="right">Días</th>
</tr>

<?php
$ptr = 0;
foreach ($datos as $lin):


    $ptr++;

    // ==============================
    // OBTENER ID de tarea
    // ==============================
    $id = "";
    if (!empty($lin["id"])) {
        $id = $lin["id"];
    } elseif (!empty($lin["url"]) && preg_match('/record=([^&]+)/', $lin["url"], $m)) {
        $id = $m[1];
    }
    if (!$id) continue;

    // ==============================
    // FECHA VENCIMIENTO (formato HTML)
    // ==============================
    $fechaSQL = "";
    if (!empty($lin["f_vencimiento"])) {
        $ts = strtotime(str_replace('/', '-', $lin["f_vencimiento"]));
        if ($ts) $fechaSQL = date("Y-m-d", $ts);
    }

    // ==============================
    // Usuario asignado
    // ==============================
    $usuario_id_seleccionado = "";
    
    // DEBUG: Ver qué campo tiene el nombre del usuario
    $nombre_asignado = "";
    if (!empty($lin["asignado"])) {
        $nombre_asignado = trim($lin["asignado"]);
    } elseif (!empty($lin["usuario"])) {
        $nombre_asignado = trim($lin["usuario"]);
    }
    
    if (!empty($nombre_asignado)) {
        // Búsqueda case-insensitive
        foreach ($lista_usuarios as $uid => $uname) {
            if (strcasecmp($nombre_asignado, $uname) === 0) {
                $usuario_id_seleccionado = $uid;
                break;
            }
        }
        
        // Si no se encontró, intentar búsqueda parcial
        if (empty($usuario_id_seleccionado)) {
            foreach ($lista_usuarios as $uid => $uname) {
                if (stripos($uname, $nombre_asignado) !== false || stripos($nombre_asignado, $uname) !== false) {
                    $usuario_id_seleccionado = $uid;
                    break;
                }
            }
        }
    }

    // ==============================
    // Color de fila según prioridad
    // ==============================
    $importancia = $lin["prioridad"];
    if (!empty($lin["dias"]) && $lin["dias"] > 10) {
        $importancia = "Baja";
    }

    $trStyle = "";
    if (stripos($importancia, "URGENTE") !== false)  $trStyle = "color:red;";
    elseif (stripos($importancia, "Alta") !== false) $trStyle = "color:orange;";
    elseif (stripos($importancia, "Baja") !== false) $trStyle = "color:green;";

    // ==============================
    // Origen + links
    // ==============================
    $htmlOrigenTipo = htmlspecialchars($lin["origen"]);
    $htmlOrigenNum  = htmlspecialchars($lin["numero"]);

    switch ($lin["origen"]) {
        case "Cases":
            $htmlOrigenTipo = "CASO";
            $htmlOrigenNum  = '<a target="_blank" href="'.$url_caso.$lin["origen_id"].'">' .
                               htmlspecialchars($lin["numero"]).'</a>';
            break;

        case "Opportunities":
            $htmlOrigenTipo = "OPORTUNIDAD";
            $htmlOrigenNum  = '<a target="_blank" href="'.$url_opor.$lin["origen_id"].'">' .
                               htmlspecialchars($lin["numero"]).'</a>';
            break;

        case "Accounts":
            $htmlOrigenTipo = "CUENTA";
            $htmlOrigenNum  = '<a target="_blank" href="'.$url_cuenta.$lin["origen_id"].'">' .
                               htmlspecialchars($lin["numero"]).'</a>';
            break;

        case "Bugs":
            $htmlOrigenTipo = "INCIDENTE";
            $htmlOrigenNum  = '<a target="_blank" href="'.$url_insidente.$lin["origen_id"].'">' .
                               htmlspecialchars($lin["numero"]).'</a>';
            break;
    }
?>

<tr data-id="<?=$id?>" style="<?=$trStyle?>">

    <td><?=$ptr?></td>

    <td>
        <a target="_blank" href="<?=htmlspecialchars($lin["url"])?>">
            <?=htmlspecialchars($lin["tarea"])?>
        </a>
    </td>

    <td>
        <select data-campo="categoria">
        <?php 
        // Limpiar bien el valor de la BD
        $categoria_actual = isset($lin["categoria"]) ? trim($lin["categoria"]) : "";
        
        foreach ($lista_categoria as $k => $v): 
            // Limpiar también la key del array
            $k_clean = trim($k);
            $cat_clean = trim($categoria_actual);
            
            // Comparación exacta después de limpiar
            $selected = ($k_clean === $cat_clean) ? "selected" : "";
        ?>
            <option value="<?=$k?>" <?=$selected?>><?=$v?></option>
        <?php endforeach; ?>
        </select>
    </td>

    <td>
        <select data-campo="prioridad">
        <?php foreach ($lista_prioridad as $k => $v): ?>
            <option value="<?=$k?>" <?=($k==$lin["prioridad"]?"selected":"")?>><?=$v?></option>
        <?php endforeach; ?>
        </select>
    </td>

    <td>
        <select data-campo="assigned_user_id">
        <?php foreach ($lista_usuarios as $uid => $uname): ?>
            <option value="<?=$uid?>" <?=($uid==$usuario_id_seleccionado?"selected":"")?>><?=$uname?></option>
        <?php endforeach; ?>
        </select>
    </td>

    <td>
        <?php
        $estado_db = trim($lin["estado"]);
        $estado_selected = "";

        // MAPA MANUAL DE CORRECCIONES (DB => Key del sistema)
        $mapa_estados = [
            "Atrasada"             => "ATRASADA",
            "En Progreso"          => "In Progress",
            "Pendiente de Cliente" => "pendiente_cliente",
            "Reasignada"           => "Reasignada"
        ];

        // 1. Verificar mapa manual
        if (isset($mapa_estados[$estado_db])) {
            $estado_selected = $mapa_estados[$estado_db];
        }
        // 2. Coincidencia directa con la LLAVE
        elseif (array_key_exists($estado_db, $lista_estado)) {
            $estado_selected = $estado_db;
        } 
        else {
            // 3. Búsqueda insensible
            $estado_db_lower = mb_strtolower($estado_db);
            
            foreach ($lista_estado as $key_list => $val_list) {
                if (mb_strtolower($key_list) === $estado_db_lower) {
                    $estado_selected = $key_list;
                    break;
                }
                if (mb_strtolower($val_list) === $estado_db_lower) {
                    $estado_selected = $key_list;
                    break;
                }
            }
        }
        ?>
        <select data-campo="estado">
        <?php foreach ($lista_estado as $k => $v): ?>
            <option value="<?=$k?>" <?=($k == $estado_selected) ? "selected" : ""?>><?=$v?></option>
        <?php endforeach; ?>
        </select>
    </td>

    <td><input type="text" data-campo="en_espera" value="<?=htmlspecialchars(isset($lin["enesperade"]) ? $lin["enesperade"] : "")?>"></td>

    <td><?=$htmlOrigenTipo?></td>
    <td align="center"><?=$htmlOrigenNum?></td>

    <td><?=htmlspecialchars($lin["f_creacion"])?></td>
    <td><?=htmlspecialchars($lin["f_modifica"])?></td>

    <td>
        <input type="date" data-campo="date_due" value="<?=$fechaSQL?>" style="width:120px; text-align:center;">
    </td>

    <td align="right"><?=htmlspecialchars($lin["dias"])?></td>

</tr>

<?php endforeach; ?>

<?php if (count($datos) === 0): ?>
<tr><td colspan="14" align="center">No se encontraron Tareas Asignadas.</td></tr>
<?php endif; ?>

</table>
</div>

<script src="js/cm_tareas_pendientes.js?v=<?=time()?>_5"></script>
<script src="js/cm_sort.js?v=<?=time()?>"></script>
<script src="js/cm_resizable_columns.js?v=<?=time()?>"></script>
