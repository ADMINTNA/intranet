<?php
// ==========================================================
// Get Invoice Details by NV
// /reconciliacion_facturacion/get_invoice_details.php
// Author: Mauricio Araneda (mAo)
// Date: 2025-12-19
// Encoding: UTF-8 without BOM
// ==========================================================

require_once __DIR__ . '/includes/sb_config.php';
validateSession();
require_once __DIR__ . '/includes/query_invoices.php';

header('Content-Type: application/json');

try {
    $nv_numero = $_GET['nv'] ?? '';
    
    if (empty($nv_numero)) {
        throw new Exception('NV número requerido');
    }
    
    $conn = DbConnect(DB_SWEET);
    
    // Query para obtener todas las facturas de una NV específica
    $sql = "SELECT DISTINCT
        ai.id,
        ai.number AS fac_numero,
        ai.name AS fac_nombre,
        ai.invoice_date AS fac_fecha,
        ai.subtotal_amount AS total_neto,
        ai.total_amount,
        CASE 
            WHEN ai.currency_id = '-99' THEN 'UF'
            ELSE cu.symbol
        END AS fac_moneda,
        CONCAT(
            'https://sweet.icontel.cl/index.php?module=AOS_Invoices&action=DetailView&record=',
            ai.id
        ) AS url_fac
    FROM aos_invoices ai
    JOIN aos_invoices_cstm aic ON aic.id_c = ai.id
    LEFT JOIN aos_quotes aq ON aq.number = ai.quote_number
    LEFT JOIN currencies cu ON cu.id = ai.currency_id
    WHERE ai.deleted = 0
      AND ai.status = 'vigente'
      AND aq.stage = 'Closed Accepted'
      AND aic.num_nota_venta1_c = ?
    ORDER BY ai.number";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $nv_numero);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $invoices = [];
    
    while ($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }
    
    $stmt->close();
    
    // Get BSale NV total
    $sql_bsale = "SELECT 
        cbd.urlPublicView,
        cbd.netAmount AS neto_bsale
    FROM icontel_clientes.cron_bsale_documents cbd
    WHERE cbd.num_doc = ?
      AND cbd.tipo_doc = 'NOTA DE VENTA'
    ORDER BY cbd.fecha_emision DESC
    LIMIT 1";
    
    $stmt_bsale = $conn->prepare($sql_bsale);
    $stmt_bsale->bind_param('s', $nv_numero);
    $stmt_bsale->execute();
    
    $result_bsale = $stmt_bsale->get_result();
    $bsale_data = $result_bsale->fetch_assoc();
    
    $stmt_bsale->close();
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'nv_numero' => $nv_numero,
        'invoices' => $invoices,
        'url_bsale' => $bsale_data['urlPublicView'] ?? null,
        'neto_bsale' => $bsale_data['neto_bsale'] ?? 0
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
