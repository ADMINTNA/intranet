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
//   - session_core.php (session_start)
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
// -------------------------------------------------------------
// CONTADORES (BADGES)
// -------------------------------------------------------------
$cnt_casos          = 0;
$cnt_sujeto_cobro   = 0;
$cnt_traslados      = 0;
$cnt_casos_baja     = 0;
$cnt_cobranza       = 0;
$cnt_potenciales    = 0;
$cnt_tareas         = 0;
$cnt_delegadas      = 0;
$cnt_notas          = 0;
$cnt_oportunidades  = 0;
$cnt_demo           = 0;
$cnt_archivadas     = 0;
$cnt_oc_pendientes  = 0;
$cnt_congelados     = 0;
$cnt_seguimiento    = 0; // ⭐ NUEVO

if (function_exists('DbConnect')) {

    $conn_badge = DbConnect("tnasolut_sweet");

    if ($conn_badge) {

        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL Kick_Off_Operaciones_Abiertos('$sg_id')";
        if ($res = $conn_badge->query($sql)) { $cnt_casos = $res->num_rows; $res->free(); }

        if (strpos($proveedores, $sg_name) !== false) {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL Kick_Off_Operaciones_Abiertos_sujeto_a_cobro()";
            if ($res = $conn_badge->query($sql)) { $cnt_sujeto_cobro = $res->num_rows; $res->free(); }
        }

        if (strpos($mao_mam, $sg_name) !== false) {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL CM_Cotizaciones_baja_traslado()";
            if ($res = $conn_badge->query($sql)) { $cnt_traslados = $res->num_rows; $res->free(); }
        }

        if (strpos($admin, $sg_name) !== false) {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL Kick_Off_Casos_Abiertos_de_baja()";
            if ($res = $conn_badge->query($sql)) { $cnt_casos_baja = $res->num_rows; $res->free(); }
        }

        if (strpos($sac, $sg_name) !== false) {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL CM_Casos_Abiertos_Congelados('$sg_id')";
            if ($res = $conn_badge->query($sql)) { $cnt_congelados = $res->num_rows; $res->free(); }

            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL CM_Casos_Abiertos_Seguimiento('$sg_id')";
            if ($res = $conn_badge->query($sql)) { $cnt_seguimiento = $res->num_rows; $res->free(); }
        }

        if (strpos($ventas, $sg_name) || strpos($admin, $sg_name)) {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "
                SELECT COUNT(*) AS total
                FROM accounts ac
                JOIN accounts_cstm acc ON acc.id_c = ac.id
                WHERE acc.estatusfinanciero_c IN (
                    'cobranza_comercial','acuerdo_cobranza_comer','suspender',
                    'Suspendido','retencion_posible_baja'
                )
            ";
            if ($res = $conn_badge->query($sql)) {
                $row = $res->fetch_assoc();
                $cnt_cobranza = $row['total'];
                $res->free();
            }
        }

        if (strpos($ventas, $sg_name)) {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL Clientes_Potenciales_Pendientes()";
            if ($res = $conn_badge->query($sql)) { $cnt_potenciales = $res->num_rows; $res->free(); }
        }

        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL Kick_Off_Operaciones_Tareas_Abiertas('$sg_id')";
        if ($res = $conn_badge->query($sql)) { $cnt_tareas = $res->num_rows; $res->free(); }

        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL Kick_Off_Tareas_Abiertas_Creadas('$sg_id')";
        if ($res = $conn_badge->query($sql)) { $cnt_delegadas = $res->num_rows; $res->free(); }

        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL cm_notas_abiertas('$sg_id')";
        if ($res = $conn_badge->query($sql)) { $cnt_notas = $res->num_rows; $res->free(); }

        if ($sg_name !== "Soporte tecnico") {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL Oportunidades_Pendientes('$sg_id')";
            if ($res = $conn_badge->query($sql)) { $cnt_oportunidades = $res->num_rows; $res->free(); }
        }

        if (strpos($ventas.$operaciones, $sg_name)) {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL Oportunidades_en_Demo()";
            if ($res = $conn_badge->query($sql)) { $cnt_demo = $res->num_rows; $res->free(); }
        }

        if (strpos($ventas, $sg_name) !== false && $sg_name != "-..MAO") {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL Oportunidades_en_Pausa()";
            if ($res = $conn_badge->query($sql)) { $cnt_archivadas = $res->num_rows; $res->free(); }
        }

        if (strpos($admin, $sg_name)) {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL CM_Ordenes_de_Compra_Pendientes()";
            if ($res = $conn_badge->query($sql)) { $cnt_oc_pendientes = $res->num_rows; $res->free(); }
        }

        $conn_badge->close();
    }
}
?>

<!-- ====================================================== -->
<!-- BARRA COMPLETA DE MÓDULOS -->
<!-- ====================================================== -->

<div id="menu-ajax-fixed">
<div id="menu-ajax">

    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos.php')">
        <span class="icon">📋</span> Casos
        <?php if($cnt_casos > 0): ?><span class="badge-count"><?=$cnt_casos?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>

<?php if (strpos($proveedores, $sg_name) !== false): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos_sujeto_a_cobro.php')">
        <span class="icon">💰</span> Sujeto Cobro
        <?php if($cnt_sujeto_cobro > 0): ?><span class="badge-count"><?=$cnt_sujeto_cobro?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($mao_mam, $sg_name) !== false): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_traslados_y_bajas.php')">
        <span class="icon">🔄</span> Traslados
        <?php if($cnt_traslados > 0): ?><span class="badge-count"><?=$cnt_traslados?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($admin, $sg_name) !== false): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos_debaja.php')">
        <span class="icon">📉</span> Casos Baja
        <?php if($cnt_casos_baja > 0): ?><span class="badge-count"><?=$cnt_casos_baja?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($sac, $sg_name) !== false): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos_congelados.php')">
        <span class="icon">🧊</span> Congelados
        <?php if($cnt_congelados > 0): ?><span class="badge-count"><?=$cnt_congelados?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>

    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_casos_abiertos_seguimiento.php')">
        <span class="icon">🕵️</span> Seguimiento
        <?php if($cnt_seguimiento > 0): ?><span class="badge-count"><?=$cnt_seguimiento?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($ventas, $sg_name) || strpos($admin, $sg_name)): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_cobranza_comercial.php')">
        <span class="icon">📊</span> Cobranza
        <?php if($cnt_cobranza > 0): ?><span class="badge-count"><?=$cnt_cobranza?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($ventas, $sg_name)): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_clientes_potenciales.php')">
        <span class="icon">📈</span> Potenciales
        <?php if($cnt_potenciales > 0): ?><span class="badge-count"><?=$cnt_potenciales?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<div class="toolbar-btn active" onclick="selectMenu(this); loadModulo('cm_tareas_pendientes.php')">
    <span class="icon">📌</span> Tareas
    <?php if($cnt_tareas > 0): ?><span class="badge-count"><?=$cnt_tareas?></span><?php endif; ?>
</div>
<span class="separator">|</span>

<div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_tareas_pendientes_delegadas.php')">
    <span class="icon">📤</span> Delegadas
    <?php if($cnt_delegadas > 0): ?><span class="badge-count"><?=$cnt_delegadas?></span><?php endif; ?>
</div>
<span class="separator">|</span>

<div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_notas_abiertas.php')">
    <span class="icon">📝</span> Notas
    <?php if($cnt_notas > 0): ?><span class="badge-count"><?=$cnt_notas?></span><?php endif; ?>
</div>

<?php if ($sg_name !== "Soporte tecnico"): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_oportunidades_abiertas.php')">
        <span class="icon">💼</span> Oportunidades
        <?php if($cnt_oportunidades > 0): ?><span class="badge-count"><?=$cnt_oportunidades?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($ventas.$operaciones, $sg_name)): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_oportunidades_en_Demo.php')">
        <span class="icon">🧪</span> Demo
        <?php if($cnt_demo > 0): ?><span class="badge-count"><?=$cnt_demo?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($ventas, $sg_name) !== false && $sg_name != "-..MAO"): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_oportunidades_Archivadas.php')">
        <span class="icon">📦</span> Archivadas
        <?php if($cnt_archivadas > 0): ?><span class="badge-count"><?=$cnt_archivadas?></span><?php endif; ?>
    </div>
    <span class="separator">|</span>
<?php endif; ?>

<?php if (strpos($admin, $sg_name)): ?>
    <div class="toolbar-btn" onclick="selectMenu(this); loadModulo('cm_ordenes_de_compra_pendientes.php')">
        <span class="icon">🧾</span> OC Pendientes
        <?php if($cnt_oc_pendientes > 0): ?><span class="badge-count"><?=$cnt_oc_pendientes?></span><?php endif; ?>
    </div>
<?php endif; ?>

</div>
</div>