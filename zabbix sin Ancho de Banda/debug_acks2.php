<?php
$_SESSION['loggedin'] = true;
$_SESSION['id'] = 17;
$_SESSION['cliente'] = 'Mauricio Araneda O.';

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

function enrich_with_local_acks($events) {
    if (!is_array($events) || empty($events)) return [];
    
    $local_acks = [];
    $con = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
    if (!$con->connect_errno) {
        $eids = array_map(function($e) { return $e['eventid']; }, $events);
        $eids_str = implode("','", $eids);
        if ($eids_str) {
            $sql = "SELECT eventid, user_name, message FROM zabbix_acks_logs WHERE eventid IN ('$eids_str')";
            echo "SQL: $sql\n";
            $res = $con->query($sql);
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $local_acks[$row['eventid']] = $row;
                }
            } else {
                echo "DB Error: " . $con->error . "\n";
            }
        }
        $con->close();
    } else {
        echo "DB Connection Error: " . $con->connect_error . "\n";
    }

    echo "Local Acks Found: " . print_r($local_acks, true) . "\n";

    foreach ($events as &$f) {
        $eid = $f['eventid'];
        if (isset($local_acks[$eid])) {
            $local = $local_acks[$eid];
            if (empty($f['acknowledges'])) {
                $f['acknowledges'] = [['alias' => $local['user_name'], 'message' => $local['message'], 'clock' => $f['clock']]];
            } else {
                $f['acknowledges'][0]['alias']   = $local['user_name'];
                $f['acknowledges'][0]['message'] = $local['message'];
            }
        }
    }
    return $events;
}

$events = zabbix_request('event.get', [
    'output'      => ['eventid', 'name', 'objectid', 'acknowledged'],
    'select_acknowledges' => ['message', 'action', 'clock', 'alias', 'name', 'surname'],
    'acknowledged' => true,
    'value'       => 1,
    'source'      => 0,
    'object'      => 0,
    'time_from'   => time() - (86400 * 7),
    'sortfield'   => ['clock'],
    'sortorder'   => 'DESC'
], $token);

$events = enrich_with_local_acks($events);

foreach ($events as $e) {
    if ($e['eventid'] == '153172272' || $e['eventid'] == '153168809' || strpos($e['name'], 'Memory usage') !== false) {
        print_r($e);
    }
}
?>
