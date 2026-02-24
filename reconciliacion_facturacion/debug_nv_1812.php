<?php
// Debug script to check BSale line items for NV 1812
$servername = "localhost";
$username = "data_studio";
$password = "1Ngr3s0.,";
$dbname = "icontel_clientes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>NV 1812 - AndesCan - Detalle de Líneas en BSale</h2>";

// First, get the document info
$sql = "SELECT * FROM cron_bsale_documents WHERE num_doc = 1812 AND tipo_doc = 'NOTA DE VENTA'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $doc = $result->fetch_assoc();
    echo "<h3>Documento</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    echo "<tr><td>Número</td><td><strong>" . $doc['num_doc'] . "</strong></td></tr>";
    echo "<tr><td>Cliente</td><td>" . htmlspecialchars($doc['razon_social']) . "</td></tr>";
    echo "<tr><td>RUT</td><td>" . $doc['rut'] . "</td></tr>";
    echo "<tr><td>Neto UF</td><td><strong>" . number_format($doc['neto_uf'], 2, ',', '.') . " UF</strong></td></tr>";
    echo "<tr><td>Total UF</td><td>" . number_format($doc['total_uf'], 2, ',', '.') . " UF</td></tr>";
    echo "<tr><td>BSale ID</td><td>" . $doc['bsale_id'] . "</td></tr>";
    echo "</table>";
    
    // Now get the line items
    echo "<h3>Líneas del Documento</h3>";
    $bsaleId = $doc['bsale_id'];
    
    $sql = "SELECT * FROM cron_bsale_document_lines WHERE document_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bsaleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Línea</th><th>Descripción</th><th>Cantidad</th><th>Precio Unit UF</th><th>Total UF</th><th>Descuento</th></tr>";
        
        $totalSum = 0;
        while ($line = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $line['line_number'] . "</td>";
            echo "<td><strong>" . htmlspecialchars($line['description']) . "</strong></td>";
            echo "<td>" . $line['quantity'] . "</td>";
            echo "<td>" . number_format($line['unit_price_uf'], 2, ',', '.') . " UF</td>";
            echo "<td><strong>" . number_format($line['total_uf'], 2, ',', '.') . " UF</strong></td>";
            echo "<td>" . $line['discount_percent'] . "%</td>";
            echo "</tr>";
            $totalSum += $line['total_uf'];
        }
        
        echo "<tr style='background: #ffffcc;'>";
        echo "<td colspan='4'><strong>TOTAL LÍNEAS</strong></td>";
        echo "<td><strong style='font-size: 18px; color: blue;'>" . number_format($totalSum, 2, ',', '.') . " UF</strong></td>";
        echo "<td></td>";
        echo "</tr>";
        echo "</table>";
        
        echo "<br><p><strong>Comparación:</strong></p>";
        echo "<ul>";
        echo "<li>Total de líneas: " . number_format($totalSum, 2, ',', '.') . " UF</li>";
        echo "<li>Neto del documento: " . number_format($doc['neto_uf'], 2, ',', '.') . " UF</li>";
        echo "<li>Diferencia: " . number_format($totalSum - $doc['neto_uf'], 2, ',', '.') . " UF</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ No se encontraron líneas para este documento</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ No se encontró la NV 1812</p>";
}

$conn->close();
?>
