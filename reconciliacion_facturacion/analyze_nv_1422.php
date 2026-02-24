<?php
// Check all NV 1422 documents in BSale
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/includes/sb_config.php";

$conn = DbConnect(DB_SWEET);

echo "<h2>All NV 1422 Documents in BSale</h2>";

$sql = "
    SELECT 
        num_doc,
        razon_social,
        fecha_emision,
        neto_uf,
        total_uf,
        netAmount AS neto_pesos,
        totalAmount AS total_pesos,
        urlPublicView
    FROM icontel_clientes.cron_bsale_documents
    WHERE tipo_doc = 'NOTA DE VENTA'
      AND num_doc = '1422'
    ORDER BY fecha_emision DESC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<p>Found <strong>{$result->num_rows}</strong> documents</p>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>NV</th><th>Cliente</th><th>Fecha</th><th>Neto UF</th><th>Neto $</th><th>Total UF</th><th>Total $</th></tr>";
    
    $totalNetoUF = 0;
    $totalNetoPesos = 0;
    
    while ($row = $result->fetch_assoc()) {
        $netoUF = floatval($row['neto_uf']);
        $netoPesos = floatval($row['neto_pesos']);
        
        // Convert if needed
        if ($netoUF == 0 && $netoPesos > 0) {
            $ufValue = getUFValue($row['fecha_emision']);
            if ($ufValue > 0) {
                $netoUF = $netoPesos / $ufValue;
            }
        }
        
        $totalNetoUF += $netoUF;
        $totalNetoPesos += $netoPesos;
        
        echo "<tr>";
        echo "<td>{$row['num_doc']}</td>";
        echo "<td>" . substr($row['razon_social'], 0, 30) . "</td>";
        echo "<td>{$row['fecha_emision']}</td>";
        echo "<td>" . number_format($netoUF, 6) . "</td>";
        echo "<td>$" . number_format($netoPesos, 2) . "</td>";
        echo "<td>" . number_format(floatval($row['total_uf']), 6) . "</td>";
        echo "<td>$" . number_format(floatval($row['total_pesos']), 2) . "</td>";
        echo "</tr>";
    }
    
    echo "<tr style='background: #f0f0f0; font-weight: bold;'>";
    echo "<td colspan='3'>TOTAL</td>";
    echo "<td>" . number_format($totalNetoUF, 6) . " UF</td>";
    echo "<td>$" . number_format($totalNetoPesos, 2) . "</td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";
    echo "</table>";
    
    echo "<h3>Analysis:</h3>";
    echo "<p>Total Neto UF (all documents): <strong>" . number_format($totalNetoUF, 6) . " UF</strong></p>";
    echo "<p>Total Neto $ (all documents): <strong>$" . number_format($totalNetoPesos, 2) . "</strong></p>";
    echo "<p>Expected from Sweet: <strong>UF 11,76</strong></p>";
    echo "<p>Difference: <strong>" . number_format(11.76 - $totalNetoUF, 6) . " UF</strong></p>";
}

$conn->close();
?>
