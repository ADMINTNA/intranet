<?php
// ==========================================================
// KickOff – CORE DE SESIÓN UNIFICADA
// ==========================================================

// 1) Configurar parámetros de cookies
session_name('icontel_intranet_sess');

// Compatibilidad con PHP < 7.3 (No soporta array en session_set_cookie_params)
session_set_cookie_params(
    0,              // lifetime
    '/',            // path
    '.icontel.cl',  // domain
    false,          // secure (Cambiado de true para compatibilidad en entornos locales/HTTP)
    true            // httponly
);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Detectar y persistir modo Dev
// Si hay parámetro dev=1 o estamos en la carpeta _dev, marcar sesión
if ((isset($_GET['dev']) && $_GET['dev'] == '1') || strpos(dirname($_SERVER['PHP_SELF']), '_dev') !== false) {
    $_SESSION['is_dev_mode'] = true;
}

// 3) Definir URL de redirección dinámica
$redirect_url = "https://intranet.icontel.cl/";
if (!empty($_SESSION['is_dev_mode'])) {
    // Si hay error en la URL original, preservarlo, si no, solo dev=1
    $sep = (strpos($redirect_url, '?') !== false) ? '&' : '?';
    $redirect_url .= $sep . "dev=1";
}

$GLOBALS['KICKOFF_REDIRECT_URL'] = $redirect_url;
?>
