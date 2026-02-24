<?php
//=====================================================
// /intranet/kickoff_office_v2/cm_casos_abiertos.php
// Casos Abiertos - Versión AJAX
// Autor: Mauricio Araneda
// Actualizado: 2026-01-06
//=====================================================
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");

// Bootstrap AJAX (sesión + config + variables)
require_once __DIR__ . "/ajax_bootstrap.php";

$conn = DbConnect('tnaoffice_suitecrm');
$sql = "CALL Kick_Off_Operaciones_Abiertos('" . $conn->real_escape_string($sg_id) . "')";                        
$resultado = $conn->query($sql);

$ptr = 0;
$contenido = "";

if ($resultado && $resultado->num_rows > 0) {  
    while ($row = $resultado->fetch_assoc()) {
        $ptr++; 
        switch ($row["prioridad"]) {    
            case "P1E":
                $contenido .= '<tr style="color: red">'; break;
            case "P1":
                $contenido .= '<tr style="color: orangered">'; break;
            case "P2":
                $contenido .= '<tr style="color: orange">'; break;
            case "P3":
                $contenido .= '<tr style="color: dimgray">'; break;
            default:
                $contenido .= '<tr>';                     
        }   

        $contenido .= "<td>{$ptr}</td>";
        $contenido .= '<td><a target="_blank" href="' . $row["url_caso"] . '">' . $row["numero"] . '</a></td>';
        $contenido .= "<td>" . $row["prioridad_descr"] . "</td>";                      
        $contenido .= "<td>" . $row["asunto"] . "</td>";
        $contenido .= "<td>" . $row["estado"] . "</td>";
        $contenido .= "<td>" . $row["categoria"] . "</td>";
        $contenido .= "<td>" . $row["nombre"] . " " . $row["apellido"] . "</td>";                    
        $contenido .= "<td>" . $row["cliente"] . "</td>";
        $contenido .= "<td>" . $row["f_creacion"] . "</td>";
        $contenido .= "<td>" . $row["f_modifica"] . "</td>";
        $contenido .= "<td align='right'>" . $row["dias"] . "&nbsp;&nbsp;</td>";					
        $contenido .= "</tr>";
    }
} else {
    $contenido = "<tr><td colspan='11'>⚠️ No se encontraron Casos Abiertos</td></tr>";
}

$conn->close();
?>

<link rel="stylesheet" href="css/kickoff.css">

<div class="tabla-scroll">
<table id="casos_abiertos" width="100%">
    <tr>
        <td colspan="10" align="left" class="titulo">
            &nbsp;&nbsp;📑 Casos Abiertos
        </td>
        <td align="right" style="font-size: 20px; color: white; background-color: #512554;">
            <a style="color: white; text-decoration: none;" href="<?=$url_nuevo_caso?>" target="new" title="Crear Nuevo Caso"><b>+</b></a>&nbsp;&nbsp;&nbsp;
        </td>
    </tr>
    <tr class="subtit">
        <th class="subtitulo">#</th>
        <th class="subtitulo">Nº</th>
        <th class="subtitulo">Prioridad</th>
        <th class="subtitulo">Asunto</th>
        <th class="subtitulo">Estado</th>                
        <th class="subtitulo">Categoría</th>
        <th class="subtitulo">Asignado a</th>                    
        <th class="subtitulo">Razón Social</th>
        <th class="subtitulo">F.Creación</th>
        <th class="subtitulo">F.Modif.</th>
        <th class="subtitulo" align="right">Días&nbsp;&nbsp;</th>
    </tr>
    <?php echo $contenido; ?>
</table>
</div>

<script src="js/cm_sort.js?v=<?=time()?>"></script>
<script src="js/cm_resizable_columns.js?v=<?=time()?>"></script>
