<?php
require_once 'z_session.php'; // Required for auth
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
    return $decoded['result'] ?? null;
}

function get_auth_token() {
    $res = zabbix_request('user.login', ['user' => ZABBIX_USER, 'password' => ZABBIX_PASS]);
    return $res;
}

$token = get_auth_token();
$events = zabbix_request('event.get', [
    'output'      => ['eventid', 'name', 'objectid', 'acknowledged'],
    'select_acknowledges' => ['message', 'action', 'alias', 'name'],
    'acknowledged' => true,
    'value'       => 1,
    'time_from'   => time() - (86400 * 7),
], $token);

echo "raw events from zabbix:\n";
foreach($events as $e) {
    if ($e['eventid'] == '153172272' || $e['eventid'] == '153168809' || strpos($e['name'], 'Memory usage') !== false) {
        print_r($e);
    }
}

// Check local DB
$con = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
$eids_str = implode("','", array_column($events, 'eventid'));
echo "\nQuerying DB for: $eids_str\n";
$res = $con->query("SELECT eventid, user_name, message FROM zabbix_acks_logs WHERE eventid IN ('$eids_str')");
echo "Rows found in local DB: " . $res->num_rows . "\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
