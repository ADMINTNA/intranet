<?php
$servername = "localhost";
$username = "data_studio";
$password = "1Ngr3s0.,";
$dbname = "icontel_clientes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_set_charset($conn, "utf8mb4");

echo "<h2>Estructura de cron_bsale_document_lines</h2>";

$sql = "DESCRIBE cron_bsale_document_lines";
$result = $conn->query($sql);

if ($result) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>Sample data from NV 1812</h2>";

// Try to find lines for NV 1812
$sql = "SELECT bdl.* 
        FROM cron_bsale_document_lines bdl
        INNER JOIN cron_bsale_documents bd ON bd.id = bdl.document_id
        WHERE bd.num_doc = 1812 AND bd.tipo_doc = 'NOTA DE VENTA'
        ORDER BY bdl.line_number";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<p><strong>Found " . $result->num_rows . " lines</strong></p>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Line</th><th>Description</th><th>Quantity</th><th>Unit Price UF</th><th>Total UF</th><th>Discount %</th></tr>";
    
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['line_number'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['description']) . "</strong></td>";
        echo "<td>" . $row['quantity'] . "</td>";
        echo "<td>" . number_format($row['unit_price_uf'], 2, ',', '.') . " UF</td>";
        echo "<td><strong>" . number_format($row['total_uf'], 2, ',', '.') . " UF</strong></td>";
        echo "<td>" . $row['discount_percent'] . "%</td>";
        echo "</tr>";
        $total += $row['total_uf'];
    }
    
    echo "<tr style='background: #ffffcc;'>";
    echo "<td colspan='4'><strong>TOTAL</strong></td>";
    echo "<td><strong style='font-size: 18px; color: blue;'>" . number_format($total, 2, ',', '.') . " UF</strong></td>";
    echo "<td></td>";
    echo "</tr>";
    echo "</table>";
} else {
    echo "<p style='color: red;'>No lines found for NV 1812</p>";
}

$conn->close();
?>
