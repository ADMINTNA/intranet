<?php
// ==========================================================
// KickOff – CORE DE SESIÓN UNIFICADA (DEV)
// ==========================================================

// 1) Configurar parámetros de cookies
session_name('icontel_intranet_sess');

session_set_cookie_params(
    0,              // lifetime
    '/',            // path
    '.icontel.cl',  // domain
    false,          // secure
    true            // httponly
);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// --- DEBUG AGRESIVO ---
$uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
$sid = session_id();
$sess_data = json_encode($_SESSION);
$cookie_data = json_encode($_COOKIE);
$log_msg = date("[Y-m-d H:i:s]") . " | SID: $sid | URI: $uri\n";
$log_msg .= "   SESSION: $sess_data\n";
$log_msg .= "   COOKIES: $cookie_data\n";
// ----------------------

// 2) Forzar modo Dev en esta carpeta
$_SESSION['is_dev_mode'] = true;

// 3) Recuperar último grupo de seguridad desde Cookie (Solo si no está en sesión)
if (!isset($_SESSION['sg_id']) && isset($_COOKIE['icontel_last_sg_id'])) {
    $_SESSION['sg_id'] = $_COOKIE['icontel_last_sg_id'];
}

// 3.1) Auto-sincronizar Sesión -> Cookie
if (!empty($_SESSION['sg_id']) && (!isset($_COOKIE['icontel_last_sg_id']) || $_COOKIE['icontel_last_sg_id'] !== $_SESSION['sg_id'])) {
    setcookie('icontel_last_sg_id', $_SESSION['sg_id'], time() + (86400 * 30), '/', '.icontel.cl', false, true);
}

// 4) Definir URL de redirección dinámica
$GLOBALS['KICKOFF_REDIRECT_URL'] = "https://intranet.icontel.cl/?dev=1";
?>
