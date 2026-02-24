<?php
// Debug script to check NV 12 and 2298 in BSale database

require_once(__DIR__ . '/../config.php');

$conn = DbConnect(DB_SWEET);

$sql = "
    SELECT 
        id_bsale,
        num_doc,
        razon_social,
        rut,
        fecha_emision,
        total_uf,
        neto_uf,
        totalAmount AS total_pesos,
        netAmount AS neto_pesos,
        state AS estado
    FROM icontel_clientes.cron_bsale_documents
    WHERE tipo_doc = 'NOTA DE VENTA'
      AND num_doc IN ('12', '2298')
    ORDER BY num_doc, id_bsale DESC
";

echo "<h2>NVs 12 and 2298 in BSale Database</h2>\n";
echo "<pre>\n";

$result = $conn->query($sql);

if ($result) {
    echo "Found " . $result->num_rows . " rows\n\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "==========================================\n";
        echo "NV: " . $row['num_doc'] . "\n";
        echo "ID BSale: " . $row['id_bsale'] . "\n";
        echo "Razón Social: " . $row['razon_social'] . "\n";
        echo "RUT: " . $row['rut'] . "\n";
        echo "Fecha: " . $row['fecha_emision'] . "\n";
        echo "NETO UF: " . $row['neto_uf'] . "\n";
        echo "TOTAL UF: " . $row['total_uf'] . "\n";
        echo "NETO Pesos: " . $row['neto_pesos'] . "\n";
        echo "TOTAL Pesos: " . $row['total_pesos'] . "\n";
        echo "Estado: " . $row['estado'] . "\n";
        echo "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "</pre>\n";

$conn->close();
