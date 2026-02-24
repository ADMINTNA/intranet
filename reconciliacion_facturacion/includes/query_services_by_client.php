<?php
// ==========================================================
// Query Services by Client
// /reconciliacion_facturacion/includes/query_services_by_client.php
// Author: Mauricio Araneda (mAo)
// Date: 2026-01-08
// Encoding: UTF-8 without BOM
// ==========================================================

/**
 * Get active services grouped by client
 * Uses the stored procedure searchactiveservicesbyaccountid for each account
 * 
 * @param array $billingTypes Array of billing types to filter: ['Mensual', 'Anual', 'Bienal', 'Posible Traslado', 'En Traslado']
 * @param bool $searchAllStates If true, bypass estado filter to search across all states
 * @return array Array of clients with their services
 */
function getServicesByClient($billingTypes = ['Mensual', 'Anual', 'Bienal', 'Posible Traslado', 'En Traslado'], $searchAllStates = false) {
    $conn = DbConnect(DB_SWEET);
    
    // Get exchange rates for currency conversion
    $uf_value = getUFValue();
    $usd_value = getUSDValue();
    
    // First, get all accounts with active services
    $sql = "
        SELECT DISTINCT
            a.id AS account_id,
            a.name AS razon_social,
            ac.rut_c AS rut
        FROM accounts a
        INNER JOIN accounts_cstm ac ON ac.id_c = a.id
        INNER JOIN aos_quotes q ON q.billing_account_id = a.id
        INNER JOIN aos_products_quotes p ON p.parent_id = q.id
        WHERE a.deleted = 0
          AND q.deleted = 0
          AND p.deleted = 0
          AND q.stage = 'Closed Accepted'
          AND p.parent_type = 'AOS_Quotes'
        ORDER BY a.name
    ";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        return [
            'error' => true,
            'message' => 'Error al obtener cuentas',
            'sql_error' => $conn->error
        ];
    }
    
    $clients = [];
    
    while ($account = $result->fetch_assoc()) {
        $accountId = $account['account_id'];
        $razonSocial = $account['razon_social'];
        
        // Detect if client uses USD (e.g., Expereo)
        $isUSDClient = (stripos($razonSocial, 'Expereo') !== false);
        
        // Get services for this account using stored procedure
        $services = getAccountServices($accountId, $billingTypes, $isUSDClient, $uf_value, $usd_value, $searchAllStates);
        
        if (!empty($services)) {
            $clients[] = [
                'account_id' => $accountId,
                'razon_social' => $account['razon_social'],
                'rut' => $account['rut'],
                'services' => $services,
                'total_uf_sweet' => array_sum(array_column($services, 'valor_uf')),
                'cantidad_servicios' => count($services),
                'nvs' => array_filter(array_unique(array_column($services, 'nv_bsale')))
            ];
        }
    }
    
    $result->free();
    $conn->close();
    
    return $clients;
}

/**
 * Get services for a specific account
 * 
 * @param string $accountId Account ID
 * @param array $billingTypes Array of billing types to filter
 * @return array Array of services
 */
function getAccountServices($accountId, $billingTypes = ['Mensual', 'Anual', 'Bienal', 'Posible Traslado', 'En Traslado'], $isUSDClient = false, $uf_value = 0, $usd_value = 0, $searchAllStates = false) {
    $conn = DbConnect(DB_SWEET);
    
    // Call stored procedure to get active services
    $sql = "CALL searchactiveservicesbyaccountid('{$accountId}')";
    $result = $conn->query($sql);
    
    if (!$result) {
        $conn->close();
        return [];
    }
    
    $services = [];
    
    while ($row = $result->fetch_assoc()) {
        $estado = trim($row['coti_estado'] ?? '');
        
        // Filter by billing types array (skip if searchAllStates is true)
        if (!$searchAllStates) {
            // Normalize estado: trim, uppercase, and normalize spaces
            $estadoNormalized = strtoupper(preg_replace('/\s+/', ' ', trim($estado)));
            
            // Normalize billing types the same way
            $billingTypesNormalized = array_map(function($type) {
                return strtoupper(preg_replace('/\s+/', ' ', trim($type)));
            }, $billingTypes);
            
            if (!in_array($estadoNormalized, $billingTypesNormalized)) {
                continue;
            }
        }
        
        $valorOriginal = floatval($row['produ_valor'] ?? 0);
        $valorUF = $valorOriginal;
        $currency = 'UF';
        
        // Convert USD to UF if this is a USD client
        if ($isUSDClient && $valorOriginal > 0 && $uf_value > 0 && $usd_value > 0) {
            $currency = 'USD';
            // Convert: USD → CLP → UF
            // Example: 519 USD * 950 CLP/USD / 38000 CLP/UF = 12.97 UF
            $valorUF = ($valorOriginal * $usd_value) / $uf_value;
        }
        
        $services[] = [
            'servicio_nombre' => $row['produ_nombre'] ?? '',
            'cantidad' => floatval($row['produ_cantidad'] ?? 0),
            'estado' => $estado,
            'cotizacion' => $row['coti_num'] ?? '',
            'factura' => $row['fac_num'] ?? '',
            'nv_bsale' => $row['nv_bsale'] ?? '',
            'valor_uf' => $valorUF,
            'valor_original' => $valorOriginal,
            'currency' => $currency,
            'codigo_servicio' => $row['codigo_servicio'] ?? '',
            'proveedor' => $row['produ_proveedor'] ?? '',
            'url_cotizacion' => $row['url_coti'] ?? '',
            'url_factura' => $row['url_fac'] ?? ''
        ];
    }
    
    // Clear multiple result sets from stored procedure
    while ($conn->more_results() && $conn->next_result()) {
        if ($r = $conn->store_result()) {
            $r->free();
        }
    }
    
    // Enrich services with factura numbers from aos_invoices table
    if (!empty($services)) {
        $cotizaciones = array_unique(array_filter(array_column($services, 'cotizacion')));
        
        if (!empty($cotizaciones)) {
            // Escape cotizacion numbers for SQL
            $cotizacionesEscaped = array_map(function($coti) use ($conn) {
                return "'" . $conn->real_escape_string($coti) . "'";
            }, $cotizaciones);
            
            $cotizacionesStr = implode(',', $cotizacionesEscaped);
            
            $sqlFacturas = "
                SELECT 
                    ai.quote_number AS cotizacion,
                    ai.number AS factura,
                    CONCAT('https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAOS_Invoices%26action%3DDetailView%26record%3D', ai.id) AS url_factura
                FROM aos_invoices ai
                WHERE ai.quote_number IN ($cotizacionesStr)
                  AND ai.deleted = 0
                  AND ai.status != 'Anulada'
                  AND ai.status != ''
                GROUP BY ai.quote_number
            ";
            
            $resultFacturas = $conn->query($sqlFacturas);
            
            if ($resultFacturas) {
                $facturas = [];
                
                while ($row = $resultFacturas->fetch_assoc()) {
                    $facturas[$row['cotizacion']] = [
                        'numero' => $row['factura'],
                        'url' => $row['url_factura']
                    ];
                }
                
                $resultFacturas->free();
                
                // Update services with factura numbers and URLs
                foreach ($services as &$service) {
                    if (isset($facturas[$service['cotizacion']])) {
                        $service['factura'] = $facturas[$service['cotizacion']]['numero'];
                        $service['url_factura'] = $facturas[$service['cotizacion']]['url'];
                    }
                }
                unset($service); // Break reference
            }
        }
    }
    
    $conn->close();
    
    return $services;
}

/**
 * Normalize RUT for comparison
 * Removes dots and dashes, converts to uppercase
 * Example: 12.345.678-9 -> 123456789, 12.345.678-K -> 12345678K
 */
function normalizeRut($rut) {
    if (empty($rut)) {
        return '';
    }
    // Remove dots and dashes, convert to uppercase
    return strtoupper(str_replace(['.', '-'], '', trim($rut)));
}

/**
 * Get BSale totals for specific NV numbers
 * 
 * @param array $nvNumbers Array of NV numbers to search
 * @return array Associative array with NV number as key and data as value
 */
function getBSaleDataByNVs($nvNumbers, $clientRut = null) {
    if (empty($nvNumbers)) {
        return [];
    }
    
    $conn = DbConnect(DB_SWEET);
    
    // Build IN clause
    $nvList = array_map(function($nv) use ($conn) {
        return "'" . $conn->real_escape_string($nv) . "'";
    }, $nvNumbers);
    $nvListStr = implode(',', $nvList);
    
    // Note: Not filtering by RUT to avoid blocking valid NVs
    // NV numbers should be unique enough identifiers
    
    $sql = "
        SELECT 
            bd.num_doc,
            bd.razon_social,
            bd.rut,
            bd.fecha_emision,
            bd.total_uf,
            bd.neto_uf,
            bd.totalAmount AS total_pesos,
            bd.netAmount AS neto_pesos,
            bd.urlPublicView,
            bd.urlPdf,
            bd.state AS estado
        FROM icontel_clientes.cron_bsale_documents bd
        WHERE bd.tipo_doc = 'NOTA DE VENTA'
          AND bd.num_doc IN ({$nvListStr})
        ORDER BY bd.num_doc, bd.id_bsale DESC
    ";
    
    // Debug: Log the SQL query
    error_log("getBSaleDataByNVs SQL: " . $sql);
    
    $result = $conn->query($sql);
    
    if (!$result) {
        error_log("getBSaleDataByNVs MySQL Error: " . $conn->error);
        $conn->close();
        return [];
    }
    
    error_log("getBSaleDataByNVs returned " . $result->num_rows . " rows");
    
    $bsaleData = [];
    
    while ($row = $result->fetch_assoc()) {
        $nvNum = $row['num_doc'];
        
        // Only store the FIRST document for each NV (which is the latest by date due to ORDER BY)
        // This prevents taking multiple documents when the same NV appears multiple times
        if (!isset($bsaleData[$nvNum])) {
            $netoUF = floatval($row['neto_uf'] ?? 0);
            $totalUF = floatval($row['total_uf'] ?? 0);
            $netoPesos = floatval($row['neto_pesos'] ?? 0);
            $totalPesos = floatval($row['total_pesos'] ?? 0);
            $conversionLabel = '';
            
            // If UF values are 0 but peso values exist, convert to UF
            if ($netoUF == 0 && $netoPesos > 0) {
                // Get UF value for the document date
                $ufValue = getUFValue($row['fecha_emision']);
                
                if ($ufValue > 0) {
                    $netoUF = $netoPesos / $ufValue;
                    $totalUF = $totalPesos / $ufValue;
                    $conversionLabel = '$→UF';
                    
                    error_log("NV {$nvNum}: Converted ${netoPesos} CLP to {$netoUF} UF (rate: {$ufValue})");
                }
            }
            
            $bsaleData[$nvNum] = [
                'nv_numero' => $nvNum,
                'razon_social' => $row['razon_social'],
                'rut' => $row['rut'],
                'fecha_emision' => $row['fecha_emision'],
                'total_uf' => $totalUF,
                'neto_uf' => $netoUF,
                'total_pesos' => $totalPesos,
                'neto_pesos' => $netoPesos,
                'url_view' => $row['urlPublicView'],
                'url_pdf' => $row['urlPdf'],
                'estado' => $row['estado'],
                'conversion_label' => $conversionLabel,
                'lines' => []
            ];
            
            error_log("NV {$nvNum}: Stored document (id_bsale highest) from {$row['fecha_emision']} with neto_uf: {$netoUF}");
        } else {
            // Skip additional documents with same NV (we only want the one with highest id_bsale)
            error_log("NV {$nvNum}: Skipping older document (lower id_bsale) from {$row['fecha_emision']}");
        }
    }
    
    $result->free();
    $conn->close();
    
    return $bsaleData;
}

/**
 * Compare Sweet services with BSale data for each client
 * 
 * @param array $clients Array of clients with services
 * @return array Array of comparisons with discrepancies
 */
function compareClientServices($clients) {
    $comparisons = [];
    
    foreach ($clients as $client) {
        // Get all NV numbers from this client's services
        $nvNumbers = array_filter($client['nvs']);
        
        // Get BSale data for these NVs filtering by client RUT
        // This ensures NV matches belong to the correct client
        $bsaleData = getBSaleDataByNVs($nvNumbers, $client['rut']);
        
        // Calculate 3 separate totals for document-level comparison
        $totalCotizaciones = 0;  // Sum of service lines with cotización
        $totalFacturas = 0;      // Sum of service lines with factura
        $totalNVBsale = 0;       // Sum of BSale document totals (not service lines)
        $totalNVServices = 0;    // Sum of service lines mapped to NV (for comparison)
        $nvDetails = [];
        
        foreach ($client['services'] as $service) {
            $nvNum = $service['nv_bsale'];
            
            // Sum cotizaciones (all services have cotizacion)
            if (!empty($service['cotizacion']) && $service['cotizacion'] !== '-') {
                $totalCotizaciones += $service['valor_uf'];
            }
            
            // Sum facturas (only services with factura)
            if (!empty($service['factura']) && $service['factura'] !== '-') {
                $totalFacturas += $service['valor_uf'];
            }
            
            // Sum service lines mapped to NV (for comparison with document total)
            if (!empty($nvNum) && intval($nvNum) < 9000000) {
                $totalNVServices += $service['valor_uf'];
            }
            
            // Store NV details for reference (links, etc.)
            if (!empty($nvNum) && intval($nvNum) < 9000000 && isset($bsaleData[$nvNum])) {
                if (!isset($nvDetails[$nvNum])) {
                    $nvDetails[$nvNum] = $bsaleData[$nvNum];
                }
            }
        }
        
        // Calculate total from BSale documents (use actual document neto_uf)
        // Use NETO (without taxes) to match Sweet CRM values
        // First, sum all NVs that have BSale data
        foreach ($nvDetails as $nvNum => $nvData) {
            $totalNVBsale += $nvData['neto_uf'];  // Use neto_uf from BSale
        }
        
        // Then, for services with NVs that don't have BSale data, use service values
        $nvCountedFallback = [];  // Track which NVs we've counted as fallback
        foreach ($client['services'] as $service) {
            $nvNum = $service['nv_bsale'];
            
            // Skip invalid NVs or NVs that already have BSale data
            if (empty($nvNum) || $nvNum === '-' || intval($nvNum) >= 9000000 || isset($nvDetails[$nvNum])) {
                continue;
            }
            
            // Use service value for NVs without BSale data (but only count each NV once)
            if (!isset($nvCountedFallback[$nvNum])) {
                $totalNVBsale += $service['valor_uf'];
                $nvCountedFallback[$nvNum] = true;
            }
        }


        // Check if all NVs are invalid (>= 9000000)
        $hasValidNV = false;
        $hasInvalidNV = false;
        foreach ($client['services'] as $service) {
            $nvNum = $service['nv_bsale'];
            if (!empty($nvNum) && $nvNum !== '-') {
                $nvNumInt = intval($nvNum);
                if ($nvNumInt >= 9000000) {
                    $hasInvalidNV = true;
                } else {
                    $hasValidNV = true;
                }
            }
        }

        // Calculate difference based on what exists
        // If all NVs are invalid (>= 9000000), compare Cotización vs Factura
        if ($hasInvalidNV && !$hasValidNV && $totalNVBsale == 0) {
            // Compare Cotización vs Factura when NV is invalid
            $diferencia = $totalCotizaciones - $totalFacturas;
            $baseTotal = $totalCotizaciones > 0 ? $totalCotizaciones : $totalFacturas;
        } else {
            // Normal comparison: Facturas/Cotizaciones vs NV BSale
            if ($totalFacturas > 0) {
                $diferencia = $totalFacturas - $totalNVBsale;
                $baseTotal = $totalFacturas;
            } else {
                $diferencia = $totalCotizaciones - $totalNVBsale;
                $baseTotal = $totalCotizaciones;
            }
        }

        $porcentajeDif = $baseTotal > 0 ? abs($diferencia / $baseTotal * 100) : 100;

        // Determine severity
        $severity = 'ok';
        $issues = [];

        // Special case: if all NVs are invalid and Cotización = Factura, only warning
        if ($hasInvalidNV && !$hasValidNV && abs($diferencia) <= 0.01) {
            $severity = 'warning';
            $issues[] = 'NV BSale inválida (no existe en BSale)';
        } else if (abs($diferencia) > 0.01) { // Tolerance of 0.01 UF
            if ($porcentajeDif > 1) {
                $severity = 'error';
                $issues[] = 'Diferencia mayor a 1%';
            } else {
                $severity = 'warning';
                $issues[] = 'Diferencia menor a 1%';
            }
        }

        // Check for missing NVs in BSale (but not invalid ones)
        foreach ($client['services'] as $service) {
            $nvNum = $service['nv_bsale'];
            if (!empty($nvNum) && $nvNum !== '-') {
                $nvNumInt = intval($nvNum);
                // Only flag as error if NV is valid range but not found
                if ($nvNumInt < 9000000 && !isset($bsaleData[$nvNum])) {
                    $severity = 'error';
                    $issues[] = "NV {$nvNum} no encontrada en BSale";
                }
            }
        }

        $comparisons[] = [
            'account_id' => $client['account_id'],
            'razon_social' => $client['razon_social'],
            'rut' => $client['rut'],
            'cantidad_servicios' => $client['cantidad_servicios'],
            'total_cotizaciones' => $totalCotizaciones,
            'total_facturas' => $totalFacturas,
            'total_nv_bsale' => $totalNVBsale,  // Real BSale document total
            'total_nv_services' => $totalNVServices,  // Sum of service lines (for comparison)
            'total_uf_sweet' => $client['total_uf_sweet'], // Keep for backward compatibility
            'total_uf_bsale' => $totalNVBsale, // Keep for backward compatibility
            'diferencia' => $diferencia,
            'porcentaje_diferencia' => $porcentajeDif,
            'severity' => $severity,
            'issues' => $issues,
            'services' => $client['services'],
            'bsale_data' => $nvDetails
        ];

    }
    
    return $comparisons;
}

?>
