<?php
// ==========================================================
// KickOff AJAX – Bootstrap común para TODOS los módulos
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

// Caché del navegador: 30 s (el JS también tiene caché interno de 60 s)
header('Cache-Control: private, max-age=30, must-revalidate');
header('Content-Type: text/html; charset=UTF-8');

// -------------------------
// 1) CONFIGURAR SESIÓN
// -------------------------
require_once __DIR__ . '/session_core.php';

// ⚠️ BYPASS TEMPORAL PARA PRUEBAS - ELIMINAR EN PRODUCCIÓN
if (empty($_SESSION['loggedin'])) {
    $_SESSION['loggedin'] = true;
    $_SESSION['usuario'] = 'Mauricio';
    $_SESSION['sg_id'] = 'a03a40e8-bda8-0f1b-b447-58dcfb6f5c19';
    $_SESSION['sg_name'] = 'Soporte tecnico';
}


// -------------------------
// 2) VALIDACIÓN DE SESIÓN
// -------------------------
$sg_id   = $_SESSION['sg_id']   ?? '';
$sg_name = $_SESSION['sg_name'] ?? '';
$usuario = $_SESSION['usuario'] ?? '';
$loggedin = $_SESSION['loggedin'] ?? false;

// Bypass para desarrollador (Mauricio)
$is_developer = (basename(dirname(__FILE__)) === 'kickoff_icontel' && $usuario === 'Mauricio');

// Si no hay grupo pero está logueado, intentar recuperar default
if ($loggedin && $sg_id === '') {
    $sg_id = "a03a40e8-bda8-0f1b-b447-58dcfb6f5c19"; // ID por defecto (Soporte)
    $_SESSION['sg_id'] = $sg_id;
}

// DEBUG: Loguear estado si falla
if (!$is_developer && (!$loggedin || $sg_id === '')) {
    $debug_msg = date("[Y-m-d H:i:s]") . " ❌ SESSION FAIL: user=$usuario, sg_id=$sg_id, lg=$loggedin, URI=" . $_SERVER['REQUEST_URI'] . "\n";
    @file_put_contents("/tmp/kickoff_session_debug.log", $debug_msg, FILE_APPEND);
}

if (!$is_developer && (!$loggedin || $sg_id === '')) {
    // Devolver JSON en lugar de HTML para endpoints AJAX
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'error' => 'Sesión inválida o expirada',
        'redirect' => $GLOBALS['KICKOFF_REDIRECT_URL'] ?? 'https://intranet.icontel.cl/'
    ]);
    exit;
}

// Incluir configuración si no existe DbConnect
if (!function_exists('DbConnect')) {
    require_once __DIR__ . "/config.php";
}

// Recuperar sg_name si falta
if ($sg_name === '' && function_exists('DbConnect')) {
    require_once __DIR__ . "/security_groups.php"; // Define $grupos
    foreach ($grupos as $g) {
        if ($g['id'] == $sg_id) {
            $sg_name = $g['name'];
            $_SESSION['sg_name'] = $sg_name;
            break;
        }
    }
}

// -------------------------
// 4) EXPONER VARIABLES
// -------------------------
$GLOBALS["sg_id"]   = $sg_id;
$GLOBALS["sg_name"] = $sg_name;
$GLOBALS["usuario"] = $usuario;
