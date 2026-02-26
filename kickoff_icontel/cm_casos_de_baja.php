<?php
// ==========================================================
// KickOff AJAX – Casos de Baja y Término de Contrato
// /kickoff_icontel/cm_casos_abiertos_debaja.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// Bootstrap AJAX
require_once __DIR__ . "/ajax_bootstrap.php";

// Seguridad
if ($sg_id === "" || $sg_name === "") {
    echo "<div style='padding:20px; color:red;'>❌ Error: sesión inválida.</div>";
    exit;
}

// Conexión SweetCRM
$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

// Ejecutar Stored Procedure
$sql = "CALL Kick_Off_Casos_Abiertos_de_baja()";
$result = $conn->query($sql);

$ptr = 0;
$contenido = "";
$muestra = ($result && $result->num_rows > 0);

// GENERAR FILAS
if ($muestra) {

    while ($row = $result->fetch_assoc()) {

        $ptr++;

        // Todos los casos de baja van en rojo (según tu versión clásica)
        $contenido .= "<tr style='color:red;'>";

        $contenido .= "<td>{$ptr}</td>";
        $contenido .= "<td>" . htmlspecialchars($row["prioridad_descr"]) . "</td>";

        $contenido .= "<td>
                        <a target='_blank' href='" . htmlspecialchars($row["url_caso"]) . "'>
                            " . htmlspecialchars($row["numero"]) . "
                        </a>
                       </td>";

        $contenido .= "<td>" . htmlspecialchars($row["asunto"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["estado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["en_espera_de"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["categoria"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["nombre"] . " " . $row["apellido"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["cliente"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["f_creacion"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($row["f_modifica"]) . "</td>";
        $contenido .= "<td align='right'>" . htmlspecialchars($row["dias"]) . "&nbsp;&nbsp;</td>";

        $contenido .= "</tr>";
    }

} else {

    $contenido = "
        <tr>
            <td colspan='12' style='text-align:center; padding:12px; color:#666;'>
                ⚠️ No se encontraron datos de Casos de Baja.
            </td>
        </tr>";
}

$conn->close();
unset($result);
unset($conn);

// CABECERA SUPERIOR
$td = '
<td colspan="12" class="titulo" 
    style="font-size:20px; background:#512554; color:white; padding:8px;">
    &nbsp;&nbsp;📉 Casos de Baja y Término de Contrato
</td>
<td align="right" style="background:#512554; padding-right:15px;">
    <a href="' . $url_nuevo_caso . '" target="new" 
       style="font-size:22px; color:white; text-decoration:none;">
       <b>+</b>
    </a>
</td>';
?>
<link rel="stylesheet" href="css/cm_tareas_pendientes.css?v=<?=time()?>">
<!-- ===================================================== -->
<!-- TABLA SCROLLEABLE RESPETANDO TUS ANCHOS               -->
<!-- ===================================================== -->

<style>
#casos_debaja tr.subtit th { background:#512554 !important; color:#fff !important; }
</style>

<div class="tabla-scroll">

<table id="casos_debaja" cellpadding="0" cellspacing="0" border="0">

    <tr class="subtitulo">
        <?= $td ?>
    </tr>

    <tr class="subtitulo" align="left">
        <th class="subtitulo" width="1%">#</th>
        <th class="subtitulo" width="2%">Prioridad</th>
        <th class="subtitulo" width="2%">Número</th>
        <th class="subtitulo" width="15%" style="white-space:nowrap">
            Asunto&nbsp;<input id="filtro-baja-asunto"
                type="text" placeholder="🔍"
                oninput="bajaFilterAsunto(this.value)"
                style="width:80px!important;padding:2px 5px!important;border:1px solid rgba(255,255,255,0.6)!important;border-radius:4px;background:rgba(255,255,255,0.2)!important;color:#fff!important;font-size:11px;font-weight:400;outline:none;vertical-align:middle"><span
                id="filtro-baja-asunto-x"
                onclick="document.getElementById('filtro-baja-asunto').value='';bajaFilterAsunto('')"
                title="Quitar filtro"
                style="display:none;cursor:pointer;color:#ffd600;font-weight:bold;font-size:13px;vertical-align:middle;margin-left:2px">✕</span>
            
        </th>
        <th class="subtitulo" width="4%">Estado</th>
        <th class="subtitulo" width="5%">En Espera De</th>
        <th class="subtitulo" width="4%">Categoría</th>
        <th class="subtitulo" width="10%">Asignado a</th>
        <th class="subtitulo" width="5%">Razón Social</th>
        <th class="subtitulo" width="6%">F. Creación</th>
        <th class="subtitulo" width="6%">F. Modif.</th>
        <th class="subtitulo" width="3%" align="right">Días&nbsp;&nbsp;</th>
    </tr>

    <?= $contenido ?>

</table>

</div>
<script>
function bajaFilterAsunto(q){
    q=q.toLowerCase();
    var x=document.getElementById('filtro-baja-asunto-x');
    if(x) x.style.display=q?'inline':'none';
    document.querySelectorAll('#casos_debaja tr').forEach(function(r){
        if(!r.querySelector('td')) return;
        var tds=r.querySelectorAll('td');
        var txt=tds[3]?tds[3].textContent.toLowerCase():'';
        r.style.display=(!q||txt.includes(q))?'':'none';
    });
}
</script>
