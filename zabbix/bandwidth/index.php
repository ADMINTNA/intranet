<?php
// bandwidth/index.php — shell HTML, sin verificación de sesión.
// La sesión se verifica en bandwidth/api.php en cada llamada de datos.
// Esto evita que el iframe redirija al login cuando la cookie no llega
// en la primera navegación lazy (SameSite=Lax).

// ── Security headers ──────────────────────────────────────────
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; frame-ancestors 'self' https://intranet.icontel.cl;");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ancho de Banda — TNA Solutions</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  <style>
    :root {
      --bg:        #0f172a;
      --card:      #1e293b;
      --card-h:    #263447;
      --border:    rgba(255,255,255,0.07);
      --text-main: #f1f5f9;
      --text-dim:  #94a3b8;
      --accent:    #3b82f6;
      --in-color:  #22d3ee;
      --out-color: #f97316;
      --success:   #22c55e;
      --warning:   #f59e0b;
      --danger:    #ef4444;
      --header-h:  56px;
    }
    body.light-theme {
      --bg:        #f0f4f8;
      --card:      #ffffff;
      --card-h:    #f0f4f8;
      --border:    rgba(0,0,0,0.08);
      --text-main: #1e293b;
      --text-dim:  #64748b;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); min-height: 100vh; }
    body.in-frame { padding-top: 0; }

    /* ── Header ── */
    .header {
      position: sticky; top: 0; z-index: 100;
      height: var(--header-h);
      background: var(--card);
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 16px;
      padding: 0 20px;
    }
    .header-title { font-size: 15px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px; }
    .header-controls { display: flex; align-items: center; gap: 10px; margin-left: auto; flex-wrap: wrap; }

    select, input[type=text] {
      background: var(--bg); color: var(--text-main);
      border: 1px solid var(--border); border-radius: 6px;
      padding: 5px 10px; font-size: 12px; font-family: inherit; cursor: pointer;
    }
    select:focus, input:focus { outline: none; border-color: var(--accent); }
    .btn {
      background: var(--accent); color: #fff; border: none;
      border-radius: 6px; padding: 5px 14px; font-size: 12px;
      font-family: inherit; font-weight: 600; cursor: pointer;
      transition: opacity .2s;
    }
    .btn:hover { opacity: .85; }
    .btn-ghost {
      background: var(--card); color: var(--text-dim);
      border: 1px solid var(--border);
    }

    /* ── Layout ── */
    .layout { display: grid; grid-template-columns: 300px 1fr; height: calc(100vh - var(--header-h)); }
    body.in-frame .layout { height: 100vh; }

    /* ── Sidebar ── */
    .sidebar {
      border-right: 1px solid var(--border);
      display: flex; flex-direction: column;
      overflow: hidden;
    }
    .sidebar-search {
      padding: 12px;
      border-bottom: 1px solid var(--border);
    }
    .sidebar-search input {
      width: 100%; padding: 7px 12px; font-size: 13px;
    }
    .sidebar-list { overflow-y: auto; flex: 1; }

    .group-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 8px 14px;
      font-size: 11px; font-weight: 700; text-transform: uppercase;
      letter-spacing: .6px; color: var(--text-dim);
      background: var(--bg);
      border-bottom: 1px solid var(--border);
      cursor: pointer; user-select: none;
      position: sticky; top: 0; z-index: 2;
    }
    .group-header:hover { color: var(--text-main); }
    .group-chevron { transition: transform .2s; }
    .group-chevron.open { transform: rotate(90deg); }

    .host-row {
      display: flex; align-items: center; gap: 10px;
      padding: 8px 14px; cursor: pointer;
      border-bottom: 1px solid var(--border);
      transition: background .15s;
    }
    .host-row:hover { background: var(--card-h); }
    .host-row.active { background: rgba(59,130,246,.12); border-left: 3px solid var(--accent); }
    .host-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .host-name-text { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .host-code { font-size: 10px; color: var(--text-dim); }

    .iface-row {
      display: flex; align-items: center; gap: 8px;
      padding: 5px 14px 5px 32px; cursor: pointer;
      border-bottom: 1px solid var(--border);
      font-size: 12px; color: var(--text-dim);
      transition: background .15s;
    }
    .iface-row:hover { background: var(--card-h); color: var(--text-main); }
    .iface-row.active { background: rgba(59,130,246,.08); color: var(--accent); font-weight: 600; }

    /* ── Main area ── */
    .main-area { display: flex; flex-direction: column; overflow: hidden; }

    /* ── Chart panel ── */
    .chart-panel { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .chart-header {
      padding: 12px 20px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    }
    .chart-title { font-size: 14px; font-weight: 700; color: var(--text-main); }
    .chart-subtitle { font-size: 11px; color: var(--text-dim); }
    .chart-controls { display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap; }

    /* Período pills */
    .period-pills { display: flex; gap: 4px; flex-wrap: wrap; }
    .pill {
      padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
      cursor: pointer; border: 1px solid var(--border);
      background: var(--card); color: var(--text-dim);
      transition: all .15s;
    }
    .pill:hover { border-color: var(--accent); color: var(--accent); }
    .pill.active { background: var(--accent); color: #fff; border-color: var(--accent); }

    .chart-wrap { flex: 1; padding: 16px 20px; min-height: 0; position: relative; }
    .chart-wrap canvas { max-height: 100%; }

    /* ── KPI strip ── */
    .kpi-strip {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 1px; border-top: 1px solid var(--border);
    }
    .kpi-box {
      padding: 12px 16px;
      background: var(--card); border-right: 1px solid var(--border);
    }
    .kpi-box:last-child { border-right: none; }
    .kpi-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--text-dim); }
    .kpi-val { font-size: 20px; font-weight: 800; margin-top: 2px; }

    /* ── Empty / loader ── */
    .empty-state {
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      height: 100%; gap: 12px; color: var(--text-dim);
    }
    .empty-state .icon { font-size: 48px; opacity: .4; }
    .empty-state p { font-size: 14px; }

    .spinner-wrap {
      display: flex; align-items: center; justify-content: center; height: 100%;
    }
    .spinner {
      width: 36px; height: 36px; border: 3px solid var(--border);
      border-top-color: var(--accent); border-radius: 50%;
      animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .badge-in  { color: var(--in-color); }
    .badge-out { color: var(--out-color); }

    /* ── Comparison mode ── */
    .compare-strip {
      padding: 8px 20px;
      background: var(--card);
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 10px;
      font-size: 12px; flex-wrap: wrap;
    }
    .compare-chip {
      display: flex; align-items: center; gap: 6px;
      background: rgba(59,130,246,.12); border: 1px solid rgba(59,130,246,.3);
      border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600;
    }
    .compare-chip .remove { cursor: pointer; color: var(--text-dim); font-size: 14px; line-height: 1; }
    .compare-chip .remove:hover { color: var(--danger); }
  </style>
</head>
<body>

<!-- Header -->
<div class="header" id="app-header">
  <div class="header-title">
    <span style="font-size:20px">📡</span>
    <span>Ancho de Banda</span>
  </div>
  <div class="header-controls">
    <!-- Period pills (also controlled from chart header, mirrored here) -->
    <span style="font-size:11px;color:var(--text-dim)">Período:</span>
    <div class="period-pills" id="header-pills">
      <span class="pill" data-p="3600">1h</span>
      <span class="pill active" data-p="86400">24h</span>
      <span class="pill" data-p="604800">7d</span>
      <span class="pill" data-p="2592000">30d</span>
      <span class="pill" data-p="5184000">2m</span>
      <span class="pill" data-p="7776000">3m</span>
      <span class="pill" data-p="15552000">6m</span>
      <span class="pill" data-p="31536000">1 año</span>
    </div>
    <span style="width:1px;height:20px;background:var(--border)"></span>
    <label style="font-size:11px;color:var(--text-dim);display:flex;align-items:center;gap:6px;cursor:pointer">
      <input type="checkbox" id="chk-compare" style="accent-color:var(--accent)"> Comparar interfaces
    </label>
    <label style="font-size:11px;color:var(--text-dim);display:flex;align-items:center;gap:6px;cursor:pointer">
      <input type="checkbox" id="chk-show-zero" style="accent-color:var(--accent)"> Mostrar ifaces en 0
    </label>

    <button class="btn btn-ghost" onclick="exportChart()" title="Exportar datos a Excel">⬇ Excel</button>

  </div>
</div>

<!-- Layout -->
<div class="layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-search">
      <input type="text" id="search" placeholder="🔍  Buscar host o código…">
    </div>
    <div class="sidebar-list" id="sidebar-list">
      <div class="empty-state" style="height:200px">
        <div class="spinner"></div>
        <p>Cargando hosts…</p>
      </div>
    </div>
  </aside>

  <!-- Main area -->
  <div class="main-area">

    <!-- Compare strip (hidden by default) -->
    <div class="compare-strip" id="compare-strip" style="display:none">
      <span style="color:var(--text-dim)">Comparando:</span>
      <div id="compare-chips" style="display:flex;gap:6px;flex-wrap:wrap"></div>
      <span style="color:var(--text-dim);font-size:11px;margin-left:4px">Haz clic en más interfaces del sidebar para agregar</span>
      <button class="btn btn-ghost" style="margin-left:auto;font-size:11px;padding:3px 10px" onclick="clearCompare()">Limpiar</button>
    </div>

    <!-- Chart panel -->
    <div class="chart-panel">
      <div class="chart-header">
        <div>
          <div class="chart-title" id="chart-title">Selecciona un host</div>
          <div class="chart-subtitle" id="chart-subtitle">Haz clic en una interfaz del panel izquierdo</div>
        </div>
        <div class="chart-controls">
          <label style="font-size:11px;color:var(--text-dim);display:flex;align-items:center;gap:4px">
            <input type="checkbox" id="chk-range" style="accent-color:var(--accent)" onchange="renderChart()"> Rango min/max
          </label>
          <label style="font-size:11px;color:var(--text-dim);display:flex;align-items:center;gap:4px">
            <input type="checkbox" id="chk-fill" checked style="accent-color:var(--accent)" onchange="renderChart()"> Área
          </label>
        </div>
      </div>

      <div class="chart-wrap">
        <div class="empty-state" id="empty-state">
          <div class="icon">📡</div>
          <p>Selecciona un host e interfaz para ver el gráfico</p>
        </div>
        <div class="spinner-wrap" id="chart-loader" style="display:none">
          <div class="spinner"></div>
        </div>
        <canvas id="bw-chart" style="display:none"></canvas>
      </div>

      <!-- KPI strip -->
      <div class="kpi-strip" id="kpi-strip" style="display:none">
        <div class="kpi-box">
          <div class="kpi-label">Máximo IN</div>
          <div class="kpi-val badge-in" id="kpi-peak-in">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Máximo OUT</div>
          <div class="kpi-val badge-out" id="kpi-peak-out">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Promedio IN</div>
          <div class="kpi-val badge-in" id="kpi-avg-in">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Promedio OUT</div>
          <div class="kpi-val badge-out" id="kpi-avg-out">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label" title="Estimación: promedio bps × duración período ÷ 8">Vol. estimado IN ⓘ</div>
          <div class="kpi-val" id="kpi-total-in" style="font-size:15px">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label" title="Estimación: promedio bps × duración período ÷ 8">Vol. estimado OUT ⓘ</div>
          <div class="kpi-val" id="kpi-total-out" style="font-size:15px">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Puntos graficados</div>
          <div class="kpi-val" id="kpi-points" style="font-size:16px;color:var(--text-dim)">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Latencia ICMP</div>
          <div class="kpi-val" id="kpi-latency" style="font-size:18px">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Pérdida paquetes</div>
          <div class="kpi-val" id="kpi-loss" style="font-size:18px">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Jitter ICMP</div>
          <div class="kpi-val" id="kpi-jitter" style="font-size:18px">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Uptime</div>
          <div class="kpi-val" id="kpi-uptime" style="font-size:14px">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">CPU</div>
          <div class="kpi-val" id="kpi-cpu" style="font-size:18px">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Memoria usada</div>
          <div class="kpi-val" id="kpi-mem" style="font-size:18px">—</div>
        </div>
        <div class="kpi-box">
          <div class="kpi-label">Uso / Velocidad</div>
          <div class="kpi-val" id="kpi-util" style="font-size:18px">—</div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
const API = 'api.php';
let allHosts    = [];
let currentPeriod = 86400;
let chartInst   = null;
let compareMode = false;
let compareItems = []; // [{label, itemid_in, itemid_out, color}]
let currentItem = null; // {hostName, ifaceName, in_itemid, out_itemid}
let currentData = null; // {in: series[], out: series[]}
const COLORS = ['#22d3ee','#f97316','#a78bfa','#34d399','#fb7185','#fbbf24','#60a5fa','#e879f9'];

// ── Formatters ───────────────────────────────────────────────
function fBits(bps) {
  if (bps == null || isNaN(bps)) return '—';
  const abs = Math.abs(bps);
  if (abs >= 1e9)  return (bps/1e9).toFixed(2)  + ' Gbps';
  if (abs >= 1e6)  return (bps/1e6).toFixed(2)  + ' Mbps';
  if (abs >= 1e3)  return (bps/1e3).toFixed(1)  + ' Kbps';
  return bps.toFixed(0) + ' bps';
}
function fBytes(b) {
  if (b == null || isNaN(b)) return '—';
  const abs = Math.abs(b);
  if (abs >= 1e12) return (b/1e12).toFixed(2) + ' TB';
  if (abs >= 1e9)  return (b/1e9).toFixed(2)  + ' GB';
  if (abs >= 1e6)  return (b/1e6).toFixed(2)  + ' MB';
  if (abs >= 1e3)  return (b/1e3).toFixed(1)  + ' KB';
  return b.toFixed(0) + ' B';
}
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ── Period selection ─────────────────────────────────────────
document.querySelectorAll('.pill[data-p]').forEach(pill => {
  pill.addEventListener('click', () => {
    document.querySelectorAll('.pill[data-p]').forEach(p => p.classList.remove('active'));
    pill.classList.add('active');
    currentPeriod = parseInt(pill.dataset.p);
    if (currentItem || compareItems.length) loadGraph();
  });
});

// ── Formato de hora ──────────────────────────────────────────
function is24h() {
  return (localStorage.getItem('zabbix-time-format') || '24h') === '24h';
}
function timeFormat(withDate) {
  return withDate
    ? (is24h() ? 'dd/MM HH:mm' : 'dd/MM hh:mm a')
    : (is24h() ? 'HH:mm'       : 'hh:mm a');
}

// ── Uptime formatter ─────────────────────────────────────────
function fUptime(sec) {
  if (!sec || sec <= 0) return '—';
  const d = Math.floor(sec / 86400);
  const h = Math.floor((sec % 86400) / 3600);
  const m = Math.floor((sec % 3600) / 60);
  if (d > 0) return d + 'd ' + h + 'h';
  if (h > 0) return h + 'h ' + m + 'm';
  return m + 'm';
}

// ── Load hosts ───────────────────────────────────────────────
async function loadHosts() {
  const list = document.getElementById('sidebar-list');
  list.innerHTML = '<div style="padding:24px;text-align:center;color:var(--text-dim);font-size:12px">⏳ Cargando hosts…</div>';
  const data = await fetch(`${API}?action=hosts`).then(r => r.json()).catch(() => []);
  // Si la sesión expiró, api.php devuelve {error: '...'}
  if (data && data.error && data.error.includes('session')) {
    document.getElementById('sidebar-list').innerHTML = `
      <div class="empty-state" style="height:200px;padding:20px;text-align:center">
        <div style="font-size:32px">🔒</div>
        <p style="margin-top:8px">Sesión expirada</p>
        <button class="btn" style="margin-top:12px" onclick="(window.top||window).location.href='https://intranet.icontel.cl/index.php?error=session'">
          Ir al login
        </button>
      </div>`;
    return;
  }
  allHosts = Array.isArray(data) ? data : [];
  renderSidebar(allHosts);
}

function renderSidebar(hosts) {
  const query    = document.getElementById('search').value.toLowerCase().trim();
  const showZero = document.getElementById('chk-show-zero').checked;
  const list     = document.getElementById('sidebar-list');

  const filtered = hosts.filter(h =>
    !query ||
    h.name.toLowerCase().includes(query) ||
    (h.service_code && h.service_code.toLowerCase().includes(query)) ||
    h.group.toLowerCase().includes(query) ||
    h.interfaces.some(i => i.name.toLowerCase().includes(query))
  );

  if (!filtered.length) {
    list.innerHTML = '<div class="empty-state" style="height:200px"><p>Sin resultados</p></div>';
    return;
  }

  // Group by group name
  const groups = {};
  filtered.forEach(h => {
    (groups[h.group] = groups[h.group] || []).push(h);
  });

  list.innerHTML = Object.entries(groups).sort(([a],[b]) => a.localeCompare(b)).map(([gname, ghosts]) => `
    <div class="group-header" onclick="toggleGroup(this)">
      <span>${esc(gname)} <span style="font-weight:400;opacity:.6">(${ghosts.length})</span></span>
      <span class="group-chevron open">›</span>
    </div>
    <div class="group-hosts">
      ${ghosts.map(h => `
        ${(() => {
          const hasTraffic = h.interfaces.some(i => (i.last_in||0) > 0 || (i.last_out||0) > 0);
          const dimStyle   = hasTraffic ? '' : 'opacity:.4;filter:grayscale(1)';
          return `<div class="host-row" data-hostid="${h.hostid}" onclick="selectHost(this, '${h.hostid}')" style="${dimStyle}">
          <div class="host-dot" style="background:var(--text-dim)"></div>
          <div style="overflow:hidden;flex:1">
            <div class="host-name-text">${esc(h.name)}</div>
            ${h.service_code ? `<div class="host-code">${esc(h.service_code)}</div>` : ''}
          </div>
          ${h.icmp_ms !== null ? `<div style="font-size:10px;font-weight:600;white-space:nowrap;color:${h.icmp_ms < 20 ? 'var(--success)' : h.icmp_ms < 100 ? 'var(--warning)' : 'var(--danger)'}">${h.icmp_ms}ms</div>` : ''}
        </div>`;
        })()}
        <div class="host-ifaces" style="display:${query && h.interfaces.some(i=>i.name.toLowerCase().includes(query)) ? '' : 'none'}" data-hostid="${h.hostid}">
          ${h.interfaces.slice()
            .filter(iface => showZero || (iface.last_in||0) > 0 || (iface.last_out||0) > 0)
            .sort((a,b) => (b.last_in||0) - (a.last_in||0)).map((iface, idx) => `
            <div class="iface-row" style="${query && iface.name.toLowerCase().includes(query) ? 'background:rgba(59,130,246,.15);' : ''}"
              data-hostid="${h.hostid}"
              data-iface="${idx}"
              data-in="${iface.in_itemid || ''}"
              data-out="${iface.out_itemid || ''}"
              data-hostname="${esc(h.name)}"
              data-ifacename="${esc(iface.name)}"
              onclick="selectIface(this)">
              <span style="font-size:10px;opacity:.5">⌐</span>
              ${esc(iface.name)}
              <span style="margin-left:auto;font-size:10px;opacity:.5">${fBits(iface.last_in||0)} ↓</span>
            </div>
          `).join('')}
        </div>
      `).join('')}
    </div>
  `).join('');
}

function toggleGroup(el) {
  const chevron   = el.querySelector('.group-chevron');
  const container = el.nextElementSibling;
  const open      = chevron.classList.toggle('open');
  container.style.display = open ? '' : 'none';
}

function selectHost(el, hostid) {
  // Toggle ifaces panel
  const ifacesEl = document.querySelector(`.host-ifaces[data-hostid="${hostid}"]`);
  if (!ifacesEl) return;
  const isOpen = ifacesEl.style.display !== 'none';
  ifacesEl.style.display = isOpen ? 'none' : '';

  // If only one iface, auto-select it
  if (!isOpen) {
    const ifaces = ifacesEl.querySelectorAll('.iface-row');
    if (ifaces.length === 1) selectIface(ifaces[0]);
  }
}

function selectIface(el) {
  const hostName  = el.dataset.hostname;
  const ifaceName = el.dataset.ifacename;
  const inItem    = el.dataset.in  ? parseInt(el.dataset.in)  : null;
  const outItem   = el.dataset.out ? parseInt(el.dataset.out) : null;
  const hostId    = el.dataset.hostid;

  // Get ICMP data from allHosts
  const hostObj   = allHosts.find(h => String(h.hostid) === String(hostId));
  const icmpMs    = hostObj?.icmp_ms   ?? null;
  const icmpLoss  = hostObj?.icmp_loss ?? null;

  if (compareMode) {
    addCompare({ label: `${hostName} — ${ifaceName}`, in_itemid: inItem, out_itemid: outItem });
    el.classList.add('active');
    return;
  }

  // Single mode
  document.querySelectorAll('.iface-row.active').forEach(r => r.classList.remove('active'));
  el.classList.add('active');

  currentItem = {
    hostName, ifaceName, in_itemid: inItem, out_itemid: outItem,
    icmp_ms:      icmpMs,
    icmp_loss:    icmpLoss,
    icmp_jitter:  hostObj?.icmp_jitter  ?? null,
    cpu_pct:      hostObj?.cpu_pct      ?? null,
    uptime_s:     hostObj?.uptime_s     ?? null,
    mem_used_pct: hostObj?.mem_used_pct ?? null,
    if_speed:     hostObj?.if_speed     ?? null,
  };
  document.getElementById('chart-title').textContent    = hostName;
  document.getElementById('chart-subtitle').textContent = ifaceName;
  loadGraph();
}

// ── Compare mode ─────────────────────────────────────────────
document.getElementById('chk-compare').addEventListener('change', e => {
  compareMode = e.target.checked;
  document.getElementById('compare-strip').style.display = compareMode ? 'flex' : 'none';
  if (!compareMode) clearCompare();
});

function addCompare(item) {
  if (compareItems.find(c => c.in_itemid === item.in_itemid)) return; // already added
  compareItems.push({ ...item, color: COLORS[compareItems.length % COLORS.length] });
  renderCompareChips();
  loadGraph();
}

function removeCompare(idx) {
  compareItems.splice(idx, 1);
  renderCompareChips();
  if (compareItems.length) loadGraph(); else clearChart();
}

function clearCompare() {
  compareItems = [];
  renderCompareChips();
  document.querySelectorAll('.iface-row.active').forEach(r => r.classList.remove('active'));
  clearChart();
}

function renderCompareChips() {
  document.getElementById('compare-chips').innerHTML = compareItems.map((c, i) => `
    <div class="compare-chip" style="border-color:${c.color}30;background:${c.color}15">
      <span style="width:8px;height:8px;border-radius:50%;background:${c.color};display:inline-block"></span>
      ${esc(c.label)}
      <span class="remove" onclick="removeCompare(${i})">×</span>
    </div>
  `).join('');
}

// ── Load graph data ───────────────────────────────────────────
async function loadGraph() {
  showLoader();
  currentData = null;

  const safeFetch = url => fetch(url).then(r => r.json()).catch(() => null);

  if (compareMode && compareItems.length) {
    const promises = compareItems.flatMap(c => [
      c.in_itemid  ? safeFetch(`${API}?action=graph&itemid=${c.in_itemid}&period=${currentPeriod}&p=${currentPeriod}`)  : Promise.resolve(null),
      c.out_itemid ? safeFetch(`${API}?action=graph&itemid=${c.out_itemid}&period=${currentPeriod}&p=${currentPeriod}`) : Promise.resolve(null),
    ]);
    const results = await Promise.all(promises);
    renderCompareChart(results);
  } else if (currentItem) {
    const [inData, outData] = await Promise.all([
      currentItem.in_itemid  ? safeFetch(`${API}?action=graph&itemid=${currentItem.in_itemid}&period=${currentPeriod}&p=${currentPeriod}`)  : Promise.resolve(null),
      currentItem.out_itemid ? safeFetch(`${API}?action=graph&itemid=${currentItem.out_itemid}&period=${currentPeriod}&p=${currentPeriod}`) : Promise.resolve(null),
    ]);
    // Verificar error de sesión en la respuesta
    const sessionErr = (inData && inData.error) || (outData && outData.error);
    if (sessionErr) {
      clearChart();
      document.getElementById('chart-title').textContent = '❌ ' + sessionErr;
      return;
    }
    currentData = { in: inData, out: outData };
    renderChart();
  }
}

function showLoader() {
  document.getElementById('empty-state').style.display  = 'none';
  document.getElementById('chart-loader').style.display = 'flex';
  document.getElementById('bw-chart').style.display     = 'none';
  document.getElementById('kpi-strip').style.display    = 'none';
}

function clearChart() {
  if (chartInst) { chartInst.destroy(); chartInst = null; }
  document.getElementById('empty-state').style.display  = 'flex';
  document.getElementById('chart-loader').style.display = 'none';
  document.getElementById('bw-chart').style.display     = 'none';
  document.getElementById('kpi-strip').style.display    = 'none';
  document.getElementById('chart-title').textContent    = 'Selecciona un host';
  document.getElementById('chart-subtitle').textContent = 'Haz clic en una interfaz del panel izquierdo';
}

// ── Render single interface chart ────────────────────────────
function renderChart() {
  if (!currentData) return;
  const { in: inD, out: outD } = currentData;
  const showRange = document.getElementById('chk-range').checked;
  const showFill  = document.getElementById('chk-fill').checked;

  const inSeries  = inD?.series  || [];
  const outSeries = outD?.series || [];

  // Build datasets
  const datasets = [];
  if (inSeries.length) {
    if (showRange && inD.use_trends) {
      datasets.push({ label: 'IN max', data: inSeries.map(p=>({x:p.ts,y:p.max})), borderColor:'rgba(34,211,238,.3)', borderWidth:1, pointRadius:0, fill:false });
      datasets.push({ label: 'IN min', data: inSeries.map(p=>({x:p.ts,y:p.min})), borderColor:'rgba(34,211,238,.3)', borderWidth:1, pointRadius:0, fill:'-1', backgroundColor:'rgba(34,211,238,.08)' });
    }
    datasets.push({
      label: '↓ IN',
      data: inSeries.map(p=>({x:p.ts, y:p.avg})),
      borderColor: '#22d3ee', borderWidth: 2, pointRadius: 0,
      fill: showFill ? 'origin' : false,
      backgroundColor: 'rgba(34,211,238,.1)',
    });
  }
  if (outSeries.length) {
    if (showRange && outD.use_trends) {
      datasets.push({ label: 'OUT max', data: outSeries.map(p=>({x:p.ts,y:p.max})), borderColor:'rgba(249,115,22,.3)', borderWidth:1, pointRadius:0, fill:false });
      datasets.push({ label: 'OUT min', data: outSeries.map(p=>({x:p.ts,y:p.min})), borderColor:'rgba(249,115,22,.3)', borderWidth:1, pointRadius:0, fill:'-1', backgroundColor:'rgba(249,115,22,.08)' });
    }
    datasets.push({
      label: '↑ OUT',
      data: outSeries.map(p=>({x:p.ts, y:p.avg})),
      borderColor: '#f97316', borderWidth: 2, pointRadius: 0,
      fill: showFill ? 'origin' : false,
      backgroundColor: 'rgba(249,115,22,.1)',
    });
  }

  drawChart(datasets, inD || outD);
  updateKPIs(inSeries, outSeries, inD || outD);
}

// ── Render compare chart ─────────────────────────────────────
function renderCompareChart(results) {
  const datasets = [];
  compareItems.forEach((c, i) => {
    const inD  = results[i * 2];
    const outD = results[i * 2 + 1];
    const col  = c.color;
    const col2 = COLORS[(i + 4) % COLORS.length];
    const label = c.label.split('—')[1]?.trim() || c.label;

    if (inD?.series?.length) {
      datasets.push({
        label: `↓ ${label}`,
        data: inD.series.map(p => ({x: p.ts, y: p.avg})),
        borderColor: col, borderWidth: 2, pointRadius: 0, fill: false,
      });
    }
    if (outD?.series?.length) {
      datasets.push({
        label: `↑ ${label}`,
        data: outD.series.map(p => ({x: p.ts, y: p.avg})),
        borderColor: col2, borderWidth: 2, pointRadius: 0, fill: false,
        borderDash: [5, 3],
      });
    }
  });

  const firstData = results.find(Boolean);
  document.getElementById('chart-title').textContent    = 'Comparación de interfaces';
  document.getElementById('chart-subtitle').textContent = `${compareItems.length} interfaces`;
  drawChart(datasets, firstData);
  document.getElementById('kpi-strip').style.display = 'none';
}

// ── Core chart draw ───────────────────────────────────────────
function drawChart(datasets, meta) {
  document.getElementById('chart-loader').style.display = 'none';
  document.getElementById('empty-state').style.display  = 'none';
  const canvas = document.getElementById('bw-chart');
  canvas.style.display = 'block';

  const isDark = !document.body.classList.contains('light-theme');
  const gridColor   = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
  const textColor   = isDark ? '#94a3b8' : '#64748b';
  const tooltipBg   = isDark ? '#1e293b' : '#fff';

  // Determine time unit based on period
  const p = currentPeriod;
  const timeUnit = p <= 7200 ? 'minute' :
                   p <= 172800 ? 'hour' :
                   p <= 2592000 ? 'day' : 'month';

  if (chartInst) chartInst.destroy();
  chartInst = new Chart(canvas, {
    type: 'line',
    data: { datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 300 },
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          labels: { color: textColor, boxWidth: 12, font: { size: 11 } },
          position: 'top',
        },
        tooltip: {
          backgroundColor: tooltipBg,
          titleColor: isDark ? '#f1f5f9' : '#1e293b',
          bodyColor: textColor,
          borderColor: gridColor,
          borderWidth: 1,
          callbacks: {
            label: ctx => ` ${ctx.dataset.label}: ${fBits(ctx.parsed.y)}`,
          },
        },
      },
      scales: {
        x: {
          type: 'time',
          time: { unit: timeUnit, tooltipFormat: timeFormat(true), displayFormats: { minute: timeFormat(false), hour: timeFormat(false), day: 'dd/MM', month: 'MM/yyyy' } },
          grid: { color: gridColor },
          ticks: { color: textColor, font: { size: 10 }, maxTicksLimit: 12 },
        },
        y: {
          grid: { color: gridColor },
          ticks: {
            color: textColor,
            font: { size: 10 },
            callback: v => fBits(v),
          },
          beginAtZero: true,
        },
      },
    },
  });

  // Source badge
  const src = meta?.use_trends ? 'trend (1h/punto)' : `history (~${meta?.points || 0} puntos)`;
  document.getElementById('chart-subtitle').textContent =
    (currentItem ? currentItem.ifaceName + ' · ' : '') + src;
}

// ── KPIs ──────────────────────────────────────────────────────
function updateKPIs(inSeries, outSeries, meta) {
  const strip = document.getElementById('kpi-strip');
  if (!inSeries.length && !outSeries.length) { strip.style.display = 'none'; return; }
  strip.style.display = 'grid';

  const vals = arr => arr.map(p => p.avg).filter(v => v > 0);
  const inVals  = vals(inSeries);
  const outVals = vals(outSeries);

  const peakIn  = inVals.length  ? Math.max(...inVals)  : 0;
  const peakOut = outVals.length ? Math.max(...outVals) : 0;
  const avgIn   = inVals.length  ? inVals.reduce((a,b)=>a+b,0)/inVals.length  : 0;
  const avgOut  = outVals.length ? outVals.reduce((a,b)=>a+b,0)/outVals.length : 0;

  // Estimate total bytes: avg bps * period / 8
  const totalInB  = (avgIn  / 8) * currentPeriod;
  const totalOutB = (avgOut / 8) * currentPeriod;

  document.getElementById('kpi-peak-in').textContent  = fBits(peakIn);
  document.getElementById('kpi-peak-out').textContent = fBits(peakOut);
  document.getElementById('kpi-avg-in').textContent   = fBits(avgIn);
  document.getElementById('kpi-avg-out').textContent  = fBits(avgOut);
  document.getElementById('kpi-total-in').textContent  = fBytes(totalInB);
  document.getElementById('kpi-total-out').textContent = fBytes(totalOutB);
  document.getElementById('kpi-points').textContent = meta?.points ?? '—';

  // Latencia ICMP
  const latEl  = document.getElementById('kpi-latency');
  const lossEl = document.getElementById('kpi-loss');
  if (currentItem) {
    const setKpi = (id, val, unit, thresholds) => {
      const el = document.getElementById(id);
      if (!el) return;
      if (val === null || val === undefined) { el.textContent = '—'; el.style.color = 'var(--text-dim)'; return; }
      el.textContent = val + (unit || '');
      if (thresholds) {
        const [good, warn] = thresholds;
        el.style.color = val <= good ? 'var(--success)' : val <= warn ? 'var(--warning)' : 'var(--danger)';
      } else {
        el.style.color = '';
      }
    };

    // Latencia
    if (latEl) {
      const ms = currentItem.icmp_ms;
      if (ms !== null && ms !== undefined) {
        latEl.textContent = ms + ' ms';
        latEl.style.color = ms < 20 ? 'var(--success)' : ms < 100 ? 'var(--warning)' : 'var(--danger)';
      } else { latEl.textContent = '—'; latEl.style.color = 'var(--text-dim)'; }
    }
    // Pérdida
    if (lossEl) {
      const loss = currentItem.icmp_loss;
      if (loss !== null && loss !== undefined) {
        lossEl.textContent = loss + ' %';
        lossEl.style.color = loss === 0 ? 'var(--success)' : loss < 10 ? 'var(--warning)' : 'var(--danger)';
      } else { lossEl.textContent = '—'; lossEl.style.color = 'var(--text-dim)'; }
    }
    // Jitter
    setKpi('kpi-jitter', currentItem.icmp_jitter, ' ms', [5, 20]);
    // Uptime
    const uptEl = document.getElementById('kpi-uptime');
    if (uptEl) { uptEl.textContent = fUptime(currentItem.uptime_s); uptEl.style.color = ''; }
    // CPU
    setKpi('kpi-cpu', currentItem.cpu_pct, ' %', [60, 85]);
    // Memoria
    setKpi('kpi-mem', currentItem.mem_used_pct, ' %', [70, 90]);
    // Velocidad contratada vs uso real
    const utilEl = document.getElementById('kpi-util');
    if (utilEl) {
      if (currentItem.if_speed && currentItem.if_speed > 0 && currentData?.in) {
        const lastIn  = currentData.in.series?.slice(-1)[0]?.v  ?? 0;
        const lastOut = currentData.out?.series?.slice(-1)[0]?.v ?? 0;
        const maxBw   = Math.max(lastIn, lastOut);
        const pct     = Math.round((maxBw / currentItem.if_speed) * 100);
        utilEl.textContent = pct + ' %';
        utilEl.style.color = pct < 60 ? 'var(--success)' : pct < 85 ? 'var(--warning)' : 'var(--danger)';
      } else {
        utilEl.textContent = '—';
        utilEl.style.color = 'var(--text-dim)';
      }
    }
  }
}

// ── Export ────────────────────────────────────────────────────
function exportChart() {
  if (!currentData && !compareItems.length) return;

  const rows = [['Timestamp', 'Fecha', 'IN (bps)', 'OUT (bps)']];
  const inS  = currentData?.in?.series  || [];
  const outS = currentData?.out?.series || [];
  const len  = Math.max(inS.length, outS.length);
  for (let i = 0; i < len; i++) {
    const ts  = inS[i]?.ts || outS[i]?.ts;
    const dt  = ts ? new Date(ts).toLocaleString('es-CL', { hour12: !is24h() }) : '';
    rows.push([ts ? ts/1000 : '', dt, inS[i]?.avg ?? '', outS[i]?.avg ?? '']);
  }

  const ws = XLSX.utils.aoa_to_sheet(rows);
  const wb = XLSX.utils.book_new();
  const sheetName = (currentItem?.ifaceName || 'BW').slice(0, 31);
  XLSX.utils.book_append_sheet(wb, ws, sheetName);
  XLSX.writeFile(wb, `BW_${(currentItem?.hostName||'export').replace(/\s+/g,'_')}_${new Date().toISOString().slice(0,10)}.xlsx`);
}

// Tema controlado por el portal vía postMessage

// Sync theme from portal parent if possible
function syncTheme() {
  try {
    if (window.self !== window.top) {
      const parentTheme = window.parent.document.body.classList.contains('light-theme');
      document.body.classList.toggle('light-theme', parentTheme);
    } else {
      const saved = localStorage.getItem('bw-theme');
      if (saved === 'light') document.body.classList.add('light-theme');
    }
  } catch(e) {}
}

// ── Search ────────────────────────────────────────────────────
document.getElementById('search').addEventListener('input', () => renderSidebar(allHosts));

// Escuchar cambios de formato de hora desde el portal
window.addEventListener('message', e => {
  if (e.data?.type === 'timeFormat') {
    localStorage.setItem('zabbix-time-format', e.data.is24h ? '24h' : '12h');
    if (currentData || compareItems.length) loadGraph();
  }
});
document.getElementById('chk-show-zero').addEventListener('change', () => renderSidebar(allHosts));


// ── Init ─────────────────────────────────────────────────────
syncTheme();
loadHosts();

// Respond to theme changes from portal
window.addEventListener('message', e => {
  if (e.data?.type === 'theme') {
    document.body.classList.toggle('light-theme', e.data.theme === 'light');
  }
});
</script>
</body>
</html>