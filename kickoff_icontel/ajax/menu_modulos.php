<?php
// ==========================================================
// KickOff AJAX – Menú de Módulos
// /kickoff_icontel/menu_modulos.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// ==========================================================

mb_internal_encoding("UTF-8");

// Las variables de sesión ya existen en icontel.php
// $sg_name  → nombre del grupo actual
// $ventas, $operaciones, $sac, $admin, $proveedores, $mao_mam
?>

<style>
#menu-ajax {
    background: #1F1D3E;
    padding: 10px 0;
    color: white;
    border-bottom: 3px solid #512554;
}

#menu-ajax ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    gap: 18px;
}

#menu-ajax li {
    cursor: pointer;
    font-size: 14px;
    padding: 6px 14px;
    border-radius: 6px;
    transition: 0.25s;
}

#menu-ajax li:hover {
    background: #512554;
}
</style>

<div id="menu-ajax">
<ul>

    <!-- Casos Abiertos (todos los grupos) -->
    <li onclick="loadModulo('cm_casos_abiertos.php')">
        Casos Abiertos
    </li>

    <!-- Casos sujetos a cobro (solo MAO / Proveedores) -->
    <?php if (strpos($proveedores, $sg_name) !== false): ?>
    <li onclick="loadModulo('cm_casos_abiertos_sujeto_a_cobro.php')">
        Casos sujetos a Cobro
    </li>
    <?php endif; ?>

    <!-- Traslados y bajas (solo MAO / MAM) -->
    <?php if (strpos($mao_mam, $sg_name) !== false): ?>
    <li onclick="loadModulo('cm_traslados_y_bajas.php')">
        Traslados y Bajas
    </li>
    <?php endif; ?>

    <!-- Casos abiertos de baja (solo Admin) -->
    <?php if (strpos($admin, $sg_name) !== false): ?>
    <li onclick="loadModulo('cm_casos_abiertos_debaja.php')">
        Casos Abiertos De Baja
    </li>
    <?php endif; ?>

    <!-- Cobranza Comercial -->
    <?php if (strpos($ventas, $sg_name) || strpos($admin, $sg_name)): ?>
    <li onclick="loadModulo('cm_cobranza_comercial.php')">
        Cobranza Comercial
    </li>
    <?php endif; ?>

    <!-- Clientes potenciales -->
    <?php if (strpos($ventas, $sg_name)): ?>
    <li onclick="loadModulo('cm_clientes_potenciales.php')">
        Clientes Potenciales
    </li>
    <?php endif; ?>

    <!-- Tareas -->
    <li onclick="loadModulo('cm_tareas_pendientes.php')">Tareas Pendientes</li>
    <li onclick="loadModulo('cm_tareas_pendientes_delegadas.php')">Tareas Delegadas</li>

    <!-- Notas -->
    <li onclick="loadModulo('cm_notas_abiertas.php')">Notas Abiertas</li>

    <!-- Oportunidades -->
    <?php if ($sg_name !== "Soporte tecnico"): ?>
    <li onclick="loadModulo('cm_oportunidades_abiertas.php')">Oportunidades Abiertas</li>
    <?php endif; ?>

    <?php if (strpos($ventas.$operaciones, $sg_name)): ?>
    <li onclick="loadModulo('cm_oportunidades_en_Demo.php')">Oportunidades en Demo</li>
    <?php endif; ?>

    <?php if (strpos($ventas, $sg_name) && $sg_name != "-..MAO"): ?>
    <li onclick="loadModulo('cm_oportunidades_Archivadas.php')">Oportunidades Archivadas</li>
    <?php endif; ?>

    <!-- Ordenes de compra (solo admin) -->
    <?php if (strpos($admin, $sg_name)): ?>
    <li onclick="loadModulo('cm_ordenes_de_compra_pendientes.php')">
        Órdenes de Compra
    </li>
    <?php endif; ?>

</ul>
</div>
