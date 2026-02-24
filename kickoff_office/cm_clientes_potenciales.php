<?php
//=====================================================
// /intranet/kickoff_office_v2/cm_clientes_potenciales.php
// Clientes Potenciales - Versión AJAX
// Autor: Mauricio Araneda
// Actualizado: 2026-01-06
//=====================================================
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
require_once __DIR__ . "/ajax_bootstrap.php";

$conn = DbConnect("tnaoffice_suitecrm");
$sql = "CALL Clientes_Potenciales_Pendientes()";       
$resultado = $conn->query($sql);
$ptr = 0;
$contenido = "";

if ($resultado && $resultado->num_rows > 0) { 
    while ($lin = $resultado->fetch_assoc()) {
        $ptr++; 
        if ($lin["dias"] > 0) {
            $contenido .= '<tr style="color: red">';					
        } else {
            switch ($lin["estado"]) {    
                case "1 Nuevo":
                    $contenido .= '<tr style="color: red">'; break;
                case "2 Asignado":
                    $contenido .= '<tr style="color: orange">'; break;
                case "3 En Proceso":
                    $contenido .= '<tr style="color: green">'; break;
                case "4 Retomar en 3 meses":
                    $contenido .= '<tr style="color: green">'; break;
                default:
                    $contenido .= '<tr>';                     
            }   				
        }	
        $contenido .= "<td>{$ptr}</td>";
        $contenido .= '<td colspan="2"><a target="_blank" href="' . $lin["url_lead"] . '">' . $lin["nombre"] . '</a></td>';
        $contenido .= "<td>" . $lin["estado"] . "</td>";
        $contenido .= "<td>" . $lin["campana"] . "</td>";
        $contenido .= "<td>" . $lin["usuario"] . "</td>";
        $contenido .= "<td>" . $lin["f_creacion"] . "</td>";
        $contenido .= "<td align='right'>" . $lin["dias"] . "&nbsp;&nbsp;</td>";
        $contenido .= "</tr>";
    }
} else {
    $contenido = "<tr><td colspan='8'>⚠️ No se encontraron Clientes Potenciales</td></tr>";
}
$conn->close(); 
?>

<link rel="stylesheet" href="css/kickoff.css">

<div class="tabla-scroll">
<table id="clientes_potenciales" width="100%">
    <tr>
        <td colspan="7" align="left" class="titulo">
            &nbsp;&nbsp;🧲 Clientes Potenciales en Proceso
        </td>
        <td align="right" style="font-size: 20px; color: white; background-color: #512554;">
            <a style="color: white; text-decoration: none;" href="<?=$url_nuevo_lead?>" target="new" title="Nuevo Cliente Potencial"><b>+</b></a>&nbsp;&nbsp;&nbsp;
        </td>
    </tr>
    <tr class="subtit">
        <th class="subtitulo">#</th>
        <th class="subtitulo" colspan="2">Nombre</th>                    
        <th class="subtitulo">Estado</th>
        <th class="subtitulo">Campaña</th>
        <th class="subtitulo">Asignado a</th>
        <th class="subtitulo">Fecha Creación</th>
        <th class="subtitulo" align="right">Días&nbsp;&nbsp;</th>
    </tr>
    <?php echo $contenido; ?>
</table>
</div>

<script src="js/cm_sort.js?v=<?=time()?>"></script>
<script src="js/cm_resizable_columns.js?v=<?=time()?>"></script>