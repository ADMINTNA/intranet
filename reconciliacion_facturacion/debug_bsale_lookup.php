<?php
// Debug script to check BSale data for specific NV numbers
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . "/includes/sb_config.php";
    
    $conn = DbConnect(DB_SWEET);
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p style='color: red;'>Failed to initialize: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

echo "<h2>Debug: Checking BSale Data Availability</h2>";

// Get some NV numbers from Sweet services
$sql = "
    SELECT DISTINCT aic.num_nota_venta1_c AS nv_numero
    FROM aos_invoices ai
    JOIN aos_invoices_cstm aic ON aic.id_c = ai.id
    WHERE ai.deleted = 0
      AND ai.status = 'vigente'
      AND aic.num_nota_venta1_c IS NOT NULL
      AND aic.num_nota_venta1_c != ''
      AND aic.num_nota_venta1_c < 900000000
    LIMIT 10
";

$result = $conn->query($sql);

echo "<h3>Sample NV numbers from Sweet (vigente invoices):</h3>";
$nvNumbers = [];
while ($row = $result->fetch_assoc()) {
    $nvNumbers[] = $row['nv_numero'];
    echo "<li>NV: {$row['nv_numero']}</li>";
}

if (empty($nvNumbers)) {
    echo "<p style='color: red;'>No NV numbers found in Sweet!</p>";
    $conn->close();
    exit;
}

// Now check if these exist in BSale
$nvList = "'" . implode("','", $nvNumbers) . "'";

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
      AND num_doc IN ($nvList)
";

echo "<h3>Checking BSale for these NV numbers:</h3>";
echo "<p><strong>SQL:</strong> $sql</p>";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'><strong>✓ Found {$result->num_rows} matching NVs in BSale</strong></p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>NV</th><th>Cliente</th><th>Fecha</th><th>Neto UF</th><th>Total UF</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['num_doc']}</td>";
        echo "<td>{$row['razon_social']}</td>";
        echo "<td>{$row['fecha_emision']}</td>";
        echo "<td>" . number_format($row['neto_uf'], 2) . "</td>";
        echo "<td>" . number_format($row['total_uf'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>✗ No matching NVs found in BSale!</strong></p>";
    echo "<p>Error: " . $conn->error . "</p>";
}

// Check what NV numbers DO exist in BSale
echo "<h3>Sample of NV numbers that DO exist in BSale:</h3>";
$sql = "
    SELECT num_doc, razon_social, fecha_emision, neto_uf
    FROM icontel_clientes.cron_bsale_documents
    WHERE tipo_doc = 'NOTA DE VENTA'
    ORDER BY fecha_emision DESC
    LIMIT 10
";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>NV</th><th>Cliente</th><th>Fecha</th><th>Neto UF</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['num_doc']}</td>";
        echo "<td>{$row['razon_social']}</td>";
        echo "<td>{$row['fecha_emision']}</td>";
        echo "<td>" . number_format($row['neto_uf'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();
?>
