<?php
require_once '../z_session.php';
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

$period = isset($_GET['period']) ? (int)$_GET['period'] : 3600;

// ==========================================
// CACHE IMPLEMENTATION (120 seconds TTL)
// ==========================================
$cache_dir = __DIR__ . '/cache';
if (!is_dir($cache_dir)) {
    @mkdir($cache_dir, 0755, true);
}

$cache_file = $cache_dir . '/bw_cache_' . $period . '.json';

// TTL dinámico según el período consultado:
// periodos cortos (1h) → 2 min, periodos largos (24h+) → 10 min, 7d → 15 min
if ($period <= 3600)        $cache_ttl = 120;
elseif ($period <= 86400)   $cache_ttl = 300;
elseif ($period <= 604800)  $cache_ttl = 600;
else                        $cache_ttl = 900;

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
    // Serve from cache — incluimos header para que el cliente sepa que es cache
    header('X-Cache: HIT');
    echo file_get_contents($cache_file);
    exit;
}
header('X-Cache: MISS');
// ==========================================

$token = get_auth_token();
if (!$token) {
    echo json_encode(['error' => 'No se pudo autenticar con Zabbix']);
    exit;
}

// 1. Get enabled hosts
$hosts_raw = zabbix_request('host.get', [
    'output'           => ['hostid', 'name', 'status'],
    'selectGroups'     => ['name'],
    'selectInterfaces' => ['ip'],
    'filter'           => ['status' => 0],
], $token);

if (!is_array($hosts_raw)) {
    echo json_encode([]);
    exit;
}

$hosts = [];
$host_ids = [];
foreach ($hosts_raw as $h) {
    $hid = $h['hostid'];
    $host_ids[] = $hid;
    $hosts[$hid] = [
        'hostid'       => $hid,
        'name'         => $h['name'],
        'service_code' => extract_service_code($h['name']),
        'group'        => $h['groups'][0]['name'] ?? 'Sin grupo',
        'ip'           => $h['interfaces'][0]['ip'] ?? '—',
        'interfaces'   => []
    ];
}

// 2. Get bandwidth items
$items = [];
$chunks = array_chunk($host_ids, 50);
foreach ($chunks as $chunk) {
    $chunk_items = zabbix_request('item.get', [
        'hostids'     => $chunk,
        'output'      => ['itemid', 'hostid', 'name', 'key_', 'lastvalue', 'units', 'value_type'],
        'search'      => [
            'name' => ['bits', 'traffic', 'bps'],
            'key_' => ['net.if', 'bits', 'traffic']
        ],
        'searchByAny' => true,
        'startSearch' => true,
    ], $token);
    if (is_array($chunk_items)) {
        $items = array_merge($items, $chunk_items);
    }
}

foreach ($items as &$it) {
    $hid = $it['hostid'];
    if (!isset($hosts[$hid])) continue;

    $key = $it['key_'];
    $name = $it['name'];
    if (preg_match('/(discard|error|drop|packet|state|status)/i', $key . $name)) continue;

    $type = '';
    if (preg_match('/\.in/i', $key) || preg_match('/(received|incoming|traffic in|bits in)/i', strtolower($name))) {
        $type = 'in';
    } elseif (preg_match('/\.out/i', $key) || preg_match('/(sent|outgoing|traffic out|bits out)/i', strtolower($name))) {
        $type = 'out';
    } elseif (preg_match('/\.speed/i', $key) || preg_match('/speed/i', strtolower($name))) {
        $type = 'speed';
    }

    $it['type'] = $type; // Store it back in the item array
    if (!$type) continue;

    $iface = 'default';
    if (preg_match('/\["?(.*?)"?\]/', $key, $m)) {
        $iface = $m[1];
    }
    
    if (preg_match('/^if(HC)?(In|Out)(Octets|Bits)\.\d+$/i', $iface) || $iface === 'default') {
        if (preg_match('/Interface\s+(.*?):/i', $name, $m)) {
            $iface = $m[1];
        } elseif ($iface === 'default' && preg_match('/:\s*(.*?)$/', $name, $m)) {
            $iface = $m[1];
        }
    }

    if (!isset($hosts[$hid]['interfaces'][$iface])) {
        $hosts[$hid]['interfaces'][$iface] = ['name' => $iface, 'in' => 0, 'out' => 0, 'speed' => 0];
    }
    $hosts[$hid]['interfaces'][$iface][$type] = (float)$it['lastvalue'];
}
unset($it);

foreach ($hosts as $hid => $h) {
    $hosts[$hid]['interfaces'] = array_values($h['interfaces']);
}

// 3. New: Fetch Accumulated Data (Last Hour, 24h, 7d, etc.)
$period = isset($_GET['period']) ? (int)$_GET['period'] : 3600;
$item_ids = [];
foreach ($items as $it) {
    if (in_array($it['type'] ?? '', ['in', 'out'])) {
        $item_ids[] = $it['itemid'];
    }
}

if (!empty($item_ids)) {
    $time_from = time() - $period;
    $history_chunks = array_chunk($item_ids, 100);
    $history_data_all = [];
    
    // Use trends for periods of 24 hours or more to avoid heavy DB load and ensure data availability
    $use_trends = ($period >= 86400);

    foreach ($history_chunks as $chunk) {
        if ($use_trends) {
            $chunk_stats = zabbix_request('trend.get', [
                'itemids'   => $chunk,
                'time_from' => $time_from,
                'output'    => ['itemid', 'value_avg']
            ], $token);
        } else {
            // Group chunk by value_type to make efficient history.get calls
            $chunk_by_type = [];
            foreach ($items as $it) {
                if (in_array($it['itemid'], $chunk)) {
                    $type = $it['value_type'] ?? 3;
                    $chunk_by_type[$type][] = $it['itemid'];
                }
            }

            $chunk_stats = [];
            foreach ($chunk_by_type as $vtype => $ids) {
                $res = zabbix_request('history.get', [
                    'itemids'   => $ids,
                    'time_from' => $time_from,
                    'history'   => (int)$vtype,
                    'output'    => ['itemid', 'value'],
                    'limit'     => 10000
                ], $token);
                if (is_array($res)) $chunk_stats = array_merge($chunk_stats, $res);
            }
        }

        if (is_array($chunk_stats)) {
            foreach ($chunk_stats as $stat) {
                $iid = $stat['itemid'];
                if (!isset($history_data_all[$iid])) $history_data_all[$iid] = [];
                // For history.get, we might get multiple points, for trend.get we get one per hour.
                $val = $use_trends ? (float)($stat['value_avg'] ?? 0) : (float)($stat['value'] ?? 0);
                $history_data_all[$iid][] = $val;
            }
        }
    }

    // Assign Averages and calculate Total Bytes
    foreach ($items as &$it) {
        $iid = $it['itemid'];
        $vals = $history_data_all[$iid] ?? [];
        $count = count($vals);
        $avg = $count > 0 ? array_sum($vals) / $count : 0;
        $total_bytes = ($avg / 8) * $period; // Bits to Bytes, then for the whole period

        $it['avg_period'] = $avg;
        $it['total_period'] = $total_bytes;
    }
}

// 4. Update hosts with aggregated metrics - USE IMPROVED MATCHING
foreach ($items as $it) {
    $hid = $it['hostid'];
    if (!isset($hosts[$hid])) continue;

    $type = $it['type'] ?? '';
    if (!$type || $type === 'speed') continue;

    $key = $it['key_'];
    $name = $it['name'];
    
    // Use the same extraction logic as the first loop
    $iface_match = 'default';
    if (preg_match('/\["?(.*?)"?\]/', $key, $m)) {
        $iface_match = $m[1];
    }
    
    if (preg_match('/^if(HC)?(In|Out)(Octets|Bits)\.\d+$/i', $iface_match) || $iface_match === 'default') {
        if (preg_match('/Interface\s+(.*?):/i', $name, $nm)) {
            $iface_match = $nm[1];
        } elseif ($iface_match === 'default' && preg_match('/:\s*(.*?)$/', $name, $nm)) {
            $iface_match = $nm[1];
        }
    }
    
    foreach ($hosts[$hid]['interfaces'] as &$iface) {
        if ($iface['name'] === $iface_match) {
            if ($type === 'in') {
                $iface['in_avg'] = $it['avg_period'] ?? 0;
                $iface['total_in'] = $it['total_period'] ?? 0;
            } else {
                $iface['out_avg'] = $it['avg_period'] ?? 0;
                $iface['total_out'] = $it['total_period'] ?? 0;
            }
        }
    }
}

$final_json = json_encode(array_values($hosts));

// Save to cache
if (is_dir($cache_dir)) {
    @file_put_contents($cache_file, $final_json);
}

echo $final_json;