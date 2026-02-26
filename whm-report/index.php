<!DOCTYPE html>
<!--
============================================================
WHM Server Report - Dashboard Principal
============================================================
Archivo    : index.php
Path       : /home/icontel/public_html/intranet/whm-report/index.php
Versión    : 1.0.0
Fecha      : 2026-02-25 20:57:00
Proyecto   : WHM Server Report - Icontel Intranet
Autor      : Icontel Dev Team
============================================================
-->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WHM Server Report - Icontel Intranet</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">
</head>
<body>

<!-- LOADER -->
<div class="loader-overlay" id="loader">
    <div class="loader-spinner"></div>
    <div class="loader-text">Conectando con WHM API...</div>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="if(event.target===this)App.closeModal()">
    <div class="modal">
        <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div class="modal-title" id="modalTitle" style="flex:1;">Detalle de Cuenta</div>
            <button class="btn-refresh" id="modalExportBtn" onclick="App.exportDetailXLS()" style="background:var(--success,#22c55e);color:#000;margin-right:12px;font-size:12px;padding:4px 10px;height:auto;line-height:normal;border-radius:4px;">⬇ XLS</button>
            <button class="modal-close" onclick="App.closeModal()" style="font-size:20px;height:auto;padding:0;line-height:1;">✕</button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<!-- APP -->
<div class="app-container">
    
    <!-- HEADER -->
    <header class="app-header">
        <div class="header-left">
            <div class="header-icon">📊</div>
            <div>
                <div class="header-title">WHM Server <span>Report</span></div>
                <div class="header-sub">Icontel Intranet · Panel de Control</div>
            </div>
            <!-- TNA HEALTH WIDGET -->
            <div id="tnaHealthWidget" class="health-widget-compact" style="margin-left:24px; display:flex; gap:16px;">
                <!-- Populated by JS -->
            </div>
        </div>
        <div class="header-right">
            <div class="time-toggle" style="margin-right:15px;display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,0.7)">
                <input type="checkbox" id="toggle24h" checked style="cursor:pointer">
                <label for="toggle24h" style="cursor:pointer">24h</label>
            </div>
            <span class="header-badge" id="timestamp">--</span>
            <button class="btn-refresh" style="background:var(--success,#22c55e);color:#000;margin-right:8px" onclick="App.exportXLS()">⬇ XLS</button>
            <button class="btn-refresh" id="btnRefresh">⟳ Actualizar</button>
        </div>
    </header>

    <div id="appContent">
        
        <!-- SUMMARY CARDS -->
        <div class="summary-grid">
            <div class="summary-card green animate-in delay-1">
                <div class="card-label">Total Cuentas</div>
                <div class="card-value green" id="totalAccounts">--</div>
                <div class="card-sub">Cuentas cPanel</div>
            </div>
            <div class="summary-card blue animate-in delay-2">
                <div class="card-label">Activas</div>
                <div class="card-value blue" id="activeAccounts">--</div>
                <div class="card-sub">En funcionamiento</div>
            </div>
            <div class="summary-card red animate-in delay-3">
                <div class="card-label">Suspendidas</div>
                <div class="card-value red" id="suspendedAccounts">--</div>
                <div class="card-sub">Cuentas pausadas</div>
            </div>
            <div class="summary-card yellow animate-in delay-4">
                <div class="card-label">Sin Movimiento</div>
                <div class="card-value yellow" id="inactiveAccounts">--</div>
                <div class="card-sub">Sin bandwidth actual</div>
            </div>
            <div class="summary-card purple animate-in delay-5">
                <div class="card-label">Disco Usado</div>
                <div class="card-value purple" id="diskUsed">--</div>
                <div class="card-sub" id="diskPercent">--</div>
            </div>
        </div>

        <!-- DISK BAR -->
        <div class="disk-bar-container animate-in delay-6">
            <div class="disk-bar-header">
                <div class="disk-bar-title">Uso Total de Disco del Servidor</div>
                <div class="disk-bar-stats" id="diskBarStats">--</div>
            </div>
            <div class="disk-bar">
                <div class="disk-bar-fill" id="diskBarFill" style="width:0%"></div>
            </div>
        </div>

        <!-- TABS -->
        <nav class="tab-nav">
            <button class="tab-btn" data-tab="overview">📈 Resumen</button>
            <button class="tab-btn" data-tab="space">💾 Top Espacio</button>
            <button class="tab-btn" data-tab="activity">📡 Top Actividad</button>
            <button class="tab-btn" data-tab="inactive">⏸️ Inactivas</button>
            <button class="tab-btn active" data-tab="all">📋 Todas</button>
            <button class="tab-btn" data-tab="alerts">🚨 Alertas</button>
            <button class="tab-btn" data-tab="resellers" onclick="App.loadResellers()">👥 Resellers</button>
            <button class="tab-btn" data-tab="firewall" onclick="App.loadFirewall()">🔒 Firewall</button>
        </nav>

        <!-- TAB: OVERVIEW -->
        <div class="tab-content" id="tab-overview">
            
            <!-- TOP ESPACIO (mini) -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title"><span class="icon">💾</span> Top 10 — Uso de Espacio</div>
                    <div class="toggle-group" id="topSpaceToggleOverview">
                        <button class="toggle-btn active" data-mode="most" onclick="App.toggleTopSpace('most', this)">Más Espacio</button>
                        <button class="toggle-btn" data-mode="least" onclick="App.toggleTopSpace('least', this)">Menos Espacio</button>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-scroll">
                        <table>
                            <thead><tr>
                                <th style="width:50px">#</th>
                                <th>Usuario</th>
                                <th>Dominio</th>
                                <th>Usado</th>
                                <th>Límite</th>
                                <th>Uso %</th>
                                <th>Plan</th>
                            </tr></thead>
                            <tbody id="topSpaceTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TOP ACTIVIDAD (mini) -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title"><span class="icon">📡</span> Top 10 — Actividad (Bandwidth)</div>
                    <div class="toggle-group" id="topActivityToggleOverview">
                        <button class="toggle-btn active" data-mode="most" onclick="App.toggleTopActivity('most', this)">Más Actividad</button>
                        <button class="toggle-btn" data-mode="least" onclick="App.toggleTopActivity('least', this)">Menos Actividad</button>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-scroll">
                        <table>
                            <thead><tr>
                                <th style="width:50px">#</th>
                                <th>Usuario</th>
                                <th>Dominio</th>
                                <th>BW Usado</th>
                                <th>BW Límite</th>
                                <th>Proporción</th>
                                <th>Estado</th>
                            </tr></thead>
                            <tbody id="topActivityTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: SPACE -->
        <div class="tab-content" id="tab-space">
            <div class="section">
                <div class="section-header">
                    <div class="section-title"><span class="icon">💾</span> Top 10 — Uso de Espacio en Disco</div>
                    <div class="toggle-group" id="topSpaceToggle">
                        <button class="toggle-btn active" data-mode="most" onclick="App.toggleTopSpace('most', this)">Más Espacio</button>
                        <button class="toggle-btn" data-mode="least" onclick="App.toggleTopSpace('least', this)">Menos Espacio</button>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-scroll">
                        <table>
                            <thead><tr>
                                <th style="width:50px">#</th>
                                <th>Usuario</th>
                                <th>Dominio</th>
                                <th>Usado</th>
                                <th>Límite</th>
                                <th>Uso %</th>
                                <th>Plan</th>
                            </tr></thead>
                            <tbody id="topSpaceTableTab"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: ACTIVITY -->
        <div class="tab-content" id="tab-activity">
            <div class="section">
                <div class="section-header">
                    <div class="section-title"><span class="icon">📡</span> Top 10 — Movimiento (Bandwidth)</div>
                    <div class="toggle-group" id="topActivityToggle">
                        <button class="toggle-btn active" data-mode="most" onclick="App.toggleTopActivity('most', this)">Más Movimiento</button>
                        <button class="toggle-btn" data-mode="least" onclick="App.toggleTopActivity('least', this)">Menos Movimiento</button>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-scroll">
                        <table>
                            <thead><tr>
                                <th style="width:50px">#</th>
                                <th>Usuario</th>
                                <th>Dominio</th>
                                <th>BW Usado</th>
                                <th>BW Límite</th>
                                <th>Proporción</th>
                                <th>Estado</th>
                            </tr></thead>
                            <tbody id="topActivityTableTab"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: INACTIVE -->
        <div class="tab-content" id="tab-inactive">
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        <span class="icon">⏸️</span> Cuentas Sin Movimiento
                        <span class="badge badge-warning" id="inactiveCount">--</span>
                    </div>
                </div>
                <div class="filter-bar">
                    <select class="filter-select" id="inactiveDaysFilter" onchange="App.filterInactive()">
                        <option value="0">Todas las inactivas</option>
                        <option value="30">+30 días sin movimiento</option>
                        <option value="60">+60 días sin movimiento</option>
                        <option value="180">+180 días sin movimiento</option>
                        <option value="365">+1 año sin movimiento</option>
                    </select>
                    <div style="font-size:11px;color:var(--text-muted)">
                        <span class="inactive-dot ok"></span> Reciente &nbsp;
                        <span class="inactive-dot warning"></span> +30 días &nbsp;
                        <span class="inactive-dot critical"></span> +60 días
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-scroll">
                        <table>
                            <thead><tr>
                                <th>Usuario</th>
                                <th>Dominio</th>
                                <th>Disco</th>
                                <th>Antigüedad</th>
                                <th>Creada</th>
                                <th>Estado</th>
                                <th>Plan</th>
                            </tr></thead>
                            <tbody id="inactiveTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: ALL ACCOUNTS -->
        <div class="tab-content active" id="tab-all">
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        <span class="icon">📋</span> Todas las Cuentas
                        <span class="badge badge-info" id="filteredCount">--</span>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="App.exportXLS()">
                        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Exportar XLS
                    </button>
                    <button class="btn btn-secondary" onclick="App.exportTNAMatrix()" title="Descarga la matriz de cumplimiento TNA">
                        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Matriz TNA
                    </button>
                </div>
                <div class="filter-bar">
                    <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Buscar usuario, dominio, email o plan..." oninput="App.filterAccounts()">
                    <select class="filter-select" id="statusFilter" onchange="App.filterAccounts()">
                        <option value="all">Todos los estados</option>
                        <option value="active">Solo activas</option>
                        <option value="suspended">Solo suspendidas</option>
                    </select>
                </div>
                
                <div id="ownerFilterContainer" style="margin-bottom:16px; display:flex; gap:12px; align-items:center; flex-wrap:wrap; background:var(--bg-card); padding:10px 15px; border-radius:8px; border:1px solid var(--border);">
                    <!-- Generado por JS -->
                </div>
                <div class="table-container">
                    <div class="table-scroll">
                        <table>
                            <thead id="allAccountsHeader"><tr>
                                <th data-sort="user" onclick="App.sortTable('allAccounts','user')">Usuario</th>
                                <th data-sort="domain" onclick="App.sortTable('allAccounts','domain')">Dominio</th>
                                <th data-sort="disk_used" onclick="App.sortTable('allAccounts','disk_used')">Disco</th>
                                <th data-sort="disk_percent" onclick="App.sortTable('allAccounts','disk_percent')">Uso %</th>
                                <th data-sort="bw_used" onclick="App.sortTable('allAccounts','bw_used')">Bandwidth</th>
                                <th data-sort="suspended" onclick="App.sortTable('allAccounts','suspended')">Estado</th>
                                <th data-sort="email_count" onclick="App.sortTable('allAccounts','email_count')">Emails</th>
                                <th data-sort="plan" onclick="App.sortTable('allAccounts','plan')">Plan</th>
                                <th data-sort="owner" onclick="App.sortTable('allAccounts','owner')">Owner</th>
                                <th>Informe</th>
                            </tr></thead>
                            <tbody id="allAccountsTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: ALERTS -->
        <div class="tab-content" id="tab-alerts">
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        <span class="icon">🚨</span> Alertas de Migración
                        <span class="badge badge-warning" id="alertCount" style="background:var(--warning,#f59e0b);color:#fff;">--</span>
                    </div>
                </div>
                <div style="background:var(--warning-dim,#fffbeb);border:1px solid var(--warning,#f59e0b);color:#92400e;padding:12px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                    <strong>Sugerencia de Migración:</strong> Las cuentas a continuación tienen un uso crítico de espacio en disco (> 80%) o superan los 15 GB en alguna de sus casillas (máximo recomendado para WHM). Se recomienda contactar al cliente para evaluar una migración a <strong>Google Workspace</strong> o <strong>Microsoft 365</strong> para asegurar la estabilidad del servicio y su correo.
                </div>
                <div class="table-container">
                    <div class="table-scroll">
                        <table>
                            <thead><tr>
                                <th>Usuario</th>
                                <th>Dominio</th>
                                <th>Disco Usado</th>
                                <th>Uso %</th>
                                <th>Límite</th>
                                <th>Estado</th>
                                <th style="text-align:center;">Informe</th>
                            </tr></thead>
                            <tbody id="alertsTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: RESELLERS -->
        <div class="tab-content" id="tab-resellers">
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        <span class="icon">👥</span> Gestión de Resellers
                        <span class="badge badge-info" id="resellerCount">--</span>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button class="btn-refresh" onclick="App.loadResellers()" style="font-size:13px">⟳ Recargar</button>
                        <button onclick="App.showCreateResellerModal()" style="padding:6px 14px;background:var(--accent);color:#000;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600">➕ Nuevo Reseller</button>
                    </div>
                </div>

                <!-- RESELLER CARDS -->
                <div id="resellerCards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:24px">
                    <p style="color:var(--text-muted);font-size:13px">Cargando resellers...</p>
                </div>

                <!-- RESELLER TABLE -->
                <div class="table-container">
                    <div class="table-scroll">
                        <table>
                            <thead><tr>
                                <th>#</th>
                                <th>Reseller</th>
                                <th>Cuentas</th>
                                <th>Disco Usado</th>
                                <th>Límite Disco</th>
                                <th>BW Usado</th>
                                <th>Estado</th>
                                <th style="text-align:center">Acciones</th>
                            </tr></thead>
                            <tbody id="resellerTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: FIREWALL -->
        <div class="tab-content" id="tab-firewall">
            <div class="firewall-grid" style="display:grid;grid-template-columns: 1.5fr 1fr;gap:20px">
                <!-- BLOQUEOS -->
                <div class="section">
                    <div class="section-header">
                        <div class="section-title"><span class="icon">🔒</span> Bloqueos Recientes / Brute Force</div>
                        <button class="btn-refresh" onclick="App.loadFirewall()" style="font-size:13px">⟳ Recargar</button>
                    </div>
                    <!-- Buscador -->
                    <input type="text" id="firewallSearch" placeholder="🔍 Buscar IP..."
                        oninput="App.filterFirewall(this.value)"
                        style="width:100%;padding:8px 12px;margin-bottom:12px;background:var(--surface);border:1px solid var(--border);border-radius:8px;color:var(--text-primary);font-size:13px;box-sizing:border-box">
                    <!-- Tabla de IPs -->
                    <div id="firewallTableContainer">
                        <p style="color:var(--text-muted);font-size:13px">Cargando...</p>
                    </div>
                </div>

                <!-- LISTA BLANCA -->
                <div class="section">
                    <div class="section-header">
                        <div class="section-title"><span class="icon">✅</span> Lista Blanca (Whitelist)</div>
                        <button class="btn-refresh" onclick="App.loadWhitelisted()" style="font-size:13px">⟳ Recargar</button>
                    </div>
                    <!-- Agregar a whitelist -->
                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;padding:12px;background:var(--accent-dim);border-radius:10px;border:1px solid var(--accent)">
                        <span style="font-size:12px;color:var(--accent);font-weight:600">Añadir a Lista Blanca</span>
                        <div style="display:flex;gap:6px">
                            <input type="text" id="manualWhitelistIp" placeholder="IP (ej: 181.xx...)"
                                style="flex:1;padding:6px 10px;background:var(--bg-primary);border:1px solid var(--border);border-radius:6px;color:var(--text-primary);font-size:12px">
                            <button onclick="App.manualWhitelist()" style="padding:6px 12px;background:var(--accent);color:#000;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600">
                                + Añadir
                            </button>
                        </div>
                    </div>
                    <!-- Tabla Whitelist -->
                    <div id="whitelistTableContainer">
                        <p style="color:var(--text-muted);font-size:13px">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /appContent -->
</div><!-- /app-container -->

<script src="assets/js/app.js?v=<?= time() ?>"></script>
</body>
</html>
