<?php
// ==========================================================
// KickOff AJAX - Menú de Módulos
// /kickoff_icontel/menu_modulos.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================
//
// IMPORTANTE:
// Este archivo se incluye DESDE kickoff_icontel/icontel.php
// donde YA están:
//   - session_start()
//   - $sg_name
//   - $ventas, $operaciones, $sac, $admin, $proveedores, $mao_mam
//
// ✘ NO poner session_start()
// ✘ NO incluir config.php
// ✘ NO incluir security_groups.php
// ✘ NO definir variables de permisos nuevamente
?>

<style>
/* Barra fija bajo el header */
#menu-ajax-fixed {
    position: sticky;
    top: 0px;
    z-index: 9998;
}

/* Barra estilo Office v2 */
#menu-ajax {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    background: linear-gradient(135deg, #1F1D3E 0%, #27304A 100%);
    overflow-x: auto;
    white-space: nowrap;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Botones estilo Office v2 */
.toolbar-btn {
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
}

.toolbar-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
}

.toolbar-btn.active {
    background: #512554;
    border-color: #64C2C8;
    box-shadow: 0 0 10px rgba(100, 194, 200, 0.3);
}

/* Badge integrado */
.badge-count {
    display: inline-block;
    background-color: #ff3b30;
    color: white;
    border-radius: 8px;
    padding: 0px 5px;
    font-size: 10px;
    font-weight: bold;
    min-width: 16px;
    text-align: center;
    line-height: 16px;
}

/* Iconos */
.toolbar-btn .icon {
    font-size: 16px;
}

/* Separadores - ocultos en nuevo diseño */
.separator {
    display: none;
}

#menu-ajax::-webkit-scrollbar {
    height: 6px;
}

#menu-ajax::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

#menu-ajax::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}
</style>

<?php
// Los badges se cargan de forma asíncrona via ajax/badges.php
// para no bloquear la carga inicial del menú.
?>

<!-- ====================================================== -->
<!-- BARRA COMPLETA DE MÓDULOS -->
<!-- ====================================================== -->

<div id="menu-ajax-fixed">
<div id="menu-ajax">

    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos.php')">
        <span class="icon">📋</span> Casos
        <span class="badge-count badge-async" id="badge-casos" style="display:none"></span>
    </div>
    <span class="separator">|</span>

<?php if (strpos($proveedores, $sg_name) !== false): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos_sujeto_a_cobro.php')">
        <span class="icon">💰</span> Sujeto Cobro
        <span class="badge-count badge-async" id="badge-sujeto_cobro" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($mao_mam, $sg_name) !== false): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_traslados_y_bajas.php')">
        <span class="icon">🔄</span> Traslados
        <span class="badge-count badge-async" id="badge-traslados" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($admin, $sg_name) !== false): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos_debaja.php')">
        <span class="icon">📉</span> Casos Baja
        <span class="badge-count badge-async" id="badge-casos_baja" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($sac, $sg_name) !== false): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos_congelados.php')">
        <span class="icon">🧊</span> Congelados
        <span class="badge-count badge-async" id="badge-congelados" style="display:none"></span>
    </div>
    <span class="separator">|</span>

    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos_seguimiento.php')">
        <span class="icon">🕵️</span> Seguimiento
        <span class="badge-count badge-async" id="badge-seguimiento" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($ventas, $sg_name) || strpos($admin, $sg_name)): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_cobranza_comercial.php')">
        <span class="icon">📊</span> Cobranza
        <span class="badge-count badge-async" id="badge-cobranza" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($ventas, $sg_name)): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_clientes_potenciales.php')">
        <span class="icon">📈</span> Potenciales
        <span class="badge-count badge-async" id="badge-potenciales" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<div class="toolbar-btn active" id="btn-def-tareas" onclick="selectMenu(this); loadModulo('cm_tareas_pendientes.php')">
    <span class="icon">📌</span> Tareas
    <span class="badge-count badge-async" id="badge-tareas" style="display:none"></span>
</div>
<span class="separator">|</span>

<div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_tareas_pendientes_delegadas.php')">
    <span class="icon">📤</span> Delegadas
    <span class="badge-count badge-async" id="badge-delegadas" style="display:none"></span>
</div>
<span class="separator">|</span>

<div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_notas_abiertas.php')">
    <span class="icon">📝</span> Notas
    <span class="badge-count badge-async" id="badge-notas" style="display:none"></span>
</div>

<?php if ($sg_name !== "Soporte tecnico"): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_oportunidades_abiertas.php')">
        <span class="icon">💼</span> Oportunidades
        <span class="badge-count badge-async" id="badge-oportunidades" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($ventas.$operaciones, $sg_name)): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_oportunidades_en_Demo.php')">
        <span class="icon">🧪</span> Demo
        <span class="badge-count badge-async" id="badge-demo" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($ventas, $sg_name) !== false && $sg_name != "-..MAO"): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_oportunidades_Archivadas.php')">
        <span class="icon">📦</span> Archivadas
        <span class="badge-count badge-async" id="badge-archivadas" style="display:none"></span>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($admin, $sg_name)): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_ordenes_de_compra_pendientes.php')">
        <span class="icon">🧾</span> OC Pendientes
        <span class="badge-count badge-async" id="badge-oc_pendientes" style="display:none"></span>
    </div>
<?php endif; ?>

</div>
</div>