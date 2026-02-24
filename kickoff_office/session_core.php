<?php
// ==========================================================
// KickOff Office – CORE DE SESIÓN UNIFICADA
// ==========================================================

// 1) Configurar parámetros de cookies
session_name('icontel_intranet_sess');

// Compatibilidad con PHP < 7.3 (Usando argumentos posicionales)
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

// 2) Recuperar último grupo de seguridad desde Cookie (Solo si no está en sesión)
// En Office usamos principalmente sec_id_office para evitar colisiones con iContel
if (!isset($_SESSION['sec_id_office']) && isset($_COOKIE['icontel_last_sg_id'])) {
    $_SESSION['sec_id_office'] = $_COOKIE['icontel_last_sg_id'];
    $_SESSION['sg_id'] = $_COOKIE['icontel_last_sg_id']; // Sincronizar para compatibilidad
}

// 2.1) Auto-sincronizar Sesión -> Cookie
if (!empty($_SESSION['sec_id_office']) && (!isset($_COOKIE['icontel_last_sg_id']) || $_COOKIE['icontel_last_sg_id'] !== $_SESSION['sec_id_office'])) {
    setcookie('icontel_last_sg_id', $_SESSION['sec_id_office'], time() + (86400 * 30), '/', '.icontel.cl', false, true);
}

// 3) Definir URL de redirección
$GLOBALS['KICKOFF_REDIRECT_URL'] = "https://intranet.icontel.cl/";
?>
