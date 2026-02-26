<?php
// z_session.php - Zabbix Session Handler
// Uses kickoff_icontel/session_core.php to bypass the session.save_path issue
// in intranet/session_config.php on the remote server.

require_once dirname(__DIR__) . '/kickoff_icontel/session_core.php';

if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Determine if the request is AJAX or an API call
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    $isJson = isset($_SERVER['CONTENT_TYPE']) && strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false;
    $isApi = strpos($_SERVER['REQUEST_URI'], 'api.php') !== false || strpos($_SERVER['REQUEST_URI'], 'api_proxy.php') !== false;

    if ($isAjax || $isJson || $isApi) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing or expired session. Please log in again.']);
        exit;
    } else {
        // Redirigir al inicio de la Intranet (HTTP redirect, sin JS)
        header('Location: https://intranet.icontel.cl/index.php?error=session');
        header('Cache-Control: no-store');
        exit;
    }
}

// RELEASE SESSION LOCK IMMEDIATELY
// This allows true parallel execution of API requests (Promise.all in JS)
// Because otherwise, PHP queues them sequentially waiting for the session file.
// None of the Zabbix scripts write back to $_SESSION anyway.
session_write_close();
?>