<?php
// Check for invoices with deleted = true
require_once(__DIR__ . '/../kickoff_icontel/includes/db_config.php');

$conn = DbConnect(DB_SWEET);

// Check table structure
echo "<h2>Table Structure for aos_invoices</h2>";
$sql = "DESCRIBE aos_invoices";
$result = $conn->query($sql);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    $result->free();
}

// Check for invoices with deleted = 1
echo "<h2>Invoices with deleted = 1</h2>";
$sql = "
    SELECT 
        ai.id,
        ai.number,
        ai.quote_number,
        ai.deleted,
        ai.status,
        ai.date_entered
    FROM aos_invoices ai
    WHERE ai.deleted = 1
    ORDER BY ai.date_entered DESC
    LIMIT 20
";

$result = $conn->query($sql);

if ($result) {
    echo "<p>Found {$result->num_rows} deleted invoices (showing first 20)</p>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Number</th><th>Quote Number</th><th>Deleted</th><th>Status</th><th>Date Entered</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['number']}</td>";
        echo "<td>{$row['quote_number']}</td>";
        echo "<td>{$row['deleted']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td>{$row['date_entered']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    $result->free();
}

// Check current query results
echo "<h2>Current Query Results (with deleted = 0 filter)</h2>";
$cotizaciones = ['1812', '1813', '1814']; // Sample cotizaciones
$cotizacionesStr = "'" . implode("','", $cotizaciones) . "'";

$sql = "
    SELECT 
        ai.quote_number AS cotizacion,
        ai.number AS factura,
        ai.deleted,
        ai.status
    FROM aos_invoices ai
    WHERE ai.quote_number IN ($cotizacionesStr)
      AND ai.deleted = 0
      AND ai.status != 'Anulada'
      AND ai.status != ''
    GROUP BY ai.quote_number
";

$result = $conn->query($sql);

if ($result) {
    echo "<p>Found {$result->num_rows} invoices</p>";
    echo "<table border='1'>";
    echo "<tr><th>Cotización</th><th>Factura</th><th>Deleted</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['cotizacion']}</td>";
        echo "<td>{$row['factura']}</td>";
        echo "<td>{$row['deleted']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    $result->free();
}

$conn->close();
?>
