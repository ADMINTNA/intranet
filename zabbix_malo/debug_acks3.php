<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ZABBIX_URL', 'http://zabbix.tnasolutions.cl:9090/zabbix/api_jsonrpc.php');
define('ZABBIX_USER', 'Admin');
define('ZABBIX_PASS', '1Ngr3s0.,');

define('Z_LOCAL_DB_HOST', 'localhost');
define('Z_LOCAL_DB_USER', 'tnasolut_app');
define('Z_LOCAL_DB_PASS', '1Ngr3s0.,');
define('Z_LOCAL_DB_NAME', 'tnasolut_app');

function zabbix_request($method, $params, $auth = null) {
    $data = [
        'jsonrpc' => '2.0',
        'method'  => $method,
        'params'  => $params,
        'id'      => 1,
    ];
    if ($auth) $data['auth'] = $auth;

    $ch = curl_init(ZABBIX_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json-rpc']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($response, true);
    if (!$decoded) return ['error' => 'curl proxy failed', 'raw' => $response];
    return $decoded['result'] ?? $decoded;
}

$token_res = zabbix_request('user.login', ['user' => ZABBIX_USER, 'password' => ZABBIX_PASS]);
$token = $token_res;

$events = zabbix_request('event.get', [
    'output'      => ['eventid', 'name', 'acknowledged'],
    'select_acknowledges' => ['message', 'action', 'clock', 'alias', 'name'],
    'acknowledged' => true,
    'value'       => 1,
    'time_from'   => time() - (86400 * 7),
], $token);

if (!is_array($events)) {
    echo "events not array: " . print_r($events, true);
    exit;
}

$found = false;
foreach ($events as $e) {
    if ($e['eventid'] == '153172272' || $e['eventid'] == '153168809' || strpos($e['name'], 'Memory usage') !== false) {
        $found = true;
        echo "\n==== ZABBIX EVENT: " . $e['eventid'] . " ====\n";
        print_r($e);
        
        $con = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
        if (!$con->connect_errno) {
            $eid = $e['eventid'];
            $res = $con->query("SELECT * FROM zabbix_acks_logs WHERE eventid = '$eid'");
            echo "ROWS in local db: " . $res->num_rows . "\n";
            while($r = $res->fetch_assoc()) {
                print_r($r);
            }
        }
    }
}
if (!$found) echo "Events 153172272/153168809 not found in Zabbix event.get";
?>