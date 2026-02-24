<?php
// db_update_acks_phase2.php
// Add host, alert, and severity columns for pure-local reporting

define('Z_LOCAL_DB_HOST', 'localhost');
define('Z_LOCAL_DB_USER', 'tnasolut_app');
define('Z_LOCAL_DB_PASS', '1Ngr3s0.,');
define('Z_LOCAL_DB_NAME', 'tnasolut_app');

$db = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);

if ($db->connect_errno) {
    die("Error de conexión: " . $db->connect_error);
}

// Add columns if they don't exist
$db->query("ALTER TABLE zabbix_acks_logs ADD COLUMN IF NOT EXISTS host_name VARCHAR(150) AFTER eventid");
$db->query("ALTER TABLE zabbix_acks_logs ADD COLUMN IF NOT EXISTS alert_name VARCHAR(255) AFTER host_name");
$db->query("ALTER TABLE zabbix_acks_logs ADD COLUMN IF NOT EXISTS severity INT AFTER alert_name");

echo "Columnas host_name, alert_name y severity añadidas o ya existentes.<br>\n";

// Optional: we leave them NULL for old records, or manually patch the 2 test records if needed.
// For now, setting defaults for the test records just so the UI doesn't break if they are queried.
$db->query("UPDATE zabbix_acks_logs SET host_name = 'Desconocido', alert_name = 'Alerta Pasada (Migrada)', severity = 3 WHERE host_name IS NULL");

$db->close();
echo "Actualización Fase 2 completada.<br>\n";
?>
