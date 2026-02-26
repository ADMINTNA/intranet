<?php
require_once 'z_session.php';
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
header('Access-Control-Allow-Methods: GET, POST');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

// CORREGIDO: SQL Injection → prepared statements con bind_param
function enrich_with_local_acks($events) {
    if (!is_array($events) || empty($events)) return [];

    $local_acks = [];
    $con = get_local_db();
    if ($con) {
        $eids = array_filter(array_map(function($e) { return $e['eventid']; }, $events));

        if (!empty($eids)) {
            $placeholders = implode(',', array_fill(0, count($eids), '?'));
            $stmt = $con->prepare(
                "SELECT eventid, user_name, message FROM zabbix_acks_logs WHERE eventid IN ($placeholders)"
            );
            if ($stmt) {
                $stmt->bind_param(str_repeat('s', count($eids)), ...$eids);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $local_acks[$row['eventid']] = $row;
                }
                $stmt->close();
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
                $f['acknowledges'][0]['alias'] = $local['user_name'];
                $f['acknowledges'][0]['name']  = $local['user_name'];
            }
        }
    }
    unset($f);
    return $events;
}

function get_all_hosts_base($token) {
    $hosts = zabbix_request('host.get', [
        'output'           => ['hostid', 'name', 'status'],
        'selectGroups'     => ['name'],
        'selectInterfaces' => ['ip'],
        'filter'           => ['status' => 0],
    ], $token);

    if (!is_array($hosts)) return [];

    $active_triggers = zabbix_request('trigger.get', [
        'output'        => ['description', 'lastchange', 'priority'],
        'selectHosts'   => ['hostid'],
        'filter'        => ['value' => 1],
        'skipDependent' => true,
        'monitored'     => true,
        'active'        => true,
    ], $token);

    $host_status_map = []; $host_lastchange_map = [];
    if (is_array($active_triggers)) {
        foreach ($active_triggers as $t) {
            foreach ($t['hosts'] as $h) {
                $hid  = $h['hostid'];
                $desc = strtolower($t['description']);
                $is_icmp      = strpos($desc, 'unavailable') !== false || strpos($desc, 'unreachable') !== false || strpos($desc, 'loss') !== false;
                $is_link_down = strpos($desc, 'link down')   !== false || strpos($desc, 'link_down')   !== false;
                $cur = $host_status_map[$hid] ?? 0;
                $new = $is_icmp ? 2 : ($is_link_down ? 3 : 1);
                if ($new > $cur) {
                    $host_status_map[$hid] = $new;
                    $host_lastchange_map[$hid] = (int)$t['lastchange'];
                } elseif ($new === $cur && $new !== 0) {
                    $new_lc = (int)$t['lastchange'];
                    if ($new_lc > ($host_lastchange_map[$hid] ?? 0)) $host_lastchange_map[$hid] = $new_lc;
                }
            }
        }
    }

    $all_hostids = array_column($hosts, 'hostid');
    $last_recovery = [];
    if (!empty($all_hostids)) {
        $resolved_events = zabbix_request('event.get', [
            'output' => ['clock', 'name'], 'selectHosts' => ['hostid'],
            'hostids' => $all_hostids, 'source' => 0, 'object' => 0, 'value' => 0,
            'sortfield' => ['clock'], 'sortorder' => 'DESC', 'limit' => 5000,
        ], $token);
        if (is_array($resolved_events)) {
            foreach ($resolved_events as $ev) {
                $evName = strtolower($ev['name']);
                if (strpos($evName,'unavailable')!==false || strpos($evName,'unreachable')!==false || strpos($evName,'loss')!==false || strpos($evName,'down')!==false) {
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
        $result[$hid] = [
            'hostid'       => $hid,
            'name'         => $h['name'],
            'service_code' => extract_service_code($h['name']),
            'status'       => $host_status_map[$hid] ?? 0,
            'group'        => $h['groups'][0]['name']   ?? 'Sin grupo',
            'ip'           => $h['interfaces'][0]['ip'] ?? '—',
            'last_change'  => $host_lastchange_map[$hid] ?? ($last_recovery[$hid] ?? 0),
        ];
    }
    return $result;
}

function get_consolidated_acks() {
    $result = [];
    $con = get_local_db();
    if (!$con) return $result;

    $cutoff = time() - 86400;
    $stmt = $con->prepare('SELECT * FROM zabbix_acks_logs WHERE clock >= ? ORDER BY clock DESC LIMIT 100');
    if ($stmt) {
        $stmt->bind_param('i', $cutoff);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $result[] = [
                'eventid'         => $row['eventid'],
                'name'            => $row['alert_name'] ?: 'Desconocido',
                'host'            => $row['host_name']  ?: 'Desconocido',
                'severity'        => (int)$row['severity'],
                'clock'           => (int)$row['clock'],
                'fecha'           => $row['fecha'],
                'hora'            => $row['hora'],
                'message'         => $row['message'],
                'acknowledged'    => 1,
                'acknowledges'    => [['alias' => $row['user_name'], 'name' => $row['user_name'], 'message' => $row['message'], 'clock' => $row['clock']]],
                'local_user_name' => $row['user_name'],
            ];
        }
        $stmt->close();
    }
    $con->close();
    return $result;
}

// ── Punto de entrada ──────────────────────────────────────────
$action = $_GET['action'] ?? 'stats';
$token  = get_auth_token();

if (!$token) {
    echo json_encode(['error' => 'No se pudo autenticar con Zabbix']);
    exit;
}

switch ($action) {

    case 'stats':
        $hosts = zabbix_request('host.get', ['output' => ['hostid'], 'filter' => ['status' => 0]], $token);
        $total = is_array($hosts) ? count($hosts) : 0;

        $active_triggers = zabbix_request('trigger.get', [
            'output' => ['triggerid', 'description', 'priority', 'acknowledged'],
            'selectHosts' => ['hostid'], 'filter' => ['value' => 1],
            'skipDependent' => true, 'monitored' => true, 'active' => true,
        ], $token);

        $host_status = [];
        $by_severity = []; // CORREGIDO: inicializado antes del loop

        if (is_array($active_triggers)) {
            foreach ($active_triggers as $t) {
                $by_severity[(int)$t['priority']] = ($by_severity[(int)$t['priority']] ?? 0) + 1;
                foreach ($t['hosts'] as $h) {
                    $hid  = $h['hostid'];
                    $desc = strtolower($t['description']);
                    $is_icmp      = strpos($desc,'icmp')!==false || strpos($desc,'ping')!==false || strpos($desc,'unavailable')!==false || strpos($desc,'unreachable')!==false;
                    $is_link_down = strpos($desc,'link down')!==false || strpos($desc,'link_down')!==false;
                    $cur = $host_status[$hid] ?? 0;
                    if ($is_icmp)          $new = 2;
                    elseif ($is_link_down) $new = 3;
                    else                   $new = 1;
                    $host_status[$hid] = max($cur, $new);
                }
            }
        }

        $down = 0; $link_down = 0; $alerts = 0; $ok = 0;
        if (is_array($hosts)) {
            foreach ($hosts as $h) {
                $s = $host_status[$h['hostid']] ?? 0;
                if ($s === 2) $down++; elseif ($s === 3) $link_down++; elseif ($s === 1) $alerts++; else $ok++;
            }
        }

        echo json_encode([
            'total' => $total, 'down' => $down, 'link_down' => $link_down,
            'alerts' => $alerts, 'ok' => $ok,
            'problems' => is_array($active_triggers) ? count($active_triggers) : 0,
            'acknowledged' => count(get_consolidated_acks()),
            'by_severity' => $by_severity,
        ]);
        break;

    case 'problems':
        $triggers = zabbix_request('trigger.get', [
            'output' => ['triggerid','description','priority','lastchange','comments'],
            'selectHosts' => ['name'], 'filter' => ['value' => 1],
            'skipDependent' => true, 'monitored' => true, 'active' => true,
            'sortfield' => 'lastchange', 'sortorder' => 'DESC',
        ], $token);

        $ack_map = [];
        $raw_problems = zabbix_request('problem.get', ['output' => ['objectid','acknowledged'], 'suppressed' => false], $token);
        if (is_array($raw_problems)) {
            foreach ($raw_problems as $rp) $ack_map[$rp['objectid']] = $rp['acknowledged'] == '1';
        }

        $result = [];
        if (is_array($triggers)) {
            foreach ($triggers as $t) {
                $host_name = $t['hosts'][0]['name'] ?? '—';
                $result[] = [
                    'eventid' => $t['triggerid'], 'host' => $host_name,
                    'service_code' => extract_service_code($host_name),
                    'name' => $t['description'], 'severity' => (int)$t['priority'],
                    'clock' => (int)$t['lastchange'], 'opdata' => '',
                    'ack' => $ack_map[$t['triggerid']] ?? false,
                ];
            }
        }
        echo json_encode($result);
        break;

    case 'acknowledged_events':
        echo json_encode(get_consolidated_acks());
        break;

    case 'hosts':
        echo json_encode(array_values(get_all_hosts_base($token)));
        break;

    // OPTIMIZACIÓN: acción combinada que devuelve stats + hosts en una sola llamada.
    // El frontend la usa en loadAll() para evitar dos round-trips al servidor.
    case 'dashboard':
        $host_base = get_all_hosts_base($token); // tiene hosts + trigger status map

        // Calcular KPIs desde el mapa ya construido
        $total      = count($host_base);
        $down = 0; $link_down = 0; $alerts = 0; $ok = 0;
        $by_severity = [];

        // Traer triggers activos para severidad (reutilizamos datos de get_all_hosts_base)
        $active_triggers = zabbix_request('trigger.get', [
            'output'        => ['triggerid', 'description', 'priority'],
            'selectHosts'   => ['hostid'],
            'filter'        => ['value' => 1],
            'skipDependent' => true, 'monitored' => true, 'active' => true,
        ], $token);

        $host_status_kpi = [];
        if (is_array($active_triggers)) {
            foreach ($active_triggers as $t) {
                $by_severity[(int)$t['priority']] = ($by_severity[(int)$t['priority']] ?? 0) + 1;
                foreach ($t['hosts'] as $h) {
                    $hid  = $h['hostid'];
                    $desc = strtolower($t['description']);
                    $is_icmp      = strpos($desc,'icmp')!==false || strpos($desc,'ping')!==false || strpos($desc,'unavailable')!==false || strpos($desc,'unreachable')!==false;
                    $is_link_down = strpos($desc,'link down')!==false || strpos($desc,'link_down')!==false;
                    $cur = $host_status_kpi[$hid] ?? 0;
                    if ($is_icmp)          $new = 2;
                    elseif ($is_link_down) $new = 3;
                    else                   $new = 1;
                    $host_status_kpi[$hid] = max($cur, $new);
                }
            }
        }

        foreach ($host_base as $h) {
            $s = $h['status'];
            if ($s === 2) $down++; elseif ($s === 3) $link_down++; elseif ($s === 1) $alerts++; else $ok++;
        }

        echo json_encode([
            'stats' => [
                'total'        => $total,
                'down'         => $down,
                'link_down'    => $link_down,
                'alerts'       => $alerts,
                'ok'           => $ok,
                'problems'     => is_array($active_triggers) ? count($active_triggers) : 0,
                'acknowledged' => count(get_consolidated_acks()),
                'by_severity'  => $by_severity,
            ],
            'hosts' => array_values($host_base),
        ]);
        break;

    case 'history':
        $hostid = (int)($_GET['hostid'] ?? 0);
        if (!$hostid) { echo json_encode([]); break; }

        $events = zabbix_request('event.get', [
            'output' => ['eventid','r_eventid','name','clock','value','severity','acknowledged'],
            'select_acknowledges' => ['message','action','clock','alias','name','surname'],
            'hostids' => $hostid, 'source' => 0, 'object' => 0, 'value' => [0,1],
            'sortfield' => ['clock'], 'sortorder' => 'DESC', 'limit' => 100,
        ], $token);

        echo json_encode(enrich_with_local_acks($events ?: []));
        break;

    case 'acknowledge':
        $eventid    = $_POST['eventid']    ?? '';
        $message    = $_POST['message']    ?? '';
        $host_name  = $_POST['host_name']  ?? '';
        $alert_name = $_POST['alert_name'] ?? '';
        $severity   = (int)($_POST['severity'] ?? 0);

        if (!$eventid) { echo json_encode(['error' => 'Missing eventid']); break; }

        $result = zabbix_request('event.acknowledge', [
            'eventids' => $eventid, 'message' => $message, 'action' => 6,
        ], $token);

        if ($result && isset($result['eventids'])) {
            $db = get_local_db();
            if ($db) {
                $local_user_id   = $_SESSION['user_id']        ?? $_SESSION['id']      ?? $_SESSION['sweet_user_id'] ?? 0;
                $local_user_name = $_SESSION['user_full_name'] ?? $_SESSION['cliente'] ?? $_SESSION['usuario']       ?? $_SESSION['name'] ?? 'Técnico';
                date_default_timezone_set('America/Santiago');
                $clock = time();
                $fecha = date('Y-m-d', $clock);
                $hora  = date('H:i:s', $clock);

                $stmt = $db->prepare(
                    'INSERT INTO zabbix_acks_logs (eventid,user_id,user_name,message,fecha,hora,clock,host_name,alert_name,severity) VALUES (?,?,?,?,?,?,?,?,?,?)'
                );
                $stmt->bind_param('sisssssssi', $eventid,$local_user_id,$local_user_name,$message,$fecha,$hora,$clock,$host_name,$alert_name,$severity);
                $stmt->execute();
                $stmt->close();
                $db->close();
            }
        }
        echo json_encode(['result' => $result]);
        break;

    case 'recent_events':
        $limit = min(500, max(10, intval($_GET['limit'] ?? 50)));

        $problems = zabbix_request('event.get', [
            'output' => ['eventid','r_eventid','name','clock','severity','acknowledged','objectid'],
            'select_acknowledges' => ['message','action','clock','alias','name','surname'],
            'source' => 0, 'object' => 0, 'value' => 1,
            'sortfield' => ['clock'], 'sortorder' => 'DESC', 'limit' => $limit,
        ], $token);

        if (!is_array($problems) || empty($problems)) { echo json_encode([]); break; }

        $r_eventids = []; $triggerids = [];
        foreach ($problems as $p) {
            if (!empty($p['r_eventid']) && $p['r_eventid'] != 0) $r_eventids[] = $p['r_eventid'];
            if (!empty($p['objectid'])) $triggerids[] = $p['objectid'];
        }

        $recovery_clocks = [];
        if (!empty($r_eventids)) {
            $r_events = zabbix_request('event.get', ['output' => ['eventid','clock'], 'eventids' => $r_eventids], $token);
            if (is_array($r_events)) foreach ($r_events as $re) $recovery_clocks[$re['eventid']] = $re['clock'];
        }

        $trigger_hosts = [];
        if (!empty($triggerids)) {
            $triggers = zabbix_request('trigger.get', [
                'output' => ['triggerid'], 'selectHosts' => ['hostid','host','name'],
                'selectGroups' => ['name'], 'triggerids' => array_unique($triggerids),
            ], $token);
            if (is_array($triggers)) {
                foreach ($triggers as $t) {
                    if (!empty($t['hosts'][0])) {
                        $h = $t['hosts'][0];
                        $trigger_hosts[$t['triggerid']] = ['name' => $h['name'] ?? $h['host'], 'group' => $t['groups'][0]['name'] ?? 'Sin grupo'];
                    }
                }
            }
        }

        $result = [];
        foreach ($problems as $p) {
            $hd = $trigger_hosts[$p['objectid']] ?? ['name' => '—', 'group' => 'Sin grupo'];
            $result[] = [
                'eventid' => $p['eventid'], 'r_eventid' => $p['r_eventid'],
                'name' => $p['name'], 'clock' => $p['clock'],
                'r_clock' => $recovery_clocks[$p['r_eventid']] ?? null,
                'severity' => $p['severity'], 'acknowledged' => $p['acknowledged'],
                'acknowledges' => $p['acknowledges'] ?? [],
                'host' => $hd['name'], 'group' => $hd['group'],
                'value' => ($p['r_eventid'] == 0) ? '1' : '0',
            ];
        }

        echo json_encode(enrich_with_local_acks($result));
        break;


    // ─────────────────────────────────────────────────────────────
    // SLA / DISPONIBILIDAD HISTÓRICA
    // Calcula % uptime por host en los últimos N días usando la BD
    // local (zabbix_acks_logs) + Zabbix event.get
    // Params: ?days=30 (default 30)
    // ─────────────────────────────────────────────────────────────
    case 'sla':
        $days    = min(90, max(1, (int)($_GET['days'] ?? 30)));
        $from_ts = time() - ($days * 86400);

        // 1. Traer todos los hosts habilitados
        $hosts_raw = zabbix_request('host.get', [
            'output'       => ['hostid', 'name'],
            'selectGroups' => ['name'],
            'filter'       => ['status' => 0],
        ], $token);

        if (!is_array($hosts_raw)) { echo json_encode([]); break; }

        $host_map = [];
        foreach ($hosts_raw as $h) {
            $host_map[$h['hostid']] = [
                'hostid'       => $h['hostid'],
                'name'         => $h['name'],
                'service_code' => extract_service_code($h['name']),
                'group'        => $h['groups'][0]['name'] ?? 'Sin grupo',
                'downtime_sec' => 0,
                'incidents'    => 0,
                'mttr_sec'     => 0,
            ];
        }

        // 2. Traer eventos PROBLEM con su recovery en el periodo
        $events = zabbix_request('event.get', [
            'output'              => ['eventid', 'r_eventid', 'clock', 'name'],
            'selectHosts'         => ['hostid'],
            'select_acknowledges' => ['clock'],
            'hostids'             => array_keys($host_map),
            'source'              => 0,
            'object'              => 0,
            'value'               => 1,
            'time_from'           => $from_ts,
            'sortfield'           => ['clock'],
            'sortorder'           => 'ASC',
            'limit'               => 10000,
        ], $token);

        // 3. Calcular downtime acumulado por host
        $r_ids = [];
        if (is_array($events)) {
            foreach ($events as $ev) {
                if (!empty($ev['r_eventid']) && $ev['r_eventid'] != 0) {
                    $r_ids[] = $ev['r_eventid'];
                }
            }
        }

        $recovery_ts = [];
        if (!empty($r_ids)) {
            $r_evs = zabbix_request('event.get', [
                'output'   => ['eventid', 'clock'],
                'eventids' => array_unique($r_ids),
            ], $token);
            if (is_array($r_evs)) {
                foreach ($r_evs as $re) $recovery_ts[$re['eventid']] = (int)$re['clock'];
            }
        }

        $period_sec = $days * 86400;
        $now        = time();

        if (is_array($events)) {
            foreach ($events as $ev) {
                foreach ($ev['hosts'] as $evh) {
                    $hid = $evh['hostid'];
                    if (!isset($host_map[$hid])) continue;

                    $start = max((int)$ev['clock'], $from_ts);
                    $end   = isset($recovery_ts[$ev['r_eventid']]) ? (int)$recovery_ts[$ev['r_eventid']] : $now;
                    $dur   = max(0, $end - $start);

                    $host_map[$hid]['downtime_sec'] += $dur;
                    $host_map[$hid]['incidents']++;
                }
            }
        }

        // 4. Calcular SLA% y MTTR
        $total_sec = $period_sec;
        foreach ($host_map as $hid => &$hdata) {
            $down  = min($hdata['downtime_sec'], $total_sec);
            $up    = $total_sec - $down;
            $hdata['sla_pct']     = round(($up / $total_sec) * 100, 3);
            $hdata['downtime_h']  = round($down / 3600, 2);
            $hdata['mttr_sec']    = $hdata['incidents'] > 0
                                    ? round($hdata['downtime_sec'] / $hdata['incidents'])
                                    : 0;
        }
        unset($hdata);

        // Ordenar por SLA ascendente (peores primero)
        usort($host_map, function($a, $b) { return $a['sla_pct'] <=> $b['sla_pct']; });
        echo json_encode(array_values($host_map));
        break;

    // ─────────────────────────────────────────────────────────────
    // RESUMEN DE TURNO
    // Métricas de respuesta por técnico en las últimas N horas.
    // Params: ?hours=24 (default 24)
    // ─────────────────────────────────────────────────────────────
    case 'shift_report':
        $hours   = min(168, max(1, (int)($_GET['hours'] ?? 24)));
        $from_ts = time() - ($hours * 3600);

        $con = get_local_db();
        if (!$con) { echo json_encode(['error' => 'DB unavailable']); break; }

        // ACKs por técnico con tiempo de respuesta (clock del evento vs clock del ACK)
        $stmt = $con->prepare(
            "SELECT user_name, COUNT(*) as total_acks,
                    AVG(clock) as avg_ack_ts,
                    MIN(clock) as first_ack,
                    MAX(clock) as last_ack,
                    GROUP_CONCAT(DISTINCT severity ORDER BY severity) as severities
             FROM zabbix_acks_logs
             WHERE clock >= ?
             GROUP BY user_name
             ORDER BY total_acks DESC"
        );
        $stmt->bind_param('i', $from_ts);
        $stmt->execute();
        $res = $stmt->get_result();
        $by_tech = [];
        while ($row = $res->fetch_assoc()) {
            $by_tech[] = [
                'user'       => $row['user_name'],
                'acks'       => (int)$row['total_acks'],
                'first_ack'  => (int)$row['first_ack'],
                'last_ack'   => (int)$row['last_ack'],
                'severities' => array_map('intval', explode(',', $row['severities'] ?? '')),
            ];
        }
        $stmt->close();

        // Total de eventos en el periodo
        $stmt2 = $con->prepare(
            "SELECT COUNT(*) as total, COUNT(DISTINCT host_name) as hosts_affected,
                    COUNT(DISTINCT user_name) as technicians
             FROM zabbix_acks_logs WHERE clock >= ?"
        );
        $stmt2->bind_param('i', $from_ts);
        $stmt2->execute();
        $summary = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        // Severidad más frecuente
        $stmt3 = $con->prepare(
            "SELECT severity, COUNT(*) as cnt FROM zabbix_acks_logs
             WHERE clock >= ? GROUP BY severity ORDER BY cnt DESC LIMIT 5"
        );
        $stmt3->bind_param('i', $from_ts);
        $stmt3->execute();
        $res3 = $stmt3->get_result();
        $by_severity = [];
        while ($row = $res3->fetch_assoc()) {
            $by_severity[(int)$row['severity']] = (int)$row['cnt'];
        }
        $stmt3->close();

        // Eventos recientes (últimos 20)
        $stmt4 = $con->prepare(
            "SELECT eventid, host_name, alert_name, severity, user_name, message, fecha, hora, clock
             FROM zabbix_acks_logs WHERE clock >= ?
             ORDER BY clock DESC LIMIT 20"
        );
        $stmt4->bind_param('i', $from_ts);
        $stmt4->execute();
        $res4 = $stmt4->get_result();
        $recent = [];
        while ($row = $res4->fetch_assoc()) $recent[] = $row;
        $stmt4->close();

        $con->close();

        echo json_encode([
            'hours'       => $hours,
            'summary'     => $summary,
            'by_tech'     => $by_tech,
            'by_severity' => $by_severity,
            'recent'      => $recent,
        ]);
        break;

    // ─────────────────────────────────────────────────────────────
    // LOG CON FILTRO DE FECHAS
    // Params: ?date_from=YYYY-MM-DD&date_to=YYYY-MM-DD&limit=100
    // ─────────────────────────────────────────────────────────────
    case 'event_log_filter':
        $date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-7 days'));
        $date_to   = $_GET['date_to']   ?? date('Y-m-d');
        $limit     = min(500, max(10, (int)($_GET['limit'] ?? 100)));
        $user      = $_GET['user']      ?? '';
        $severity  = isset($_GET['severity']) ? (int)$_GET['severity'] : -1;

        // Validar fechas
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) $date_from = date('Y-m-d', strtotime('-7 days'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to))   $date_to   = date('Y-m-d');

        $con = get_local_db();
        if (!$con) { echo json_encode(['error' => 'DB unavailable']); break; }

        $where  = ['fecha BETWEEN ? AND ?'];
        $types  = 'ss';
        $params = [$date_from, $date_to];

        if ($user !== '') {
            $where[]  = 'user_name = ?';
            $types   .= 's';
            $params[] = $user;
        }
        if ($severity >= 0) {
            $where[]  = 'severity = ?';
            $types   .= 'i';
            $params[] = $severity;
        }

        $sql  = 'SELECT * FROM zabbix_acks_logs WHERE ' . implode(' AND ', $where)
              . ' ORDER BY clock DESC LIMIT ?';
        $types  .= 'i';
        $params[] = $limit;

        $stmt = $con->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res    = $stmt->get_result();
        $result = [];
        while ($row = $res->fetch_assoc()) $result[] = $row;
        $stmt->close();

        // Obtener lista de técnicos disponibles para el filtro
        $users_stmt = $con->prepare(
            'SELECT DISTINCT user_name FROM zabbix_acks_logs ORDER BY user_name'
        );
        $users_stmt->execute();
        $users_res = $users_stmt->get_result();
        $users = [];
        while ($u = $users_res->fetch_assoc()) $users[] = $u['user_name'];
        $users_stmt->close();

        $con->close();
        echo json_encode(['events' => $result, 'users' => $users]);
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
}
