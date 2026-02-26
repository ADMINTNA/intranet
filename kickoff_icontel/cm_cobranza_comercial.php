<?php
// ==========================================================
// KickOff AJAX - Cobranza Comercial
// /kickoff_icontel/cm_cobranza_comercial.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

// Forzar cabecera UTF-8 para el navegador
header('Content-Type: text/html; charset=UTF-8');

mb_internal_encoding("UTF-8");

// Bootstrap AJAX (sesión + $sg_id + $sg_name + DbConnect)
require_once __DIR__ . "/ajax_bootstrap.php";

// Seguridad mínima
if ($sg_id === "" || $sg_name === "") {
    echo "<div style='padding:20px; color:red;'>❌ Error: sesión inválida.</div>";
    exit;
}

// ---------------------------------------------
// Cargar el INCLUDE ORIGINAL
// (usa las mismas funciones, SPs y formato)
// ---------------------------------------------
require_once __DIR__ . "/includes/cm_cobranza_comercia_include.php";
?>
<link rel="stylesheet" href="css/kickoff.css?v=<?=time()?>">

<link rel="stylesheet" href="css/cm_cobranza_comercial.css?v=<?=time()?>">
<link rel="stylesheet" href="css/cm_tareas_pendientes.css?v=<?=time()?>">

<script>
// Definir ruta base para AJAX - usar ruta absoluta del directorio kickoff_icontel
window.KICKOFF_BASE_PATH = '/kickoff_icontel/';
console.log("🛠 Base path configurado:", window.KICKOFF_BASE_PATH);
</script>

<style>
/* 🔒 CORRECCIÓN LOCAL: Sticky Headers (Estructura Plana) */
#cobranza tr:first-child td {
    position: sticky !important;
    top: 0 !important;
    z-index: 50 !important;
    background: #512554 !important;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
}

#cobranza tr:nth-child(2) th,
#cobranza tr.subtit th,
#cobranza tr.subtitulo th {
    position: sticky !important;
    top: 34px !important; /* Más altura para asegurar que baje del título */
    z-index: 40 !important;
    background: #512554 !important;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
}

/* Hover effect para headers sortable */
#cobranza th.sortable:hover {
    background: #6B3570 !important;
    color: #ECF0F1 !important;
}
</style>

<!-- OVERLAY OSCURO -->
<div id="panel-overlay" onclick="cerrarPanelFiltros()"></div>

<!-- PANEL FLOTANTE DE FILTROS (MODAL) -->
<div id="panel-filtros" class="panel-filtros-hidden">
    <div class="panel-filtros-header">
        <span>🔍 Filtros de Búsqueda</span>
        <button type="button" onclick="cerrarPanelFiltros()" class="btn-cerrar">✕</button>
    </div>
    <div class="panel-filtros-body">
        <div class="filtro-group">
            <label>RUT:</label>
            <input type="text" class="filtro-input" data-col="1" placeholder="Buscar RUT...">
        </div>
        <div class="filtro-group">
            <label>Cliente:</label>
            <input type="text" class="filtro-input" data-col="2" placeholder="Buscar cliente...">
        </div>
        <div class="filtro-group">
            <label>Estado Sweet:</label>
            <div class="estado-checks">
                <?php foreach ($LISTA_ESTADO_SWEET as $item): ?>
                <label class="check-label">
                    <input type="checkbox" class="filtro-estado-check" value="<?= htmlspecialchars($item['key']) ?>">
                    <?= htmlspecialchars($item['label']) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="filtro-group">
            <label>Comentario:</label>
            <input type="text" class="filtro-input" data-col="4" placeholder="Buscar en comentarios...">
        </div>
        <div class="filtro-group filtro-inline">
            <div>
                <label>Documentos:</label>
                <input type="text" class="filtro-input" data-col="6" placeholder="ej: >5">
            </div>
            <div>
                <label>Días vencidos:</label>
                <input type="text" class="filtro-input" data-col="9" placeholder="ej: >60">
            </div>
        </div>
    </div>
    <div class="panel-filtros-footer">
        <button type="button" onclick="limpiarFiltros()" class="btn-limpiar">🧹 Limpiar</button>
        <button type="button" onclick="aplicarYCerrar()" class="btn-aplicar">✅ Aplicar Filtros</button>
    </div>
</div>

<div class="tabla-scroll">
<table id="cobranza" width="100%">
    <tr style="background:#512554; color:white;">
        <td colspan="9" class="titulo">
            &nbsp;&nbsp;📊💰 Cobranza Comercial (Docs. Vencidos > 60 días)
        </td>
        <td colspan="2" class="titulo" align="right">
            <button type="button" id="btn-filtrar" onclick="togglePanelFiltros()" class="btn-filtrar">🔍 Filtrar</button>
            &nbsp;
            <button type="button" id="btn-limpiar-header" onclick="limpiarFiltros()" class="btn-limpiar-header">🧹 Limpiar</button>
            &nbsp;
        </td>
    </tr>
    <tr class="subtit subtitulo">
        <th>#</th>
        <th class="sortable" data-col="rut">RUT</th>
        <th width="15%" class="sortable" data-col="razon" style="white-space:nowrap">
            Razón Social&nbsp;<input id="filtro-cob-razon"
                type="text" placeholder="🔍"
                oninput="cobFilterRazon(this.value)"
                style="width:80px!important;padding:2px 5px!important;border:1px solid rgba(255,255,255,0.6)!important;border-radius:4px;background:rgba(255,255,255,0.2)!important;color:#fff!important;font-size:11px;font-weight:400;outline:none;vertical-align:middle"><span
                id="filtro-cob-razon-x"
                onclick="document.getElementById('filtro-cob-razon').value='';cobFilterRazon('')"
                title="Quitar filtro"
                style="display:none;cursor:pointer;color:#ffd600;font-weight:bold;font-size:13px;vertical-align:middle;margin-left:2px">✕</span>
        </th>
        <th width="12%" class="sortable" data-col="estado">Estado Sweet</th>
        <th width="15%" class="sortable" data-col="comentario">Comentario</th>
        <th class="sortable" data-col="tipo">Tipo</th>
        <th class="sortable" data-col="docs">Docs</th>
        <th class="sortable" data-col="monto" style="text-align:right;">Monto Total</th>
        <th class="sortable" data-col="fecha">Fecha Ref.</th>
        <th class="sortable" data-col="dias">Días</th>
        <th>Duemint</th>
    </tr>

    <?= $contenido ?>

</table>
</div>

<script>
function cobFilterRazon(q){
    q=q.toLowerCase();
    var x=document.getElementById('filtro-cob-razon-x');
    if(x) x.style.display=q?'inline':'none';
    document.querySelectorAll('#cobranza tr').forEach(function(r){
        if(!r.querySelector('td')) return;
        var tds=r.querySelectorAll('td');
        if(tds.length<5) return;                                     // skip título y totales
        if(parseInt(tds[0].getAttribute('colspan')||'0')>2) return;  // skip título colspan
        var txt=tds[2]?tds[2].textContent.toLowerCase():'';
        r.style.display=(!q||txt.includes(q))?'':'none';
    });
}
</script>

<script src="js/cm_cobranza_comercial_v2.js?v=2"></script>
