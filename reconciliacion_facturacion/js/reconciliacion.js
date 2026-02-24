// ==========================================================
// Sweet ↔ BSale Reconciliation - JavaScript
// /reconciliacion_facturacion/js/reconciliacion.js
// Author: Mauricio Araneda (mAo)
// Date: 2025-12-18
// ==========================================================

// Global state
let currentData = [];
let currentStats = {};
let currentSummary = {};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Reconciliation app initialized');

    // Set up event listeners
    document.getElementById('severity_filter').addEventListener('change', applyFilters);
    document.getElementById('search_input').addEventListener('input', debounce(applyFilters, 300));

    // Load data on Enter key in search
    document.getElementById('search_input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            loadComparison();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const dropdown = document.getElementById('billing_type_dropdown');
        const button = document.getElementById('billing_type_button');
        if (dropdown && !dropdown.contains(e.target) && !button.contains(e.target)) {
            dropdown.style.display = 'none';
            button.classList.remove('open');
        }
    });
});

// Load comparison data
function loadComparison() {
    const billingTypes = getSelectedBillingTypes();
    const severity = document.getElementById('severity_filter').value;
    const search = document.getElementById('search_input').value;

    showLoading(true);

    const formData = new FormData();
    formData.append('action', 'get_comparison_by_client');
    // Send as JSON array
    formData.append('billing_type', JSON.stringify(billingTypes));
    formData.append('severity', severity);
    formData.append('search', search);

    fetch('ajax_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            showLoading(false);

            if (data.success) {
                // Debug: Show query info in console
                if (data.debug) {
                    console.log('=== RECONCILIATION DEBUG ===');
                    console.log('Billing Type:', data.debug.billing_type);
                    console.log('Severity:', data.debug.severity);
                    console.log('Search:', data.debug.search);
                    console.log('Total Records:', data.total_records);
                    console.log('Filtered Records:', data.filtered_records);
                }

                currentData = data.data;
                currentStats = data.stats;
                currentSummary = data.summary;

                // Store data globally for sorting
                window.currentTableData = data.data;

                updateStatistics(data.stats);
                updateSummary(data.summary);
                updateTable(data.data);

                // Show/hide sections
                document.getElementById('stats_dashboard').style.display = 'grid';
                document.getElementById('totals_dashboard').style.display = 'grid';
                document.getElementById('billing_summary').style.display = 'block';
                document.getElementById('results_container').style.display = 'block';
                document.getElementById('empty_state').style.display = 'none';

                console.log(`✅ Loaded ${data.data.length} clients`);
            } else {
                alert('Error: ' + data.message);
                if (data.sql_error) {
                    console.error('SQL Error:', data.sql_error);
                }
            }
        })
        .catch(error => {
            showLoading(false);
            console.error('Error:', error);
            alert('Error al cargar los datos. Por favor intenta nuevamente.');
        });
}

// Apply filters without reloading from server
function applyFilters() {
    loadComparison();
}

// Update statistics display
function updateStatistics(stats) {
    document.getElementById('stat_total').textContent = stats.total;
    document.getElementById('stat_ok').textContent = stats.ok;
    document.getElementById('stat_warnings').textContent = stats.warnings;
    document.getElementById('stat_errors').textContent = stats.errors;

    // Calculate totals by document type from currentData
    if (currentData && currentData.length > 0) {
        let totalCotizaciones = 0;
        let totalFacturas = 0;
        let totalNVBsale = 0;

        currentData.forEach(client => {
            totalCotizaciones += parseFloat(client.total_cotizaciones || 0);
            totalFacturas += parseFloat(client.total_facturas || 0);
            totalNVBsale += parseFloat(client.total_nv_bsale || 0);
        });

        document.getElementById('stat_total_cotizaciones').textContent = `UF ${formatNumber(totalCotizaciones)}`;
        document.getElementById('stat_total_facturas').textContent = `UF ${formatNumber(totalFacturas)}`;
        document.getElementById('stat_total_nv_bsale').textContent = `UF ${formatNumber(totalNVBsale)}`;
    }
}

// Update billing summary
function updateSummary(summary) {
    for (const [type, data] of Object.entries(summary)) {
        const countEl = document.getElementById(`summary_${type}_count`);
        const amountEl = document.getElementById(`summary_${type}_amount`);
        if (countEl && amountEl) {
            countEl.textContent = data.count;
            amountEl.textContent = `UF ${formatNumber(data.total_uf)}`;
        }
    }
}

// Update table with client-based data
function updateTable(data) {
    const tbody = document.getElementById('table_body');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center; padding:40px;">No se encontraron registros con los filtros seleccionados</td></tr>';
        document.getElementById('record_count').textContent = '0 registros';
        return;
    }

    document.getElementById('record_count').textContent = `${data.length} cliente${data.length !== 1 ? 's' : ''}`;

    data.forEach(client => {
        const row = document.createElement('tr');
        row.className = `row-${client.severity}`;
        row.style.cursor = 'pointer';
        row.onclick = () => showClientDetails(client.account_id);

        const statusBadge = getSeverityBadge(client.severity);

        // Format UF values for 3 separate totals
        const totalCotizaciones = formatNumber(client.total_cotizaciones || 0);
        const totalFacturas = formatNumber(client.total_facturas || 0);
        const totalNVBsale = formatNumber(client.total_nv_bsale || 0);
        const diferencia = formatNumber(client.diferencia);
        const porcentaje = formatNumber(client.porcentaje_diferencia);

        row.innerHTML = `
            <td>${statusBadge}</td>
            <td style="font-weight: 600;">${escapeHtml(client.razon_social || '-')}</td>
            <td>${escapeHtml(client.rut || '-')}</td>
            <td style="text-align: center;">${client.cantidad_servicios}</td>
            <td style="text-align: right; font-weight: 600;">UF ${totalCotizaciones}</td>
            <td style="text-align: right; font-weight: 600; color: ${client.total_facturas > 0 ? '#4a9eff' : '#666'};">UF ${totalFacturas}</td>
            <td style="text-align: right; font-weight: 600;">UF ${totalNVBsale}</td>
            <td style="text-align: right; ${getDiferenciaStyle(client.diferencia)}">
                UF ${diferencia}
            </td>
            <td style="text-align: right; ${getDiferenciaStyle(client.diferencia)}">
                ${porcentaje}%
            </td>
            <td style="text-align: center;">
                <button onclick="event.stopPropagation(); showClientDetails('${client.account_id}')" 
                        class="btn-detail" 
                        title="Ver detalle de servicios"
                        style="background: var(--accent); color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85em; font-weight: 600; transition: all 0.2s;">
                    📋 Ver Detalle
                </button>
            </td>
        `;

        tbody.appendChild(row);
    });
}

// Helper function to style difference column
function getDiferenciaStyle(diferencia) {
    const diff = Math.abs(parseFloat(diferencia) || 0);
    if (diff > 10) return 'color: var(--error); font-weight: 700;';
    if (diff > 1) return 'color: var(--warning); font-weight: 600;';
    return 'color: var(--success);';
}

// Helper function to format numbers (Chilean format: . for thousands, , for decimals)
function formatNumber(value) {
    const num = parseFloat(value) || 0;
    return num.toFixed(2)
        .replace('.', ',')  // Replace decimal point with comma
        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");  // Add thousand separators
}

// Helper functions
function getSeverityBadge(severity) {
    const badges = {
        'ok': '<span class="status-badge badge-ok">✅ OK</span>',
        'warning': '<span class="status-badge badge-warning">⚠️ Advertencia</span>',
        'error': '<span class="status-badge badge-error">❌ Error</span>'
    };
    return badges[severity] || badges['ok'];
}

function getBillingTypeBadge(type) {
    const labels = {
        'unique': 'Única',
        'monthly': 'Mensual',
        'annual': 'Anual',
        'biennial': 'Bienal'
    };
    return `<span class="billing-type-badge">${labels[type] || type}</span>`;
}

function formatDate(dateStr) {
    if (!dateStr || dateStr === '0000-00-00') return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-CL');
}

function formatCurrency(amount) {
    if (!amount) return '$0';
    return '$' + Number(amount).toLocaleString('es-CL', { maximumFractionDigits: 0 });
}

function formatCurrencyWithSymbol(amount, currency) {
    if (!amount) return '-';

    currency = (currency || '').toUpperCase();

    if (currency === 'UF') {
        return 'UF ' + Number(amount).toLocaleString('es-CL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else if (currency === 'USD') {
        return 'US$ ' + Number(amount).toLocaleString('es-CL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        return '$' + Number(amount).toLocaleString('es-CL', { maximumFractionDigits: 0 });
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showLoading(show) {
    document.getElementById('loading_overlay').style.display = show ? 'flex' : 'none';
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export to CSV
function exportCSV() {
    const billingTypes = getSelectedBillingTypes();
    const severity = document.getElementById('severity_filter').value;
    const search = document.getElementById('search_input').value;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'ajax_handler.php';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'export_csv';
    form.appendChild(actionInput);

    const billingInput = document.createElement('input');
    billingInput.type = 'hidden';
    billingInput.name = 'billing_type';
    billingInput.value = JSON.stringify(billingTypes);
    form.appendChild(billingInput);

    const severityInput = document.createElement('input');
    severityInput.type = 'hidden';
    severityInput.name = 'severity';
    severityInput.value = severity;
    form.appendChild(severityInput);

    const searchInput = document.createElement('input');
    searchInput.type = 'hidden';
    searchInput.name = 'search';
    searchInput.value = search;
    form.appendChild(searchInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    console.log('📥 Exporting CSV...');
}

// Export client detail to CSV
function exportClientDetail(accountId) {
    const billingTypes = getSelectedBillingTypes();

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'ajax_handler.php';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'export_client_detail';
    form.appendChild(actionInput);

    const accountInput = document.createElement('input');
    accountInput.type = 'hidden';
    accountInput.name = 'account_id';
    accountInput.value = accountId;
    form.appendChild(accountInput);

    const billingInput = document.createElement('input');
    billingInput.type = 'hidden';
    billingInput.name = 'billing_type';
    billingInput.value = JSON.stringify(billingTypes);
    form.appendChild(billingInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    console.log('📥 Exporting client detail for account:', accountId);
}


// Global variable to track current sort
let currentSort = {
    column: null,
    direction: 'asc'
};

// Sort table by column
function sortTable(column) {
    // Toggle direction if clicking same column
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }

    // Get current data from global variable (we need to store it)
    if (!window.currentTableData) {
        console.warn('No data to sort');
        return;
    }

    const data = [...window.currentTableData];

    // Sort data
    data.sort((a, b) => {
        let aVal, bVal;

        if (column === 'severity') {
            // Sort by severity: error > warning > ok
            const severityOrder = { 'error': 3, 'warning': 2, 'ok': 1 };
            aVal = severityOrder[a.severity] || 0;
            bVal = severityOrder[b.severity] || 0;
        } else {
            // Get values directly from client object
            aVal = a[column];
            bVal = b[column];

            // Handle different data types
            if (column === 'cantidad_servicios' || column === 'total_cotizaciones' || column === 'total_facturas' || column === 'total_nv_bsale' || column === 'total_uf_sweet' || column === 'total_uf_bsale' || column === 'diferencia' || column === 'porcentaje_diferencia') {
                // Numeric columns
                aVal = parseFloat(aVal) || 0;
                bVal = parseFloat(bVal) || 0;
            } else {
                // String columns
                aVal = (aVal || '').toString().toLowerCase();
                bVal = (bVal || '').toString().toLowerCase();
            }
        }

        // Compare
        if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });

    // Update sort indicators
    document.querySelectorAll('th .sort-indicator').forEach(indicator => {
        indicator.textContent = '';
    });

    const currentHeader = document.querySelector(`th[data-sort="${column}"] .sort-indicator`);
    if (currentHeader) {
        currentHeader.textContent = currentSort.direction === 'asc' ? ' ▲' : ' ▼';
    }

    // Re-render table
    updateTable(data);
}


function showClientDetails(accountId) {
    // Find client data in current table data
    const clientData = window.currentTableData.find(c => c.account_id === accountId);

    if (!clientData) {
        alert('No se encontraron datos del cliente');
        return;
    }

    const modal = document.getElementById('invoice_details_modal');
    const modalBody = document.getElementById('invoice_details_body');

    // Get selected billing types to filter services
    const selectedBillingTypes = getSelectedBillingTypes().map(type => type.toUpperCase());

    // Filter services by selected billing types
    const filteredServices = clientData.services.filter(service => {
        const serviceEstado = (service.estado || '').toUpperCase();
        return selectedBillingTypes.includes(serviceEstado);
    });

    // Build services table
    let html = `
        <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid rgba(255,255,255,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h3 style="margin: 0; color: #ffffff;">${escapeHtml(clientData.razon_social)}</h3>
                <button onclick="exportClientDetail('${accountId}')" 
                        style="background: var(--accent); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 0.9em; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 6px;"
                        onmouseover="this.style.background='#5a7fff'"
                        onmouseout="this.style.background='var(--accent)'">
                    📥 Exportar Detalle
                </button>
            </div>
            <p style="margin: 0; color: #cccccc; font-size: 0.9em;">RUT: ${escapeHtml(clientData.rut)}</p>
            <p style="margin: 5px 0 0 0; color: #cccccc; font-size: 0.9em;">Servicios: ${filteredServices.length}</p>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: rgba(255,255,255,0.05); border-bottom: 2px solid rgba(255,255,255,0.1);">
                    <th style="padding: 12px; text-align: left; color: #ffffff;">Servicio</th>
                    <th style="padding: 12px; text-align: center; color: #ffffff;">Cant.</th>
                    <th style="padding: 12px; text-align: center; color: #ffffff;">Estado</th>
                    <th style="padding: 12px; text-align: center; color: #ffffff;">Cotización</th>
                    <th style="padding: 12px; text-align: center; color: #ffffff;">Factura</th>
                    <th style="padding: 12px; text-align: center; color: #ffffff;">NV BSale</th>
                </tr>
            </thead>
            <tbody>
    `;

    let servicesWithoutInvoice = 0;

    // Initialize totals
    let totalCantidad = 0;
    let totalCotizaciones = 0;
    let totalFacturas = 0;
    let totalBSale = 0;

    filteredServices.forEach((service, index) => {
        const rowBg = index % 2 === 0 ? 'transparent' : 'rgba(255,255,255,0.03)';
        const nvNum = service.nv_bsale || '-';
        const bsaleData = clientData.bsale_data[nvNum];

        // Detect if service has quote but no invoice
        const hasQuote = service.cotizacion && service.cotizacion !== '-';
        const hasInvoice = service.factura && service.factura !== '-';
        const missingInvoice = hasQuote && !hasInvoice;

        if (missingInvoice) {
            servicesWithoutInvoice++;
        }

        // Apply alert styling for missing invoice
        let alertStyle = '';
        if (missingInvoice) {
            alertStyle = 'background: rgba(255, 193, 7, 0.15); border-left: 3px solid #ffc107;';
        } else {
            alertStyle = `background: ${rowBg};`;
        }

        // Create NV link if exists in BSale - show service value from BSale line items
        let nvDisplay = nvNum;

        // Check if NV is invalid (>= 9000000 means "Sin NV" - no invoice)
        const nvNumInt = parseInt(nvNum);
        let bsaleValueNumeric = 0;

        if (!isNaN(nvNumInt) && nvNumInt >= 9000000) {
            nvDisplay = `<span style="color: #999; font-style: italic;">Sin NV</span>`;
        } else if (bsaleData && bsaleData.url_view) {
            // Try to find the matching BSale line item for this service
            let bsaleLineValue = null;
            if (bsaleData.lines && bsaleData.lines.length > 0) {
                // Match by service name (case-insensitive, partial match)
                const serviceName = service.servicio_nombre.toLowerCase();
                const matchingLine = bsaleData.lines.find(line => {
                    const lineDesc = line.description.toLowerCase();
                    // Check if service name is contained in line description or vice versa
                    return lineDesc.includes(serviceName) || serviceName.includes(lineDesc);
                });

                if (matchingLine) {
                    bsaleLineValue = matchingLine.total_uf;
                    bsaleValueNumeric = parseFloat(bsaleLineValue);
                }
            }

            // Use BSale line value if found, otherwise fall back to service value
            const displayValue = bsaleLineValue !== null ? bsaleLineValue : service.valor_uf;

            // Show conversion label if value was converted from CLP or USD
            const conversionLabel = bsaleData.conversion_label ? ` <span style="color: #ffa500;">(${bsaleData.conversion_label})</span>` : '';

            nvDisplay = `<a href="${bsaleData.url_view}" target="_blank" style="color: #4a9eff; text-decoration: none; font-weight: 600;">${nvNum}</a><br><span style="font-size: 0.85em; color: #999;">UF ${formatNumber(displayValue)}${conversionLabel}</span>`;
        } else if (nvNum !== '-') {
            nvDisplay = `${nvNum}<br><span style="font-size: 0.85em; color: #ff9999;">No encontrado</span>`;
        }


        // Create cotizacion link with value
        let cotiDisplay = service.cotizacion || '-';
        if (service.url_cotizacion) {
            cotiDisplay = `<a href="${service.url_cotizacion}" target="_blank" style="color: #4a9eff; text-decoration: none;">${service.cotizacion}</a><br><span style="font-size: 0.85em; color: #999;">UF ${formatNumber(service.valor_uf)}</span>`;
            // Show original USD value if converted
            if (service.currency === 'USD') {
                cotiDisplay += `<br><small style="color: #888;">(${formatNumber(service.valor_original)} USD)</small>`;
            }
        } else if (service.cotizacion) {
            cotiDisplay = `${service.cotizacion}<br><span style="font-size: 0.85em; color: #999;">UF ${formatNumber(service.valor_uf)}</span>`;
            if (service.currency === 'USD') {
                cotiDisplay += `<br><small style="color: #888;">(${formatNumber(service.valor_original)} USD)</small>`;
            }
        }

        // Create factura link with value
        let facDisplay = service.factura || '-';
        if (service.url_factura) {
            facDisplay = `<a href="${service.url_factura}" target="_blank" style="color: #4a9eff; text-decoration: none;">${service.factura}</a><br><span style="font-size: 0.85em; color: #999;">UF ${formatNumber(service.valor_uf)}</span>`;
            if (service.currency === 'USD') {
                facDisplay += `<br><small style="color: #888;">(${formatNumber(service.valor_original)} USD)</small>`;
            }
        } else if (service.factura) {
            facDisplay = `${service.factura}<br><span style="font-size: 0.85em; color: #999;">UF ${formatNumber(service.valor_uf)}</span>`;
            if (service.currency === 'USD') {
                facDisplay += `<br><small style="color: #888;">(${formatNumber(service.valor_original)} USD)</small>`;
            }
        }

        // Build estado badge with warning if missing invoice
        let estadoBadge = `<span style="background: rgba(74, 158, 255, 0.2); padding: 2px 8px; border-radius: 4px; font-size: 0.85em;">${escapeHtml(service.estado)}</span>`;
        if (missingInvoice) {
            estadoBadge += `<br><span style="background: rgba(255, 193, 7, 0.3); padding: 2px 8px; border-radius: 4px; font-size: 0.75em; color: #ffc107; margin-top: 4px; display: inline-block;">⚠️ Sin Factura</span>`;
        }

        // Accumulate totals
        totalCantidad += parseInt(service.cantidad) || 0;
        totalCotizaciones += parseFloat(service.valor_uf) || 0;
        if (hasInvoice) {
            totalFacturas += parseFloat(service.valor_uf) || 0;
        }

        html += `
            <tr style="${alertStyle} border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 12px; color: #ffffff;">${escapeHtml(service.servicio_nombre)}</td>
                <td style="padding: 12px; text-align: center; color: #cccccc;">${service.cantidad}</td>
                <td style="padding: 12px; text-align: center; color: #cccccc;">
                    ${estadoBadge}
                </td>
                <td style="padding: 12px; text-align: center; color: #cccccc;">${cotiDisplay}</td>
                <td style="padding: 12px; text-align: center; color: #cccccc;">${facDisplay}</td>
                <td style="padding: 12px; text-align: center; color: #cccccc;">${nvDisplay}</td>
            </tr>
        `;
    });

    // Calculate totalBSale using BSale document totals (neto_uf) when available
    // IMPORTANT: Only sum NVs from filtered services, not all NVs in bsale_data

    // Step 1: Collect unique NVs from filtered services
    const nvsInFilteredServices = new Set();
    filteredServices.forEach(service => {
        const nvNum = service.nv_bsale || '-';
        const nvNumInt = parseInt(nvNum);

        // Only track valid NVs
        if (nvNum !== '-' && !isNaN(nvNumInt) && nvNumInt < 9000000) {
            nvsInFilteredServices.add(nvNum);
        }
    });

    // Step 2: For each unique NV in filtered services, use BSale neto_uf or sum services
    const nvProcessed = new Set();
    nvsInFilteredServices.forEach(nvNum => {
        const bsaleData = clientData.bsale_data[nvNum];

        if (bsaleData && bsaleData.neto_uf) {
            // Use BSale document total (neto_uf)
            totalBSale += parseFloat(bsaleData.neto_uf) || 0;
            nvProcessed.add(nvNum);
            console.log(`NV ${nvNum}: Using BSale neto_uf = ${bsaleData.neto_uf}`);
            console.log(`  Full BSale data:`, bsaleData);
        } else {
            // Sum all services with this NV (fallback when no BSale data)
            let nvServiceTotal = 0;
            filteredServices.forEach(service => {
                if (service.nv_bsale === nvNum) {
                    nvServiceTotal += parseFloat(service.valor_uf) || 0;
                }
            });
            totalBSale += nvServiceTotal;
            nvProcessed.add(nvNum);
            console.log(`NV ${nvNum}: Using service sum = ${nvServiceTotal} (no BSale data)`);
        }
    });

    console.log(`Total NV BSale: ${totalBSale}`);

    html += `
            </tbody>
        </table>
    `;

    // Add informative note if there are services without invoice
    if (servicesWithoutInvoice > 0) {
        html += `
            <div style="margin: 15px 0; padding: 12px; background: rgba(255, 193, 7, 0.1); border-left: 3px solid #ffc107; border-radius: 4px;">
                <div style="display: flex; align-items: center; color: #ffc107;">
                    <span style="font-size: 1.2em; margin-right: 8px;">ℹ️</span>
                    <strong>Servicios sin factura (${servicesWithoutInvoice})</strong>
                </div>
                <p style="margin: 8px 0 0 0; color: #cccccc; font-size: 0.9em;">
                    Los servicios marcados con "⚠️ Sin Factura" se comparan usando el valor de <strong>Cotización</strong> vs <strong>NV BSale</strong>.
                </p>
            </div>
        `;
    }

    html += `
        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid rgba(255,255,255,0.1); color: #ffffff;">
            <h4 style="margin: 0 0 12px 0; color: #ffffff; font-size: 1.1em;">📊 TOTALES</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                <div style="background: rgba(102, 126, 234, 0.1); padding: 12px; border-radius: 6px; border-left: 3px solid #667eea;">
                    <div style="font-size: 0.85em; color: #cccccc; margin-bottom: 4px;">Total Cotizaciones</div>
                    <div style="font-size: 1.2em; font-weight: 700; color: #667eea;">UF ${formatNumber(totalCotizaciones)}</div>
                </div>
                <div style="background: rgba(74, 158, 255, 0.1); padding: 12px; border-radius: 6px; border-left: 3px solid #4a9eff;">
                    <div style="font-size: 0.85em; color: #cccccc; margin-bottom: 4px;">Total Facturas</div>
                    <div style="font-size: 1.2em; font-weight: 700; color: #4a9eff;">UF ${formatNumber(totalFacturas)}</div>
                </div>
                <div style="background: rgba(245, 87, 108, 0.1); padding: 12px; border-radius: 6px; border-left: 3px solid #f5576c;">
                    <div style="font-size: 0.85em; color: #cccccc; margin-bottom: 4px;">Total NV BSale</div>
                    <div style="font-size: 1.2em; font-weight: 700; color: #f5576c;">UF ${formatNumber(totalBSale)}</div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid rgba(255,255,255,0.1); color: #ffffff;">
            <h4 style="margin: 0 0 12px 0; color: #ffffff; font-size: 1.1em;">📈 RESUMEN</h4>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span>Total UF Sweet:</span>
                <strong style="color: #4a9eff;">UF ${formatNumber(totalFacturas > 0 ? totalFacturas : totalCotizaciones)}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span>Total UF BSale:</span>
                <strong style="color: #4a9eff;">UF ${formatNumber(totalBSale)}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.1);">
                <span><strong>Diferencia:</strong></span>
                <strong style="${getDiferenciaStyle((totalFacturas > 0 ? totalFacturas : totalCotizaciones) - totalBSale)}">UF ${formatNumber((totalFacturas > 0 ? totalFacturas : totalCotizaciones) - totalBSale)} (${formatNumber(totalBSale > 0 ? Math.abs(((totalFacturas > 0 ? totalFacturas : totalCotizaciones) - totalBSale) / totalBSale * 100) : 100)}%)</strong>
            </div>
    `;

    // Show issues if any
    if (clientData.issues && clientData.issues.length > 0) {
        html += `
            <div style="margin-top: 15px; padding: 12px; background: rgba(255, 100, 100, 0.1); border-left: 3px solid var(--error); border-radius: 4px;">
                <strong style="color: var(--error);">⚠️ Problemas detectados:</strong>
                <ul style="margin: 8px 0 0 20px; color: #ffcccc;">
        `;
        clientData.issues.forEach(issue => {
            html += `<li>${escapeHtml(issue)}</li>`;
        });
        html += `
                </ul>
            </div>
        `;
    }

    html += `</div>`;

    modalBody.innerHTML = html;
    modal.style.display = 'flex';
}


// Close invoice details modal
function closeInvoiceDetailsModal() {
    document.getElementById('invoice_details_modal').style.display = 'none';
}

// Close modal when clicking outside
document.addEventListener('click', function (event) {
    const modal = document.getElementById('invoice_details_modal');
    if (event.target === modal) {
        closeInvoiceDetailsModal();
    }
});

// Multi-select dropdown functions
function toggleBillingTypeDropdown() {
    const dropdown = document.getElementById('billing_type_dropdown');
    const button = document.getElementById('billing_type_button');
    const isVisible = dropdown.style.display === 'block';

    dropdown.style.display = isVisible ? 'none' : 'block';
    button.classList.toggle('open', !isVisible);
}

function updateBillingTypeFilter() {
    const checkboxes = document.querySelectorAll('#billing_type_dropdown input[type="checkbox"]');
    const selected = Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    const label = document.getElementById('billing_type_label');
    if (selected.length === 0) {
        label.textContent = 'Seleccione tipos';
    } else if (selected.length === 1) {
        label.textContent = selected[0];
    } else if (selected.length === 5) {
        label.textContent = 'Todos los tipos';
    } else {
        label.textContent = `${selected.length} tipos seleccionados`;
    }

    // Trigger filter update
    applyFilters();
}

function getSelectedBillingTypes() {
    const checkboxes = document.querySelectorAll('#billing_type_dropdown input[type="checkbox"]');
    const selected = Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    return selected.length > 0 ? selected : ['Mensual']; // Default to Mensual if none selected
}

console.log('✅ Reconciliation JavaScript loaded');

// Load currency indicators
function loadCurrencyIndicators() {
    fetch('includes/get_currency_rates.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const ufValue = parseFloat(data.uf_value || 0);
                const usdValue = parseFloat(data.usd_value || 0);
                const date = data.date || '';

                // Format numbers with Chilean format (. for thousands, , for decimals)
                const ufFormatted = ufValue.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                const usdFormatted = usdValue.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                const currencyHTML = `UF ${ufFormatted}&nbsp;&nbsp;US ${usdFormatted}&nbsp;&nbsp;Al ${date}`;
                document.getElementById('currency_indicators').innerHTML = currencyHTML;
            } else {
                document.getElementById('currency_indicators').innerHTML = '<span class="currency-loading">Error al cargar tasas</span>';
            }
        })
        .catch(error => {
            console.error('Error loading currency rates:', error);
            document.getElementById('currency_indicators').innerHTML = '<span class="currency-loading">Error al cargar tasas</span>';
        });
}

// Load currency indicators on page load
document.addEventListener('DOMContentLoaded', function () {
    loadCurrencyIndicators();
});

// Filter by severity when clicking dashboard cards
function filterBySeverity(severity) {
    const severityFilter = document.getElementById("severity_filter");
    severityFilter.value = severity;

    // Trigger the filter change
    loadComparison();

    // Visual feedback - scroll to results
    setTimeout(() => {
        const resultsContainer = document.getElementById("results_container");
        if (resultsContainer && resultsContainer.style.display !== "none") {
            resultsContainer.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    }, 300);
}
