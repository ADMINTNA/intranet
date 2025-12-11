<?php
function getClientIP() {
    $ip = $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER["HTTP_CF_CONNECTING_IP"]  // Cloudflare
        ?? $_SERVER['HTTP_X_FORWARDED']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_FORWARDED']
        ?? $_SERVER['HTTP_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';

    // Validación IPv4
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $ip = '127.0.0.1';
    }

    return $ip;
}

// Ejecutar
$ipCliente = getClientIP();
echo "<h3>IP del Cliente:</h3><p>$ipCliente</p>";
?>
