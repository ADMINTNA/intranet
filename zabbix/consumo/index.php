<?php
require_once '../z_session.php';
// ── Security headers ──────────────────────────────────────────
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; frame-ancestors 'self' https://intranet.icontel.cl;");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zabbix Consumo Explorer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --border: rgba(255,255,255,0.06);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --accent: #3b82f6;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --provider-bg: rgba(59,130,246,0.08);
            --header-bg: #1e293b;
        }

        body.light-theme {
            --bg: #f8fafc;
            --card: #ffffff;
            --border: rgba(0,0,0,0.08);
            --text-main: #0f172a;
            --text-dim: #475569;
            --accent: #2563eb;
            --provider-bg: rgba(37,99,235,0.04);
            --header-bg: #ffffff;
            --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }

        body.light-theme {
            background-image: 
                radial-gradient(ellipse 80% 50% at 20% 10%, rgba(76,95,196,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(38,166,154,0.05) 0%, transparent 60%);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.5;
            padding: 24px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .title-group h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .title-group p {
            color: var(--text-dim);
            font-size: 14px;
        }

        .search-container {
            flex: 0 0 400px;
            position: relative;
        }

        input, select {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 12px 18px;
            border-radius: 14px;
            color: var(--text-main);
            width: 100%;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            backdrop-filter: blur(4px);
        }

        input:focus, select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .stats-summary {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 20px 24px;
            border-radius: 16px;
            flex: 1;
            min-width: 180px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(8px);
            transition: transform 0.2s, background 0.2s, box-shadow 0.2s;
            box-shadow: var(--card-shadow, none);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--accent);
            opacity: 0.8;
        }

        .stat-card:hover { 
            transform: translateY(-2px);
            background: var(--bg-card-h, var(--card));
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .stat-label { font-size: 11px; color: var(--text-dim); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--text-main); font-family: 'JetBrains Mono', monospace; }

        .list-container {
            display: flex;
            flex-direction: column;
            gap: 12px;

        }

        .theme-toggle {
            background: none;
            border: 1px solid var(--border);
            color: var(--text-dim);
            padding: 8px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .theme-toggle:hover {
            color: var(--text-main);
            border-color: var(--accent);
            background: rgba(59,130,246,0.05);
        }

        .theme-toggle svg { width: 20px; height: 20px; }
        .light-icon { display: none; }
        .dark-icon { display: block; }

        body.light-theme .light-icon { display: block; }
        body.light-theme .dark-icon { display: none; }

        .provider-name { font-weight: 700; flex: 1; }
        .provider-stats { font-family: 'JetBrains Mono', monospace; font-size: 13px; display: flex; gap: 24px; }
        
        .section-header {
            margin: 40px 0 20px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .section-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .section-header .count-badge {
            background: rgba(255,255,255,0.05);
            padding: 2px 10px;
            border-radius: 100px;
            font-size: 12px;
            color: var(--text-dim);
        }

        .infra-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 32px;
        }

        .infra-row {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: var(--card-shadow, none);
        }

        .infra-row:hover { 
            background: var(--bg-card-h, var(--card));
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        body.light-theme .infra-row {
            background: #ffffff;
        }

        .host-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            transition: transform 0.2s, background 0.2s;
        }

        .host-card:hover {
            background: #232f45;
        }

        .host-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .host-name { font-size: 16px; font-weight: 700; }
        .host-meta { font-size: 12px; color: var(--text-dim); margin-top: 2px; }
        .service-code { 
            background: rgba(59,130,246,0.1);
            color: var(--accent);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
        }

        .iface-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .iface-card {
            background: rgba(0,0,0,0.2);
            padding: 12px;
            border-radius: 12px;
            border: 1px solid var(--border);
            transition: background 0.2s;
        }

        body.light-theme .iface-card {
            background: #f1f5f9;
            border-color: rgba(0,0,0,0.03);
        }

        .iface-name { font-size: 12px; font-weight: 600; margin-bottom: 8px; color: var(--text-dim); }

        .traffic-vals {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 8px;
        }

        .val-in { color: var(--success); }
        .val-out { color: var(--accent); }

        .usage-container {
            height: 6px;
            background: rgba(255,255,255,0.05);
            border-radius: 3px;
            overflow: hidden;
        }

        .usage-bar {
            height: 100%;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .empty-state {
            text-align: center;
            padding: 60px;
            color: var(--text-dim);
        }

        .loader {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        [v-cloak] { display: none; }

        .group-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: all 0.2s;
            box-shadow: var(--card-shadow, none);
        }

        .group-header {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            gap: 16px;
        }

        .group-header:hover { background: rgba(255,255,255,0.02); }

        .group-toggle {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.05);
            border-radius: 4px;
            font-size: 10px;
            transition: transform 0.2s;
        }

        .group-card.is-expanded .group-toggle { transform: rotate(90-deg); }

        .group-info { flex: 1; }
        .group-name { font-size: 16px; font-weight: 700; color: var(--text-main); }
        .group-count { font-size: 11px; color: var(--text-dim); }

        .group-aggregate {
            display: flex;
            gap: 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 600;
        }

        .group-details {
            border-top: 1px solid var(--border);
            background: rgba(0,0,0,0.15);
            display: none;
            padding: 20px;
        }

        body.light-theme .group-details {
            background: #f8fafc;
        }

        .group-card.is-expanded .group-details { display: block; }

        .host-item {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        body.light-theme .host-item {
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .host-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

        footer {
            margin-top: 40px;
            padding: 20px 0;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-dim);
            font-size: 13px;
        }

        .refresh-control {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: var(--text-dim);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .refresh-control select {
            width: auto;
            padding: 6px 10px;
            font-size: 11px;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
        }

        .ranking-table {
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .ranking-table th {
            text-align: left;
            padding: 12px 20px;
            background: rgba(255,255,255,0.03);
            font-size: 11px;
            font-weight: 600;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
        }

        .ranking-table td {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .ranking-table tr:last-child td { border-bottom: none; }
        .ranking-table tr:hover { background: rgba(59,130,246,0.05); }

        .rank-number {
            font-weight: 700;
            color: var(--accent);
            width: 40px;
        }

        .rank-name { font-weight: 600; }
        .rank-val { font-family: 'JetBrains Mono', monospace; font-weight: 600; text-align: right; }

        /* Hub Integration */
        body.in-frame header { display: none !important; }
        body.in-frame .container { margin-top: 0 !important; padding-top: 10px !important; }

        /* Controls Bar */
        .controls-bar {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 12px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .controls-bar .search-container {
            flex: 1;
            position: relative;
        }

        .controls-bar input, .controls-bar select {
            padding: 10px 14px;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .controls-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="container" id="app">
        <div class="controls-bar">
            <div class="search-container">
                <input type="text" id="search" placeholder="Filtrar por host, grupo o código..." autocomplete="off">
            </div>
            
            <div style="display: flex; gap: 12px; align-items: center;">
                <select id="iface-filter" onchange="render()" style="width: 150px;">
                    <option value="all">Ver todas</option>
                    <option value="active" selected>Solo activos</option>
                    <option value="zero">Sin Consumo</option>
                </select>

                <select id="range-selector" onchange="fetchData()" style="width: 140px;">
                    <option value="3600" selected>Última Hora</option>
                    <option value="86400">Últimas 24h</option>
                    <option value="604800">Últimos 7 días</option>
                    <option value="2592000">Últimos 30 días</option>
                </select>

                <div class="refresh-control" style="padding-left: 16px; border-left: 1px solid var(--border); display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; font-weight: 700; color: var(--text-dim)">REFRESCO:</span>
                    <select id="refresh-interval" onchange="updateRefreshTimer()" style="width: 80px; padding: 6px;">
                        <option value="0">OFF</option>
                        <option value="30000" selected>30s</option>
                        <option value="60000">1m</option>
                        <option value="300000">5m</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="stats-summary">
            <div class="stat-card">
                <div class="stat-label">Hosts Monitoreados</div>
                <div class="stat-value" id="stat-total">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tráfico Total (IN)</div>
                <div class="stat-value" id="stat-in">0 bps</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tráfico Total (OUT)</div>
                <div class="stat-value" id="stat-out">0 bps</div>
            </div>
            <div class="stat-card">
                <div class="stat-label" id="label-stat-in-period">Acumulado 1h (IN)</div>
                <div class="stat-value" id="stat-in-period">0 B</div>
            </div>
            <div class="stat-card">
                <div class="stat-label" id="label-stat-out-period">Acumulado 1h (OUT)</div>
                <div class="stat-value" id="stat-out-period">0 B</div>
            </div>
        </div>

        <!-- SECCIÓN 1: INFRAESTRUCTURA -->
        <div class="section-header">
            <h2>Infraestructura y Proveedores</h2>
            <span id="provider-count" class="count-badge">0</span>
        </div>
        <div id="provider-list" class="infra-list"></div>

        <!-- SECCIÓN 2: SERVICIOS POR CLIENTE -->
        <div class="section-header">
            <h2>Consumo por Cliente (Top 10)</h2>
        </div>
        <table class="ranking-table">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th>Cliente</th>
                    <th style="text-align: right">Tráfico Actual</th>
                    <th style="text-align: right">Consumo <span class="period-label-raw"></span></th>
                </tr>
            </thead>
            <tbody id="ranking-list">
                <tr><td colspan="4" class="empty-state" style="padding: 20px">No hay datos disponibles</td></tr>
            </tbody>
        </table>

        <div class="section-header">
            <h2>Detalle de Servicios</h2>
            <span id="client-count" class="count-badge">0</span>
        </div>
        <div id="host-list" class="list-container"></div>

        <div id="loader" class="empty-state">
            <div style="display:inline-block" class="loader"></div>
            <p style="margin-top:12px">Cargando datos de Zabbix...</p>
        </div>

        <footer>
            <div id="last-update">Última actualización: —</div>
            <div style="opacity: 0.5">Zabbix Consumo Explorer v2.1</div>
        </footer>
    </div>

    <script>
        let allHosts = [];
        let expandedGroups = new Set();
        let currentPeriod = 3600;

        function initTheme() {
            const savedTheme = localStorage.getItem('zabbix-theme') || 'light';
            const btn = document.querySelector('.theme-toggle');
            if (savedTheme === 'light') {
                document.body.classList.add('light-theme');
                if (btn) btn.textContent = '🌙';
            } else {
                document.body.classList.remove('light-theme');
                if (btn) btn.textContent = '☀️';
            }
        }

        function toggleTheme() {
            document.body.classList.toggle('light-theme');
            const newTheme = document.body.classList.contains('light-theme') ? 'light' : 'dark';
            localStorage.setItem('zabbix-theme', newTheme);
            const btn = document.querySelector('.theme-toggle');
            if (btn) btn.textContent = newTheme === 'light' ? '🌙' : '☀️';
        }

        initTheme();

        function extractClient(name) {
            // Group by first part before hyphen, parenthesis or space
            // E.g. "Rosita-Ñuñoa" -> "Rosita"
            // E.g. "Fukai - Bella" -> "Fukai"
            const parts = name.split(/[\-\(\s]/);
            return parts[0].trim();
        }

        function toggleGroup(groupName) {
            const card = document.querySelector(`[data-group="${groupName}"]`);
            if (expandedGroups.has(groupName)) {
                expandedGroups.delete(groupName);
                if (card) card.classList.remove('is-expanded');
            } else {
                expandedGroups.add(groupName);
                if (card) card.classList.add('is-expanded');
            }
        }

        const periodLabels = {
            '3600': '1h',
            '86400': '24h',
            '604800': '7d',
            '2592000': '30d'
        };

        async function fetchData() {
            const loader = document.getElementById('loader');
            const rangeSelector = document.getElementById('range-selector');
            const hostList = document.getElementById('host-list');
            const providerList = document.getElementById('provider-list');
            
            currentPeriod = rangeSelector.value;
            
            // Immediate feedback: clear current data and show loader
            hostList.innerHTML = '';
            providerList.innerHTML = '';
            loader.style.display = 'block';
            
            try {
                const response = await fetch(`api.php?period=${currentPeriod}`);
                const data = await response.json();
                allHosts = data;
                document.getElementById('last-update').textContent = `Última actualización: ${new Date().toLocaleTimeString('es-CL', {hour12: (localStorage.getItem('zabbix-time-format')||'24h')==='12h'})}`;
                render();
            } catch (error) {
                console.error('Error fetching data:', error);
                hostList.innerHTML = `<div class="empty-state">Error cargando datos: ${error.message}</div>`;
            } finally {
                loader.style.display = 'none';
            }
        }

        function formatBits(bits) {
            if (bits === 0) return '0 bps';
            const k = 1000;
            const sizes = ['bps', 'Kbps', 'Mbps', 'Gbps'];
            const i = Math.floor(Math.log(bits) / Math.log(k));
            return parseFloat((bits / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function render() {
            const query = document.getElementById('search').value.toLowerCase();
            const ifaceFilter = document.getElementById('iface-filter').value;

            const filtered = allHosts.map(h => {
                let ifaces = h.interfaces;
                if (ifaceFilter === 'active') {
                    ifaces = h.interfaces.filter(i => (i.in + i.out) > 0 || ((i.total_in || 0) + (i.total_out || 0)) > 0);
                } else if (ifaceFilter === 'zero') {
                    ifaces = h.interfaces.filter(i => (i.in + i.out) === 0 && ((i.total_in || 0) + (i.total_out || 0)) === 0);
                }
                return { ...h, interfaces: ifaces };
            }).filter(h => {
                const matchSearch = h.name.toLowerCase().includes(query) || 
                                    h.group.toLowerCase().includes(query) || 
                                    (h.service_code && h.service_code.toLowerCase().includes(query));
                
                const hasIface = h.interfaces.length > 0;
                
                // To avoid hiding matching hosts if they have no eligible interfaces but are on 'all', we check:
                if (ifaceFilter === 'all') return matchSearch;
                
                // If not 'all', requires both search match AND at least one eligible interface
                return matchSearch && hasIface;
            });

            // Stats by Provider (Zabbix Groups)
            const providerTotals = {};
            let globalIn = 0;
            let globalOut = 0;
            let globalTotalInPeriod = 0;
            let globalTotalOutPeriod = 0;
            
            // Grouping Logic
            const groups = {};
            filtered.forEach(h => {
                const client = extractClient(h.name);
                if (!groups[client]) {
                    groups[client] = { name: client, hosts: [], in: 0, out: 0, total_period: 0 };
                }
                groups[client].hosts.push(h);

                const provider = h.group || 'Sin proveedor';
                if (!providerTotals[provider]) {
                    providerTotals[provider] = { in: 0, out: 0, total_period: 0 };
                }

                h.interfaces.forEach(iface => {
                    groups[client].in += iface.in;
                    groups[client].out += iface.out;
                    groups[client].total_period += (iface.total_in || 0) + (iface.total_out || 0);

                    providerTotals[provider].in += iface.in;
                    providerTotals[provider].out += iface.out;
                    providerTotals[provider].total_period += (iface.total_in || 0) + (iface.total_out || 0);

                    globalIn += iface.in;
                    globalOut += iface.out;
                    globalTotalInPeriod += (iface.total_in || 0);
                    globalTotalOutPeriod += (iface.total_out || 0);
                });
            });

            const pLabel = periodLabels[currentPeriod] || '1h';
            document.getElementById('stat-total').textContent = filtered.length;
            document.getElementById('stat-in').textContent = formatBits(globalIn);
            document.getElementById('stat-out').textContent = formatBits(globalOut);
            
            document.getElementById('label-stat-in-period').textContent = `Acumulado ${pLabel} (IN)`;
            document.getElementById('label-stat-out-period').textContent = `Acumulado ${pLabel} (OUT)`;
            document.getElementById('stat-in-period').textContent = formatBytes(globalTotalInPeriod);
            document.getElementById('stat-out-period').textContent = formatBytes(globalTotalOutPeriod);
            
            document.querySelectorAll('.period-label-raw').forEach(el => el.textContent = pLabel);

            // Render Ranking (Top 10)
            const rankingList = document.getElementById('ranking-list');
            const sortedForRanking = Object.values(groups).sort((a, b) => b.total_period - a.total_period).slice(0, 10);
            
            if (sortedForRanking.length === 0) {
                rankingList.innerHTML = '<tr><td colspan="4" class="empty-state">No hay datos</td></tr>';
            } else {
                rankingList.innerHTML = sortedForRanking.map((g, idx) => `
                    <tr>
                        <td class="rank-number">${idx + 1}</td>
                        <td class="rank-name">${g.name}</td>
                        <td class="rank-val">
                            <span class="val-in">↓ ${formatBits(g.in)}</span> 
                            <span class="val-out">↑ ${formatBits(g.out)}</span>
                        </td>
                        <td class="rank-val" style="color: var(--text-main)">${formatBytes(g.total_period)}</td>
                    </tr>
                `).join('');
            }

            // Render Providers (Section 1)
            const provContainer = document.getElementById('provider-list');
            const sortedProviders = Object.entries(providerTotals)
                .sort((a, b) => (b[1].in + b[1].out) - (a[1].in + a[1].out));
            
            document.getElementById('provider-count').textContent = sortedProviders.length;
            provContainer.innerHTML = sortedProviders.map(([name, stats]) => `
                <div class="infra-row">
                    <span class="provider-name">${name}</span>
                    <div class="provider-stats">
                        <span class="val-in">⬇ ${formatBits(stats.in)}</span>
                        <span class="val-out">⬆ ${formatBits(stats.out)}</span>
                        <span style="color:var(--text-dim); min-width: 140px; text-align: right;">Total ${pLabel}: ${formatBytes(stats.total_period)}</span>
                    </div>
                </div>
            `).join('');

            // Render Clients (Section 2)
            const container = document.getElementById('host-list');
            const sortedGroups = Object.values(groups).sort((a, b) => (b.in + b.out) - (a.in + a.out));

            document.getElementById('client-count').textContent = sortedGroups.length;
            
            if (sortedGroups.length === 0) {
                container.innerHTML = '<div class="empty-state">No se encontraron clientes o hosts</div>';
                return;
            }

            container.innerHTML = sortedGroups.map(g => {
                const isExpanded = expandedGroups.has(g.name);
                const filteredHosts = g.hosts;
                
                // Safety check (shouldn't be empty if group exists)
                if (filteredHosts.length === 0) return '';

                return `
                    <div class="group-card ${isExpanded ? 'is-expanded' : ''}" data-group="${g.name}">
                        <div class="group-header" onclick="toggleGroup('${g.name}')">
                            <div class="group-toggle">▶</div>
                            <div class="group-info">
                                <div class="group-name">${g.name}</div>
                                <div class="group-count">${g.hosts.length} enlace${g.hosts.length !== 1 ? 's' : ''}</div>
                            </div>
                            <div class="group-aggregate">
                                <span class="val-in">↓ ${formatBits(g.in)}</span>
                                <span class="val-out">↑ ${formatBits(g.out)}</span>
                                <span style="font-size: 11px; opacity:0.6; margin-left:10px">${formatBytes(g.total_period)} / ${pLabel}</span>
                            </div>
                        </div>
                        <div class="group-details">
                            ${filteredHosts.map(h => `
                                <div class="host-item">
                                    <div class="host-header" style="margin-bottom: 12px">
                                        <div class="host-info">
                                            <div style="font-size: 14px; font-weight: 600;">${h.name}</div>
                                            <div class="host-meta" style="font-size: 11px">${h.group} • ${h.ip}</div>
                                        </div>
                                        ${h.service_code ? `<div class="service-code">${h.service_code}</div>` : ''}
                                    </div>
                                    <div class="iface-list">
                                        ${h.interfaces.length > 0 ? h.interfaces.map(iface => {
                                            const usagePct = iface.speed > 0 ? Math.min(100, Math.round((Math.max(iface.in, iface.out) / iface.speed) * 100)) : 0;
                                            const barColor = usagePct > 80 ? 'var(--danger)' : (usagePct > 50 ? 'var(--warning)' : 'var(--success)');
                                            const isSaturated = iface.speed > 0 && usagePct >= 80;
                                            const satBadge = isSaturated
                                                ? `<span style="background:rgba(244,67,54,.18);color:var(--danger);font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;margin-left:6px">⚠ ${usagePct}% SAT</span>`
                                                : '';
                                            return `
                                                <div class="iface-card" style="${isSaturated ? 'border:1px solid var(--danger);' : ''}">
                                                    <div class="iface-name" style="display:flex; justify-content:space-between; align-items:center">
                                                        <span>${iface.name}${satBadge}</span>
                                                        <span style="font-weight:400; opacity:0.7">${pLabel}: ${formatBytes((iface.total_in || 0) + (iface.total_out || 0))}</span>
                                                    </div>
                                                    <div class="traffic-vals">
                                                        <span class="val-in">↓ ${formatBits(iface.in)}</span>
                                                        <span class="val-out">↑ ${formatBits(iface.out)}</span>
                                                    </div>
                                                    <div class="usage-container">
                                                        <div class="usage-bar" style="width: ${usagePct}%; background-color: ${barColor}; transition: width .4s ease"></div>
                                                    </div>
                                                    <div style="font-size: 10px; color: var(--text-dim); margin-top: 4px; display: flex; justify-content: space-between;">
                                                        <span style="color:${barColor};font-weight:${isSaturated ? '700' : '400'}">${usagePct}% uso</span>
                                                        <span>Cap: ${iface.speed > 0 ? formatBits(iface.speed) : '—'}</span>
                                                    </div>
                                                </div>
                                            `;
                                        }).join('') : '<div style="font-size: 11px; color: var(--text-dim); font-style: italic;">Sin interfaces monitoreadas</div>'}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }).join('');
        }

        document.getElementById('search').addEventListener('input', render);

        let refreshTimer = null;
        function updateRefreshTimer() {
            if (refreshTimer) clearInterval(refreshTimer);
            if (window.self !== window.top) return;
            const interval = parseInt(document.getElementById('refresh-interval').value);
            if (interval > 0) {
                refreshTimer = setInterval(fetchData, interval);
            }
        }

        // Initial fetch
        fetchData();
        // Start default timer
        updateRefreshTimer();

        if (window.self !== window.top) {
            document.body.classList.add('in-frame');
            const rc = document.querySelector('.refresh-control');
            if (rc) rc.style.display = 'none';
        }
    </script>
</body>
</html>