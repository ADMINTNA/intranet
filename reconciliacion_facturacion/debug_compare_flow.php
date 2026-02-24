<?php
// Debug script to test the exact flow of compareClientServices
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Capture error_log output
$logFile = __DIR__ . '/debug_log.txt';
ini_set('error_log', $logFile);

// Clear previous log
if (file_exists($logFile)) {
    unlink($logFile);
}

require_once __DIR__ . "/includes/sb_config.php";
require_once __DIR__ . "/includes/query_services_by_client.php";

echo "<h2>Debug: Testing compareClientServices Flow</h2>";

// Test with TNA OFFICE SPA which we know has NV 2117
$testNVs = ['2117', '2', '12'];

echo "<h3>Step 1: Testing getBSaleDataByNVs() directly</h3>";
$bsaleData = getBSaleDataByNVs($testNVs);

echo "<p>Requested NVs: " . implode(', ', $testNVs) . "</p>";
echo "<p>BSale data returned: " . count($bsaleData) . " entries</p>";

if (!empty($bsaleData)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>NV</th><th>Cliente</th><th>Fecha</th><th>Neto UF</th><th>Has URL?</th><th>Has Lines?</th></tr>";
    foreach ($bsaleData as $nv => $data) {
        echo "<tr>";
        echo "<td>{$nv}</td>";
        echo "<td>" . substr($data['razon_social'], 0, 30) . "</td>";
        echo "<td>{$data['fecha_emision']}</td>";
        echo "<td>" . number_format($data['neto_uf'], 2) . "</td>";
        echo "<td>" . (!empty($data['url_view']) ? 'Yes' : 'No') . "</td>";
        echo "<td>" . count($data['lines']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No BSale data found!</p>";
}

echo "<h3>Step 2: Testing full getServicesByClient() flow</h3>";
$clients = getServicesByClient('all');

if (is_array($clients) && isset($clients['error'])) {
    echo "<p style='color: red;'>Error: " . $clients['message'] . "</p>";
} else {
    echo "<p>Found " . count($clients) . " clients</p>";
    
    // Find TNA OFFICE SPA
    $tnaOffice = null;
    foreach ($clients as $client) {
        if (stripos($client['razon_social'], 'TNA OFFICE') !== false) {
            $tnaOffice = $client;
            break;
        }
    }
    
    if ($tnaOffice) {
        echo "<h4>TNA OFFICE SPA Details:</h4>";
        echo "<p>Account ID: {$tnaOffice['account_id']}</p>";
        echo "<p>Services: {$tnaOffice['cantidad_servicios']}</p>";
        echo "<p>NVs found in services:</p>";
        echo "<ul>";
        foreach ($tnaOffice['services'] as $service) {
            $nv = $service['nv_bsale'] ?? '-';
            echo "<li>{$service['servicio_nombre']}: NV = {$nv}</li>";
        }
        echo "</ul>";
        
        echo "<h4>Testing compareClientServices for TNA OFFICE:</h4>";
        $comparison = compareClientServices([$tnaOffice]);
        
        if (!empty($comparison)) {
            $comp = $comparison[0];
            echo "<p>BSale data entries: " . count($comp['bsale_data']) . "</p>";
            
            if (!empty($comp['bsale_data'])) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>NV</th><th>Cliente</th><th>Neto UF</th></tr>";
                foreach ($comp['bsale_data'] as $nv => $data) {
                    echo "<tr>";
                    echo "<td>{$nv}</td>";
                    echo "<td>" . substr($data['razon_social'], 0, 30) . "</td>";
                    echo "<td>" . number_format($data['neto_uf'], 2) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: red;'>❌ No BSale data in comparison result!</p>";
            }
            
            echo "<p>Issues: " . implode(', ', $comp['issues']) . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>TNA OFFICE SPA not found in clients</p>";
    }
}

echo "<h3>Debug Logs:</h3>";
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    if (!empty($logs)) {
        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc; overflow-x: auto;'>";
        echo htmlspecialchars($logs);
        echo "</pre>";
    } else {
        echo "<p>No logs generated</p>";
    }
} else {
    echo "<p>Log file not found</p>";
}

?>
