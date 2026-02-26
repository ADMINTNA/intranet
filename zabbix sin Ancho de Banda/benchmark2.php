<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ZABBIX_URL', 'http://zabbix.tnasolutions.cl:9090/zabbix/api_jsonrpc.php');
define('ZABBIX_USER', 'Admin');
define('ZABBIX_PASS', '1Ngr3s0.,');

function z_req($method, $params, $auth=null) {
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
    echo "[$diff s] $method \n";
    $decoded = json_decode($r,true);
    return $decoded['result']??null;
}

$start = microtime(true);
$login_res = z_req('user.login', ['user'=>ZABBIX_USER,'password'=>ZABBIX_PASS]);
$token = is_array($login_res) ? null : $login_res;
if(!$token) { echo "Login failed\n"; exit; }

echo "\n--- action=stats ---\n";
z_req('host.get', ['output'=>['hostid'],'filter'=>['status'=>0]], $token);
z_req('trigger.get', ['output'=>['triggerid','description','priority','acknowledged'],'selectHosts'=>['hostid'],'filter'=>['value'=>1],'skipDependent'=>true,'monitored'=>true,'active'=>true], $token);

echo "\n--- action=problems ---\n";
z_req('trigger.get', ['output'=>['triggerid','description','priority','lastchange','comments'],'selectHosts'=>['name'],'filter'=>['value'=>1],'skipDependent'=>true,'monitored'=>true,'active'=>true,'sortfield'=>'lastchange','sortorder'=>'DESC'], $token);
z_req('problem.get', ['output'=>['objectid','acknowledged'],'suppressed'=>false], $token);

echo "\n--- action=hosts ---\n";
z_req('host.get', ['output'=>['hostid','name','status'],'selectInterfaces'=>['ip'],'selectGroups'=>['name'],'filter'=>['status'=>0]], $token);

echo "\n--- action=recent_events ---\n";
z_req('event.get', ['output'=>['eventid','r_eventid','name','clock','severity','acknowledged','objectid'],'select_acknowledges'=>['message','action','clock','alias','name','surname'],'source'=>0,'object'=>0,'value'=>1,'sortfield'=>['clock'],'sortorder'=>'DESC','limit'=>50], $token);

echo "\nTotal API logic time: " . round(microtime(true)-$start,3) . "s\n";
?>
