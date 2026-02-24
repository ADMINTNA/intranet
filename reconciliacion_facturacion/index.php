<?php
require_once __DIR__ . '/includes/sb_config.php';
validateSession();

// Get current month as default
$currentMonth = date('Y-m');
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reconciliación Sweet ↔ BSale | iConTel</title>
    <link rel="stylesheet" href="css/reconciliacion.css?v=<?=time()?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-content">
                <div class="header-left">
                    <h1>📊 Reconciliación de Facturación</h1>
                    <p class="subtitle">Sweet CRM ↔ BSale</p>
                </div>
                <div class="header-right">
                    <div class="currency-indicators" id="currency_indicators">
                        <span class="currency-loading">Cargando tasas...</span>
                    </div>
                    <button class="btn-secondary" onclick="window.close()">
                        Salir y Cerrar
                    </button>
                </div>
            </div>
        </header>
        
        <!-- Control Panel -->
        <div class="control-panel">
            <div class="panel-section">
                <label class="control-label">🔍 Filtros:</label>
                <div class="filter-controls">
                    <!-- Multi-select billing type filter -->
                    <div class="multi-select-wrapper">
                        <button type="button" class="multi-select-button" id="billing_type_button" onclick="toggleBillingTypeDropdown()">
                            <span id="billing_type_label">3 tipos seleccionados</span>
                            <span class="dropdown-arrow">▼</span>
                        </button>
                        <div class="multi-select-dropdown" id="billing_type_dropdown" style="display: none;">
                            <label class="multi-select-option">
                                <input type="checkbox" value="Mensual" checked onchange="updateBillingTypeFilter()">
                                <span>Mensual</span>
                            </label>
                            <label class="multi-select-option">
                                <input type="checkbox" value="Anual" onchange="updateBillingTypeFilter()">
                                <span>Anual</span>
                            </label>
                            <label class="multi-select-option">
                                <input type="checkbox" value="Bienal" onchange="updateBillingTypeFilter()">
                                <span>Bienal</span>
                            </label>
                            <label class="multi-select-option">
                                <input type="checkbox" value="Posible Traslado" checked onchange="updateBillingTypeFilter()">
                                <span>Posible Traslado</span>
                            </label>
                            <label class="multi-select-option">
                                <input type="checkbox" value="En Traslado" checked onchange="updateBillingTypeFilter()">
                                <span>En Traslado</span>
                            </label>
                        </div>
                    </div>
                    
                    <select id="severity_filter" class="filter-select">
                        <option value="all">Todos los estados</option>
                        <option value="ok">✅ Sin problemas</option>
                        <option value="warning">⚠️ Advertencias</option>
                        <option value="error">❌ Errores</option>
                    </select>
                    
                    <input type="text" id="search_input" class="search-input" placeholder="Buscar cliente, RUT, NV...">
                </div>
            </div>
            
            <div class="panel-section">
                <button class="btn-primary" onclick="loadComparison()">
                    🔄 Actualizar Datos
                </button>
                <button class="btn-secondary" onclick="exportCSV()">
                    📥 Exportar CSV
                </button>
            </div>
        </div>
        
        
        <!-- Statistics Dashboard - Status Cards -->
        <div class="stats-dashboard" id="stats_dashboard" style="display: none;">
            <div class="stat-card stat-total" onclick="filterBySeverity('all')" style="cursor: pointer;" title="Mostrar todos los registros">
                <div class="stat-icon">📋</div>
                <div class="stat-content">
                    <div class="stat-value" id="stat_total">0</div>
                    <div class="stat-label">Total Registros</div>
                </div>
            </div>
            
            <div class="stat-card stat-ok" onclick="filterBySeverity('ok')" style="cursor: pointer;" title="Filtrar solo registros sin problemas">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-value" id="stat_ok">0</div>
                    <div class="stat-label">Sin Problemas</div>
                </div>
            </div>
            
            <div class="stat-card stat-warning" onclick="filterBySeverity('warning')" style="cursor: pointer;" title="Filtrar solo advertencias">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <div class="stat-value" id="stat_warnings">0</div>
                    <div class="stat-label">Advertencias</div>
                </div>
            </div>
            
            <div class="stat-card stat-error" onclick="filterBySeverity('error')" style="cursor: pointer;" title="Filtrar solo errores">
                <div class="stat-icon">❌</div>
                <div class="stat-content">
                    <div class="stat-value" id="stat_errors">0</div>
                    <div class="stat-label">Errores</div>
                </div>
            </div>
        </div>
        
        <!-- Document Totals Dashboard -->
        <div class="totals-dashboard" id="totals_dashboard" style="display: none;">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-icon">📄</div>
                <div class="stat-content">
                    <div class="stat-value" id="stat_total_cotizaciones">UF 0</div>
                    <div class="stat-label">Total Cotizaciones</div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #4a9eff 0%, #2575fc 100%);">
                <div class="stat-icon">🧾</div>
                <div class="stat-content">
                    <div class="stat-value" id="stat_total_facturas">UF 0</div>
                    <div class="stat-label">Total Facturas</div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stat-icon">📋</div>
                <div class="stat-content">
                    <div class="stat-value" id="stat_total_nv_bsale">UF 0</div>
                    <div class="stat-label">Total NV BSale</div>
                </div>
            </div>
        </div>
        
        <!-- Billing Type Summary -->
        <div class="billing-summary" id="billing_summary" style="display: none;">
            <h3>💰 Resumen por Tipo de Facturación</h3>
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-header">Mensual</div>
                    <div class="summary-count" id="summary_mensual_count">0</div>
                    <div class="summary-amount" id="summary_mensual_amount">UF 0</div>
                </div>
                <div class="summary-card">
                    <div class="summary-header">Anual</div>
                    <div class="summary-count" id="summary_anual_count">0</div>
                    <div class="summary-amount" id="summary_anual_amount">UF 0</div>
                </div>
                <div class="summary-card">
                    <div class="summary-header">Bienal</div>
                    <div class="summary-count" id="summary_bienal_count">0</div>
                    <div class="summary-amount" id="summary_bienal_amount">UF 0</div>
                </div>
                <div class="summary-card">
                    <div class="summary-header">Posible Traslado</div>
                    <div class="summary-count" id="summary_posible_traslado_count">0</div>
                    <div class="summary-amount" id="summary_posible_traslado_amount">UF 0</div>
                </div>
                <div class="summary-card">
                    <div class="summary-header">En Traslado</div>
                    <div class="summary-count" id="summary_en_traslado_count">0</div>
                    <div class="summary-amount" id="summary_en_traslado_amount">UF 0</div>
                </div>
            </div>
        </div>
        
        <!-- Loading Indicator -->
        <div class="loading-overlay" id="loading_overlay" style="display: none;">
            <div class="loading-spinner"></div>
            <p>Cargando datos...</p>
        </div>
        
        <!-- Results Table -->
        <div class="results-container" id="results_container" style="display: none;">
            <div class="table-header">
                <h3>📊 Resultados de Comparación</h3>
                <span class="record-count" id="record_count">0 registros</span>
            </div>
            
            <div class="table-scroll">
                <table class="comparison-table" id="comparison_table">
                    <thead>
                        <tr>
                            <th onclick="sortTable('severity')" data-sort="severity" style="cursor: pointer; user-select: none;">Estado <span class="sort-indicator"></span></th>
                            <th onclick="sortTable('razon_social')" data-sort="razon_social" style="cursor: pointer; user-select: none;">Cliente <span class="sort-indicator"></span></th>
                            <th onclick="sortTable('rut')" data-sort="rut" style="cursor: pointer; user-select: none;">RUT <span class="sort-indicator"></span></th>
                            <th onclick="sortTable('cantidad_servicios')" data-sort="cantidad_servicios" style="cursor: pointer; user-select: none;">Servicios <span class="sort-indicator"></span></th>
                            <th onclick="sortTable('total_cotizaciones')" data-sort="total_cotizaciones" style="cursor: pointer; user-select: none;">Total Cotizaciones <span class="sort-indicator"></span></th>
                            <th onclick="sortTable('total_facturas')" data-sort="total_facturas" style="cursor: pointer; user-select: none;">Total Facturas <span class="sort-indicator"></span></th>
                            <th onclick="sortTable('total_nv_bsale')" data-sort="total_nv_bsale" style="cursor: pointer; user-select: none;">Total NV BSale <span class="sort-indicator"></span></th>
                            <th onclick="sortTable('diferencia')" data-sort="diferencia" style="cursor: pointer; user-select: none;">Diferencia UF <span class="sort-indicator"></span></th>
                            <th onclick="sortTable('porcentaje_diferencia')" data-sort="porcentaje_diferencia" style="cursor: pointer; user-select: none;">% Dif <span class="sort-indicator"></span></th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="table_body">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Empty State -->
        <div class="empty-state" id="empty_state">
            <div class="empty-icon">🔍</div>
            <h3>Selecciona filtros para comenzar</h3>
            <p>Usa los controles superiores para seleccionar el tipo de facturación y filtros, luego haz clic en "Actualizar Datos"</p>
        </div>
        
        <!-- Invoice Details Modal -->
        <div id="invoice_details_modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 10000; align-items: center; justify-content: center;">
            <div style="background: #1a1a1a; border-radius: 12px; max-width: 900px; width: 90%; max-height: 80vh; overflow: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.5); position: relative; border: 1px solid rgba(255,255,255,0.1);">
                <div style="position: sticky; top: 0; background: #1a1a1a; padding: 20px; border-bottom: 2px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; z-index: 1;">
                    <h2 style="margin: 0; color: #ffffff;">Detalle de Servicios del Cliente</h2>
                    <button onclick="closeInvoiceDetailsModal()" style="background: rgba(255,255,255,0.1); border: none; font-size: 24px; cursor: pointer; color: #ffffff; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">×</button>
                </div>
                <div id="invoice_details_body" style="padding: 20px; background: #1a1a1a; color: #ffffff;">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/reconciliacion.js?v=<?=time()?>"></script>
    <script>
        // Auto-load data on page load with default filters
        window.addEventListener('load', function() {
            setTimeout(function() {
                loadComparison();
            }, 500);
        });
    </script>
</body>
</html>
