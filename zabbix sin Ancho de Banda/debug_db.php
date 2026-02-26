<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('Z_LOCAL_DB_HOST', 'localhost');
define('Z_LOCAL_DB_USER', 'tnasolut_app');
define('Z_LOCAL_DB_PASS', '1Ngr3s0.,');
define('Z_LOCAL_DB_NAME', 'tnasolut_app');

$con = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
if ($con->connect_errno) {
    echo "DB Connection Error: " . $con->connect_error . "\n";
    exit;
}

$res = $con->query("SELECT * FROM zabbix_acks_logs ORDER BY id DESC LIMIT 5");
echo "<pre>ROWS in local db: " . $res->num_rows . "\n";
while($r = $res->fetch_assoc()) {
    print_r($r);
}

// And check api_proxy.php response
echo "\n--- fetching acknowledged_events from api_proxy ---\n";
// Can't easily without session cookie
?>
