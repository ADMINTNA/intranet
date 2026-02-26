<?php
require_once 'z_session.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

define('ZABBIX_URL', 'http://zabbix.tnasolutions.cl:9090/zabbix/api_jsonrpc.php');
define('ZABBIX_USER', 'Admin');
define('ZABBIX_PASS', '1Ngr3s0.,');

define('Z_LOCAL_DB_HOST', 'localhost');
define('Z_LOCAL_DB_USER', 'tnasolut_app');
define('Z_LOCAL_DB_PASS', '1Ngr3s0.,');
define('Z_LOCAL_DB_NAME', 'tnasolut_app');

// Helper to cross-reference with LOCAL logs for precise user attribution
function enrich_with_local_acks($events) {
    if (!is_array($events) || empty($events)) return [];
    
    $local_acks = [];
    $con = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
    if (!$con->connect_errno) {
        $eids = array_map(function($e) { return $e['eventid']; }, $events);
        $eids_str = implode("','", $eids);
        if ($eids_str) {
            $res = $con->query("SELECT eventid, user_name, message FROM zabbix_acks_logs WHERE eventid IN ('$eids_str')");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $local_acks[$row['eventid']] = $row;
                }
            }
        }
        $con->close();
    }

    foreach ($events as &$f) {
        $eid = $f['eventid'];
        if (isset($local_acks[$eid])) {
            $local = $local_acks[$eid];
            $f['local_user_name'] = $local['user_name'];
            if (empty($f['acknowledges'])) {
                $f['acknowledges'] = [['alias' => $local['user_name'], 'message' => $local['message'], 'clock' => $f['clock']]];
            } else {
                // Modificamos todos los ACKs o el primero para asegurar
                $f['acknowledges'][0]['alias']   = $local['user_name'];
                $f['acknowledges'][0]['name']    = $local['user_name'];
            }
        }
    }
    return $events;
}

function zabbix_request($method, $params, $auth = null) {
    $payload = [
        'jsonrpc' => '2.0',
        'method'  => $method,
        'params'  => $params,
        'id'      => 1,
    ];
    if ($auth) {
        $payload['auth'] = $auth;
    }

    $ch = curl_init(ZABBIX_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("Zabbix API Error: " . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['result'] ?? null;
}

function get_auth_token() {
    return zabbix_request('user.login', [
        'user'     => ZABBIX_USER,
        'password' => ZABBIX_PASS,
    ]);
}

// Extrae el código de servicio desde el nombre del host
// Ej: "Seidor Hendaya (GTD010212519)" → "GTD010212519"
function extract_service_code($name) {
    if (preg_match('/\(\s*([A-Z0-9]{6,20})\s*\)\s*$/', trim($name), $m)) {
        return $m[1];
    }
    return '';
}

// Helper to get base host records with consistent status mapping and metadata
function get_all_hosts_base($token) {
    // 1. Get enabled hosts
    $hosts = zabbix_request('host.get', [
        'output'           => ['hostid', 'name', 'status'],
        'selectGroups'     => ['name'],
        'selectInterfaces' => ['ip'],
        'filter'           => ['status' => 0],
    ], $token);

    if (!is_array($hosts)) return [];

    // 2. Get active triggers for status
    $active_triggers = zabbix_request('trigger.get', [
        'output'        => ['description', 'lastchange', 'priority'],
        'selectHosts'   => ['hostid'],
        'filter'        => ['value' => 1],
        'skipDependent' => true,
        'monitored'     => true,
        'active'        => true,
    ], $token);

    $host_status_map     = []; 
    $host_lastchange_map = []; 
    if (is_array($active_triggers)) {
        foreach ($active_triggers as $t) {
            foreach ($t['hosts'] as $h) {
                $hid  = $h['hostid'];
                $desc = strtolower($t['description']);
                $is_icmp      = strpos($desc, 'unavailable') !== false ||
                                strpos($desc, 'unreachable') !== false ||
                                strpos($desc, 'loss')        !== false;
                $is_link_down = strpos($desc, 'link down')   !== false ||
                                strpos($desc, 'link_down')   !== false;

                $cur = $host_status_map[$hid] ?? 0;
                $new = $is_icmp ? 2 : ($is_link_down ? 3 : 1);

                if ($new > $cur) {
                    $host_status_map[$hid] = $new;
                    $host_lastchange_map[$hid] = (int)$t['lastchange'];
                } elseif ($new == $cur && $new != 0) {
                    $old_lc = $host_lastchange_map[$hid] ?? 0;
                    $new_lc = (int)$t['lastchange'];
                    if ($new_lc > $old_lc) $host_lastchange_map[$hid] = $new_lc;
                }
            }
        }
    }

    // 3. Last recovery events for UP duration
    $all_hostids = array_column($hosts, 'hostid');
    $last_recovery = [];
    if (!empty($all_hostids)) {
        $resolved_events = zabbix_request('event.get', [
            'output'      => ['clock', 'name'],
            'selectHosts' => ['hostid'],
            'hostids'     => $all_hostids,
            'source'      => 0,
            'object'      => 0,
            'value'       => 0,
            'sortfield'   => ['clock'],
            'sortorder'   => 'DESC',
            'limit'       => 5000,
        ], $token);

        if (is_array($resolved_events)) {
            foreach ($resolved_events as $ev) {
                $evName = strtolower($ev['name']);
                if (strpos($evName, 'unavailable') !== false || strpos($evName, 'unreachable') !== false || strpos($evName, 'loss') !== false || strpos($evName, 'down') !== false) {
                    foreach ($ev['hosts'] as $evh) {
                        if (!isset($last_recovery[$evh['hostid']])) $last_recovery[$evh['hostid']] = (int)$ev['clock'];
                    }
                }
            }
        }
    }

    $result = [];
    foreach ($hosts as $h) {
        $hid = $h['hostid'];
        $group = isset($h['groups'][0]['name']) ? $h['groups'][0]['name'] : 'Sin grupo';
        $ip    = isset($h['interfaces'][0]['ip']) ? $h['interfaces'][0]['ip'] : '—';
        $status = $host_status_map[$hid] ?? 0;
        $lc = $host_lastchange_map[$hid] ?? ($last_recovery[$hid] ?? 0);

        $result[$hid] = [
            'hostid'       => $hid,
            'name'         => $h['name'],
            'service_code' => extract_service_code($h['name']),
            'status'       => $status,
            'group'        => $group,
            'ip'           => $ip,
            'last_change'  => $lc,
        ];
    }
    return $result;
}

$action = $_GET['action'] ?? 'stats';
$token  = get_auth_token();

if (!$token) {
    echo json_encode(['error' => 'No se pudo autenticar con Zabbix']);
    exit;
}

// Helper to get consolidated acknowledged events directly from purely LOCAL database
function get_consolidated_acks($token = null) {
    $result = [];
    $con = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
    if (!$con->connect_errno) {
        $con->set_charset("utf8mb4");
        // Get ACKs from the last 24 hours
        $cutoff = time() - 86400;
        $sql = "SELECT * FROM zabbix_acks_logs WHERE clock >= $cutoff ORDER BY clock DESC LIMIT 100";
        $res = $con->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                // Simulate Zabbix event payload but populated completely from local DB
                $result[] = [
                    'eventid'   => $row['eventid'],
                    'name'      => $row['alert_name'] ?: 'Desconocido',
                    'host'      => $row['host_name'] ?: 'Desconocido',
                    'severity'  => (int)$row['severity'],
                    'clock'     => (int)$row['clock'],
                    'fecha'     => $row['fecha'],
                    'hora'      => $row['hora'],
                    'message'   => $row['message'],
                    'acknowledged' => 1,
                    'acknowledges' => [
                        [
                            'alias'   => $row['user_name'],
                            'name'    => $row['user_name'],
                            'message' => $row['message'],
                            'clock'   => $row['clock']
                        ]
                    ],
                    'local_user_name' => $row['user_name']
                ];
            }
        }
        $con->close();
    }
    return $result;
}

switch ($action) {

    case 'stats':
        // Get all enabled hosts
        $hosts = zabbix_request('host.get', [
            'output' => ['hostid'],
            'filter' => ['status' => 0],
        ], $token);

        $total = count($hosts);

        // Get all active triggers with their host IDs
        $active_triggers = zabbix_request('trigger.get', [
            'output'        => ['triggerid', 'description', 'priority', 'acknowledged'],
            'selectHosts'   => ['hostid'],
            'filter'        => ['value' => 1],
            'skipDependent' => true,
            'monitored'     => true,
            'active'        => true,
        ], $token);

        // Build map: hostid => worst status
        // Status: 3=link_down, 2=icmp_down, 1=alerts, 0=ok
        $host_status = [];
        if (is_array($active_triggers)) {
            foreach ($active_triggers as $t) {
                foreach ($t['hosts'] as $h) {
                    $hid  = $h['hostid'];
                    $desc = strtolower($t['description']);
                    $is_icmp      = strpos($desc, 'icmp')        !== false ||
                                    strpos($desc, 'ping')        !== false ||
                                    strpos($desc, 'unavailable') !== false ||
                                    strpos($desc, 'unreachable') !== false;
                    $is_link_down = strpos($desc, 'link down')   !== false ||
                                    strpos($desc, 'link_down')   !== false;
                    $cur = $host_status[$hid] ?? 0;
                    if ($is_icmp)           $new = 2;
                    elseif ($is_link_down)  $new = 3;
                    else                    $new = 1;
                    $host_status[$hid] = max($cur, $new);
                }
            }
        }

        $down = 0; $link_down = 0; $alerts = 0; $ok = 0;
        foreach ($hosts as $h) {
            $s = $host_status[$h['hostid']] ?? 0;
            if ($s === 2)      $down++;
            elseif ($s === 3)  $link_down++;
            elseif ($s === 1)  $alerts++;
            else               $ok++;
        }


        // Severity breakdown from active triggers
        if (is_array($active_triggers)) {
            foreach ($active_triggers as $t) {
                $by_severity[(int)$t['priority']]++;
            }
        }

        // Consolidate ACK count (Active + Resolved 24h)
        $ack_data = get_consolidated_acks($token);
        $acknowledged = count($ack_data);

        echo json_encode([
            'total'       => $total,
            'down'        => $down,
            'link_down'   => $link_down,
            'alerts'      => $alerts,
            'ok'          => $ok,
            'problems'    => count($active_triggers),
            'acknowledged' => $acknowledged,
            'by_severity' => $by_severity,
        ]);
        break;

    case 'problems':
        // Use trigger.get which reliably returns host names
        $triggers = zabbix_request('trigger.get', [
            'output'      => ['triggerid', 'description', 'priority', 'lastchange', 'comments'],
            'selectHosts' => ['name'],
            'filter'      => ['value' => 1],   // 1 = PROBLEM state
            'skipDependent' => true,
            'monitored'   => true,
            'active'      => true,
            'sortfield'   => 'lastchange',
            'sortorder'   => 'DESC',
        ], $token);

        // Also get acknowledged status from problem.get (lightweight)
        $ack_map = [];
        $raw_problems = zabbix_request('problem.get', [
            'output'     => ['objectid', 'acknowledged'],
            'suppressed' => false,
        ], $token);
        if (is_array($raw_problems)) {
            foreach ($raw_problems as $rp) {
                $ack_map[$rp['objectid']] = $rp['acknowledged'] == '1';
            }
        }

        $result = [];
        if (is_array($triggers)) {
            foreach ($triggers as $t) {
                $host_name = isset($t['hosts'][0]['name']) ? $t['hosts'][0]['name'] : '—';
                $svc_code  = extract_service_code($host_name);
                $result[] = [
                    'eventid'      => $t['triggerid'],
                    'host'         => $host_name,
                    'service_code' => $svc_code,
                    'name'         => $t['description'],
                    'severity'     => (int)$t['priority'],
                    'clock'        => (int)$t['lastchange'],
                    'opdata'       => '',
                    'ack'          => $ack_map[$t['triggerid']] ?? false,
                ];
            }
        }
        echo json_encode($result);
        break;

    case 'acknowledged_events':
        echo json_encode(get_consolidated_acks($token));
        break;

    case 'hosts':
        $host_base = get_all_hosts_base($token);
        echo json_encode(array_values($host_base));
        break;


    case 'history':
        $hostid = $_GET['hostid'] ?? 0;
        if (!$hostid) {
            echo json_encode([]);
            break;
        }

        $events = zabbix_request('event.get', [
            'output'      => ['eventid', 'r_eventid', 'name', 'clock', 'value', 'severity', 'acknowledged'],
            'select_acknowledges' => ['message', 'action', 'clock', 'alias', 'name', 'surname'],
            'hostids'     => $hostid,
            'source'      => 0, // EVENT_SOURCE_TRIGGERS
            'object'      => 0, // EVENT_OBJECT_TRIGGER
            'value'       => [0, 1], // OK and PROBLEM
            'sortfield'   => ['clock'],
            'sortorder'   => 'DESC',
            'limit'       => 100,
        ], $token);

        $events = enrich_with_local_acks($events);
        echo json_encode(is_array($events) ? $events : []);
        break;

    case 'acknowledge':
        $eventid = $_POST['eventid'] ?? '';
        $message = $_POST['message'] ?? '';
        $host_name  = $_POST['host_name'] ?? '';
        $alert_name = $_POST['alert_name'] ?? '';
        $severity   = (int)($_POST['severity'] ?? 0);
        
        if (!$eventid) {
            echo json_encode(['error' => 'Missing eventid']);
            break;
        }

        // Zabbix 4.4 event.acknowledge bitmask
        // 2 = Acknowledge
        // 4 = Add message
        $result = zabbix_request('event.acknowledge', [
            'eventids' => $eventid,
            'message'  => $message,
            'action'   => 6 
        ], $token);

        if ($result && isset($result['eventids'])) {
            // Save to local DB for precise attribution
            $db = new mysqli(Z_LOCAL_DB_HOST, Z_LOCAL_DB_USER, Z_LOCAL_DB_PASS, Z_LOCAL_DB_NAME);
            if (!$db->connect_errno) {
                $db->set_charset("utf8mb4");
                // Determine user name from session
        // Insert into local DB
        $local_user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? $_SESSION['sweet_user_id'] ?? 0;
        $local_user_name = $_SESSION['user_full_name'] ?? $_SESSION['cliente'] ?? $_SESSION['usuario'] ?? $_SESSION['name'] ?? 'Técnico';
        $clock = time();
        date_default_timezone_set('America/Santiago');
        $fecha = date('Y-m-d', $clock);
        $hora = date('H:i:s', $clock);
        
        $stmt = $db->prepare("INSERT INTO zabbix_acks_logs (eventid, user_id, user_name, message, fecha, hora, clock, host_name, alert_name, severity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssssssi", $eventid, $local_user_id, $local_user_name, $message, $fecha, $hora, $clock, $host_name, $alert_name, $severity);
        $stmt->execute();
                $db->close();
            }
        }

        echo json_encode(['result' => $result]);
        break;

    case 'recent_events':
        $limit = min(500, max(10, intval($_GET['limit'] ?? 50)));
        // Usamos event.get con value=1 para obtener los últimos problemas (activos o resueltos)
        $problems = zabbix_request('event.get', [
            'output'      => ['eventid', 'r_eventid', 'name', 'clock', 'severity', 'acknowledged', 'objectid'],
            'select_acknowledges' => ['message', 'action', 'clock', 'alias', 'name', 'surname'],
            'source'      => 0, // Triggers
            'object'      => 0, // Triggers
            'value'       => 1, // Sólo eventos PROBLEM (que pueden estar resueltos)
            'sortfield'   => ['clock'],
            'sortorder'   => 'DESC',
            'limit'       => $limit,
        ], $token);

        if (!is_array($problems) || empty($problems)) {
            echo json_encode([]);
            break;
        }

        // 1. Recolectar IDs para búsquedas por lotes
        $r_eventids = [];
        $triggerids = [];
        foreach ($problems as $p) {
            if (isset($p['r_eventid']) && $p['r_eventid'] != 0) {
                $r_eventids[] = $p['r_eventid'];
            }
            if (isset($p['objectid'])) {
                $triggerids[] = $p['objectid'];
            }
        }

        // 2. Obtener marcas de tiempo de recuperación
        $recovery_clocks = [];
        if (!empty($r_eventids)) {
            $r_events = zabbix_request('event.get', [
                'output'   => ['eventid', 'clock'],
                'eventids' => $r_eventids,
            ], $token);
            if (is_array($r_events)) {
                foreach ($r_events as $re) {
                    $recovery_clocks[$re['eventid']] = $re['clock'];
                }
            }
        }

        // 3. Obtener hosts a través de los triggers (necesario en 4.4)
        $trigger_hosts = [];
        if (!empty($triggerids)) {
            $triggers = zabbix_request('trigger.get', [
                'output'      => ['triggerid'],
                'selectHosts' => ['hostid', 'host', 'name'],
                'selectGroups' => ['name'],
                'triggerids'  => array_unique($triggerids),
            ], $token);
            if (is_array($triggers)) {
                foreach ($triggers as $t) {
                    if (isset($t['hosts'][0])) {
                        $h = $t['hosts'][0];
                        $groupName = isset($t['groups'][0]['name']) ? $t['groups'][0]['name'] : 'Sin grupo';
                        $trigger_hosts[$t['triggerid']] = [
                            'name' => $h['name'] ?? $h['host'],
                            'group' => $groupName
                        ];
                    }
                }
            }
        }

        // 4. Mapear resultados
        $result = [];
        foreach ($problems as $p) {
            $host_data = $trigger_hosts[$p['objectid']] ?? ['name' => '—', 'group' => 'Sin grupo'];
            $host_name = $host_data['name'];
            $host_group = $host_data['group'];
            $result[] = [
                'eventid'   => $p['eventid'],
                'r_eventid' => $p['r_eventid'],
                'name'      => $p['name'],
                'clock'     => $p['clock'],
                'r_clock'   => isset($recovery_clocks[$p['r_eventid']]) ? $recovery_clocks[$p['r_eventid']] : null,
                'severity'  => $p['severity'],
                'acknowledged' => $p['acknowledged'],
                'acknowledges' => $p['acknowledges'] ?? [],
                'host'      => $host_name,
                'group'     => $host_group,
                'value'     => ($p['r_eventid'] == 0) ? '1' : '0'
            ];
        }

        $result = enrich_with_local_acks($result);
        echo json_encode($result);
        break;

        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
}
