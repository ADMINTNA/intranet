<?php
// ==========================================================
// Reconciliation Engine
// /reconciliacion_facturacion/includes/reconciliation_engine.php
// Author: Mauricio Araneda (mAo)
// Date: 2025-12-18
// Encoding: UTF-8 without BOM
// ==========================================================

require_once __DIR__ . '/sb_config.php';
require_once __DIR__ . '/query_invoices.php';

/**
 * Compare Sweet and BSale grouped data and detect discrepancies
 * Now works with data grouped by NV number
 */
function analyzeDiscrepancies($groupedData) {
    $discrepancies = [];
    
    foreach ($groupedData as $group) {
        $issues = [];
        $severity = 'ok'; // ok, warning, error
        
        // Check if BSale record exists
        if (empty($group['id_bsale'])) {
            $issues[] = 'No existe en BSale';
            $severity = 'error';
        } else {
            // Compare totals: total_neto_facturas (Sweet) vs neto_bsale (BSale)
            $sweet_total = floatval($group['total_neto_facturas'] ?? 0);
            $bsale_total = floatval($group['neto_bsale'] ?? 0);
            
            // Calculate difference
            $diferencia = abs($sweet_total - $bsale_total);
            
            // Calculate percentage if sweet_total > 0
            if ($sweet_total > 0) {
                $diff_percent = $diferencia / $sweet_total;
                
                if ($diff_percent > 0.01) { // More than 1% difference
                    $issues[] = sprintf(
                        'Diferencia: Sweet=%s UF (%d facturas), BSale=%s UF, Dif=%s UF (%.2f%%)',
                        number_format($sweet_total, 2, ',', '.'),
                        intval($group['cant_facturas'] ?? 0),
                        number_format($bsale_total, 2, ',', '.'),
                        number_format($diferencia, 2, ',', '.'),
                        $diff_percent * 100
                    );
                    $severity = ($diff_percent > 0.05) ? 'error' : 'warning';
                }
            } elseif ($bsale_total > 0) {
                // Sweet has 0 but BSale has value
                $issues[] = sprintf(
                    'Sweet sin monto pero BSale tiene %s UF',
                    number_format($bsale_total, 2, ',', '.')
                );
                $severity = 'error';
            }
        }
        
        $discrepancies[] = [
            'group' => $group,
            'issues' => $issues,
            'severity' => $severity,
            'billing_type' => 'monthly' // All vigente invoices are monthly
        ];
    }
    
    return $discrepancies;
}

/**
 * Get statistics about discrepancies
 */
function getDiscrepancyStats($discrepancies) {
    $stats = [
        'total' => count($discrepancies),
        'ok' => 0,
        'warnings' => 0,
        'errors' => 0,
        'by_type' => [
            'unique' => ['ok' => 0, 'warnings' => 0, 'errors' => 0],
            'monthly' => ['ok' => 0, 'warnings' => 0, 'errors' => 0],
            'annual' => ['ok' => 0, 'warnings' => 0, 'errors' => 0],
            'biennial' => ['ok' => 0, 'warnings' => 0, 'errors' => 0]
        ]
    ];
    
    foreach ($discrepancies as $disc) {
        $severity = $disc['severity'];
        $type = $disc['billing_type'];
        
        $stats[$severity === 'ok' ? 'ok' : ($severity === 'warning' ? 'warnings' : 'errors')]++;
        $stats['by_type'][$type][$severity === 'ok' ? 'ok' : ($severity === 'warning' ? 'warnings' : 'errors')]++;
    }
    
    return $stats;
}

/**
 * Filter discrepancies by criteria
 */
function filterDiscrepancies($discrepancies, $filters = []) {
    $filtered = $discrepancies;
    
    // Filter by billing type (all are monthly now, but keep for compatibility)
    if (!empty($filters['billing_type']) && $filters['billing_type'] !== 'monthly') {
        // If filtering for non-monthly, return empty since all are monthly
        return [];
    }
    
    // Filter by severity
    if (!empty($filters['severity'])) {
        $filtered = array_filter($filtered, function($disc) use ($filters) {
            return $disc['severity'] === $filters['severity'];
        });
    }
    
    // Filter by search term
    if (!empty($filters['search'])) {
        $search = strtolower($filters['search']);
        $filtered = array_filter($filtered, function($disc) use ($search) {
            $grp = $disc['group'];
            return (
                strpos(strtolower($grp['razon_social'] ?? ''), $search) !== false ||
                strpos($grp['nv_numero'] ?? '', $search) !== false ||
                strpos($grp['num_doc'] ?? '', $search) !== false
            );
        });
    }
    
    return array_values($filtered);
}

?>
