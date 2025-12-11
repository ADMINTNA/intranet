<?php
require_once __DIR__ . '/session_config.php';
// ... tu código de la página ...
ini_set('session.save_path', '/home/icontel/tmp_sessions');
echo "Session ID: " . session_id();
?>
