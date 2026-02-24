<?php
// Check all NVs for Comercial BC
$servername = "localhost";
$username = "icontel_sweet";
$password = "Mauricio1984";
$dbname = "icontel_clientes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Find all NVs for Comercial BC
$sql = "SELECT 
    num_doc,
    razon_social,
    total_uf,
    neto_uf,
    fecha_emision
FROM cron_bsale_documents
WHERE tipo_doc = 'NOTA DE VENTA'
  AND razon_social LIKE '%Comercial BC%'
ORDER BY num_doc";

$result = $conn->query($sql);

echo "<h2>Todas las NVs de Comercial BC en BSale</h2>";

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>NV #</th><th>Cliente</th><th>Total UF</th><th>Neto UF</th><th>Fecha</th></tr>";
    
    $totalSum = 0;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['num_doc']) . "</td>";
        echo "<td>" . htmlspecialchars($row['razon_social']) . "</td>";
        echo "<td><strong>" . number_format($row['total_uf'], 2, ',', '.') . " UF</strong></td>";
        echo "<td>" . number_format($row['neto_uf'], 2, ',', '.') . " UF</td>";
        echo "<td>" . htmlspecialchars($row['fecha_emision']) . "</td>";
        echo "</tr>";
        $totalSum += $row['total_uf'];
    }
    
    echo "<tr style='background: #ffffcc;'>";
    echo "<td colspan='2'><strong>TOTAL</strong></td>";
    echo "<td><strong style='font-size: 18px; color: blue;'>" . number_format($totalSum, 2, ',', '.') . " UF</strong></td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";
    echo "</table>";
    
    echo "<br><p><strong>Total calculado:</strong> " . number_format($totalSum, 2, ',', '.') . " UF</p>";
    echo "<p>Este es el total que debería aparecer en 'Total NV BSale' para Comercial BC</p>";
} else {
    echo "<p style='color: red;'>❌ No se encontraron NVs para Comercial BC</p>";
}

$conn->close();
?>
