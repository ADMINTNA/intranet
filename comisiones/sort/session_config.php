<?php
// ==========================================
// Configuración global de sesión para Comisiones
// ==========================================
session_name('icontel_intranet_sess');

// Asegurar cookie válida para todos los subdominios
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '.icontel.cl');

// Seguridad
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
