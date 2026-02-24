<?php
//=====================================================
// /intranet/kickoff_office_v2/cm_notas_abiertas.php
// Notas Abiertas - Versión AJAX
// Autor: Mauricio Araneda
// Actualizado: 2026-01-06
//=====================================================
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");

// Bootstrap AJAX (sesión + config + variables)
require_once __DIR__ . "/ajax_bootstrap.php";

$conn = DbConnect("tnaoffice_suitecrm");

// URL para crear nueva nota
$url_nueva_nota = "https://sweet.tnaoffice.cl/index.php?module=Notes&action=EditView&return_module=Notes&return_action=DetailView";

// Llamada al procedimiento almacenado
$sql = "CALL cm_notas_abiertas('" . $conn->real_escape_string($sg_id) . "')";
$result = $conn->query($sql);

$ptr = 0;
$contenido = "";

if ($result && $result->num_rows > 0) { 
    while ($row = $result->fetch_assoc()) {
        $ptr++; 
        $dias = (int)$row["dias_sin_modificar"];

        // Color según días sin modificar
        if ($dias > 4) {
            $contenido .= '<tr style="color: red;">';
        } elseif ($dias >= 3 && $dias <= 4) {
            $contenido .= '<tr style="color: orange;">';
        } else {
            $contenido .= '<tr style="color: green;">';
        }

        $contenido .= "<td>{$ptr}</td>";
        $contenido .= '<td><a target="_blank" href="' . $row["url_nota"] . '">' . htmlspecialchars($row["asunto"]) . '</a></td>';
        $contenido .= "<td>" . $row["fecha_creacion"] . "</td>";                      
        $contenido .= "<td>" . $row["relacionado_con"] . "</td>";                      
        $contenido .= "<td>" . $row["nota_estado"] . "</td>";
        $contenido .= "<td>" . $row["departamento"] . "</td>";
        $contenido .= "<td>" . $row["asignado_a"] . "</td>";
        $contenido .= "<td>" . $row["modificado_por"] . "</td>";
        $contenido .= "<td>" . $row["fecha_modificacion"] . "</td>";
        $contenido .= "<td align='right'>{$dias}&nbsp;&nbsp;</td>";	
        $contenido .= "</tr>";
    }
} else {
    $contenido = "<tr><td colspan='10'>⚠️ No se encontraron Notas Abiertas</td></tr>";
}

$conn->close();
?>

<link rel="stylesheet" href="css/kickoff.css">

<style>
#notas_abiertas {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto;
}

#notas_abiertas th,
#notas_abiertas td {
    padding: 6px 8px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}

#notas_abiertas th.subtitulo {
    background-color: #512554;
    color: #C39BD3;
    font-weight: bold;
    text-align: left;
    white-space: nowrap;
}
</style>

<div class="tabla-scroll">
<table width="100%" style="margin-bottom: 0;">
    <tr>
        <td colspan="9" align="left" class="titulo" style="color: #C39BD3; background-color: #512554;">
            &nbsp;&nbsp;📋 Notas Abiertas
        </td>
        <td align="right" style="font-size: 22px; color: #C39BD3; background-color: #512554;">
            <a href="<?=$url_nueva_nota?>" target="_blank" title="Crear Nueva Nota" style="color: #C39BD3; text-decoration: none;"><b>+</b></a>&nbsp;&nbsp;
        </td>
    </tr>
</table>

<div class="tabla-scroll">
<table id="notas_abiertas" width="100%">
    
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
        <th class="subtitulo" align="right">Días&nbsp;&nbsp;</th>
    </tr>

    <?php echo $contenido; ?>
</table>
</div>

<script src="js/cm_sort.js?v=<?=time()?>"></script>
<script src="js/cm_resizable_columns.js?v=<?=time()?>"></script>
