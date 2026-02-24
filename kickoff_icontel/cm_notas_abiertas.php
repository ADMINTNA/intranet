<?php
// ==========================================================
// KickOff AJAX – Notas Abiertas
// /kickoff_icontel/cm_notas_abiertas.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// Bootstrap común AJAX (sesión + config + $sg_id, $sg_name, $db_sweet, etc.)
require_once __DIR__ . "/ajax_bootstrap.php";
require_once __DIR__ . "/config.php";

// Validación estricta de sesión para AJAX
if (empty($_SESSION['sg_id']) || empty($_SESSION['sg_name'])) {
    error_log("NOTAS → sg_id o sg_name vacío");
    echo "<div style='padding:20px; color:red;'>❌ Error: sesión inválida.</div>";
    return;
}

$sg_id   = $_SESSION['sg_id'];
$sg_name = $_SESSION['sg_name'];

// ----------------------------------------------------------
// Conexión
// ----------------------------------------------------------
$conn = DbConnect($db_sweet);

// URL crear nota
$url_nueva_nota =
    "https://sweet.icontel.cl/index.php?module=Notes&action=EditView&return_module=Notes&return_action=DetailView";

// SP
$sql = "CALL cm_notas_abiertas('" . $conn->real_escape_string($sg_id) . "')";
$result = $conn->query($sql);

// ----------------------------------------------------------
// Procesar registros
// ----------------------------------------------------------
$ptr = 0;
$contenido = "";

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $ptr++;
        $dias = (int)$row["dias_sin_modificar"];

        // Color según días sin modificar
        if ($dias > 4) {
            $color = "color:red;";
        } elseif ($dias >= 3) {
            $color = "color:orange;";
        } else {
            $color = "color:green;";
        }

        $contenido .= "<tr style='$color'>";

        $contenido .= "<td>$ptr</td>";

        $contenido .= '<td><a target="_blank" href="' . $row["url_nota"] . '">' .
                        htmlspecialchars($row["asunto"], ENT_QUOTES, "UTF-8") .
                      '</a></td>';

        $contenido .= "<td>" . htmlspecialchars($row["fecha_creacion"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["relacionado_con"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["nota_estado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["departamento"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["asignado_a"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["modificado_por"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["fecha_modificacion"]) . "</td>";
        $contenido .= "<td align='right'>$dias&nbsp;</td>";

        $contenido .= "</tr>";
    }

} else {

    $contenido = "
        <tr>
            <td colspan='10' style='padding:10px; text-align:center; color:#AAA;'>
                ⚠️ No se encontraron Notas Abiertas
            </td>
        </tr>";
}

$conn->close();
unset($result);

// ----------------------------------------------------------
// Encabezado de tabla
// ----------------------------------------------------------
$td = '
<td colspan="9" align="left" class="titulo"
    style="font-size:18px; font-weight:bold; color:#C39BD3; background:#512554; padding:8px;">
    &nbsp;&nbsp;📝 Notas Abiertas
</td>
<td align="right" style="font-size:22px; font-weight:bold; color:#C39BD3; background:#512554; padding-right:12px;">
    <a href="' . $url_nueva_nota . '" target="_blank" title="Crear Nueva Nota"
       style="color:#C39BD3; text-decoration:none; font-size:24px;"><b>+</b></a>
</td>';
?>

<div class="tabla-scroll">
<table id="notas_abiertas" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <?= $td ?>
    </tr>

    <tr class="subtit">
        <th class="subtitulo">#</th>
        <th class="subtitulo">Asunto</th>
        <th class="subtitulo">F. Creación</th>
        <th class="subtitulo">Relacionado Con</th>
        <th class="subtitulo">Estado</th>
        <th class="subtitulo">Categoría</th>
        <th class="subtitulo">Asignado a</th>
        <th class="subtitulo">Modificado Por</th>
        <th class="subtitulo">F. Modif.</th>
        <th class="subtitulo" align="right">Días&nbsp;</th>
    </tr>

    <?= $contenido ?>
</table>
</div>