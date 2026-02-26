<?php
define('ZABBIX_URL', 'http://zabbix.tnasolutions.cl:9090/zabbix/api_jsonrpc.php');
define('ZABBIX_USER', 'Admin');
define('ZABBIX_PASS', '1Ngr3s0.,');

function z_req($method, $params, $auth=null) {
    static $total_time = 0;
    $d = ['jsonrpc'=>'2.0','method'=>$method,'params'=>$params,'id'=>1];
    if($auth)$d['auth']=$auth;
    $s=microtime(true);
    $ch=curl_init(ZABBIX_URL);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json-rpc']);
    curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($d));
    $r=curl_exec($ch);curl_close($ch);
    $e=microtime(true);
    $diff = round($e-$s,3);
    $total_time += $diff;
    echo "[$diff s] $method \n";
    return [json_decode($r,true)['result']??null, $total_time];
}

$start = microtime(true);
list($token, $tt) = z_req('user.login', ['user'=>ZABBIX_USER,'password'=>ZABBIX_PASS]);

echo "\n--- action=hosts ---\n";
z_req('host.get', ['output'=>['hostid']]); // wait, filter missing
z_req('host.get', ['output'=>['hostid','name','status'],'selectInterfaces'=>['ip'],'selectGroups'=>['name']]);

echo "\n--- action=stats ---\n";
z_req('host.get', ['output'=>['hostid'],'filter'=>['status'=>0]], $token);
z_req('trigger.get', ['output'=>['triggerid','description','priority'],'selectHosts'=>['hostid'],'filter'=>['value'=>1],'skipDependent'=>true,'monitored'=>true,'active'=>true], $token);

echo "\n--- action=acknowledged context ---\n";
z_req('event.get', ['output'=>['eventid'],'acknowledged'=>true,'value'=>1,'time_from'=>time()-(86400*7)], $token);

echo "\n--- action=problems ---\n";
z_req('trigger.get', ['output'=>['triggerid'],'selectHosts'=>['name'],'filter'=>['value'=>1],'skipDependent'=>true,'monitored'=>true,'active'=>true], $token);
z_req('problem.get', ['output'=>['objectid'],'suppressed'=>false], $token);

echo "\n--- action=recent_events ---\n";
z_req('event.get', ['output'=>['eventid'],'value'=>1,'sortfield'=>'clock','sortorder'=>'DESC','limit'=>50], $token);

echo "\nTotal API logic time: " . round(microtime(true)-$start,3) . "s\n";