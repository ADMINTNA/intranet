<?php
// Standalone backfill script
define('ZABBIX_URL', 'http://zabbix.tnasolutions.cl:9090/zabbix/api_jsonrpc.php');
define('ZABBIX_USER', 'Admin');
define('ZABBIX_PASS', '1Ngr3s0.,');

function zr($method, $params, $auth = null) {
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
    $data = json_decode($response, true);
    return $data['result'] ?? null;
}

$token = zr('user.login', ['user' => ZABBIX_USER, 'password' => ZABBIX_PASS]);

$db = new mysqli("localhost", "tnasolut_app", "1Ngr3s0.,", "tnasolut_app");
if ($db->connect_errno) die("DB Error");

$to_backfill = [
    [
        'name' => 'Memory usage', 
        'clock' => 1740323640, // 12:14
        'msg' => 'Resulto, mAo, de prueba',
        'user' => 'Mauricio'
    ],
    [
        'name' => 'High ICMP', 
        'clock' => 1740319620, // 11:07
        'msg' => 'Verificado, esperando comportamiento futuro',
        'user' => 'Mauricio'
    ]
];

foreach ($to_backfill as $item) {
    $events = zr('event.get', [
        'output' => ['eventid', 'name', 'clock'],
        'search' => ['name' => $item['name']],
        'time_from' => $item['clock'] - 300,
        'time_till' => $item['clock'] + 300
    ], $token);

    if (!empty($events)) {
        $eid = $events[0]['eventid'];
        $stmt = $db->prepare("DELETE FROM zabbix_acks_logs WHERE eventid = ?");
        $stmt->bind_param("s", $eid);
        $stmt->execute();

        $user_id = 1; // Assuming 1 for main admin/Mauricio
        $stmt = $db->prepare("INSERT INTO zabbix_acks_logs (eventid, user_id, user_name, message, clock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $eid, $user_id, $item['user'], $item['msg'], $item['clock']);
        if ($stmt->execute()) {
            echo "✅ Backfilled event $eid ({$item['name']})\n";
        }
    } else {
        echo "❌ Event not found for {$item['name']} around {$item['clock']}\n";
    }
}
$db->close();
?>
