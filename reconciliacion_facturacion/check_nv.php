<?php
// Simple query to check NV 493 total in BSale
$servername = "localhost";
$username = "icontel_sweet";
$password = "Mauricio1984";
$dbname = "icontel_clientes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
    num_doc,
    razon_social,
    total_uf,
    neto_uf,
    fecha_emision,
    state
FROM cron_bsale_documents
WHERE tipo_doc = 'NOTA DE VENTA'
  AND num_doc = '493'";

$result = $conn->query($sql);

echo "<h2>NV 493 en BSale</h2>";

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    echo "<tr><td>NV Número</td><td>" . htmlspecialchars($row['num_doc']) . "</td></tr>";
    echo "<tr><td>Cliente</td><td>" . htmlspecialchars($row['razon_social']) . "</td></tr>";
    echo "<tr><td><strong>Total UF (BSale)</strong></td><td><strong style='font-size: 18px; color: blue;'>" . number_format($row['total_uf'], 2, ',', '.') . " UF</strong></td></tr>";
    echo "<tr><td>Neto UF</td><td>" . number_format($row['neto_uf'], 2, ',', '.') . " UF</td></tr>";
    echo "<tr><td>Fecha</td><td>" . htmlspecialchars($row['fecha_emision']) . "</td></tr>";
    echo "<tr><td>Estado</td><td>" . htmlspecialchars($row['state']) . "</td></tr>";
    echo "</table>";
    
    echo "<br><h3>Comparación:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Fuente</th><th>Total UF</th></tr>";
    echo "<tr><td>BSale (NV 493 real)</td><td><strong>" . number_format($row['total_uf'], 2, ',', '.') . " UF</strong></td></tr>";
    echo "<tr><td>Aplicación (suma Sweet)</td><td>11,98 UF</td></tr>";
    echo "<tr><td>Esperado por usuario</td><td>14,22 UF</td></tr>";
    echo "<tr><td style='background: yellow;'><strong>Diferencia BSale vs App</strong></td><td style='background: yellow;'><strong>" . number_format($row['total_uf'] - 11.98, 2, ',', '.') . " UF</strong></td></tr>";
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ NV 493 no encontrada en BSale</p>";
}

$conn->close();
?>
