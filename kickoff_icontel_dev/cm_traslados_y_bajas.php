<?php
// ==========================================================
// KickOff AJAX – Cotizaciones de Baja o Traslado
// /kickoff_icontel/cm_traslados_y_bajas.php
// Autor: Mauricio Araneda (mAo)
// Versión AJAX Optimizada – UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// ------------------------------------------------------
// Bootstrap AJAX (sesiòn + sg + DbConnect + URLs Sweet)
// ------------------------------------------------------
require_once __DIR__ . "/ajax_bootstrap.php";

// Seguridad mínima
if ($sg_id === "" || $sg_name === "") {
    echo "<div style='padding:20px; color:red;'>❌ Error: sesión inválida.</div>";
    exit;
}

// ------------------------------------------------------
// Conexión a SweetCRM
// ------------------------------------------------------
$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

// SP original
$sql = "CALL CM_Cotizaciones_baja_traslado()";
$resultado = $conn->query($sql);

$ptr       = 0;
$contenido = "";
$muestra   = ($resultado && $resultado->num_rows > 0);

// ------------------------------------------------------
// Construcción de filas
// ------------------------------------------------------
if ($muestra) {

    while ($lin = $resultado->fetch_assoc()) {

        $ptr++;

        // ------------------------------
        // Color según estado de cotización
        // ------------------------------
        switch ($lin["coti_estado"]) {

            case "SUSPENDIDO":
                $color = "color:red;";
                break;

            case "Posible Traslado":
                $color = "color:orangered;";
                break;

            case "Generar Baja":
                $color = "color:orange;";
                break;

            case "Cotizar":
                $color = "color:green;";
                break;

            default:
                $color = "color:#333;";
        }

        $contenido .= "<tr style='{$color}'>";

        // Celdas seguras
        $contenido .= "<td width='1%'>{$ptr}</td>";
        $contenido .= '<td width="1%"><a target="_blank" href="' . htmlspecialchars($lin["url_coti"]) . '">' .
                        htmlspecialchars($lin["coti_numero"]) . '</a></td>';

        $contenido .= "<td>" . htmlspecialchars($lin["coti_nombre"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["coti_estado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["asignado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["coti_ejecutiva"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["coti_moneda"]) . "</td>";

        $contenido .= "<td align='right'>" . number_format($lin["coti_neto"], 2) . "</td>";

        $contenido .= '<td><a target="_blank" href="' . htmlspecialchars($lin["url_opor"]) . '">' .
                        htmlspecialchars($lin["opor_numero"]) . '</a></td>';

        $contenido .= "<td>" . htmlspecialchars($lin["cliente"]) . "</td>";

        // Fecha formateada correctamente
        $fecha = ($lin["coti_fecha_u_m"])
            ? date("d/m/Y", strtotime($lin["coti_fecha_u_m"]))
            : "";

        $contenido .= "<td>{$fecha}</td>";
        $contenido .= "<td align='right'>" . htmlspecialchars($lin["dias"]) . "</td>";

        $contenido .= "</tr>";
    }

} else {
    $contenido = "
    <tr>
        <td colspan='12' style='padding:12px; text-align:center; color:#777;'>
            ⚠️ No se encontraron Cotizaciones de Baja/Traslado.
        </td>
    </tr>";
}

$conn->close();
unset($resultado);
unset($conn);

// URL nueva cotización viene desde ajax_bootstrap.php
// $url_nueva_cotizacion ya está creado allí
?>

<!-- ======================================================= -->
<!--      TABLA – Cotizaciones de Baja o Traslado            -->
<!-- ======================================================= -->

<div class="tabla-scroll">
<table id="cotizaciones" width="100%" cellpadding="0" cellspacing="0" border="0">

    <tr style="background:#512554; color:white;">
        <td colspan="11" class="titulo" style="padding:8px;">
            &nbsp;&nbsp;⚠️ Cotizaciones de Baja o Traslado
        </td>
        <td align="right" style="padding-right:12px; font-size:22px;">
            <a href="<?= $url_nueva_cotizacion ?>" 
               target="new" 
               style="color:white; text-decoration:none;"
               title="Crear Nueva Cotización">+</a>
        </td>
    </tr>

    <tr class="subtit" style="background:#512554; color:white;">
        <th class="subtitulo" width="1%">#</th>
        <th class="subtitulo"width="1%">Nº</th>
        <th class="subtitulo" width="20%">Asunto</th>
        <th class="subtitulo" width="4%">Estado</th>
        <th class="subtitulo" width="8%">Asignado a</th>
        <th class="subtitulo" width="8%">Ejecutiv@</th>
        <th class="subtitulo" width="1%">$</th>
        <th class="subtitulo" width="1%">Neto</th>
        <th class="subtitulo" width="2%">OP Nº</th>
        <th class="subtitulo" width="15%">Cliente</th>
        <th class="subtitulo" width="1%">Modificada</th>
        <th class="subtitulo" width="1%" align="right">Días</th>
    </tr>

    <?= $contenido ?>

</table>
</div>

