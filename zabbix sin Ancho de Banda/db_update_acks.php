<?php
// db_update_acks.php
// Add fecha and hora columns to zabbix_acks_logs for easier direct querying

define('Z_LOCAL_DB_HOST', 'localhost');
define('Z_LOCAL_DB_USER', 'tnasolut_app');
define('Z_LOCAL_DB_PASS', '1Ngr3s0.,');
define('Z_LOCAL_DB_NAME', 'tnasolut_app');

$db = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);

if ($db->connect_errno) {
    die("Error de conexión: " . $db->connect_error);
}

// Ensure timezone is CL
date_default_timezone_set('America/Santiago');

// Add columns if they don't exist
$db->query("ALTER TABLE zabbix_acks_logs ADD COLUMN IF NOT EXISTS fecha DATE AFTER message");
$db->query("ALTER TABLE zabbix_acks_logs ADD COLUMN IF NOT EXISTS hora TIME AFTER fecha");

echo "Columnas añadidas o ya existentes.<br>\n";

// Fetch existing rows to update with correct timezone dates (converting from unix clock)
$result = $db->query("SELECT id, clock FROM zabbix_acks_logs WHERE fecha IS NULL OR hora IS NULL OR fecha = '0000-00-00'");
if ($result) {
    $stmt = $db->prepare("UPDATE zabbix_acks_logs SET fecha = ?, hora = ? WHERE id = ?");
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $clock = $row['clock'];
        $fecha = date('Y-m-d', $clock);
        $hora = date('H:i:s', $clock);
        
        $stmt->bind_param("ssi", $fecha, $hora, $id);
        $stmt->execute();
        echo "Actualizado fila $id: $fecha $hora <br>\n";
    }
}

$db->close();
echo "Actualización completada.<br>\n";
?>
