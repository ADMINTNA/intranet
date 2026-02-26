<?php
// zabbix_acks_logs_backfill.php
error_reporting(E_ALL); ini_set('display_errors', 1);

define('ZABBIX_URL', 'http://zabbix.tnasolutions.cl:9090/zabbix/api_jsonrpc.php');
define('ZABBIX_USER', 'Admin');
define('ZABBIX_PASS', '1Ngr3s0.,');

function z_req($method, $params, $auth=null) {
    $d = ['jsonrpc'=>'2.0', 'method'=>$method, 'params'=>$params, 'id'=>1];
    if($auth) $d['auth']=$auth;
    $ch=curl_init(ZABBIX_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json-rpc']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($d));
    $r=curl_exec($ch); curl_close($ch);
    return json_decode($r,true)['result']??null;
}

define('Z_LOCAL_DB_HOST', 'localhost');
define('Z_LOCAL_DB_USER', 'tnasolut_app');
define('Z_LOCAL_DB_PASS', '1Ngr3s0.,');
define('Z_LOCAL_DB_NAME', 'tnasolut_app');

$db = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
if ($db->connect_errno) die("DB Error");
$db->set_charset("utf8mb4");

$token = z_req('user.login', ['user'=>ZABBIX_USER, 'password'=>ZABBIX_PASS]);
if (!$token) die("Zabbix Auth Error");

$res = $db->query("SELECT id, eventid FROM zabbix_acks_logs WHERE host_name = 'Desconocido' OR host_name = '' OR host_name IS NULL");
$count = 0;

while($row = $res->fetch_assoc()) {
    // Get event details from Zabbix
    $e = z_req('event.get', [
        'output' => ['name', 'severity', 'objectid'],
        'eventids' => $row['eventid']
    ], $token);
    
    if (is_array($e) && !empty($e)) {
        $ev = $e[0];
        $alert_name = $ev['name'];
        $sev = (int)$ev['severity'];
        
        // Get host from trigger
        $t = z_req('trigger.get', [
            'output' => ['triggerid'],
            'selectHosts' => ['name'],
            'triggerids' => [$ev['objectid']]
        ], $token);
        
        $host_name = 'Desconocido';
        if (is_array($t) && !empty($t) && isset($t[0]['hosts'][0]['name'])) {
            $host_name = $t[0]['hosts'][0]['name'];
        }
        
        $stmt = $db->prepare("UPDATE zabbix_acks_logs SET host_name=?, alert_name=?, severity=? WHERE id=?");
        $stmt->bind_param("ssii", $host_name, $alert_name, $sev, $row['id']);
        $stmt->execute();
        $count++;
    }
}

echo "Migrados $count registros viejos recuperando sus variables de Zabbix.<br>";
$db->close();
?>