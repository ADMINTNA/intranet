<?php
require_once '../z_session.php';
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
header('X-Content-Type-Options: nosniff');

// ─────────────────────────────────────────────────────────────
// bandwidth/api.php — API de series temporales para gráficos
//
// Acciones:
//   ?action=hosts           → lista de hosts con sus items de BW
//   ?action=graph           → serie temporal de un item
//     &itemid=123           → item a graficar
//     &period=86400         → período en segundos
//   ?action=items           → items de BW de un host
//     &hostid=123
// ─────────────────────────────────────────────────────────────

// ── Cache ─────────────────────────────────────────────────────
$cache_dir = __DIR__ . '/cache';
if (!is_dir($cache_dir)) @mkdir($cache_dir, 0750, true);

function get_cached($key) {
    global $cache_dir;
    $file = $cache_dir . '/bwg_' . md5($key) . '.json';
    if (!file_exists($file)) return null;
    $age = time() - filemtime($file);
    // TTL según el tipo de clave
    if (str_contains($key, 'hosts'))         $ttl = 300;   // 5 min
    elseif (str_contains($key, 'items'))     $ttl = 300;
    elseif (str_contains($key, 'p=3600'))    $ttl = 120;   // 2 min para 1h
    elseif (str_contains($key, 'p=86400'))   $ttl = 300;   // 5 min para 24h
    elseif (str_contains($key, 'p=604800'))  $ttl = 600;   // 10 min para 7d
    else                                      $ttl = 900;   // 15 min para períodos largos
    return $age < $ttl ? file_get_contents($file) : null;
}

function set_cached($key, $data) {
    global $cache_dir;
    $file = $cache_dir . '/bwg_' . md5($key) . '.json';
    @file_put_contents($file, $data);
}

// ─────────────────────────────────────────────────────────────
$action = $_GET['action'] ?? 'hosts';

// Cachear todo excepto peticiones sin parámetros útiles
$cache_key = $action . '_' . http_build_query($_GET);
$cached    = get_cached($cache_key);
if ($cached) {
    header('X-Cache: HIT');
    echo $cached;
    exit;
}
header('X-Cache: MISS');

$token = get_auth_token();
if (!$token) {
    echo json_encode(['error' => 'No se pudo autenticar con Zabbix']);
    exit;
}

// ─────────────────────────────────────────────────────────────
switch ($action) {

    // ── Lista de hosts con sus interfaces y items de BW ──────
    case 'hosts':
        $hosts_raw = zabbix_request('host.get', [
            'output'           => ['hostid', 'name'],
            'selectGroups'     => ['name'],
            'selectInterfaces' => ['ip'],
            'filter'           => ['status' => 0],
        ], $token);

        if (!is_array($hosts_raw)) { echo json_encode([]); break; }

        $host_ids = array_column($hosts_raw, 'hostid');
        $host_map = [];
        foreach ($hosts_raw as $h) {
            $host_map[$h['hostid']] = [
                'hostid'       => $h['hostid'],
                'name'         => $h['name'],
                'service_code' => extract_service_code($h['name']),
                'group'        => $h['groups'][0]['name'] ?? 'Sin grupo',
                'ip'           => $h['interfaces'][0]['ip'] ?? '—',
                'interfaces'   => [],
            ];
        }

        // Traer items de BW en chunks
        $all_items = [];
        foreach (array_chunk($host_ids, 50) as $chunk) {
            $chunk_items = zabbix_request('item.get', [
                'hostids'     => $chunk,
                'output'      => ['itemid', 'hostid', 'name', 'key_', 'units', 'value_type', 'lastvalue'],
                'search'      => ['name' => ['bits', 'traffic', 'bps'], 'key_' => ['net.if', 'bits', 'traffic']],
                'searchByAny' => true,
                'startSearch' => true,
            ], $token);
            if (is_array($chunk_items)) $all_items = array_merge($all_items, $chunk_items);
        }

        foreach ($all_items as $it) {
            $hid  = $it['hostid'];
            if (!isset($host_map[$hid])) continue;
            $key  = $it['key_'];
            $name = $it['name'];
            if (preg_match('/(discard|error|drop|packet|state|status)/i', $key . $name)) continue;

            // Determinar dirección
            if (preg_match('/\.in/i', $key) || preg_match('/(received|incoming|traffic in|bits in)/i', strtolower($name)))
                $dir = 'in';
            elseif (preg_match('/\.out/i', $key) || preg_match('/(sent|outgoing|traffic out|bits out)/i', strtolower($name)))
                $dir = 'out';
            else continue;

            // Extraer nombre de interfaz
            $iface = 'default';
            if (preg_match('/\["?(.*?)"?\]/', $key, $m)) $iface = $m[1];
            if (preg_match('/^if(HC)?(In|Out)(Octets|Bits)\.\d+$/i', $iface) || $iface === 'default') {
                if (preg_match('/Interface\s+(.*?):/i', $name, $m))        $iface = $m[1];
                elseif ($iface === 'default' && preg_match('/:\s*(.*?)$/', $name, $m)) $iface = $m[1];
            }

            if (!isset($host_map[$hid]['interfaces'][$iface])) {
                $host_map[$hid]['interfaces'][$iface] = ['name' => $iface, 'in_itemid' => null, 'out_itemid' => null, 'last_in' => 0, 'last_out' => 0];
            }
            $host_map[$hid]['interfaces'][$iface]["{$dir}_itemid"] = $it['itemid'];
            $host_map[$hid]['interfaces'][$iface]["last_{$dir}"]   = (float)($it['lastvalue'] ?? 0);
        }

        // Limpiar hosts sin interfaces
        foreach ($host_map as $hid => &$h) {
            $h['interfaces'] = array_values($h['interfaces']);
        }
        unset($h);

        $result = array_values(array_filter($host_map, fn($h) => count($h['interfaces']) > 0));
        usort($result, fn($a, $b) => strcmp($a['name'], $b['name']));
        $out = json_encode($result);
        set_cached($cache_key, $out);
        echo $out;
        break;

    // ── Serie temporal de un item ─────────────────────────────
    case 'graph':
        $itemid = (int)($_GET['itemid'] ?? 0);
        $period = (int)($_GET['period'] ?? 86400);

        if (!$itemid) { echo json_encode(['error' => 'itemid requerido']); break; }

        // Obtener info del item (value_type)
        $item_info = zabbix_request('item.get', [
            'itemids' => [$itemid],
            'output'  => ['itemid', 'name', 'value_type', 'units'],
        ], $token);

        $value_type = isset($item_info[0]) ? (int)$item_info[0]['value_type'] : 0;
        $time_from  = time() - $period;
        $now        = time();

        // Decidir fuente y resolución según período
        // < 24h  → history.get (puntos crudos, máx 2000)
        // >= 24h → trend.get   (un punto por hora, min/avg/max)
        $use_trends    = ($period >= 86400);
        $series        = [];

        if ($use_trends) {
            // trend.get devuelve clock, value_min, value_avg, value_max
            $raw = zabbix_request('trend.get', [
                'itemids'   => [$itemid],
                'time_from' => $time_from,
                'time_till' => $now,
                'output'    => ['clock', 'value_min', 'value_avg', 'value_max'],
            ], $token);

            if (is_array($raw)) {
                // Para períodos muy largos, reducir puntos agrupando por día o por Nh
                $max_points = 500;
                $step = max(1, (int)ceil(count($raw) / $max_points));
                for ($i = 0; $i < count($raw); $i += $step) {
                    $slice = array_slice($raw, $i, $step);
                    $avg_clock = (int)round(array_sum(array_column($slice, 'clock')) / count($slice));
                    $avg_val   = array_sum(array_column($slice, 'value_avg')) / count($slice);
                    $min_val   = min(array_column($slice, 'value_min'));
                    $max_val   = max(array_column($slice, 'value_max'));
                    $series[]  = [
                        'ts'  => $avg_clock * 1000, // ms para Chart.js
                        'avg' => round($avg_val),
                        'min' => round($min_val),
                        'max' => round($max_val),
                    ];
                }
            }
        } else {
            // history.get — datos crudos, limitar a 2000 puntos
            $raw = zabbix_request('history.get', [
                'itemids'   => [$itemid],
                'time_from' => $time_from,
                'time_till' => $now,
                'history'   => $value_type,
                'output'    => ['clock', 'value'],
                'sortfield' => ['clock'],
                'sortorder' => 'ASC',
                'limit'     => 2000,
            ], $token);

            if (is_array($raw)) {
                // Muestrear si hay demasiados puntos
                $max_points = 500;
                $step = max(1, (int)ceil(count($raw) / $max_points));
                for ($i = 0; $i < count($raw); $i += $step) {
                    $slice = array_slice($raw, $i, $step);
                    $avg_clock = (int)round(array_sum(array_column($slice, 'clock')) / count($slice));
                    $avg_val   = array_sum(array_column($slice, 'value')) / count($slice);
                    $series[]  = [
                        'ts'  => $avg_clock * 1000,
                        'avg' => round($avg_val),
                        'min' => round($avg_val),
                        'max' => round($avg_val),
                    ];
                }
            }
        }

        $out = json_encode([
            'itemid'     => $itemid,
            'period'     => $period,
            'use_trends' => $use_trends,
            'points'     => count($series),
            'series'     => $series,
        ]);
        set_cached($cache_key, $out);
        echo $out;
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
}
