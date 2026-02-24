<?php
// ==========================================================
// Query Invoices from Sweet/BSale
// /reconciliacion_facturacion/includes/query_invoices.php
// Author: Mauricio Araneda (mAo)
// Date: 2025-12-18
// Encoding: UTF-8 without BOM
// ==========================================================

require_once __DIR__ . '/sb_config.php';

/**
 * Execute the main query to get invoices from Sweet and BSale
 */
function getInvoicesComparison($startDate, $endDate) {
    $conn = DbConnect(DB_SWEET);
    
    // Prepare the SQL query with date parameters
    // Solo facturas mensuales (vigente) con último documento BSale
    $sql = "SELECT
    /* ==== Número de Nota de Venta ==== */
    aic.num_nota_venta1_c AS nv_numero,

    /* ==== Primera factura del grupo ==== */
    MIN(ai.id) AS primera_fac_id,
    MIN(ai.number) AS primera_fac_numero,

    /* ==== Totales facturas asociadas ==== */
    SUM(ai.subtotal_amount) AS total_neto_facturas,
    COUNT(DISTINCT ai.id)   AS cant_facturas,

    /* ==== Nota de Venta Bsale ==== */
    cbd.id_bsale,
    cbd.num_doc,
    cbd.fecha_emision,
    cbd.razon_social,
    cbd.urlPublicView,
    cbd.netAmount           AS neto_bsale,

    /* ==== Diferencia ==== */
    SUM(ai.subtotal_amount) - cbd.netAmount AS diferencia

FROM aos_invoices ai
JOIN aos_invoices_cstm aic 
     ON aic.id_c = ai.id
LEFT JOIN aos_quotes aq 
       ON aq.number = ai.quote_number

/* === Última Nota de Venta por num_doc (por id_bsale más alto) === */
LEFT JOIN (
    SELECT cbd1.*
    FROM icontel_clientes.cron_bsale_documents cbd1
    JOIN (
        SELECT num_doc, MAX(id_bsale) AS max_id
        FROM icontel_clientes.cron_bsale_documents
        WHERE tipo_doc = 'NOTA DE VENTA'
        GROUP BY num_doc
    ) ult
      ON ult.num_doc = cbd1.num_doc
     AND ult.max_id = cbd1.id_bsale
    WHERE cbd1.tipo_doc = 'NOTA DE VENTA'
) cbd
  ON cbd.num_doc = aic.num_nota_venta1_c

WHERE ai.deleted = 0
  AND ai.status = 'vigente'
  AND aq.stage = 'Closed Accepted'
  AND aic.num_nota_venta1_c < 900000000

GROUP BY
    aic.num_nota_venta1_c,
    cbd.id_bsale,
    cbd.num_doc,
    cbd.fecha_emision,
    cbd.razon_social,
    cbd.netAmount

ORDER BY cbd.fecha_emision DESC";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("ERROR preparing statement: " . $conn->error);
        throw new Exception("Error preparing SQL query: " . $conn->error);
    }
    
    // Debug: Log the query
    error_log("=== RECONCILIATION QUERY DEBUG ===");
    error_log("Query: " . $sql);
    
    // No hay parámetros que bindear - la query trae todas las facturas vigentes
    
    if (!$stmt->execute()) {
        $error_msg = "ERROR executing statement: " . $stmt->error;
        error_log($error_msg);
        
        // Cerrar conexiones
        $stmt->close();
        $conn->close();
        
        // Retornar el error para debugging
        return [
            'error' => true,
            'message' => $error_msg,
            'sql_error' => $stmt->error
        ];
    }
    
    $result = $stmt->get_result();
    
    if (!$result) {
        $error_msg = "ERROR getting result: " . $stmt->error;
        error_log($error_msg);
        
        $stmt->close();
        $conn->close();
        
        return [
            'error' => true,
            'message' => $error_msg
        ];
    }
    
    $invoices = [];
    
    while ($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }
    
    error_log("Total invoices returned: " . count($invoices));
    
    $stmt->close();
    $conn->close();
    
    return $invoices;
}

/**
 * Detect billing type based on invoice status
 */
function detectBillingType($invoiceName, $quoteNumber = null, $status = null) {
    // Si tenemos el status, usarlo directamente
    if ($status) {
        if (strtolower($status) === 'vigente') {
            return 'monthly'; // Vigente = Mensual/Recurrente
        }
    }
    
    // Fallback: detectar por nombre si no hay status
    $name = strtolower($invoiceName);
    
    // Check for keywords in invoice name
    if (strpos($name, 'mensual') !== false || strpos($name, 'monthly') !== false) {
        return 'monthly';
    }
    
    if (strpos($name, 'anual') !== false || strpos($name, 'annual') !== false || strpos($name, 'yearly') !== false) {
        return 'annual';
    }
    
    if (strpos($name, 'bienal') !== false || strpos($name, 'biennial') !== false || strpos($name, '2 años') !== false) {
        return 'biennial';
    }
    
    // Default to unique if no pattern detected
    return 'unique';
}

/**
 * Get summary statistics by billing type
 */
function getSummaryByBillingType($invoices) {
    $summary = [
        'unique' => ['count' => 0, 'total_clp' => 0],
        'monthly' => ['count' => 0, 'total_clp' => 0],
        'annual' => ['count' => 0, 'total_clp' => 0],
        'biennial' => ['count' => 0, 'total_clp' => 0]
    ];
    
    // Get current UF and USD values
    $uf_value = getUFValue();
    $usd_value = getUSDValue();
    
    foreach ($invoices as $invoice) {
        $billingType = detectBillingType($invoice['fac_nombre'], $invoice['coti_numero'], $invoice['fac_tipo'] ?? null);
        
        // Convert to CLP
        $amount_clp = convertToCLP(
            $invoice['total_amount'], 
            $invoice['fac_moneda'],
            $uf_value,
            $usd_value
        );
        
        $summary[$billingType]['count']++;
        $summary[$billingType]['total_clp'] += $amount_clp;
    }
    
    return $summary;
}

?>
