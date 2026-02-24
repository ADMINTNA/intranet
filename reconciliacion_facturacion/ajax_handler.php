<?php
// ==========================================================
// AJAX Handler
// /reconciliacion_facturacion/ajax_handler.php
// Author: Mauricio Araneda (mAo)
// Date: 2025-12-18
// Encoding: UTF-8 without BOM
// ==========================================================

header('Content-Type: application/json; charset=UTF-8');
mb_internal_encoding("UTF-8");

require_once __DIR__ . '/includes/sb_config.php';
require_once __DIR__ . '/includes/query_invoices.php';
require_once __DIR__ . '/includes/reconciliation_engine.php';
require_once __DIR__ . '/includes/api_bsale.php';
require_once __DIR__ . '/includes/query_services_by_client.php';

// Validate session
validateSession();

// Get action
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'get_comparison':
            $startDate = $_POST['start_date'] ?? date('Y-m-01');
            $endDate = $_POST['end_date'] ?? date('Y-m-t');
            $billingType = $_POST['billing_type'] ?? 'all';
            $severity = $_POST['severity'] ?? 'all';
            $search = $_POST['search'] ?? '';
            
            // Get invoices
            $invoices = getInvoicesComparison($startDate, $endDate);
            
            // Check if there was an error
            if (is_array($invoices) && isset($invoices['error']) && $invoices['error'] === true) {
                $response = [
                    'success' => false,
                    'message' => $invoices['message'] ?? 'Error desconocido',
                    'sql_error' => $invoices['sql_error'] ?? null
                ];
                break;
            }
            
            // Analyze discrepancies
            $discrepancies = analyzeDiscrepancies($invoices);
            
            // Apply filters
            $filters = [];
            if ($billingType !== 'all') {
                $filters['billing_type'] = $billingType;
            }
            if ($severity !== 'all') {
                $filters['severity'] = $severity;
            }
            if (!empty($search)) {
                $filters['search'] = $search;
            }
            
            $filtered = filterDiscrepancies($discrepancies, $filters);
            
            // Get statistics and summary based on FILTERED data
            $stats = getDiscrepancyStats($filtered);
            
            // For grouped data, all are monthly so summary is simple
            $totalGroups = count($filtered);
            $totalAmount = array_sum(array_map(function($disc) {
                return floatval($disc['group']['total_neto_facturas'] ?? 0);
            }, $filtered));
            
            $summary = [
                'monthly' => [
                    'count' => $totalGroups,
                    'total_clp' => $totalAmount
                ],
                'unique' => ['count' => 0, 'total_clp' => 0],
                'annual' => ['count' => 0, 'total_clp' => 0],
                'biennial' => ['count' => 0, 'total_clp' => 0]
            ];
            
            // Debug: Get billing types detected
            $billingTypes = array_map(function($disc) {
                return [
                    'nv' => $disc['group']['nv_numero'] ?? 'N/A',
                    'cliente' => substr($disc['group']['razon_social'] ?? 'N/A', 0, 30),
                    'type' => $disc['billing_type']
                ];
            }, $discrepancies);
            
            $response = [
                'success' => true,
                'data' => $filtered,
                'stats' => $stats,
                'summary' => $summary,
                'total_records' => count($invoices),
                'filtered_records' => count($filtered),
                'debug' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'billing_type' => $billingType,
                    'severity' => $severity,
                    'search' => $search,
                    'detected_types' => $billingTypes
                ]
            ];
            break;
            
        case 'get_comparison_by_client':
            // Decode billing_type from JSON array
            $billingTypeRaw = $_POST['billing_type'] ?? 'all';
            $billingType = json_decode($billingTypeRaw, true);
            
            // If not an array or empty, default to all
            if (!is_array($billingType) || empty($billingType)) {
                $billingType = ['Mensual', 'Anual', 'Bienal', 'Posible Traslado', 'En Traslado'];
            }
            
            $severity = $_POST['severity'] ?? 'all';
            $search = $_POST['search'] ?? '';
            
            // Get services grouped by client (always respect billing type filter)
            $clients = getServicesByClient($billingType, false);
            
            // Check if there was an error
            if (is_array($clients) && isset($clients['error']) && $clients['error'] === true) {
                $response = [
                    'success' => false,
                    'message' => $clients['message'] ?? 'Error desconocido',
                    'sql_error' => $clients['sql_error'] ?? null
                ];
                break;
            }
            
            // Compare with BSale
            $comparisons = compareClientServices($clients);
            
            // Apply filters
            $filtered = $comparisons;
            
            // Filter by severity
            if ($severity !== 'all') {
                $filtered = array_filter($filtered, function($comp) use ($severity) {
                    return $comp['severity'] === $severity;
                });
            }
            
            // Filter by search
            if (!empty($search)) {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $filtered = array_filter($filtered, function($comp) use ($searchLower) {
                    $razonSocial = mb_strtolower($comp['razon_social'], 'UTF-8');
                    $rut = mb_strtolower($comp['rut'], 'UTF-8');
                    
                    // Search in client name and RUT
                    if (strpos($razonSocial, $searchLower) !== false || strpos($rut, $searchLower) !== false) {
                        return true;
                    }
                    
                    // Search in document numbers (NV, Factura, Cotización) and service names
                    foreach ($comp['services'] as $service) {
                        $nvBsale = mb_strtolower($service['nv_bsale'] ?? '', 'UTF-8');
                        $factura = mb_strtolower($service['factura'] ?? '', 'UTF-8');
                        $cotizacion = mb_strtolower($service['cotizacion'] ?? '', 'UTF-8');
                        $serviceName = mb_strtolower($service['servicio_nombre'] ?? '', 'UTF-8');
                        
                        if (strpos($nvBsale, $searchLower) !== false || 
                            strpos($factura, $searchLower) !== false ||
                            strpos($cotizacion, $searchLower) !== false ||
                            strpos($serviceName, $searchLower) !== false) {
                            return true;
                        }
                    }
                    
                    return false;
                });
            }
            
            // Re-index array after filtering
            $filtered = array_values($filtered);
            
            // Calculate statistics
            $stats = [
                'total' => count($filtered),
                'ok' => 0,
                'warnings' => 0,
                'errors' => 0
            ];
            
            foreach ($filtered as $comp) {
                switch ($comp['severity']) {
                    case 'ok':
                        $stats['ok']++;
                        break;
                    case 'warning':
                        $stats['warnings']++;
                        break;
                    case 'error':
                        $stats['errors']++;
                        break;
                }
            }
            
            // Calculate summary by billing type
            $summary = [
                'mensual' => ['count' => 0, 'total_uf' => 0],
                'anual' => ['count' => 0, 'total_uf' => 0],
                'bienal' => ['count' => 0, 'total_uf' => 0],
                'posible_traslado' => ['count' => 0, 'total_uf' => 0],
                'en_traslado' => ['count' => 0, 'total_uf' => 0]
            ];
            
            foreach ($filtered as $comp) {
                foreach ($comp['services'] as $service) {
                    $estado = strtolower(str_replace(' ', '_', $service['estado']));
                    if (isset($summary[$estado])) {
                        $summary[$estado]['count']++;
                        $summary[$estado]['total_uf'] += $service['valor_uf'];
                    }
                }
            }
            
            $response = [
                'success' => true,
                'data' => $filtered,
                'stats' => $stats,
                'summary' => $summary,
                'total_records' => count($comparisons),
                'filtered_records' => count($filtered),
                'debug' => [
                    'billing_type' => $billingType,
                    'severity' => $severity,
                    'search' => $search
                ]
            ];
            break;
            
        case 'get_details':
            $invoiceId = $_POST['invoice_id'] ?? '';
            
            if (empty($invoiceId)) {
                throw new Exception('Invoice ID required');
            }
            
            // Get detailed information
            $conn = DbConnect(DB_SWEET);
            $stmt = $conn->prepare("SELECT * FROM aos_invoices WHERE id = ? AND deleted = 0");
            $stmt->bind_param('s', $invoiceId);
            $stmt->execute();
            $result = $stmt->get_result();
            $invoice = $result->fetch_assoc();
            $stmt->close();
            $conn->close();
            
            $response = [
                'success' => true,
                'invoice' => $invoice
            ];
            break;
            
        case 'update_bsale':
            // This would update BSale via API
            // For now, return placeholder
            $response = [
                'success' => false,
                'message' => 'BSale update not yet implemented'
            ];
            break;
            
        case 'update_sweet':
            // This would update Sweet via API
            // For now, return placeholder
            $response = [
                'success' => false,
                'message' => 'Sweet update not yet implemented'
            ];
            break;
            
        case 'export_csv':
            $billingTypeRaw = $_POST['billing_type'] ?? 'all';
            $billingType = json_decode($billingTypeRaw, true);
            
            if (!is_array($billingType) || empty($billingType)) {
                $billingType = ['Mensual', 'Anual', 'Bienal', 'Posible Traslado', 'En Traslado'];
            }
            $severity = $_POST['severity'] ?? 'all';
            $search = $_POST['search'] ?? '';
            
            // Get services grouped by client (always respect billing type filter)
            $clients = getServicesByClient($billingType, false);
            
            // Compare with BSale
            $comparisons = compareClientServices($clients);
            
            // Apply filters
            $filtered = $comparisons;
            
            if ($severity !== 'all') {
                $filtered = array_filter($filtered, function($comp) use ($severity) {
                    return $comp['severity'] === $severity;
                });
            }
            
            if (!empty($search)) {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $filtered = array_filter($filtered, function($comp) use ($searchLower) {
                    $razonSocial = mb_strtolower($comp['razon_social'], 'UTF-8');
                    $rut = mb_strtolower($comp['rut'], 'UTF-8');
                    
                    // Search in client name and RUT
                    if (strpos($razonSocial, $searchLower) !== false || strpos($rut, $searchLower) !== false) {
                        return true;
                    }
                    
                    // Search in document numbers (NV, Factura, Cotización) and service names
                    foreach ($comp['services'] as $service) {
                        $nvBsale = mb_strtolower($service['nv_bsale'] ?? '', 'UTF-8');
                        $factura = mb_strtolower($service['factura'] ?? '', 'UTF-8');
                        $cotizacion = mb_strtolower($service['cotizacion'] ?? '', 'UTF-8');
                        $serviceName = mb_strtolower($service['servicio_nombre'] ?? '', 'UTF-8');
                        
                        if (strpos($nvBsale, $searchLower) !== false || 
                            strpos($factura, $searchLower) !== false ||
                            strpos($cotizacion, $searchLower) !== false ||
                            strpos($serviceName, $searchLower) !== false) {
                            return true;
                        }
                    }
                    
                    return false;
                });
            }
            
            // Generate CSV
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="reconciliacion_clientes_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($output, [
                'Cliente',
                'RUT',
                'Servicios',
                'Total Cotizaciones (UF)',
                'Total Facturas (UF)',
                'Total NV BSale (UF)',
                'Diferencia (UF)',
                'Diferencia (%)',
                'Estado',
                'Problemas'
            ]);
            
            // Data
            foreach ($filtered as $comp) {
                fputcsv($output, [
                    $comp['razon_social'],
                    $comp['rut'],
                    $comp['cantidad_servicios'],
                    number_format($comp['total_cotizaciones'], 2, ',', '.'),
                    number_format($comp['total_facturas'], 2, ',', '.'),
                    number_format($comp['total_nv_bsale'], 2, ',', '.'),
                    number_format($comp['diferencia'], 2, ',', '.'),
                    number_format($comp['porcentaje_diferencia'], 2, ',', '.'),
                    $comp['severity'],
                    implode('; ', $comp['issues'])
                ]);
            }
            
            fclose($output);
            exit;
            
        case 'export_client_detail':
            $accountId = $_POST['account_id'] ?? '';
            $billingTypeRaw = $_POST['billing_type'] ?? 'all';
            $billingType = json_decode($billingTypeRaw, true);
            
            if (!is_array($billingType) || empty($billingType)) {
                $billingType = ['Mensual', 'Anual', 'Bienal', 'Posible Traslado', 'En Traslado'];
            }
            
            if (empty($accountId)) {
                throw new Exception('Account ID required');
            }
            
            // Respect billing type filters to exclude unwanted states (e.g., 'De Baja')
            $searchAllStates = false;
            
            // Get services grouped by client
            $clients = getServicesByClient($billingType, $searchAllStates);
            
            // Compare with BSale
            $comparisons = compareClientServices($clients);
            
            // Find the specific client
            $clientData = null;
            foreach ($comparisons as $comp) {
                if ($comp['account_id'] === $accountId) {
                    $clientData = $comp;
                    break;
                }
            }
            
            if (!$clientData) {
                throw new Exception('Cliente no encontrado');
            }
            
            // Generate CSV
            $filename = 'detalle_' . preg_replace('/[^a-z0-9]/i', '_', $clientData['razon_social']) . '_' . date('Y-m-d') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Client header info
            fputcsv($output, ['DETALLE DE SERVICIOS DEL CLIENTE']);
            fputcsv($output, ['Cliente:', $clientData['razon_social']]);
            fputcsv($output, ['RUT:', $clientData['rut']]);
            fputcsv($output, ['Cantidad de Servicios:', $clientData['cantidad_servicios']]);
            fputcsv($output, ['Fecha de Exportación:', date('Y-m-d H:i:s')]);
            fputcsv($output, []); // Empty row
            
            // Service details headers
            fputcsv($output, [
                'Servicio',
                'Cantidad',
                'Estado',
                'Cotización',
                'Valor Cotización (UF)',
                'Factura',
                'Valor Factura (UF)',
                'NV BSale',
                'Valor NV BSale (UF)',
                'Moneda Original',
                'Valor Original'
            ]);
            
            // Service details data
            $totalCantidad = 0;
            $totalCotizaciones = 0;
            $totalFacturas = 0;
            $totalBSale = 0;
            
            foreach ($clientData['services'] as $service) {
                $nvNum = $service['nv_bsale'] ?? '-';
                $bsaleData = $clientData['bsale_data'][$nvNum] ?? null;
                
                // Get BSale value
                $bsaleValue = '-';
                $bsaleValueNumeric = 0;
                if ($bsaleData && isset($bsaleData['lines'])) {
                    $serviceName = strtolower($service['servicio_nombre']);
                    foreach ($bsaleData['lines'] as $line) {
                        $lineDesc = strtolower($line['description']);
                        if (strpos($lineDesc, $serviceName) !== false || strpos($serviceName, $lineDesc) !== false) {
                            $bsaleValueNumeric = floatval($line['total_uf']);
                            $bsaleValue = number_format($bsaleValueNumeric, 2, ',', '.');
                            break;
                        }
                    }
                }
                
                // Check if NV is "Sin NV" (>= 9000000)
                $nvNumInt = intval($nvNum);
                if ($nvNumInt >= 9000000) {
                    $nvNum = 'Sin NV';
                    $bsaleValue = '-';
                    $bsaleValueNumeric = 0;
                }
                
                // Accumulate totals
                $totalCantidad += intval($service['cantidad']);
                $totalCotizaciones += floatval($service['valor_uf']);
                
                // For facturas, only add if it exists
                if ($service['factura']) {
                    $totalFacturas += floatval($service['valor_uf']);
                }
                
                $totalBSale += $bsaleValueNumeric;
                
                fputcsv($output, [
                    $service['servicio_nombre'],
                    $service['cantidad'],
                    $service['estado'],
                    $service['cotizacion'] ?? '-',
                    number_format($service['valor_uf'], 2, ',', '.'),
                    $service['factura'] ?? '-',
                    $service['factura'] ? number_format($service['valor_uf'], 2, ',', '.') : '-',
                    $nvNum,
                    $bsaleValue,
                    $service['currency'] ?? 'UF',
                    isset($service['valor_original']) ? number_format($service['valor_original'], 2, ',', '.') : '-'
                ]);
            }
            
            // Totals row
            fputcsv($output, []); // Empty row
            fputcsv($output, [
                'TOTALES',
                $totalCantidad,
                '',
                '',
                number_format($totalCotizaciones, 2, ',', '.'),
                '',
                number_format($totalFacturas, 2, ',', '.'),
                '',
                number_format($totalBSale, 2, ',', '.'),
                '',
                ''
            ]);
            
            // Summary section
            fputcsv($output, []); // Empty row
            fputcsv($output, ['RESUMEN']);
            fputcsv($output, ['Total UF Sweet:', number_format($clientData['total_uf_sweet'], 2, ',', '.')]);
            fputcsv($output, ['Total UF BSale:', number_format($clientData['total_uf_bsale'], 2, ',', '.')]);
            fputcsv($output, ['Diferencia (UF):', number_format($clientData['diferencia'], 2, ',', '.')]);
            fputcsv($output, ['Diferencia (%):', number_format($clientData['porcentaje_diferencia'], 2, ',', '.')]);
            fputcsv($output, ['Estado:', $clientData['severity']]);
            
            // Issues section
            if (!empty($clientData['issues'])) {
                fputcsv($output, []); // Empty row
                fputcsv($output, ['PROBLEMAS DETECTADOS']);
                foreach ($clientData['issues'] as $issue) {
                    fputcsv($output, [$issue]);
                }
            }
            
            fclose($output);
            exit;
            
        default:
            throw new Exception('Unknown action: ' . $action);
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
