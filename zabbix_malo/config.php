<?php
// config.php — Configuración centralizada de zabbix_mao/
// ─────────────────────────────────────────────────────────────
// INSTRUCCIONES PARA ROTAR CREDENCIALES:
//   Este es el ÚNICO archivo que debe editarse al cambiar credenciales.
//   Las credenciales se leen desde variables de entorno del servidor.
//
//   En Apache (.htaccess o VirtualHost):
//     SetEnv ZABBIX_URL  https://zabbix.tnasolutions.cl/zabbix/api_jsonrpc.php
//     SetEnv ZABBIX_USER Admin
//     SetEnv ZABBIX_PASS nueva_contrasena
//     SetEnv DB_HOST     localhost
//     SetEnv DB_USER     tnasolut_app
//     SetEnv DB_PASS     nueva_contrasena
//     SetEnv DB_NAME     tnasolut_app
//
//   En PHP-FPM (pool www.conf):
//     env[ZABBIX_URL]  = https://...
//     env[ZABBIX_PASS] = nueva_contrasena
//     ...
//
//   Para desarrollo local, crear un archivo .env.php (NO versionado):
//     putenv('ZABBIX_PASS=mi_pass_local');
// ─────────────────────────────────────────────────────────────

// Cargar .env.php local si existe (solo para desarrollo, nunca en producción)
$_env_local = __DIR__ . '/.env.php';
if (file_exists($_env_local) && php_sapi_name() !== 'cli') {
    // Solo carga si el archivo es legible únicamente por el owner del servidor
    $perms = fileperms($_env_local) & 0777;
    if ($perms <= 0640) {
        require_once $_env_local;
    }
}

// ─── Zabbix API ────────────────────────────────────────────
defined('ZABBIX_URL')  || define('ZABBIX_URL',  getenv('ZABBIX_URL')  ?: 'http://zabbix.tnasolutions.cl:9090/zabbix/api_jsonrpc.php');
defined('ZABBIX_USER') || define('ZABBIX_USER', getenv('ZABBIX_USER') ?: 'Admin');
defined('ZABBIX_PASS') || define('ZABBIX_PASS', getenv('ZABBIX_PASS') ?: '');

// ─── Base de datos local ────────────────────────────────────
defined('Z_LOCAL_DB_HOST') || define('Z_LOCAL_DB_HOST', getenv('DB_HOST') ?: 'localhost');
defined('Z_LOCAL_DB_USER') || define('Z_LOCAL_DB_USER', getenv('DB_USER') ?: 'tnasolut_app');
defined('Z_LOCAL_DB_PASS') || define('Z_LOCAL_DB_PASS', getenv('DB_PASS') ?: '');
defined('Z_LOCAL_DB_NAME') || define('Z_LOCAL_DB_NAME', getenv('DB_NAME') ?: 'tnasolut_app');

// ─── Configuración de la aplicación ────────────────────────
defined('ALLOWED_ORIGIN') || define('ALLOWED_ORIGIN', getenv('ALLOWED_ORIGIN') ?: 'https://intranet.icontel.cl');
defined('ZABBIX_CACHE_TTL') || define('ZABBIX_CACHE_TTL', 120); // segundos

// ─────────────────────────────────────────────────────────────
// Función compartida para llamadas a la API de Zabbix.
// Usada por api_proxy.php, consumo/api.php y cualquier
// archivo futuro. NO duplicar en otros archivos.
// ─────────────────────────────────────────────────────────────
if (!function_exists('zabbix_request')) {
    function zabbix_request($method, $params, $auth = null) {
        $payload = [
            'jsonrpc' => '2.0',
            'method'  => $method,
            'params'  => $params,
            'id'      => 1,
        ];
        if ($auth) {
            $payload['auth'] = $auth;
        }

        $ch = curl_init(ZABBIX_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT,        30);
        // Verificar certificado SSL en producción
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('[zabbix_mao] Zabbix API cURL error: ' . curl_error($ch));
        }
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['result'] ?? null;
    }
}

// ─────────────────────────────────────────────────────────────
// Obtiene (y cachea en sesión) el token de autenticación Zabbix.
// Evita hacer un user.login en cada request.
// ─────────────────────────────────────────────────────────────
if (!function_exists('get_auth_token')) {
    function get_auth_token() {
        // Reutilizar token cacheado en sesión si no ha expirado
        if (!empty($_SESSION['_z_token']) && !empty($_SESSION['_z_token_ts'])) {
            if ((time() - $_SESSION['_z_token_ts']) < 1500) { // 25 min (sesión Zabbix = 30 min)
                return $_SESSION['_z_token'];
            }
        }

        $token = zabbix_request('user.login', [
            'user'     => ZABBIX_USER,
            'password' => ZABBIX_PASS,
        ]);

        if ($token) {
            // Guardar en sesión (session_write_close() ya fue llamado en z_session.php,
            // así que reabrimos momentáneamente solo para escribir el token)
            session_start();
            $_SESSION['_z_token']    = $token;
            $_SESSION['_z_token_ts'] = time();
            session_write_close();
        }

        return $token;
    }
}

// ─────────────────────────────────────────────────────────────
// Helper: extrae el código de servicio del nombre del host.
// Ej: "Seidor Hendaya (GTD010212519)" → "GTD010212519"
// ─────────────────────────────────────────────────────────────
if (!function_exists('extract_service_code')) {
    function extract_service_code($name) {
        if (preg_match('/\(\s*([A-Z0-9]{6,20})\s*\)\s*$/', trim($name), $m)) {
            return $m[1];
        }
        return '';
    }
}

// ─────────────────────────────────────────────────────────────
// Helper: conexión a la BD local (reutilizable).
// Retorna mysqli o null si falla.
// ─────────────────────────────────────────────────────────────
if (!function_exists('get_local_db')) {
    function get_local_db() {
        $con = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
        if ($con->connect_errno) {
            error_log('[zabbix_mao] DB connection error: ' . $con->connect_error);
            return null;
        }
        $con->set_charset('utf8mb4');
        return $con;
    }
}
