<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WHM Server Report - Icontel Intranet</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Detalle de Cuenta</div>
            <button class="modal-close" onclick="App.closeModal()">✕</button>
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
        </div>
        <div class="header-right">
            <span class="header-badge" id="timestamp">--</span>
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
            <button class="tab-btn active" data-tab="overview">📈 Resumen</button>
            <button class="tab-btn" data-tab="space">💾 Top Espacio</button>
            <button class="tab-btn" data-tab="activity">📡 Top Actividad</button>
            <button class="tab-btn" data-tab="inactive">⏸️ Inactivas</button>
            <button class="tab-btn" data-tab="all">📋 Todas</button>
        </nav>

        <!-- TAB: OVERVIEW -->
        <div class="tab-content active" id="tab-overview">
            
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
                        <option value="90">+90 días sin movimiento</option>
                        <option value="180">+180 días sin movimiento</option>
                        <option value="365">+1 año sin movimiento</option>
                    </select>
                    <div style="font-size:11px;color:var(--text-muted)">
                        <span class="inactive-dot ok"></span> Reciente &nbsp;
                        <span class="inactive-dot warning"></span> +30 días &nbsp;
                        <span class="inactive-dot critical"></span> +90 días
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
        <div class="tab-content" id="tab-all">
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        <span class="icon">📋</span> Todas las Cuentas
                        <span class="badge badge-info" id="filteredCount">--</span>
                    </div>
                </div>
                <div class="filter-bar">
                    <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Buscar usuario, dominio, email o plan..." oninput="App.filterAccounts()">
                    <select class="filter-select" id="statusFilter" onchange="App.filterAccounts()">
                        <option value="all">Todos los estados</option>
                        <option value="active">Solo activas</option>
                        <option value="suspended">Solo suspendidas</option>
                    </select>
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
                                <th data-sort="plan" onclick="App.sortTable('allAccounts','plan')">Plan</th>
                                <th data-sort="owner" onclick="App.sortTable('allAccounts','owner')">Owner</th>
                            </tr></thead>
                            <tbody id="allAccountsTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /appContent -->
</div><!-- /app-container -->

<script src="assets/js/app.js"></script>
</body>
</html>
