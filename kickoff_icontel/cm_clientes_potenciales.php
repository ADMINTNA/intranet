<?php
// ==========================================================
// KickOff AJAX – Clientes Potenciales Pendientes
// /kickoff_icontel/cm_clientes_potenciales.php
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
// Procedimiento almacenado
// ------------------------------------------------------
$sql = "CALL Clientes_Potenciales_Pendientes()";
$resultado = $conn->query($sql);

$contenido = "";
$ptr       = 0;
$muestra   = ($resultado && $resultado->num_rows > 0);

// ------------------------------------------------------
// Construcción de filas
// ------------------------------------------------------
if ($muestra) {

    while ($lin = $resultado->fetch_assoc()) {

        $ptr++;

        // Color según estado o días
        if ((int)$lin["dias"] > 0) {
            $color = "color:red;";
        } else {
            switch (trim($lin["estado"])) {
                case "1 Nuevo":
                    $color = "color:red;";
                    break;
                case "2 Asignado":
                    $color = "color:orange;";
                    break;
                case "3 En Proceso":
                case "4 Retomar en 3 meses":
                    $color = "color:green;";
                    break;
                default:
                    $color = "color:#333;";
            }
        }

        // Fila
        $contenido .= "<tr style='{$color}'>";
        $contenido .= "<td>{$ptr}</td>";

        // Nombre + link
        $contenido .= "<td colspan='2'>
                         <a target='_blank' href='" . htmlspecialchars($lin["url_lead"]) . "'>
                             " . htmlspecialchars($lin["nombre"]) . "
                         </a>
                       </td>";

        $contenido .= "<td>" . htmlspecialchars($lin["estado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["campana"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["usuario"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["f_creacion"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["f_prox_paso"]) . "</td>";
        $contenido .= "<td align='right'>" . htmlspecialchars($lin["dias"]) . "&nbsp;&nbsp;</td>";

        $contenido .= "</tr>";
    }

} else {

    $contenido = "
        <tr>
            <td colspan='9' style='text-align:center; padding:12px; color:#666;'>
                ⚠️ No se encontraron Clientes Potenciales pendientes.
            </td>
        </tr>";
}

$conn->close();
unset($resultado);
unset($conn);

?>

<!-- =================================================== -->
<!--  TABLA CLIENTES POTENCIALES — KickOff AJAX          -->
<!-- =================================================== -->

<div class="tabla-scroll">
<table id="clientes_potenciales" width="100%" cellpadding="0" cellspacing="0" border="0">

    <tr style="background:#512554; color:white;">
        <td colspan="8" align="left" class="titulo" style="padding:8px;">
            &nbsp;&nbsp;🗂️ Clientes Potenciales en Proceso
        </td>
        <td align="right" style="font-size:20px; padding-right:15px; background:#512554;">
            <a href="<?= $url_nuevo_lead ?>" 
               target="new" 
               title="Crear nuevo Lead"
               style="color:white; text-decoration:none; font-size:22px;">+</a>
        </td>
    </tr>

    <tr class="subtit" style="background:#512554; color:white;">
        <th class="subtitulo">#</th>
        <th class="subtitulo" colspan="2" width="25%">Nombre</th>
        <th class="subtitulo" width="10%">Estado</th>
        <th class="subtitulo">Campaña</th>
        <th class="subtitulo" width="11%">Asignado a</th>
        <th class="subtitulo" width="10%">F. Creación</th>
        <th class="subtitulo" width="10%">F. Próximo Paso</th>
        <th class="subtitulo" width="1%" align="right">Días&nbsp;&nbsp;</th>
    </tr>

    <?= $contenido ?>

</table>
</div>