<?php
// Script de diagnóstico para verificar sesión
session_name('icontel_intranet_sess');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '.icontel.cl');
ini_set('session.cookie_secure', '0');
ini_set('session.cookie_httponly', '1');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug Sesión Office</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd; }
        .ok { color: green; }
        .error { color: red; }
        pre { background: #f9f9f9; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h2>🔍 Debug de Sesión - Office Module</h2>
    
    <div class="box">
        <h3>Estado de la Sesión</h3>
        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>Session Name:</strong> <?php echo session_name(); ?></p>
        <p><strong>Session Status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? '<span class="ok">ACTIVA</span>' : '<span class="error">INACTIVA</span>'; ?></p>
    </div>
    
    <div class="box">
        <h3>Variables de Sesión</h3>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
    
    <div class="box">
        <h3>Cookies</h3>
        <pre><?php print_r($_COOKIE); ?></pre>
    </div>
    
    <div class="box">
        <h3>Validación de Login</h3>
        <?php if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
            <p class="error">❌ NO LOGGEADO - $_SESSION['loggedin'] no está establecido o es false</p>
        <?php else: ?>
            <p class="ok">✅ LOGGEADO CORRECTAMENTE</p>
            <p><strong>Usuario:</strong> <?php echo $_SESSION['name'] ?? 'N/A'; ?></p>
            <p><strong>Rol:</strong> <?php echo $_SESSION['rol'] ?? 'N/A'; ?></p>
            <p><strong>sec_id_office:</strong> <?php echo $_SESSION['sec_id_office'] ?? 'N/A'; ?></p>
        <?php endif; ?>
    </div>
    
    <div class="box">
        <h3>Configuración de Sesión PHP</h3>
        <p><strong>session.cookie_path:</strong> <?php echo ini_get('session.cookie_path'); ?></p>
        <p><strong>session.cookie_domain:</strong> <?php echo ini_get('session.cookie_domain'); ?></p>
        <p><strong>session.cookie_secure:</strong> <?php echo ini_get('session.cookie_secure'); ?></p>
        <p><strong>session.cookie_httponly:</strong> <?php echo ini_get('session.cookie_httponly'); ?></p>
        <p><strong>session.cookie_samesite:</strong> <?php echo ini_get('session.cookie_samesite'); ?></p>
    </div>
    
    <div class="box">
        <h3>Información del Servidor</h3>
        <p><strong>URL Actual:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?></p>
        <p><strong>HTTP Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'N/A'; ?></p>
        <p><strong>HTTPS:</strong> <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'SÍ' : 'NO'; ?></p>
    </div>
</body>
</html>
