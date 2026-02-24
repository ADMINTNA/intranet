<?php
define('ZABBIX_URL', 'http://zabbix.tnasolutions.cl:9090/zabbix/api_jsonrpc.php');
define('ZABBIX_USER', 'Admin');
define('ZABBIX_PASS', '1Ngr3s0.,');

function zabbix_request($method, $params, $auth = null) {
    $payload = [
        'jsonrpc' => '2.0',
        'method'  => $method,
        'params'  => $params,
        'id'      => 1,
    ];
    if ($auth) $payload['auth'] = $auth;
    $ch = curl_init(ZABBIX_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true)['result'] ?? null;
}

$token = zabbix_request('user.login', ['user' => ZABBIX_USER, 'password' => ZABBIX_PASS]);
if (!$token) die("Auth failed\n");

$host = zabbix_request('host.get', [
    'filter' => ['name' => 'Rosita-Ñuñoa'],
    'selectGroups' => 'extend',
    'selectInterfaces' => 'extend',
    'selectItems' => ['name', 'key_', 'lastvalue', 'units'],
    'output' => 'extend'
], $token);

echo json_encode($host, JSON_PRETTY_PRINT);
