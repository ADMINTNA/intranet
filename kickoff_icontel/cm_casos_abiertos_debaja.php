<?php
// ==========================================================
// KickOff AJAX – Casos Abiertos de Baja
// /kickoff_icontel/cm_casos_abiertos_debaja.php
// Autor: Mauricio Araneda (mAo)
// Actualizado: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// ------------------------------------------------------
// CARGA DE CONTEXTO AJAX (sesión + config + sg_id + sg_name)
// ------------------------------------------------------
require_once __DIR__ . "/ajax_bootstrap.php";

// Validación por seguridad
if ($sg_id === "" || $sg_name === "") {
    echo "<div style='padding:20px; color:red;'>
            ❌ Error: sesión inválida.
          </div>";
    exit;
}

// ------------------------------------------------------
// CONEXIÓN A SWEET
// ------------------------------------------------------
$conn = DbConnect($db_sweet);

// Procedimiento almacenado
$sql = "CALL Kick_Off_Casos_Abiertos_de_baja()";
$result = $conn->query($sql);

$ptr = 0;
$contenido = "";
$muestra  = ($result && $result->num_rows > 0);

// ------------------------------------------------------
// PROCESAR RESULTADOS
// ------------------------------------------------------
if ($muestra) {

    while ($row = $result->fetch_assoc()) {
        $ptr++;

        // Siempre color rojo (casos de baja)
        $contenido .= '<tr style="color: red;">';

        $contenido .= "<td>{$ptr}</td>";
        $contenido .= "<td>{$row['prioridad_descr']}</td>";
        $contenido .= '<td><a target="_blank" href="'.$row["url_caso"].'">'.$row["numero"].'</a></td>';
        $contenido .= "<td>{$row['asunto']}</td>";
        $contenido .= "<td>{$row['estado']}</td>";
        $contenido .= "<td>{$row['en_espera_de']}</td>";
        $contenido .= "<td>{$row['tipo']}</td>";
        $contenido .= "<td>{$row['categoria']}</td>";
        $contenido .= "<td>{$row['nombre']} {$row['apellido']}</td>";
        $contenido .= "<td>{$row['cliente']}</td>";
        $contenido .= "<td>{$row['f_creacion']}</td>";
        $contenido .= "<td>{$row['f_modifica']}</td>";
        $contenido .= "<td align='right'>{$row['dias']}&nbsp;&nbsp;</td>";

        $contenido .= "</tr>";
    }

} else {

    $contenido = "<tr><td colspan='13'>No se encontraron datos de Casos de BAJA.</td></tr>";
}

$conn->close();
unset($result);
unset($conn);

// URL para crear nuevo caso
$url_nuevo_caso = 
    "https://sweet.icontel.cl/index.php?module=Cases&action=EditView";


// ------------------------------------------------------
// CABECERA SUPERIOR
// ------------------------------------------------------
$td = '
<td colspan="12" align="left" valign="middle" class="titulo"
    style="background:#512554; color:white; font-size:16px; height:38px;">
    &nbsp;&nbsp;📕 Casos de BAJA
</td>
<td align="right" valign="middle"
    style="font-size: 20px; background:#512554; color:white;">
    <a href="' . $url_nuevo_caso . '" target="new"
       title="Crear Nuevo Caso"
       style="color:white; text-decoration:none;"><b>+</b></a>
    &nbsp;&nbsp;&nbsp;
</td>';
?>
<link rel="stylesheet" href="css/cm_tareas_pendientes.css?v=<?=time()?>">

<div class="tabla-scroll">
<table id="casos_debaja" border="0" align="center" width="100%" cellpadding="0" cellspacing="0">

    <tr align="left" class="subtitulo">
        <?= $td ?>
    </tr>

    <tr align="left" class="subtit">
        <th>#</th>
        <th>Prioridad</th>
        <th>Número</th>
        <th style="white-space:nowrap">
            Asunto&nbsp;<input id="filtro-debaja-asunto"
                type="text" placeholder="🔍"
                oninput="debajaFilterAsunto(this.value)"
                style="width:80px!important;padding:2px 5px!important;border:1px solid rgba(255,255,255,0.6)!important;border-radius:4px;background:rgba(255,255,255,0.2)!important;color:#fff!important;font-size:11px;font-weight:400;outline:none;vertical-align:middle"><span
                id="filtro-debaja-asunto-x"
                onclick="document.getElementById('filtro-debaja-asunto').value='';debajaFilterAsunto('')"
                title="Quitar filtro"
                style="display:none;cursor:pointer;color:#ffd600;font-weight:bold;font-size:13px;vertical-align:middle;margin-left:2px">✕</span>
            
        </th>
        <th>Estado</th>
        <th>En Espera de</th>
        <th>Tipo</th>
        <th>Categoría</th>
        <th>Asignado a</th>
        <th>Razón Social</th>
        <th>F. Creación</th>
        <th>F. Modificación</th>
        <th align="right">Días&nbsp;&nbsp;</th>
    </tr>

    <?= $contenido ?>

</table>
</div>

<?php if (!$muestra): ?>
    <script>capa('casos_debaja');</script>
<?php endif; ?>

<script>
function debajaFilterAsunto(q){
    q=q.toLowerCase();
    var x=document.getElementById('filtro-debaja-asunto-x');
    if(x) x.style.display=q?'inline':'none';
    document.querySelectorAll('#casos_debaja tr').forEach(function(r){
        if(!r.querySelector('td')) return;
        var tds=r.querySelectorAll('td');
        var txt=tds[3]?tds[3].textContent.toLowerCase():'';
        r.style.display=(!q||txt.includes(q))?'':'none';
    });
}
</script>
