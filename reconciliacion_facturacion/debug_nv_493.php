<?php
// Debug script to check NV 493 details in BSale
require_once "../config.php";
require_once "../kickoff_icontel/db_connect.php";


echo "<h2>Debug: NV 493 en BSale</h2>";

$conn = DbConnect("icontel_clientes");

// Get the full NV 493 data from BSale
$sql = "SELECT 
    num_doc,
    razon_social,
    rut,
    fecha_emision,
    total_uf,
    neto_uf,
    totalAmount AS total_pesos,
    netAmount AS neto_pesos,
    urlPublicView,
    urlPdf,
    state AS estado
FROM cron_bsale_documents
WHERE tipo_doc = 'NOTA DE VENTA'
  AND num_doc = '493'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    echo "<h3>Datos de NV 493 en BSale:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    echo "<tr><td>Número NV</td><td>" . $row['num_doc'] . "</td></tr>";
    echo "<tr><td>Razón Social</td><td>" . $row['razon_social'] . "</td></tr>";
    echo "<tr><td>RUT</td><td>" . $row['rut'] . "</td></tr>";
    echo "<tr><td>Fecha Emisión</td><td>" . $row['fecha_emision'] . "</td></tr>";
    echo "<tr><td><strong>Total UF</strong></td><td><strong>" . number_format($row['total_uf'], 2, ',', '.') . " UF</strong></td></tr>";
    echo "<tr><td>Neto UF</td><td>" . number_format($row['neto_uf'], 2, ',', '.') . " UF</td></tr>";
    echo "<tr><td>Total Pesos</td><td>$" . number_format($row['total_pesos'], 0, ',', '.') . "</td></tr>";
    echo "<tr><td>Neto Pesos</td><td>$" . number_format($row['neto_pesos'], 0, ',', '.') . "</td></tr>";
    echo "<tr><td>Estado</td><td>" . $row['estado'] . "</td></tr>";
    echo "<tr><td>URL Vista</td><td><a href='" . $row['urlPublicView'] . "' target='_blank'>Ver en BSale</a></td></tr>";
    echo "<tr><td>URL PDF</td><td><a href='" . $row['urlPdf'] . "' target='_blank'>Ver PDF</a></td></tr>";
    echo "</table>";
    
    echo "<br><br>";
    echo "<h3>Comparación:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Fuente</th><th>Total UF</th></tr>";
    echo "<tr><td>BSale (NV 493)</td><td><strong>" . number_format($row['total_uf'], 2, ',', '.') . " UF</strong></td></tr>";
    echo "<tr><td>Sweet (suma de servicios con NV 493)</td><td>11,98 UF</td></tr>";
    echo "<tr><td>Diferencia</td><td style='color: red;'><strong>" . number_format($row['total_uf'] - 11.98, 2, ',', '.') . " UF</strong></td></tr>";
    echo "</table>";
    
} else {
    echo "<p style='color: red;'>❌ NV 493 no encontrada en BSale</p>";
}

mysqli_close($conn);

echo "<br><br>";
echo "<p><a href='index.php'>← Volver a reconciliación</a></p>";
?>
