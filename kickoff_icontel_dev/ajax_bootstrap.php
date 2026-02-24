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
require_once __DIR__ . '/session_core.php';

// -------------------------
// MAPEO DE VARIABLES DE SESIÓN
// -------------------------
// El login principal usa $_SESSION['name'], KickOff usa $_SESSION['usuario']
if (isset($_SESSION['name']) && !isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = $_SESSION['name'];
}

// Cargar configuración (requerido para DbConnect en security_groups)
if (!function_exists('DbConnect')) {
    require_once __DIR__ . "/config.php";
}

// Obtener sg_name si no existe
if (isset($_SESSION['sg_id']) && empty($_SESSION['sg_name'])) {
    require_once __DIR__ . '/security_groups.php';
    foreach ($grupos as $g) {
        if ($g['id'] == $_SESSION['sg_id']) {
            $_SESSION['sg_name'] = $g['name'];
            break;
        }
    }
}

// -------------------------
// 2) VALIDACIÓN DE SESIÓN
// -------------------------
$sg_id   = $_SESSION['sg_id']   ?? '';
$sg_name = $_SESSION['sg_name'] ?? '';
$usuario = $_SESSION['usuario'] ?? '';
$loggedin = $_SESSION['loggedin'] ?? false;

// Bypass para desarrollador (Mauricio)
$is_developer = (strpos(basename(dirname(__FILE__)), 'kickoff_icontel') !== false && $usuario === 'Mauricio');

// Si no hay grupo pero está logueado, intentar recuperar default
if ($loggedin && $sg_id === '') {
    $sg_id = "a03a40e8-bda8-0f1b-b447-58dcfb6f5c19"; // ID por defecto (Soporte)
    $_SESSION['sg_id'] = $sg_id;
}

// DEBUG: Loguear estado si falla
if (!$is_developer && (!$loggedin || $sg_id === '')) {
    $debug_msg = date("[Y-m-d H:i:s]") . " ❌ SESSION FAIL: user=$usuario, sg_id=$sg_id, lg=$loggedin, URI=" . $_SERVER['REQUEST_URI'] . "\n";
    @file_put_contents("/tmp/kickoff_session_debug.log", $debug_msg, FILE_APPEND);
    
    // 🔍 MOSTRAR DEBUG EN PANTALLA
    echo "<div style='background:#ffcccc; padding:15px; margin:10px; border:2px solid #ff0000;'>";
    echo "<strong>🔍 DEBUG - ajax_bootstrap.php</strong><br>";
    echo "<strong>Validación falló:</strong><br>";
    echo "- \$loggedin: " . var_export($loggedin, true) . "<br>";
    echo "- \$sg_id: " . htmlspecialchars($sg_id) . "<br>";
    echo "- \$usuario: " . htmlspecialchars($usuario) . "<br>";
    echo "<br><strong>Variables de sesión completas:</strong><br>";
    echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";
    echo "</div>";
}

if (!$is_developer && (!$loggedin || $sg_id === '')) {
    // Devolver JSON en lugar de HTML para endpoints AJAX
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'error' => 'Sesión inválida o expirada',
        'redirect' => $GLOBALS['KICKOFF_REDIRECT_URL'] ?? 'https://intranet.icontel.cl/?dev=1'
    ]);
    exit;
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
