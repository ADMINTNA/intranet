<?php
// Debug NV 291 for Bidfood Chile
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/includes/sb_config.php";

$conn = DbConnect(DB_SWEET);

echo "<h2>Debug: NV 291 for Bidfood Chile</h2>";

// Check all documents with NV 291 in BSale
$sql = "
    SELECT 
        num_doc,
        razon_social,
        rut,
        fecha_emision,
        neto_uf,
        total_uf,
        netAmount AS neto_pesos,
        totalAmount AS total_pesos,
        urlPublicView
    FROM icontel_clientes.cron_bsale_documents
    WHERE tipo_doc = 'NOTA DE VENTA'
      AND num_doc = '291'
    ORDER BY fecha_emision DESC
";

echo "<h3>All NV 291 Documents in BSale:</h3>";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<p>Found <strong>{$result->num_rows}</strong> document(s)</p>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>NV</th><th>Cliente</th><th>RUT</th><th>Fecha</th><th>Neto UF</th><th>Total UF</th><th>Neto $</th></tr>";
    
    $totalNetoUF = 0;
    
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
        
        echo "<tr>";
        echo "<td>{$row['num_doc']}</td>";
        echo "<td>" . substr($row['razon_social'], 0, 40) . "</td>";
        echo "<td>{$row['rut']}</td>";
        echo "<td>{$row['fecha_emision']}</td>";
        echo "<td>" . number_format($netoUF, 2) . "</td>";
        echo "<td>" . number_format(floatval($row['total_uf']), 2) . "</td>";
        echo "<td>$" . number_format($netoPesos, 2) . "</td>";
        echo "</tr>";
    }
    
    echo "<tr style='background: #f0f0f0; font-weight: bold;'>";
    echo "<td colspan='4'>TOTAL SUMADO</td>";
    echo "<td>" . number_format($totalNetoUF, 2) . " UF</td>";
    echo "<td colspan='2'></td>";
    echo "</tr>";
    echo "</table>";
    
    echo "<h3>Analysis:</h3>";
    echo "<p><strong>Total sumado por el sistema:</strong> UF " . number_format($totalNetoUF, 2) . "</p>";
    echo "<p><strong>Esperado (del documento BSale):</strong> UF 10,11</p>";
    echo "<p><strong>Mostrado en el modal:</strong> UF 16,61</p>";
    
    if ($totalNetoUF != 10.11) {
        echo "<p style='color: red;'><strong>⚠️ PROBLEMA:</strong> El sistema está sumando múltiples documentos con el mismo NV 291</p>";
        echo "<p>Esto puede ser porque hay varios clientes con NV 291, o varios documentos para el mismo cliente.</p>";
    }
}

// Check services for Bidfood Chile
echo "<h3>Services for Bidfood Chile:</h3>";

$sql = "
    SELECT a.id, a.name, a.billing_address_country
    FROM accounts a
    WHERE a.name LIKE '%BIDFOOD%'
      AND a.deleted = 0
    LIMIT 5
";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($account = $result->fetch_assoc()) {
        echo "<h4>{$account['name']} (ID: {$account['id']})</h4>";
        
        // Get services
        $sql2 = "CALL searchactiveservicesbyaccountid('{$account['id']}')";
        $result2 = $conn->query($sql2);
        
        if ($result2 && $result2->num_rows > 0) {
            echo "<p>Services with NV 291:</p>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Service</th><th>NV BSale</th><th>Value UF</th></tr>";
            
            $hasNV291 = false;
            while ($row = $result2->fetch_assoc()) {
                if ($row['nv_bsale'] == '291') {
                    $hasNV291 = true;
                    echo "<tr>";
                    echo "<td>{$row['produ_nombre']}</td>";
                    echo "<td>{$row['nv_bsale']}</td>";
                    echo "<td>" . number_format($row['produ_valor'], 2) . "</td>";
                    echo "</tr>";
                }
            }
            
            if (!$hasNV291) {
                echo "<tr><td colspan='3'>No services with NV 291</td></tr>";
            }
            echo "</table>";
        }
        
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
