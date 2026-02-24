<?php
// Debug script to check NV 1422 specifically
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/includes/sb_config.php";

$conn = DbConnect(DB_SWEET);

echo "<h2>Debug: Checking NV 1422 for Bsale Chile S.A.</h2>";

// Check if NV 1422 exists in BSale
$sql = "
    SELECT 
        num_doc,
        razon_social,
        fecha_emision,
        neto_uf,
        total_uf,
        urlPublicView
    FROM icontel_clientes.cron_bsale_documents
    WHERE tipo_doc = 'NOTA DE VENTA'
      AND num_doc = '1422'
";

echo "<h3>Searching for NV 1422 in BSale:</h3>";
echo "<p><strong>SQL:</strong> $sql</p>";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'><strong>✓ Found {$result->num_rows} document(s) for NV 1422</strong></p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>NV</th><th>Cliente</th><th>Fecha</th><th>Neto UF</th><th>Total UF</th><th>URL</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['num_doc']}</td>";
        echo "<td>{$row['razon_social']}</td>";
        echo "<td>{$row['fecha_emision']}</td>";
        echo "<td>" . number_format($row['neto_uf'], 2) . "</td>";
        echo "<td>" . number_format($row['total_uf'], 2) . "</td>";
        echo "<td>" . (!empty($row['urlPublicView']) ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>✗ NV 1422 NOT found in BSale!</strong></p>";
    echo "<p>MySQL Error: " . $conn->error . "</p>";
}

// Now test with getBSaleDataByNVs function
echo "<h3>Testing getBSaleDataByNVs(['1422']):</h3>";

require_once __DIR__ . "/includes/query_services_by_client.php";

$bsaleData = getBSaleDataByNVs(['1422']);

if (!empty($bsaleData)) {
    echo "<p style='color: green;'><strong>✓ Function returned data</strong></p>";
    echo "<pre>";
    print_r($bsaleData);
    echo "</pre>";
} else {
    echo "<p style='color: red;'><strong>✗ Function returned empty array</strong></p>";
}

// Check services for Bsale Chile S.A.
echo "<h3>Checking services for Bsale Chile S.A.:</h3>";

$sql = "
    SELECT a.id, a.name, a.billing_address_country
    FROM accounts a
    WHERE a.name LIKE '%Bsale Chile%'
      AND a.deleted = 0
";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $account = $result->fetch_assoc();
    echo "<p>Account ID: {$account['id']}</p>";
    echo "<p>Account Name: {$account['name']}</p>";
    
    // Get services
    $sql2 = "CALL searchactiveservicesbyaccountid('{$account['id']}')";
    $result2 = $conn->query($sql2);
    
    if ($result2 && $result2->num_rows > 0) {
        echo "<p>Found {$result2->num_rows} services</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Service</th><th>NV BSale</th><th>Value UF</th></tr>";
        
        $count = 0;
        while ($row = $result2->fetch_assoc() && $count < 5) {
            echo "<tr>";
            echo "<td>{$row['produ_nombre']}</td>";
            echo "<td>{$row['nv_bsale']}</td>";
            echo "<td>" . number_format($row['produ_valor'], 2) . "</td>";
            echo "</tr>";
            $count++;
        }
        echo "</table>";
        
        // Clear result sets
        while ($conn->more_results() && $conn->next_result()) {
            if ($r = $conn->store_result()) {
                $r->free();
            }
        }
    }
}

$conn->close();
?>
