<?php
// ==========================================================
// KickOff AJAX – Bootstrap común para TODOS los módulos
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

// -------------------------
// 1) CONFIGURAR SESIÓN
// -------------------------
// -------------------------
// 1) CONFIGURAR SESIÓN
// -------------------------
require_once __DIR__ . '/session_core.php';

// -------------------------
// 2) VALIDACIÓN DE SESIÓN
// -------------------------
// Para usuarios de Office, usar sec_id_office en lugar de sg_id
$sg_id   = $_SESSION['sec_id_office'] ?? $_SESSION['sg_id'] ?? '';
$sg_name = $_SESSION['sg_name'] ?? '';
$usuario = $_SESSION['usuario'] ?? $_SESSION['name'] ?? '';

if ($sg_id === '') {

    // Log para debugging
    error_log("❌ AJAX ERROR — SESIÓN VACÍA: sg_id={$sg_id}, usuario={$usuario}");
    
    // Devolver JSON o detener ejecución sin destruir la sesión del padre
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode([
        'success' => false,
        'error' => 'Sesión o grupo no válido',
        'redirect' => $GLOBALS['KICKOFF_REDIRECT_URL'] ?? 'https://intranet.icontel.cl'
    ]));
}

// Log OK
//error_log("✔ AJAX OK — sg_id={$sg_id}, sg_name={$sg_name}, usuario={$usuario}");

// -------------------------
// 3) INCLUIR CONFIGURACIÓN
// -------------------------
require_once __DIR__ . "/config.php"; // rutas absolutas para evitar errores

// -------------------------
// 4) VARIABLES DE BASE DE DATOS
// -------------------------
$db_sweet = "tnaoffice_suitecrm"; // Base de datos de SuiteCRM

// -------------------------
// 5) EXPONER VARIABLES
// -------------------------
$GLOBALS["sg_id"]   = $sg_id;
$GLOBALS["sg_name"] = $sg_name;
$GLOBALS["usuario"] = $usuario;
$GLOBALS["db_sweet"] = $db_sweet;
