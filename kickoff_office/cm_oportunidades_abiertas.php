<?php
//=====================================================
// /intranet/kickoff_office_v2/cm_oportunidades_abiertas.php
// Oportunidades Abiertas - Versión AJAX
// Autor: Mauricio Araneda
// Actualizado: 2026-01-06
//=====================================================
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");

// Bootstrap AJAX (sesión + config + variables)
require_once __DIR__ . "/ajax_bootstrap.php";

$conn = DbConnect("tnaoffice_suitecrm");
$sql = "CALL Oportunidades_Pendientes('" . $conn->real_escape_string($sg_id) . "')";       
$resultado = $conn->query($sql);

$ptr = 0;
$contenido = "";
$datos = [];

if ($resultado && $resultado->num_rows > 0) {  
    while ($lin = $resultado->fetch_assoc()) { 
        $datos[] = $lin;
    }
    
    // Ordenar por 'dias' ascendente
    usort($datos, function($a, $b) { 
        return $a['dias'] - $b['dias']; 
    });
    
    foreach ($datos as $lin) {
        $ptr++; 
        $importancia = $lin["estado"];
        if ($lin["dias"] > 10) { 
            $importancia = "cotizar"; 
        }
        
        switch ($importancia) {    
            case "1 Escalado Urgente":
                $contenido .= '<tr style="color: red">'; break;
            case "2 Aceptadado, listo para Instalar":
                $contenido .= '<tr style="color: orangered">'; break;
            case "4 Pre Instalación":
                $contenido .= '<tr style="color: orangered">'; break;
            case "3 Generar NV":
                $contenido .= '<tr style="color: orange">'; break;
            case "Cotizar":
                $contenido .= '<tr style="color: green">'; break;
            default:
                $contenido .= '<tr>';                     
        }   
        
        $contenido .= "<td>{$ptr}</td>";
        $contenido .= '<td colspan="2"><a target="_blank" href="' . $lin["url_opor"] . '">' . $lin["nombre"] . '</a></td>';
        $contenido .= "<td>" . $lin["numero"] . "</td>";
        $contenido .= "<td>" . $lin["cliente"] . "</td>";
        $contenido .= "<td>" . $lin["estado"] . "</td>";
        $contenido .= "<td>" . $lin["asignado"] . "</td>";
        $contenido .= "<td>" . $lin["ejecutivo"] . "</td>";					
        $contenido .= "<td>" . $lin["f_creacion"] . "</td>";
        $contenido .= "<td>" . $lin["f_modifica"] . "</td>";
        $contenido .= "<td>" . $lin["f_proximo_paso"] . "</td>";					
        $contenido .= "<td align='right'>" . $lin["dias"] . "&nbsp;&nbsp;</td>";
        $contenido .= "</tr>";
    }
} else {
    $contenido = "<tr><td colspan='12'>⚠️ No se encontraron Oportunidades</td></tr>";
}

$conn->close(); 
?>

<link rel="stylesheet" href="css/kickoff.css">

<div class="tabla-scroll">
<table width="100%" style="margin-bottom: 0;">
    <tr>
        <td colspan="11" align="left" class="titulo">
            &nbsp;&nbsp;💼 Oportunidades en Curso
        </td>
        <td align="right" style="font-size: 20px; color: white; background-color: #512554;">
            <a style="color: white; text-decoration: none;" href="<?=$url_nueva_oportunidad?>" target="new" title="Nueva Oportunidad"><b>+</b></a>&nbsp;&nbsp;&nbsp;
        </td>
    </tr>
</table>

<div class="tabla-scroll">
<table id="oportunidades" width="100%">
    
    <tr class="subtit">
        <th class="subtitulo">#</th>
        <th class="subtitulo" colspan="2">Asunto</th>                    
        <th class="subtitulo">Número</th>
        <th class="subtitulo">Cliente</th>
        <th class="subtitulo">Estado</th>
        <th class="subtitulo">Asignado a</th>
        <th class="subtitulo">Ejecutiv@</th>
        <th class="subtitulo">Fecha<br>Creación</th>
        <th class="subtitulo">Fecha<br>Modificación</th>               
        <th class="subtitulo">Fecha<br>P.Paso</th>                               
        <th class="subtitulo" align="right">Días<br>Restantes</th>
    </tr>

    <?php echo $contenido; ?>
</table>
</div>

<script src="js/cm_sort.js?v=<?=time()?>"></script>
<script src="js/cm_resizable_columns.js?v=<?=time()?>"></script>