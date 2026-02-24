<?php
// =============================================================
// session_config.php â versiÃ³n estable PHP 7.x (unificaciÃ³n total)
// =============================================================

// Ruta de sesiones
ini_set('session.save_path', '/home/icontel/tmp_sessions');
@mkdir('/home/icontel/tmp_sessions', 0700, true);

// ConfiguraciÃ³n de la cookie
session_name('icontel_intranet_sess');
ini_set('session.cookie_path', '/');                     // vÃ¡lida en todo el sitio
ini_set('session.cookie_domain', 'intranet.icontel.cl'); // dominio sin www
ini_set('session.cookie_secure', '1');                   // solo HTTPS
ini_set('session.cookie_httponly', '1');                 // no accesible por JS
ini_set('session.gc_maxlifetime', 28800);                // 8 horas

// Iniciar sesiÃ³n (solo una vez)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Workaround SameSite=Lax para PHP<7.3
$params = session_get_cookie_params();
setcookie(
    session_name(),
    session_id(),
    0,
    $params['path'] . '; SameSite=Lax',
    $params['domain'],
    isset($_SERVER['HTTPS']),
    true
);

// Control de tiempo
$INACTIVITY_LIMIT = 60 * 60 * 4; // 4h inactividad
$ABSOLUTE_LIMIT   = 60 * 60 * 24; // 24h total
$now = time();

if (isset($_SESSION['created_at']) && ($now - $_SESSION['created_at'] > $ABSOLUTE_LIMIT)) {
    session_unset();
    session_destroy();
    header('Location: /login.html?error=session_expired_abs');
    exit;
}

if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity'] > $INACTIVITY_LIMIT)) {
    session_unset();
    session_destroy();
    header('Location: /login.html?error=session_expired_idle');
    exit;
}

$_SESSION['last_activity'] = $now;