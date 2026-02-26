<?php
// z_session.php - Zabbix Session Handler
// Uses kickoff_icontel/session_core.php to bypass the session.save_path issue
// in intranet/session_config.php on the remote server.

require_once dirname(__DIR__) . '/kickoff_icontel/session_core.php';

if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    $isJson = isset($_SERVER['CONTENT_TYPE']) && strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false;
    $isApi  = strpos($_SERVER['REQUEST_URI'], 'api.php') !== false || strpos($_SERVER['REQUEST_URI'], 'api_proxy.php') !== false;

    if ($isAjax || $isJson || $isApi) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing or expired session. Please log in again.']);
        exit;
    } else {
        // Página HTML — puede estar cargando dentro de un iframe.
        // Usamos JS para redirigir window.top (el portal completo) al login,
        // en vez de un header Location que el browser seguiría dentro del iframe.
        http_response_code(401);
        header('Cache-Control: no-store');
        $login_url = htmlspecialchars('https://intranet.icontel.cl/index.php?error=session');
        echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8">
<style>
  body{margin:0;display:flex;align-items:center;justify-content:center;
       height:100vh;background:#0f172a;font-family:Arial,sans-serif;color:#f1f5f9;
       flex-direction:column;gap:12px}
  p{font-size:13px;color:#94a3b8;margin:0}
  button{background:#3b82f6;color:#fff;border:none;border-radius:6px;
         padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;margin-top:4px}
</style>
</head>
<body>
  <div style="font-size:40px">🔒</div>
  <strong>Sesión expirada</strong>
  <p>Redirigiendo al login…</p>
  <button onclick="(window.top||window).location.href='{$login_url}'">Ir al login</button>
  <script>
    try { window.top.location.href = '{$login_url}'; }
    catch(e) { window.location.href = '{$login_url}'; }
  </script>
</body>
</html>
HTML;
        exit;
    }
}

// Liberar lock de sesión para permitir requests paralelos (Promise.all)
session_write_close();
?>
