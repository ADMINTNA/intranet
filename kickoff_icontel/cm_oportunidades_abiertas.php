<?php
// ==========================================================
// KickOff AJAX – Oportunidades Abiertas
// /kickoff_icontel/cm_oportunidades_abiertas.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// ------------------------------------------------------
// Bootstrap AJAX (sesión + $sg_id + $sg_name + DbConnect)
// ------------------------------------------------------
require_once __DIR__ . "/ajax_bootstrap.php";

// Seguridad mínima
if ($sg_id === "" || $sg_name === "") {
    echo "<div style='padding:20px; color:red;'>❌ Error: sesión inválida.</div>";
    exit;
}

// ------------------------------------------------------
// Conectar con Sweet
// ------------------------------------------------------
$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

// Stored Procedure real
$sql = "CALL Oportunidades_Pendientes('" . $conn->real_escape_string($sg_id) . "')";
$resultado = $conn->query($sql);

// ------------------------------------------------------
// Procesamiento
// ------------------------------------------------------
$ptr       = 0;
$contenido = "";
$datos     = [];
$muestra   = false;

if ($resultado && $resultado->num_rows > 0) {

    $muestra = true;

    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }

    // Ordenar por días ASC
    usort($datos, function($a, $b) {
        return intval($a['dias']) - intval($b['dias']);
    });

    foreach ($datos as $lin) {

        $ptr++;
        $dias   = intval($lin["dias"]);
        $estado = $lin["estado"];

        // -----------------------------
        // Color según estado / días
        // -----------------------------
        if ($dias > 10) {
            $importancia = "Cotizar";
        } else {
            $importancia = $estado;
        }

        switch ($importancia) {
            case "1 Escalado Urgente":
                $color = "color:red;";
                break;
            case "2 Aceptadado, listo para Instalar":
            case "4 Pre Instalación":
                $color = "color:orangered;";
                break;
            case "3 Generar NV":
                $color = "color:orange;";
                break;
            case "Cotizar":
                $color = "color:green;";
                break;
            default:
                $color = "color:#333;";
        }

        // -----------------------------
        // Fila HTML
        // -----------------------------
        $contenido .= "<tr style='{$color}'>";

        $contenido .= "<td>{$ptr}</td>";

        $contenido .= '<td colspan="2"><a target="_blank" href="' .
            htmlspecialchars($lin["url_opor"]) . '">' .
            htmlspecialchars($lin["nombre"]) . '</a></td>';

        $contenido .= "<td>" . htmlspecialchars($lin["numero"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["cliente"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["estado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["asignado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["ejecutivo"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["f_creacion"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["f_modifica"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["proximo_paso"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["f_proximo_paso"]) . "</td>";
        $contenido .= "<td align='right'>" . $dias . "&nbsp;&nbsp;</td>";

        $contenido .= "</tr>";
    }

} else {

    $contenido = "
        <tr>
            <td colspan='13' style='text-align:center; padding:12px; color:#666;'>
                ⚠️ No se encontraron Oportunidades pendientes.
            </td>
        </tr>";
}

$conn->close();

// URL de nueva oportunidad
$url_nueva_oportunidad = "https://sweet.icontel.cl/index.php?module=Opportunities&action=EditView";
?>
<link rel="stylesheet" href="css/cm_tareas_pendientes.css?v=<?=time()?>">

<!-- ===================================================== -->
<!-- TABLA FINAL – VERSIÓN AJAX -->
<!-- ===================================================== -->

<div class="tabla-scroll">
<table id="oportunidades" width="100%" cellspacing="0" cellpadding="0" border="0">

    <tr style="background:#512554; color:white;">
        <td colspan="12" align="left" class="titulo" style="padding:8px;">
            &nbsp;&nbsp;💼 Oportunidades en Curso
        </td>

        <td align="right" style="background:#512554; padding-right:15px;">
            <a href="<?= $url_nueva_oportunidad ?>"
               target="new"
               title="Crear Nueva Oportunidad"
               style="color:white; font-size:22px; text-decoration:none;">
               +
            </a>
        </td>
    </tr>

    <tr class="subtit" style="background:#512554; color:white;">
        <th width="1%">#</th>
        <th colspan="2" width="18%">Asunto</th>
        <th width="2%">Número</th>
        <th width="13%">Cliente</th>
        <th width="6%">Estado</th>
        <th width="8%">Asignado a</th>
        <th width="8%">Ejecutiv@</th>
        <th width="5%">Fecha<br>Creación</th>
        <th width="5%">Fecha<br>Modificación</th>
        <th width="20%">Próximo Paso</th>
        <th width="5%">Fecha<br>P. Paso</th>
        <th width="5%" align="right">Días<br>Restantes</th>
    </tr>

    <?= $contenido ?>

</table>
</div>