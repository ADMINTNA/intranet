<?php
//=====================================================
// /intranet/kickoff_icontel/cm_casos_abiertos_congelados.php
// Casos Abiertos – Versión AJAX
// Autor: Mauricio Araneda
// Actualizado: 03-12-2025
// Codificación: UTF-8 sin BOM
//=====================================================

mb_internal_encoding("UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mantener sesión del KickOff
require_once __DIR__ . '/session_core.php';

// Cargar contexto del KickOff
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/security_groups.php";

// Variables necesarias
$db_sweet = $db_sweet ?? "tnasolut_sweet";
$sg_id    = $_SESSION['sg_id'] ?? "";

// URL para crear nuevo caso
$url_nuevo_caso = "https://sweet.icontel.cl/index.php?module=Cases&action=EditView";

// -------------------------------------------------------------
// Conexión a Sweet + Procedimiento Almacenado
// -------------------------------------------------------------
$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

$sql = "CALL CM_Casos_Abiertos_Congelados('".$conn->real_escape_string($sg_id)."')";
$result = $conn->query($sql);

$ptr       = 0;
$contenido = "";
$muestra   = ($result && $result->num_rows > 0);

if ($muestra) {

    while ($row = $result->fetch_assoc()) {
        $ptr++;

        // Colores por prioridad
        switch ($row["prioridad"]) {
            case "P1E": $contenido .= '<tr style="color:red;">'; break;
            case "P1":  $contenido .= '<tr style="color:orangered;">'; break;
            case "P2":  $contenido .= '<tr style="color:orange;">'; break;
            case "P3":  $contenido .= '<tr style="color:green;">'; break;
            default:    $contenido .= '<tr>';
        }

        $contenido .= "<td>{$ptr}</td>";
        $contenido .= "<td>".htmlspecialchars($row["prioridad_descr"])."</td>";
        $contenido .= '<td><a target="_blank" href="'.htmlspecialchars($row["url_caso"]).'">'
                    . htmlspecialchars($row["numero"])
                    . '</a></td>';
        $contenido .= "<td>".htmlspecialchars($row["asunto"])."</td>";
        $contenido .= "<td>".htmlspecialchars($row["estado"])."</td>";
        $contenido .= "<td>".htmlspecialchars($row["en_espera_de"])."</td>";
        $contenido .= "<td>".htmlspecialchars($row["categoria"])."</td>";
        $contenido .= "<td>".htmlspecialchars($row["nombre"]." ".$row["apellido"])."</td>";
        $contenido .= "<td>".htmlspecialchars($row["cliente"])."</td>";
        $contenido .= "<td>".htmlspecialchars($row["f_creacion"])."</td>";
        $contenido .= "<td>".htmlspecialchars($row["f_modifica"])."</td>";
        $contenido .= "</tr>";
    }

} else {
    $contenido = "<tr><td colspan='11'>No se encontraron Casos Abiertos Congelados.</td></tr>";
}

$conn->close();
unset($result);
unset($conn);
?>

<!-- ===================================================== -->
<!-- TABLA HTML — Casos Abiertos Congelados -->
<!-- ===================================================== -->

<div class="tabla-scroll">
<table id="casos_congelados" align="center" width="100%">
    <tr align="center">
        <td colspan="10" align="left" valign="bottom" class="titulo">
            &nbsp;&nbsp;🧊 Casos Abiertos Congelados
        </td>
        <td style="font-size:20px; background-color:#512554;">
            <a href="<?= $url_nuevo_caso ?>" title="Crear Caso" target="new"><b>+</b></a>
        </td>
    </tr>

    <tr align="left" class="subtit" style="color:white; background-color:midnightblue;">
        <th class="subtitulo" width="3%">#</th>
        <th class="subtitulo" width="8%">Prioridad</th>
        <th class="subtitulo">Número</th>
        <th class="subtitulo" width="15%">Asunto</th>
        <th class="subtitulo">Estado</th>
        <th class="subtitulo" width="12%">En Espera De</th>
        <th class="subtitulo">Categoría</th>
        <th class="subtitulo">Asignado a</th>
        <th class="subtitulo">Razón Social</th>
        <th class="subtitulo" width="6%">Fecha<br>Creación</th>
        <th class="subtitulo" width="6%">Fecha<br>Modificación</th>
    </tr>

    <?= $contenido ?>
</table>
</div>
