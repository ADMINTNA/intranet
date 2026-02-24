<?php
// ==========================================================
// KickOff Office V2 - Menú de Módulos con Badges
// /kickoff_office/menu_modulos.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2026-01-06
// ==========================================================

// Este archivo se incluye desde index.php donde ya están:
// - session_start()
// - $sg_name, $sg_id
// - config.php, security_groups.php

// -------------------------------------------------------------
// CONTADORES (BADGES)
// -------------------------------------------------------------
$cnt_casos = 0;
$cnt_potenciales = 0;
$cnt_tareas = 0;
$cnt_delegadas = 0;
$cnt_notas = 0;
$cnt_oportunidades = 0;

if (function_exists('DbConnect')) {
    $conn_badge = DbConnect("tnaoffice_suitecrm");
    
    if ($conn_badge) {
        // Casos Abiertos
        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL Kick_Off_Operaciones_Abiertos('$sg_id')";
        if ($res = $conn_badge->query($sql)) { 
            $cnt_casos = $res->num_rows; 
            $res->free(); 
        }
        
        // Clientes Potenciales
        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL Clientes_Potenciales_Pendientes()";
        if ($res = $conn_badge->query($sql)) { 
            $cnt_potenciales = $res->num_rows; 
            $res->free(); 
        }
        
        // Tareas
        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL Kick_Off_Operaciones_Tareas_Abiertas('$sg_id')";
        if ($res = $conn_badge->query($sql)) { 
            $cnt_tareas = $res->num_rows; 
            $res->free(); 
        }
        
        // Tareas Delegadas
        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL Kick_Off_Tareas_Abiertas_Creadas('$sg_id')";
        if ($res = $conn_badge->query($sql)) { 
            $cnt_delegadas = $res->num_rows; 
            $res->free(); 
        }
        
        // Notas
        while($conn_badge->more_results()) $conn_badge->next_result();
        $sql = "CALL cm_notas_abiertas('$sg_id')";
        if ($res = $conn_badge->query($sql)) { 
            $cnt_notas = $res->num_rows; 
            $res->free(); 
        }
        
        // Oportunidades
        if ($sg_name !== "Soporte tecnico") {
            while($conn_badge->more_results()) $conn_badge->next_result();
            $sql = "CALL Oportunidades_Pendientes('$sg_id')";
            if ($res = $conn_badge->query($sql)) { 
                $cnt_oportunidades = $res->num_rows; 
                $res->free(); 
            }
        }
        
        $conn_badge->close();
    }
}
?>

<style>
.btn-modulo {
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

.btn-modulo:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
}

.btn-modulo.active {
    background: #512554;
    border-color: #64C2C8;
    box-shadow: 0 0 10px rgba(100, 194, 200, 0.3);
}

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

<!-- Touch Bar AJAX con Badges -->
<div id="menu-ajax" style="
    position: sticky;
    top: 0;
    z-index: 9998;
    display: flex;
    gap: 5px;
    padding: 8px 15px;
    background: linear-gradient(135deg, #1F1D3E 0%, #27304A 100%);
    overflow-x: auto;
    white-space: nowrap;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
">
    <button id="btn-def-casos" class="btn-modulo" onclick="loadModulo('cm_casos_abiertos.php'); selectMenu(this);">
        📑 Casos
        <?php if($cnt_casos > 0): ?><span class="badge-count"><?=$cnt_casos?></span><?php endif; ?>
    </button>
    
    <button id="btn-def-tareas" class="btn-modulo" onclick="loadModulo('cm_tareas_pendientes.php'); selectMenu(this);">
        ⏳ Tareas
        <?php if($cnt_tareas > 0): ?><span class="badge-count"><?=$cnt_tareas?></span><?php endif; ?>
    </button>
    
    <button id="btn-def-delegadas" class="btn-modulo" onclick="loadModulo('cm_tareas_pendientes_delegadas.php'); selectMenu(this);">
        📤 Delegadas
        <?php if($cnt_delegadas > 0): ?><span class="badge-count"><?=$cnt_delegadas?></span><?php endif; ?>
    </button>
    
    <button id="btn-def-clientes" class="btn-modulo" onclick="loadModulo('cm_clientes_potenciales.php'); selectMenu(this);">
        🧲 Clientes
        <?php if($cnt_potenciales > 0): ?><span class="badge-count"><?=$cnt_potenciales?></span><?php endif; ?>
    </button>
    
    <button id="btn-def-notas" class="btn-modulo" onclick="loadModulo('cm_notas_abiertas.php'); selectMenu(this);">
        📝 Notas
        <?php if($cnt_notas > 0): ?><span class="badge-count"><?=$cnt_notas?></span><?php endif; ?>
    </button>
    
    <button id="btn-def-oportunidades" class="btn-modulo" onclick="loadModulo('cm_oportunidades_abiertas.php'); selectMenu(this);">
        💼 Oportunidades
        <?php if($cnt_oportunidades > 0): ?><span class="badge-count"><?=$cnt_oportunidades?></span><?php endif; ?>
    </button>
</div>
