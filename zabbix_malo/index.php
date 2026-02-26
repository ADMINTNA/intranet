<?php
require_once 'z_session.php';
// ── Security headers ──────────────────────────────────────────
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; media-src 'self'; connect-src 'self'; frame-ancestors 'self' https://intranet.icontel.cl;");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Zabbix Monitor — TNA Solutions</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<style>
  /* === RESET & BASE === */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg-base:     #0a0e1a;
    --bg-card:     rgba(255,255,255,0.04);
    --bg-card-h:   rgba(255,255,255,0.08);
    --border:      rgba(255,255,255,0.08);
    --text-main:   #e8eaf6;
    --text-muted:  #7986cb;
    --text-dim:    #4a5080;
    --accent:      #5c6bc0;
    --accent-glow: rgba(92,107,192,0.3);
    --header-bg:   rgba(10,14,26,0.8);
    --table-head:  #0e1220;
    --modal-bg:    #0f1526;

    --sev-disaster: #f44336;
    --sev-high:     #ff5722;
    --sev-avg:      #ff9800;
    --sev-warn:     #ffc107;
    --sev-info:     #42a5f5;
    --sev-nc:       #607d8b;
    --sev-ok:       #26a69a;
  }

  /* === LIGHT THEME === */
  body.light {
    --bg-base:     #f8fafc;
    --bg-card:     #ffffff;
    --bg-card-h:   #ffffff;
    --border:      rgba(0,0,0,0.08);
    --text-main:   #0f172a;
    --text-muted:  #334155;
    --text-dim:    #64748b;
    --accent:      #2563eb;
    --accent-glow: rgba(37,99,235,0.15);
    --header-bg:   #ffffff;
    --table-head:  #f1f5f9;
    --modal-bg:    #ffffff;
    --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
  }
  body.light {
    background-image:
      radial-gradient(ellipse 80% 50% at 20% 10%, rgba(76,95,196,0.08) 0%, transparent 60%),
      radial-gradient(ellipse 60% 40% at 80% 80%, rgba(38,166,154,0.05) 0%, transparent 60%);
  }
  body.light .sev-5 { background: rgba(244,67,54,.1);  }
  body.light .sev-4 { background: rgba(255,87,34,.1);  }
  body.light .sev-3 { background: rgba(255,152,0,.1);  }
  body.light .sev-2 { background: rgba(255,193,7,.1);  }
  body.light .sev-1 { background: rgba(66,165,245,.1); }
  body.light .sev-0 { background: rgba(96,125,139,.1); }

  html, body { height: 100%; }

  body {
    font-family: 'Inter', sans-serif;
    background: var(--bg-base);
    color: var(--text-main);
    min-height: 100vh;
    background-image:
      radial-gradient(ellipse 80% 50% at 20% 10%, rgba(92,107,192,0.12) 0%, transparent 60%),
      radial-gradient(ellipse 60% 40% at 80% 80%, rgba(38,166,154,0.08) 0%, transparent 60%);
    transition: background-color .3s, color .3s;
  }

  /* === HEADER === */
  body.in-frame .header { display: none !important; }
  body.in-frame .container { margin-top: 0 !important; padding-top: 10px !important; }

  .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 32px;
    background: var(--header-bg);
    border-bottom: 1px solid var(--border);
    backdrop-filter: blur(12px);
    position: sticky;
    top: 0;
    z-index: 100;
    transition: background .3s;
  }

  .header-brand {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .header-logo {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--accent), #26a69a);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }

  .header-title { font-size: 18px; font-weight: 600; }
  .header-sub   { font-size: 12px; color: var(--text-muted); margin-top: 1px; }

  .header-controls {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  #last-update {
    font-size: 12px;
    color: var(--text-dim);
  }

  .btn-refresh {
    background: var(--accent);
    border: none;
    border-radius: 8px;
    color: #fff;
    padding: 8px 18px;
    font-size: 13px;
    font-family: inherit;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all .2s;
  }
  .btn-refresh:hover { background: #3f51b5; transform: translateY(-1px); box-shadow: 0 4px 16px var(--accent-glow); }
  .btn-refresh.loading svg { animation: spin .7s linear infinite; }

  /* === LAYOUT === */
  .container { max-width: 1440px; margin: 0 auto; padding: 28px 32px; }

  /* === KPI CARDS === */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
  }

  .kpi-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 22px 24px;
    display: flex;
    box-shadow: var(--card-shadow, none);
    flex-direction: column;
    gap: 8px;
    transition: all .25s;
    backdrop-filter: blur(8px);
    position: relative;
    overflow: hidden;
  }
  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 16px 16px 0 0;
    background: var(--kpi-color, var(--accent));
  }
  .kpi-card:hover { background: var(--bg-card-h); transform: translateY(-2px); }

  .kpi-label { font-size: 12px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: .8px; }
  .kpi-value { font-size: 40px; font-weight: 700; line-height: 1; color: var(--kpi-color, var(--text-main)); }
  .kpi-icon  { position: absolute; right: 20px; top: 20px; font-size: 28px; opacity: .2; }

  .kpi-total    { --kpi-color: var(--accent); }
  .kpi-down     { --kpi-color: var(--sev-disaster); }
  .kpi-link     { --kpi-color: #e65100; }
  .kpi-warn     { --kpi-color: var(--sev-avg); }
  .kpi-ok       { --kpi-color: var(--sev-ok); }
  .kpi-alerts   { --kpi-color: var(--sev-high); }
  .kpi-problems { --kpi-color: var(--sev-warn); }

  /* === TABS === */
  .tabs-nav {
    display: flex;
    gap: 12px;
    margin: 20px 0;
    padding: 0 4px;
    border-bottom: 1px solid var(--border);
  }
  .tab-btn {
    padding: 10px 24px;
    background: transparent;
    border: none;
    color: var(--text-dim);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .tab-btn:hover { color: var(--text-main); }
  .tab-btn.active {
    color: var(--accent);
    border-bottom-color: var(--accent);
    background: rgba(67, 97, 238, 0.08);
    border-radius: 8px 8px 0 0;
  }
  .tab-content { display: none; }
  .tab-content.active { display: block; animation: fadeIn 0.3s ease-out; }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* === BANDWIDTH TABLE === */
  .bw-table { width: 100%; border-collapse: collapse; }
  .bw-table th {
    text-align: left;
    padding: 12px 16px;
    font-size: 11px;
    text-transform: uppercase;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border);
    position: sticky; top: 0; background: var(--table-head); z-index: 10;
  }
  .bw-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); font-size: 13px; }
  .bw-row:hover { background: rgba(255,255,255,0.03); }
  .bw-bar-bg { height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden; margin-top: 4px; width: 100%; }
  .bw-bar-fill { height: 100%; background: var(--accent); border-radius: 3px; }

  /* === MAIN GRID === */
  .main-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    align-items: stretch;
  }

  /* === PANEL === */
  .panel {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: visible;
    backdrop-filter: blur(8px);
    box-shadow: var(--card-shadow, none);
  }

  .panel-header {
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 10;
  }

  .panel-title {
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .badge {
    background: var(--accent);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    min-width: 22px;
    text-align: center;
  }
  .badge-red { background: var(--sev-disaster); }

  /* === PROBLEMS TABLE === */
  .problems-wrap { overflow-x: auto; max-height: 420px; overflow-y: auto; }

  .problems-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .problems-table th {
    padding: 10px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .6px;
    position: sticky;
    top: 0;
    background: var(--table-head);
    z-index: 1;
  }
  .problems-table td {
    padding: 10px 16px;
    border-top: 1px solid var(--border);
    vertical-align: middle;
  }
  .problems-table tr:hover td { background: var(--bg-card-h); }

  .sev-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    white-space: nowrap;
    color: #fff;
  }

  .sev-5 { background: rgba(244,67,54,.2);  color: var(--sev-disaster); border: 1px solid rgba(244,67,54,.4); }
  .sev-4 { background: rgba(255,87,34,.2);  color: var(--sev-high);     border: 1px solid rgba(255,87,34,.4); }
  .sev-3 { background: rgba(255,152,0,.2);  color: var(--sev-avg);      border: 1px solid rgba(255,152,0,.4); }
  .sev-2 { background: rgba(255,193,7,.2);  color: var(--sev-warn);     border: 1px solid rgba(255,193,7,.4); }
  .sev-1 { background: rgba(66,165,245,.2); color: var(--sev-info);     border: 1px solid rgba(66,165,245,.4); }
  .sev-0 { background: rgba(96,125,139,.2); color: var(--sev-nc);       border: 1px solid rgba(96,125,139,.4); }

  .host-name { font-weight: 500; }
  .problem-name { color: var(--text-muted); font-size: 12px; margin-top: 2px; }
  .time-ago { color: var(--text-dim); font-size: 11px; white-space: nowrap; }
  .svc-code {
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    color: var(--accent);
    background: rgba(92,107,192,0.15);
    border: 1px solid rgba(92,107,192,0.3);
    border-radius: 4px;
    padding: 1px 6px;
    margin-top: 3px;
    letter-spacing: .4px;
    font-family: monospace;
  }

  .empty-msg {
    text-align: center;
    padding: 40px;
    color: var(--text-dim);
    font-size: 13px;
  }

  /* === SIDEBAR === */
  .sidebar { display: flex; flex-direction: column; gap: 20px; }

  /* === CHART === */
  .chart-wrap {
    padding: 20px;
    position: relative;
  }
  .chart-center-label {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
    margin-top: 10px;
  }
  .chart-center-label .big { font-size: 28px; font-weight: 700; }
  .chart-center-label .small { font-size: 11px; color: var(--text-muted); }

  /* === HOSTS GRID === */
  .hosts-list {
    padding: 12px;
    max-height: 380px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
    z-index: 1;
  }

  .host-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid var(--border);
    transition: all .2s;
    cursor: pointer;
    background: var(--bg-card);
    box-shadow: var(--card-shadow, none);
  }
  .host-item:hover { background: var(--bg-card-h); transform: translateY(-1px); }

  body.light .host-item { background: #ffffff; }
  body.light .host-item:hover { background: #f8fafc; }

  .host-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .dot-ok      { background: var(--sev-ok);      box-shadow: 0 0 8px var(--sev-ok); }
  .dot-down    { background: var(--sev-disaster); box-shadow: 0 0 8px var(--sev-disaster); animation: pulse 1.5s infinite; }
  .dot-alerts  { background: var(--sev-avg);      box-shadow: 0 0 6px var(--sev-avg); }
  .dot-unknown { background: var(--sev-nc); }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: .4; }
  }

  .host-info { flex: 1; min-width: 0; }
  .host-info .hname { font-size: 12px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .host-info .hgroup { font-size: 11px; color: var(--text-dim); }

  .host-status-txt { font-size: 10px; font-weight: 600; }
  .txt-ok      { color: var(--sev-ok); }
  .txt-down    { color: var(--sev-disaster); }
  .txt-alerts  { color: var(--sev-avg); }
  .txt-unknown { color: var(--sev-nc); }

  /* === SEARCH === */
  .search-wrap { padding: 12px 12px 0; }
  .search-input {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-main);
    padding: 8px 12px;
    font-size: 13px;
    font-family: inherit;
    outline: none;
    transition: border .2s;
  }
  .search-input:focus { border-color: var(--accent); }
  .search-input::placeholder { color: var(--text-dim); }

  /* === SCROLLBAR === */
  ::-webkit-scrollbar { width: 5px; height: 5px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

  /* === LOADER === */
  .loader-overlay {
    position: fixed; inset: 0;
    background: var(--bg-base);
    z-index: 999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    transition: opacity .5s;
  }
  .loader-overlay.hide { opacity: 0; pointer-events: none; }
  .spinner {
    width: 44px; height: 44px;
    border: 3px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin .8s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  .auto-bar {
    height: 3px;
    background: var(--accent);
    opacity: .3;
    position: fixed;
    bottom: 0; left: 0;
    transition: width 1s linear;
  }

  /* === THEME TOGGLE === */
  .btn-theme {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-muted);
    padding: 7px 12px;
    font-size: 16px;
    cursor: pointer;
    transition: all .2s;
    line-height: 1;
  }
  .btn-theme:hover { background: var(--bg-card-h); transform: scale(1.1); }

  @media (max-width: 900px) {
    .main-grid { grid-template-columns: 1fr; }
    .container { padding: 16px; }
    .header { padding: 14px 16px; }
  }

  /* === HOST MODAL === */
  .modal-overlay {
    position: fixed; inset: 0;
    background: rgba(5,8,18,0.85);
    backdrop-filter: blur(6px);
    z-index: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s;
  }
  .modal-overlay.open {
    opacity: 1;
    pointer-events: all;
  }
  .modal-box {
    background: var(--modal-bg);
    border: 1px solid var(--border);
    border-radius: 20px;
    width: 640px;
    max-width: 92vw;
    max-height: 80vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transform: scale(0.85);
    transition: transform .3s cubic-bezier(.34,1.56,.64,1);
    box-shadow: 0 24px 80px rgba(0,0,0,.3);
  }
  .modal-overlay.open .modal-box {
    transform: scale(1);
  }
  .modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .modal-host-dot {
    width: 14px; height: 14px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .modal-title { font-size: 16px; font-weight: 600; flex: 1; min-width: 0; }
  .modal-subtitle { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
  .modal-close {
    background: rgba(255,255,255,0.06);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-muted);
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 16px;
    transition: all .2s;
    flex-shrink: 0;
  }
  .modal-close:hover { background: rgba(255,255,255,0.12); color: var(--text-main); }
  .modal-body {
    overflow-y: auto;
    padding: 16px;
    flex: 1;
  }
  .modal-tabs { display: flex; border-bottom: 1px solid var(--border); margin-bottom: 12px; }
  .modal-tab { flex: 1; text-align: center; padding: 10px 0; font-size: 13px; font-weight: 500; color: var(--text-muted); cursor: pointer; border-bottom: 2px solid transparent; transition: all .2s; }
  .modal-tab:hover { color: var(--text-main); }
  .modal-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
  .modal-tab-content { display: none; }
  .modal-tab-content.active { display: block; }
  .modal-alert-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid var(--border);
    margin-bottom: 8px;
    transition: background .2s;
  }
  .modal-alert-item:hover { background: var(--bg-card-h); }
  .modal-alert-sev { flex-shrink: 0; margin-top: 2px; }
  .modal-alert-info { flex: 1; min-width: 0; }
  .modal-alert-name { font-size: 13px; font-weight: 500; }
  .modal-alert-time { font-size: 11px; color: var(--text-dim); margin-top: 4px; }
  .modal-no-alerts {
    text-align: center;
    padding: 40px;
    color: var(--sev-ok);
    font-size: 14px;
  }

  /* === EXPORT BUTTONS === */
  .modal-actions {
    display: flex;
    gap: 8px;
    margin-right: 8px;
  }
  .btn-export {
    background: rgba(255,255,255,0.06);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-main);
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all .2s;
  }
  .btn-export:hover {
    background: var(--accent);
    border-color: var(--accent);
    color: white;
  }
  body.light .btn-export {
    background: #f1f5f9;
  }
  body.light .btn-export:hover {
    background: var(--accent);
    color: white;
  }

  /* === ACK STYLES === */
  .btn-ack {
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 4px;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
  }
  .btn-ack:hover { transform: scale(1.05); filter: brightness(1.1); }
  
  .ack-badge {
    color: var(--sev-ok);
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  #ack-modal .modal-box { width: 400px; }
  .ack-input {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-main);
    padding: 12px;
    font-size: 13px;
    margin: 10px 0;
    outline: none;
    resize: none;
  }
  body.light .ack-input { background: #f1f5f9; color: #000; }
  .btn-ack-submit {
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
  }

  .ack-list-item {
    font-size: 11px;
    border-left: 2px solid var(--accent);
    padding-left: 8px;
    margin-top: 6px;
    color: var(--text-muted);
  }

</style>
</head>
<body>

<!-- Loader -->
<div class="loader-overlay" id="loader">
  <div class="spinner"></div>
  <div style="text-align:center">
    <span id="loader-msg" style="font-size:14px;color:var(--text-main);font-weight:600">Cargando Zabbix...</span>
  </div>
</div>

<!-- Host Detail Modal -->
<div class="modal-overlay" id="host-modal" onclick="closeModal(event)">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-host-dot" id="modal-dot"></div>
      <div style="flex:1;min-width:0">
        <div class="modal-title" id="modal-host-name">Host</div>
        <div class="modal-subtitle" id="modal-host-meta"></div>
      </div>
      <div class="modal-actions" id="modal-actions" style="display:none">
        <button class="btn-export" onclick="exportKpiToXls()" title="Exportar a Excel">
          <span style="font-size:14px">📊</span> XLS
        </button>
        <button class="btn-export" onclick="exportKpiToPdf()" title="Exportar a PDF">
          <span style="font-size:14px">📄</span> PDF
        </button>
      </div>
      <div class="modal-close" onclick="document.getElementById('host-modal').classList.remove('open')">✕</div>
    </div>
    <div class="modal-body" id="modal-body"></div>
  </div>
</div>

<!-- Progress bar for auto-refresh -->
<div class="auto-bar" id="auto-bar" style="width:100%"></div>

<!-- ACK Modal -->
<div class="modal-overlay" id="ack-modal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box">
    <div class="modal-header">
      <div style="flex:1">
        <div class="modal-title">Reconocer Evento</div>
        <div class="modal-subtitle">Registrar comentario en Zabbix</div>
      </div>
      <div class="modal-close" onclick="document.getElementById('ack-modal').classList.remove('open')">✕</div>
    </div>
    <div class="modal-body">
      <div id="ack-event-name" style="font-size:12px;color:var(--text-muted);margin-bottom:10px"></div>
      <textarea id="ack-message" class="ack-input" rows="3" placeholder="Ej: Revisando enlaces, proveedor notificado..."></textarea>
      <button class="btn-ack-submit" onclick="submitAck()">Confirmar Reconocimiento</button>
    </div>
  </div>
</div>


<!-- Header -->
<header class="header">
  <div class="header-brand">
    <div class="header-logo">📡</div>
    <div>
      <div class="header-title">Monitor de Red</div>
      <div class="header-sub">TNA Solutions — Zabbix Dashboard</div>
    </div>
  </div>
  <div class="header-controls">
    <span id="last-update">—</span>
    <select id="refresh-interval" onchange="resetAutoRefresh()" title="Intervalo de actualización" style="background:rgba(255,255,255,0.05);color:var(--text-main);border:1px solid var(--border);border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer">
      <option value="30">30s</option>
      <option value="60">1m</option>
      <option value="300" selected>5m</option>
      <option value="600">10m</option>
    </select>
    <button class="btn-theme" id="btn-sound" onclick="toggleSound()" title="Activar sonido de alertas">🔇</button>
    <button class="btn-theme" id="btn-theme" onclick="toggleTheme()" title="Cambiar tema">☀️</button>
    <button class="btn-refresh" id="btn-refresh" onclick="loadAll()">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
      </svg>
      Actualizar
    </button>
  </div>
</header>

<!-- Main -->
<div class="container">

  <!-- KPIs -->
  <div class="kpi-grid">
    <div class="kpi-card kpi-total">
      <span class="kpi-icon">🖥</span>
      <div class="kpi-label">Total Hosts</div>
      <div class="kpi-value" id="kpi-total">—</div>
    </div>
    <div class="kpi-card kpi-ok" style="cursor:pointer" onclick="openKpiModal(0,'Disponibles','✅')">
      <span class="kpi-icon">✅</span>
      <div class="kpi-label">Disponibles</div>
      <div class="kpi-value" id="kpi-ok">—</div>
    </div>
    <div class="kpi-card kpi-down" style="cursor:pointer" onclick="openKpiModal(2,'Caídos (ICMP)','🔴')">
      <span class="kpi-icon">🔴</span>
      <div class="kpi-label">Caídos (ICMP)</div>
      <div class="kpi-value" id="kpi-down">—</div>
    </div>
    <div class="kpi-card kpi-link" style="cursor:pointer" onclick="openKpiModal(3,'Link Down','🔗')">
      <span class="kpi-icon">🔗</span>
      <div class="kpi-label">Link Down</div>
      <div class="kpi-value" id="kpi-link">—</div>
    </div>
    <div class="kpi-card kpi-problems" style="cursor:pointer" onclick="openKpiModal(1,'Con alertas','⚠️')">
      <span class="kpi-icon">⚠️</span>
      <div class="kpi-label">Con alertas</div>
      <div class="kpi-value" id="kpi-unknown">—</div>
    </div>
    <div class="kpi-card kpi-alerts">
      <span class="kpi-icon">🚨</span>
      <div class="kpi-label">Alertas activas</div>
      <div class="kpi-value" id="kpi-alerts">—</div>
    </div>
    <div class="kpi-card" style="cursor:pointer; border-left: 4px solid var(--sev-ok)" onclick="openKpiModal('ack','Reconocidos','✔')">
      <span class="kpi-icon">📜</span>
      <div class="kpi-label">Reconocidos (ACK)</div>
      <div class="kpi-value" id="kpi-ack">0</div>
    </div>
  </div>

  </div>

  <!-- Tabs Navigation -->
  <div class="tabs-nav" style="margin-bottom:12px; display:flex; align-items:center; gap:12px; justify-content:space-between; flex-wrap:wrap">
    <div style="display:flex; gap:12px; flex-wrap:wrap">
      <button class="tab-btn active" onclick="switchTab('dashboard')">📊 Dashboard General</button>
      <button class="tab-btn" onclick="switchTab('sla')">📈 SLA</button>
      <button class="tab-btn" onclick="switchTab('turno')">🕐 Turno</button>
      <button class="tab-btn" onclick="switchTab('historial')">🔍 Historial</button>
      
      <select id="filter-group" onchange="filterHosts()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:6px;padding:6px 12px;font-size:13px;cursor:pointer;min-width:180px">
        <option value="all">Todos los grupos</option>
      </select>

      <select id="filter-severity" onchange="filterHosts()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:6px;padding:6px 12px;font-size:13px;cursor:pointer">
        <option value="all">Todas las severidades</option>
        <option value="5">Desastre (Crítico)</option>
        <option value="4">Alta</option>
        <option value="3">Media (Incidencia)</option>
        <option value="2">Advertencia</option>
        <option value="1">Información</option>
      </select>
    </div>
    <div class="search-box" style="flex:1; min-width:300px; max-width:400px; margin:0; position:relative;">
      <input type="text" id="global-search" placeholder="Filtrar hosts por nombre, grupo o código..." onkeyup="handleGlobalSearch()" style="width:100%; padding-right:30px; box-sizing:border-box;">
      <button onclick="document.getElementById('global-search').value=''; handleGlobalSearch();" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-muted); font-size:18px; padding:0; display:flex; align-items:center; justify-content:center; width:20px; height:20px;" title="Limpiar búsqueda">&times;</button>
    </div>
  </div>

  <div class="main-grid">
    <!-- Center Column (Togglable) -->
    <div class="center-content">
      
      <!-- Tab: Dashboard -->
      <div id="tab-dashboard" class="tab-content active">
        <!-- Log de Eventos (Ahora arriba) -->
        <div class="panel" id="event-log-panel">
          <div class="panel-header">
            <div class="panel-title">🕓 Últimos Eventos
              <span class="badge badge-red" id="event-log-count">0</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <button class="btn-refresh" style="padding:4px 8px;font-size:11px;background:var(--sev-ok);border:none;border-radius:4px;color:#fff;cursor:pointer" onclick="exportEventLogToXls()" title="Exportar a Excel">📊 XLS</button>
              <button class="btn-refresh" style="padding:4px 8px;font-size:11px;background:var(--sev-disaster);border:none;border-radius:4px;color:#fff;cursor:pointer" onclick="exportEventLogToPdf()" title="Exportar a PDF">📄 PDF</button>
              <div style="width:1px;height:16px;background:var(--border);margin:0 4px"></div>
              
              <label style="font-size:12px;color:var(--text-muted)" for="filter-event-status">Estado:</label>
              <select id="filter-event-status" onchange="filterHosts()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:3px 8px;font-size:12px;cursor:pointer">
                <option value="all">Todos</option>
                <option value="1" selected>Problem</option>
                <option value="0">Resolved</option>
              </select>
              <div style="width:1px;height:16px;background:var(--border);margin:0 4px"></div>

              <label style="font-size:12px;color:var(--text-muted)" for="event-log-limit">Mostrar:</label>
              <select id="event-log-limit" onchange="loadEventLog()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:3px 8px;font-size:12px;cursor:pointer">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100" selected>100</option>
                <option value="500">500</option>
              </select>
            </div>
          </div>
          <div style="overflow-x:auto;max-height:520px;overflow-y:auto">
            <table class="problems-table" style="width:100%">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Host</th>
                  <th>Grupo</th>
                  <th>Alerta</th>
                  <th>Tipo</th>
                  <th>ACK</th>
                </tr>
              </thead>
              <tbody id="event-log-body">
                <tr><td colspan="7" class="empty-msg">Cargando…</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div> <!-- end center-content -->

    <!-- Sidebar (Always Visible) -->
    <div class="sidebar">
      <div class="panel" style="height:100%; display:flex; flex-direction:column">
        <div class="panel-header">
          <div class="panel-title">📊 Alertas por Severidad</div>
        </div>
        <div class="chart-wrap" style="flex:1; display:flex; align-items:center; justify-content:center; padding:20px">
          <canvas id="sev-chart" style="max-height:300px"></canvas>
        </div>
      </div>
    </div>

  </div> <!-- end main-grid -->

<!-- ====== TAB: SLA ====== -->
<div id="tab-sla" class="tab-panel" style="display:none">
  <div class="container" style="padding-top:8px">
    <div class="panel">
      <div class="panel-header" style="flex-wrap:wrap;gap:8px">
        <div class="panel-title">📈 Disponibilidad SLA por Host</div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <label style="font-size:12px;color:var(--text-muted)">Período:</label>
          <select id="sla-days" onchange="loadSLA()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px">
            <option value="7">Últimos 7 días</option>
            <option value="30" selected>Últimos 30 días</option>
            <option value="60">Últimos 60 días</option>
            <option value="90">Últimos 90 días</option>
          </select>
          <select id="sla-filter-group" onchange="renderSLA()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px">
            <option value="all">Todos los grupos</option>
          </select>
          <label style="font-size:12px;color:var(--text-muted)">Umbral crítico:</label>
          <select id="sla-threshold" onchange="renderSLA()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px">
            <option value="99">99%</option>
            <option value="99.5">99.5%</option>
            <option value="99.9">99.9%</option>
            <option value="95" selected>95%</option>
          </select>
          <button onclick="exportSLAXls()" style="background:var(--bg-card);color:var(--text-muted);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px;cursor:pointer">⬇ Excel</button>
        </div>
      </div>
      <!-- KPIs resumen SLA -->
      <div id="sla-kpis" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;padding:12px 16px 4px"></div>
      <!-- Tabla SLA -->
      <div style="overflow-x:auto;padding:0 8px 8px">
        <table style="width:100%;border-collapse:collapse;font-size:13px" id="sla-table">
          <thead>
            <tr style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">
              <th style="padding:8px 12px;text-align:left;background:var(--table-head);position:sticky;top:0">Host</th>
              <th style="padding:8px 12px;text-align:left;background:var(--table-head);position:sticky;top:0">Grupo</th>
              <th style="padding:8px 12px;text-align:center;background:var(--table-head);position:sticky;top:0">SLA %</th>
              <th style="padding:8px 12px;text-align:center;background:var(--table-head);position:sticky;top:0">Downtime</th>
              <th style="padding:8px 12px;text-align:center;background:var(--table-head);position:sticky;top:0">Incidentes</th>
              <th style="padding:8px 12px;text-align:center;background:var(--table-head);position:sticky;top:0">MTTR</th>
              <th style="padding:8px 12px;text-align:center;background:var(--table-head);position:sticky;top:0">Estado</th>
            </tr>
          </thead>
          <tbody id="sla-tbody">
            <tr><td colspan="7" style="padding:32px;text-align:center;color:var(--text-muted)">Cargando SLA…</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ====== TAB: TURNO ====== -->
<div id="tab-turno" class="tab-panel" style="display:none">
  <div class="container" style="padding-top:8px">
    <div class="panel">
      <div class="panel-header" style="flex-wrap:wrap;gap:8px">
        <div class="panel-title">🕐 Resumen de Turno</div>
        <div style="display:flex;align-items:center;gap:8px">
          <label style="font-size:12px;color:var(--text-muted)">Período:</label>
          <select id="turno-hours" onchange="loadTurno()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px">
            <option value="8">Últimas 8 horas</option>
            <option value="12">Últimas 12 horas</option>
            <option value="24" selected>Últimas 24 horas</option>
            <option value="48">Últimas 48 horas</option>
            <option value="168">Última semana</option>
          </select>
        </div>
      </div>
      <!-- KPIs turno -->
      <div id="turno-kpis" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;padding:12px 16px"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:0 16px 16px">
        <!-- Por técnico -->
        <div>
          <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Por Técnico</div>
          <div id="turno-tech"></div>
        </div>
        <!-- Últimos eventos -->
        <div>
          <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Últimos eventos atendidos</div>
          <div id="turno-recent" style="max-height:400px;overflow-y:auto"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ====== TAB: HISTORIAL FILTRABLE ====== -->
<div id="tab-historial" class="tab-panel" style="display:none">
  <div class="container" style="padding-top:8px">
    <div class="panel">
      <div class="panel-header" style="flex-wrap:wrap;gap:8px">
        <div class="panel-title">🔍 Historial de Eventos</div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <input type="date" id="hist-from" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 8px;font-size:12px">
          <span style="color:var(--text-muted);font-size:12px">→</span>
          <input type="date" id="hist-to" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 8px;font-size:12px">
          <select id="hist-user" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px">
            <option value="">Todos los técnicos</option>
          </select>
          <select id="hist-severity" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px">
            <option value="-1">Todas las severidades</option>
            <option value="0">Not classified</option>
            <option value="1">Information</option>
            <option value="2">Warning</option>
            <option value="3">Average</option>
            <option value="4">High</option>
            <option value="5">Disaster</option>
          </select>
          <select id="hist-limit" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px">
            <option value="50">50 resultados</option>
            <option value="100" selected>100 resultados</option>
            <option value="200">200 resultados</option>
            <option value="500">500 resultados</option>
          </select>
          <button onclick="loadHistorial()" style="background:var(--accent);color:#fff;border:none;border-radius:4px;padding:5px 14px;font-size:12px;cursor:pointer;font-weight:600">Buscar</button>
          <button onclick="exportHistorialXls()" style="background:var(--bg-card);color:var(--text-muted);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:12px;cursor:pointer">⬇ Excel</button>
        </div>
      </div>
      <div id="hist-count" style="padding:4px 16px;font-size:12px;color:var(--text-muted)"></div>
      <div style="overflow-x:auto;padding:0 8px 8px">
        <table style="width:100%;border-collapse:collapse;font-size:12px">
          <thead>
            <tr style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">
              <th style="padding:8px 12px;text-align:left;background:var(--table-head);position:sticky;top:0">Fecha</th>
              <th style="padding:8px 12px;text-align:left;background:var(--table-head);position:sticky;top:0">Hora</th>
              <th style="padding:8px 12px;text-align:left;background:var(--table-head);position:sticky;top:0">Host</th>
              <th style="padding:8px 12px;text-align:left;background:var(--table-head);position:sticky;top:0">Alerta</th>
              <th style="padding:8px 12px;text-align:center;background:var(--table-head);position:sticky;top:0">Sev.</th>
              <th style="padding:8px 12px;text-align:left;background:var(--table-head);position:sticky;top:0">Técnico</th>
              <th style="padding:8px 12px;text-align:left;background:var(--table-head);position:sticky;top:0">Comentario</th>
            </tr>
          </thead>
          <tbody id="hist-tbody">
            <tr><td colspan="7" style="padding:32px;text-align:center;color:var(--text-muted)">Selecciona un rango de fechas y presiona Buscar</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<!-- ====== KPI POR SERVICIO + LOG DE EVENTOS (2 columnas) ====== -->
<div class="container" style="margin-top:0;padding-top:0">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start">

    <!-- KPI por Servicio -->
    <div class="panel" id="service-kpi-panel">
      <div class="panel-header">
        <div class="panel-title">🏆 KPI por Servicio
          <span class="badge" id="service-kpi-count">0</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <label style="font-size:12px;color:var(--text-muted)">Ordenar:</label>
          <select id="service-kpi-sort" onchange="renderServiceRanking()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:3px 8px;font-size:12px;cursor:pointer">
            <option value="worst">Peores primero</option>
            <option value="best">Mejores primero</option>
          </select>
        </div>
      </div>
      <div id="service-kpi-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:8px;padding:12px;max-height:380px;overflow-y:auto">
        <div class="empty-msg">Cargando…</div>
      </div>
    </div>

    <!-- Estado de la Red (Ahora abajo) -->
    <div class="panel" id="network-status-panel">
      <div class="panel-header" style="flex-wrap:wrap; gap:8px">
        <div class="panel-title">
          🖥️ Estado de la Red
          <span class="badge" id="hosts-count">0</span>
          <span class="badge badge-red" id="problems-count" style="margin-left:4px">0</span>
        </div>
        <div style="display:flex;align-items:center;gap:6px">
          <select id="filter-network-status" onchange="filterHosts()" style="background:var(--bg-card);color:var(--text-main);border:1px solid var(--border);border-radius:4px;padding:3px 8px;font-size:12px;cursor:pointer">
            <option value="all">Todos los estados</option>
            <option value="0">OK</option>
            <option value="1">ALERTAS</option>
            <option value="2">CAÍDO</option>
            <option value="3">LINK DOWN</option>
          </select>
          <input type="checkbox" id="filter-only-alerts" onchange="filterHosts()" style="cursor:pointer">
          <label for="filter-only-alerts" style="font-size:12px;color:var(--text-muted);cursor:pointer;user-select:none;margin-right:6px" title="Muestra alertas, caídos y link down">Solo con alertas</label>
          <button onclick="exportNetworkStatusXls()" style="background:#217346;color:#fff;border:none;border-radius:4px;padding:4px 10px;font-size:11px;cursor:pointer;font-weight:600" title="Exportar a Excel">📥 XLS</button>
          <button onclick="exportNetworkStatusPdf()" style="background:#F40F02;color:#fff;border:none;border-radius:4px;padding:4px 10px;font-size:11px;cursor:pointer;font-weight:600" title="Exportar a PDF">📥 PDF</button>
        </div>
      </div>
      <div class="hosts-list" id="hosts-list" style="max-height:380px;overflow-y:auto">
        <div class="empty-msg">Cargando…</div>
      </div>
    </div>




<script>
const API = 'api_proxy.php';
const CURRENT_USER = '<?php echo $_SESSION["user_full_name"] ?? $_SESSION["cliente"] ?? $_SESSION["usuario"] ?? $_SESSION["name"] ?? "Técnico"; ?>';
let refreshInterval = 300; // segundos (dinámico)

let allProblems  = [];
let allHosts     = [];
let allEvents    = [];
let sevChart     = null;
let refreshTimer;
let countdown;
let currentTab   = 'dashboard';
let eventLogLoading = false;
let knownProblemIds = new Set();
let isFirstLoad = true;
let soundEnabled = false;

// Global error handler for UI feedback
window.onerror = function(msg, url, lineNo, columnNo, error) {
  const loaderMsg = document.getElementById('loader-msg');
  if (loaderMsg) {
    loaderMsg.innerHTML = `<span style="color:var(--sev-disaster);font-weight:700">❌ Error:</span> ${msg}`;
  }
  return false;
};

function switchTab(tabId) {
  currentTab = tabId;

  // Update button active state
  document.querySelectorAll('.tab-btn').forEach(btn => {
    const isTarget = btn.getAttribute('onclick')?.includes(`'${tabId}'`);
    btn.classList.toggle('active', isTarget);
  });

  // Show/hide tab-content (dashboard)
  document.querySelectorAll('.tab-content').forEach(cont => {
    cont.classList.toggle('active', cont.id === `tab-${tabId}`);
  });

  // Show/hide tab-panel (sla, turno, historial)
  document.querySelectorAll('.tab-panel').forEach(panel => {
    panel.style.display = panel.id === `tab-${tabId}` ? 'block' : 'none';
  });

  // Also hide the bottom panels (service kpi + network status) for non-dashboard tabs
  const bottomPanels = document.querySelector('.container[style*="margin-top:0"]');
  if (bottomPanels) bottomPanels.style.display = tabId === 'dashboard' ? '' : 'none';

  // Load data for the new tab
  if (tabId === 'dashboard') loadAll();
  else if (tabId === 'sla')       { if (!slaData.length) loadSLA(); }
  else if (tabId === 'turno')     { loadTurno(); }
  else if (tabId === 'historial') { initHistorial(); }
}

function handleGlobalSearch() {
  filterHosts();
}

const SEV_LABELS = ['No clasificado', 'Información', 'Warning', 'Promedio', 'Alto', 'Desastre'];
const SEV_COLORS = ['#607d8b','#42a5f5','#ffc107','#ff9800','#ff5722','#f44336'];

// =====================
//  DATA LOADING
// =====================
async function loadAll() {
  const btn = document.getElementById('btn-refresh');
  const loader = document.getElementById('loader');
  const loaderMsg = document.getElementById('loader-msg');
  const loaderDebug = document.getElementById('loader-debug');
  
  if (btn) btn.classList.add('loading');
  
  const updateStatus = (msg) => {
    if (loaderMsg) loaderMsg.textContent = msg;
  };

  try {
    updateStatus('Cargando datos...');

    // OPTIMIZACIÓN: dashboard + problems en paralelo.
    // dashboard = stats + hosts en un solo round-trip al servidor.
    const [dashboard, problems] = await Promise.all([
      fetch(`${API}?action=dashboard`).then(async r => {
        if (!r.ok) throw new Error(`HTTP ${r.status} en dashboard`);
        return r.json();
      }),
      fetch(`${API}?action=problems`).then(async r => {
        if (!r.ok) throw new Error(`HTTP ${r.status} en problemas`);
        return r.json();
      }),
    ]);

    const stats = dashboard.stats;
    const hosts = dashboard.hosts;

    if (stats.error) throw new Error(stats.error);
    renderKPIs(stats);

    allProblems = problems;
    renderProblems(problems);

    allHosts = hosts;
    populateFilters(hosts);
    renderHosts(hosts);

    updateStatus('Preparando vista...');
    try {
      renderChart(stats.by_severity ?? {});
    } catch(ce) { console.warn('Gráfico falló:', ce); }
    
    renderServiceRanking();
    updateTimestamp();
    
    updateStatus('Finalizando...');
    await loadEventLog();
    
    if (loader) loader.classList.add('hide');
  } catch(e) {
    console.error('Error:', e);
    if (loaderMsg) {
      loaderMsg.innerHTML = `<span style="color:var(--sev-disaster)">❌ ERROR</span><br><small style="font-size:11px">${e.message}</small>`;
      const spinner = loader.querySelector('.spinner');
      if (spinner) spinner.style.display = 'none';
    }
  } finally {
    if (btn) btn.classList.remove('loading');
    resetAutoRefresh();
  }
}


function formatBits(bits) {
  if (bits === 0) return '0 bps';
  const k = 1000;
  const sizes = ['bps', 'Kbps', 'Mbps', 'Gbps'];
  const i = Math.floor(Math.log(bits) / Math.log(k));
  return parseFloat((bits / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// =====================
//  KPIs
// =====================
function renderKPIs(stats) {
  setVal('kpi-total',   stats.total     ?? '?');
  setVal('kpi-ok',      stats.ok        ?? '?');
  setVal('kpi-down',    stats.down      ?? '?');
  setVal('kpi-link',    stats.link_down ?? '?');
  setVal('kpi-unknown', stats.alerts    ?? '?');
  setVal('kpi-alerts',  stats.problems  ?? '?');
  setVal('kpi-ack',     stats.acknowledged ?? '0');
}

function setVal(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

// =====================
//  PROBLEMS
// =====================
function renderProblems(data) {
  allProblems = Array.isArray(data) ? data : [];
  document.getElementById('problems-count').textContent = allProblems.length;
  // Problems are now rendered inline in the host list
  filterHosts();
}

// =====================
//  HOSTS
// =====================
function renderHosts(data) {
  allHosts = Array.isArray(data) ? data : [];
  // Sort alphabetically by name
  allHosts.sort((a, b) => a.name.localeCompare(b.name, 'es', { sensitivity: 'base' }));
  document.getElementById('hosts-count').textContent = allHosts.length;
  filterHosts();
}

// =====================
//  FILTERING
// =====================
function populateFilters(hosts) {
  const groupSelect = document.getElementById('filter-group');
  if (!groupSelect || groupSelect.options.length > 1) return;

  const groups = [...new Set(hosts.map(h => h.group))].sort();
  groups.forEach(g => {
    const opt = document.createElement('option');
    opt.value = g;
    opt.textContent = g;
    groupSelect.appendChild(opt);
  });
}

function filterHosts() {
  try {
    const q = document.getElementById('global-search').value.toLowerCase();
    const group = document.getElementById('filter-group').value;
    const severityLimit = document.getElementById('filter-severity').value;

    const filtered = (allHosts || []).filter(h => {
      // Search query match
      const hName = (h.name || '').toLowerCase();
      const hCode = (h.service_code || '').toLowerCase();
      const hGroup = (h.group || '').toLowerCase();
      
      const matchesQuery = hName.includes(q) || hCode.includes(q) || hGroup.includes(q);
      
      // Group match
      const matchesGroup = group === 'all' || h.group === group;

      // Severity match
      let matchesSeverity = true;
      if (severityLimit !== 'all') {
        const limit = parseInt(severityLimit, 10);
        const hostProblems = (allProblems || []).filter(p => p.host === h.name);
        matchesSeverity = hostProblems.some(p => parseInt(p.severity, 10) >= limit);
      }

      // Exact Status match (Dropdown)
      let matchesNetworkStatus = true;
      const statusDropdown = document.getElementById('filter-network-status');
      if (statusDropdown && statusDropdown.value !== 'all') {
        matchesNetworkStatus = String(h.status) === String(statusDropdown.value);
      }

      // "Only Alerts" match (Checkbox)
      let matchesOnlyAlerts = true;
      const onlyAlertsCb = document.getElementById('filter-only-alerts');
      if (onlyAlertsCb && onlyAlertsCb.checked) {
        // As defined by existing logic: any status > 0 has some kind of problem
        // (1 = alerts, 2 = icmp down, 3 = link down)
        matchesOnlyAlerts = h.status > 0;
      }

      return matchesQuery && matchesGroup && matchesSeverity && matchesNetworkStatus && matchesOnlyAlerts;
    });

    const filteredEvents = (allEvents || []).filter(ev => {
      const evName = (ev.name || '').toLowerCase();
      const evHost = (ev.host || '').toLowerCase();
      const matchesQuery = evName.includes(q) || evHost.includes(q);
      
      let matchesGroup = true;
      if (group !== 'all') {
        const evGroup = (ev.group || '').toLowerCase();
        if (evGroup !== group.toLowerCase()) {
          matchesGroup = false;
        }
      }

      let matchesSeverity = true;
      if (severityLimit !== 'all') {
        matchesSeverity = parseInt(ev.severity, 10) >= parseInt(severityLimit, 10);
      }

      let matchesStatus = true;
      const statusLimitElem = document.getElementById('filter-event-status');
      if (statusLimitElem) {
        const statusLimit = statusLimitElem.value;
        if (statusLimit !== 'all') {
          matchesStatus = String(ev.value) === String(statusLimit);
        }
      }

      return matchesQuery && matchesGroup && matchesSeverity && matchesStatus;
    });

  // Re-render events
  window._currentFilteredEvents = filteredEvents;
  renderEventLog(filteredEvents);

  const container = document.getElementById('hosts-list');
  if (!filtered.length) {
    container.innerHTML = '<div class="empty-msg">Sin coincidencias</div>';
    return;
  }

  const nowSecs = Math.floor(Date.now() / 1000);

  container.innerHTML = filtered.map((h, idx) => {
    const { dotClass, txtClass, label } = hostStatus(h.status);

    // Duration since last state change (UP or DOWN)
    let durationHtml = '';
    const isDown = h.status === 2 || h.status === 3;
    
    if (h.last_change && h.last_change > 0) {
      const diffSecs = nowSecs - h.last_change;
      const timeStr  = timeAgo(diffSecs, true);
      const color = isDown ? 'var(--sev-disaster)' : (h.status === 0 ? 'var(--sev-ok)' : 'var(--sev-warning)');
      const icon = isDown ? '⬇' : '⬆';
      durationHtml = `<span style="font-size:10px;color:${color};font-weight:600;margin:0 10px">${icon} ${timeStr}</span>`;
    } else {
      durationHtml = `<span style="font-size:10px;color:var(--text-dim);margin:0 10px">⬆ —</span>`;
    }

    // Active problems count/badge
    const hostProblems = allProblems.filter(p => p.host === h.name);
    let problemsHtml = '';
    if (hostProblems.length > 0) {
      problemsHtml = `<span style="font-size:10px;color:var(--sev-disaster);font-weight:700;margin-right:10px" title="${hostProblems.map(p => p.name).join(', ')}">🚨 ${hostProblems.length}</span>`;
    }

    return `
      <div class="host-item" style="cursor:pointer;display:flex;align-items:center;padding:6px 12px;gap:8px" onclick="openHostModal(${idx})">
        <div class="host-dot ${dotClass}" style="flex-shrink:0"></div>
        <div style="flex:1;display:flex;align-items:center;overflow:hidden;white-space:nowrap">
          <span class="hname" style="font-weight:700;margin-right:8px" title="${esc(h.name)}">${esc(h.name)}</span>
          <span class="hgroup" style="font-size:10px;color:var(--text-dim);opacity:0.7">${h.service_code ? `${esc(h.service_code)} · ` : ''}${esc(h.group)}</span>
        </div>
        ${durationHtml}
        ${problemsHtml}
        <span class="host-status-txt ${txtClass}" style="font-size:9px;padding:2px 8px;border-radius:10px;margin-left:auto">${label}</span>
      </div>
    `;
  }).join('');

  // Store filtered list for modal access
  window._filteredHosts = filtered;
  
  } catch(e) {
    console.error('Crash preventing filtering:', e);
  }
}


function hostStatus(status) {
  if (status === 2) return { dotClass: 'dot-down',   txtClass: 'txt-down',   label: 'CAÍDO' };
  if (status === 3) return { dotClass: 'dot-alerts', txtClass: 'txt-alerts', label: 'LINK DOWN' };
  if (status === 1) return { dotClass: 'dot-alerts', txtClass: 'txt-alerts', label: 'ALERTAS' };
  return                   { dotClass: 'dot-ok',     txtClass: 'txt-ok',     label: 'OK' };
}

// =====================
//  SERVICE RANKING
// =====================
function renderServiceRanking() {
  const grid = document.getElementById('service-kpi-grid');
  if (!grid || !allHosts.length) return;

  const sortMode = document.getElementById('service-kpi-sort')?.value ?? 'worst';

  const groups = {};
  for (const h of allHosts) {
    const g = h.group || 'Sin grupo';
    if (!groups[g]) groups[g] = { total: 0, ok: 0, alerts: 0, down: 0, link: 0 };
    groups[g].total++;
    if      (h.status === 0) groups[g].ok++;
    else if (h.status === 1) groups[g].alerts++;
    else if (h.status === 2) groups[g].down++;
    else if (h.status === 3) groups[g].link++;
  }

  let services = Object.entries(groups).map(([name, s]) => {
    const score = Math.round((s.ok / s.total) * 100);
    return { name, ...s, score };
  });

  services.sort((a, b) => sortMode === 'worst' ? a.score - b.score : b.score - a.score);
  document.getElementById('service-kpi-count').textContent = services.length;

  grid.innerHTML = services.map(s => {
    const color = s.score >= 90 ? 'var(--sev-ok)' :
                  s.score >= 60 ? 'var(--sev-warning)' :
                  s.score >  0  ? 'var(--sev-high)' : 'var(--sev-disaster)';
    const medal = s.score === 100 ? '🟢' : s.score >= 90 ? '🟡' : s.score >= 60 ? '🟠' : '🔴';
    return `
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:12px;display:flex;flex-direction:column;gap:6px;">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:2px">
          <span style="font-size:15px">${medal}</span>
          <div style="font-size:12px;font-weight:700;color:var(--text-main);overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${esc(s.name)}">${esc(s.name)}</div>
        </div>
        <div style="background:var(--bg-dark);border-radius:4px;height:6px;overflow:hidden">
          <div style="height:100%;width:${s.score}%;background:${color};border-radius:4px;transition:width 0.5s ease"></div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:22px;font-weight:800;color:${color}">${s.score}%</span>
          <span style="font-size:10px;color:var(--text-dim)">${s.total} host${s.total !== 1 ? 's' : ''}</span>
        </div>
        <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:2px">
          ${s.ok     ? `<span style="font-size:10px;background:rgba(76,175,80,.15);color:var(--sev-ok);padding:1px 6px;border-radius:10px">✔ ${s.ok} OK</span>` : ''}
          ${s.alerts ? `<span style="font-size:10px;background:rgba(255,193,7,.15);color:var(--sev-warning);padding:1px 6px;border-radius:10px">⚠ ${s.alerts} alerta${s.alerts !== 1 ? 's' : ''}</span>` : ''}
          ${s.down   ? `<span style="font-size:10px;background:rgba(244,67,54,.15);color:var(--sev-disaster);padding:1px 6px;border-radius:10px">↓ ${s.down} caído</span>` : ''}
          ${s.link   ? `<span style="font-size:10px;background:rgba(255,87,34,.15);color:var(--sev-high);padding:1px 6px;border-radius:10px">🔗 ${s.link} link</span>` : ''}
        </div>
      </div>
    `;
  }).join('');
}

// =====================
//  EVENT LOG (GLOBAL)
// =====================
async function loadEventLog() {
  if (eventLogLoading) return;
  eventLogLoading = true;
  const tbody = document.getElementById('event-log-body');
  if (!tbody) { eventLogLoading = false; return; }
  tbody.innerHTML = '<tr><td colspan="5" class="empty-msg">Cargando…</td></tr>';

  const limit = document.getElementById('event-log-limit')?.value ?? 50;
  try {
    const events = await fetch(`${API}?action=recent_events&limit=${limit}`).then(r => r.json());
    if (Array.isArray(events)) {
      allEvents = events;
      
      let newProblemFound = false;
      const currentActiveIds = new Set();
      
      events.forEach(ev => {
        if (ev.value === '1' || ev.value === 1) {
          currentActiveIds.add(ev.eventid);
          if (!isFirstLoad && !knownProblemIds.has(ev.eventid)) {
            newProblemFound = true;
          }
        }
      });
      
      if (newProblemFound) {
        try {
          const audio = new Audio('beep_alert.ogg'); // Archivo local — no depende de Google
          if (soundEnabled) {
            audio.play().catch(e => console.warn('Audio play failed:', e));
          }
          
          const titleEl = document.querySelector('.header-title');
          if (titleEl) {
            const origColor = titleEl.style.color;
            const origText = titleEl.textContent;
            titleEl.textContent = "🚨 NUEVA ALERTA 🚨";
            titleEl.style.color = "var(--sev-disaster)";
            setTimeout(() => {
              titleEl.textContent = origText;
              titleEl.style.color = origColor;
            }, 5000);
          }
        } catch(e) {}
      }
      
      knownProblemIds = currentActiveIds;
      isFirstLoad = false;
      
    } else {
      allEvents = [];
    }
    // Re-apply filters which will also render the event log
    filterHosts();
  } catch(e) {
    console.error('Error cargando log de eventos:', e);
    tbody.innerHTML = '<tr><td colspan="7" class="empty-msg" style="color:var(--sev-disaster)">❌ Error cargando eventos</td></tr>';
  } finally {
    eventLogLoading = false;
  }
}

function renderEventLog(events) {
  const tbody = document.getElementById('event-log-body');
  if (!tbody) return;

  document.getElementById('event-log-count').textContent = Array.isArray(events) ? events.length : 0;

  if (!Array.isArray(events) || !events.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="empty-msg">Sin eventos recientes</td></tr>';
    return;
  }

  tbody.innerHTML = events.map(ev => {
    const hostSafe = esc(ev.host).replace(/'/g, "\\'");
    const isProblem = ev.value === '1' || ev.value === 1;
    const rowBg = isProblem ? '' : 'background:rgba(76,175,80,0.04)';
    const typeIcon = isProblem ? '🔴' : '🟢';
    const typeLabel = isProblem ? 'PROBLEM' : 'RESOLVED';
    const typeColor = isProblem ? 'var(--sev-disaster)' : 'var(--sev-ok)';
    
    const startDate = formatDateOnly(ev.clock);
    const startTime = formatTimeOnly(ev.clock);
    const endDate = ev.r_clock ? formatDateOnly(ev.r_clock) : null;
    const endTime = ev.r_clock ? formatTimeOnly(ev.r_clock) : null;
    const duration = ev.r_clock ? timeAgo(ev.r_clock - ev.clock, true) : null;

    return `
      <tr style="cursor:pointer;${rowBg}" onclick="openHostModalByName('${hostSafe}')" class="problem-row">
        <td style="white-space:nowrap;font-size:12px">
          <div style="font-weight:700">${startDate}</div>
          ${endDate ? `<div style="font-size:10px;color:var(--text-dim);margin-top:2px">Fin: ${endDate}</div>` : ''}
        </td>
        <td style="white-space:nowrap;font-size:12px">
          <div style="font-weight:700">${startTime}</div>
          ${endTime ? `<div style="font-size:10px;color:var(--text-dim);margin-top:2px">Fin: ${endTime}</div>` : ''}
        </td>
        <td>
          <div class="host-name">${esc(ev.host)}</div>
          ${duration ? `<div style="font-size:10px;color:var(--text-dim);margin-top:2px">⏳ ${duration}</div>` : ''}
        </td>
        <td style="font-size:12px;color:var(--text-main);opacity:0.9">${esc(ev.group || 'Sin grupo')}</td>
        <td class="problem-name" style="max-width:320px">${esc(ev.name)}</td>
        <td>
          <span style="font-size:11px;font-weight:700;color:${typeColor}">${typeIcon} ${typeLabel}</span>
          <div><span class="sev-badge sev-${ev.severity}">${esc(SEV_LABELS[ev.severity] ?? ev.severity)}</span></div>
        </td>
        <td>
          ${Number(ev.acknowledged) === 1 
            ? `
              <div class="ack-badge" title="Reconocido">✔ ACK</div>
              ${ev.acknowledges && ev.acknowledges.length > 0 ? `
                <div style="font-size:10px;color:var(--text-muted);margin-top:2px">
                  ${(() => {
                    const msg = ev.acknowledges[0].message;
                    // Backwards compatibility: check if it has the "ACK por..." prefix
                    const match = msg.match(/^ACK por (.*?):/);
                    return match ? esc(match[1]) : esc(ev.acknowledges[0].alias || ev.acknowledges[0].name || 'Técnico');
                  })()}
                  <br><strong>Fecha:</strong> ${formatDateOnly(ev.acknowledges[0].clock)} &nbsp;|&nbsp; <strong>Hora:</strong> ${formatTimeOnly(ev.acknowledges[0].clock)}
                </div>
              ` : ''}
            ` 
            : `<button class="btn-ack" onclick="event.stopPropagation(); openAckModal('${ev.eventid}', '${hostSafe}', '${esc(ev.name).replace(/'/g, "\\'")}', '${ev.severity}')">Dar ACK</button>`

          }
        </td>
      </tr>
    `;
  }).join('');
}



// =====================
//  CHART
// =====================
function renderChart(bySeverity) {
  const labels = [];
  const values = [];
  const colors = [];

  for (let i = 5; i >= 0; i--) {
    const v = bySeverity[i] ?? 0;
    if (v > 0) {
      labels.push(SEV_LABELS[i]);
      values.push(v);
      colors.push(SEV_COLORS[i]);
    }
  }

  const ctx = document.getElementById('sev-chart')?.getContext('2d');
  if (!ctx || typeof Chart === 'undefined') {
    console.warn('Chart.js no disponible para renderizar.');
    return;
  }

  sevChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data: values,
        backgroundColor: colors,
        borderColor: 'transparent',
        borderWidth: 0,
        hoverOffset: 8,
      }]
    },
    options: {
      cutout: '68%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#7986cb',
            font: { size: 11, family: 'Inter' },
            boxWidth: 10,
            padding: 10,
          }
        },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.label}: ${ctx.parsed}`
          }
        }
      }
    }
  });
}

// =====================
//  AUTO-REFRESH
// =====================
function resetAutoRefresh() {
  clearInterval(refreshTimer);
  clearInterval(countdown);

  if (window !== window.top) {
      document.body.classList.add('in-frame');
      const bar = document.getElementById('auto-bar');
      if (bar) bar.style.display = 'none';
      return; // Parent portal manages the refresh
  }

  const bar = document.getElementById('auto-bar');
  if (bar) {
      let elapsed = 0;
      bar.style.width = '100%';
      bar.style.transition = 'none';
      bar.style.display = 'block';

      const intervalSelect = document.getElementById('refresh-interval');
      refreshInterval = intervalSelect ? parseInt(intervalSelect.value) : 60;

      countdown = setInterval(() => {
        elapsed++;
        const pct = Math.max(0, 100 - (elapsed / refreshInterval * 100));
        bar.style.transition = 'width 1s linear';
        bar.style.width = pct + '%';
      }, 1000);

      refreshTimer = setTimeout(() => {
        loadAll();
      }, refreshInterval * 1000);
  }
}

// =====================
//  HELPERS
// =====================
function updateTimestamp() {
  const now = new Date();
  document.getElementById('last-update').textContent =
    'Actualizado: ' + now.toLocaleTimeString('es-CL');
}

function timeAgo(ts, isRaw = false) {
  const diff = isRaw ? ts : Math.floor(Date.now() / 1000) - ts;
  if (diff < 60)   return diff + 's';
  if (diff < 3600) return Math.floor(diff/60) + 'm';
  if (diff < 86400)return Math.floor(diff/3600) + 'h ' + Math.floor((diff%3600)/60) + 'm';
  return Math.floor(diff/86400) + 'd ' + Math.floor((diff%86400)/3600) + 'h';
}

function esc(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// =====================
//  HOST MODAL
// =====================
function openHostModalByName(hostname) {
  const host = allHosts.find(h => h.name === hostname);
  if (!host) {
    alert('Información del host no disponible en la vista actual.');
    return;
  }
  const { dotClass, txtClass, label } = hostStatus(host.status);
  const alerts = allProblems.filter(p => p.host === host.name);

  renderHostDetailTabs(host, dotClass, label, alerts, document.getElementById('modal-body'));
  document.getElementById('host-modal').classList.add('open');
}

function openHostModal(idx) {
  const host = (window._filteredHosts || allHosts)[idx];
  if (!host) return;
  const { dotClass, txtClass, label } = hostStatus(host.status);
  const alerts = allProblems.filter(p => p.host === host.name);

  document.getElementById('modal-actions').style.display = 'none';
  renderHostDetailTabs(host, dotClass, label, alerts, document.getElementById('modal-body'));
  document.getElementById('host-modal').classList.add('open');
}

function closeModal(e) {
  if (e.target === document.getElementById('host-modal')) {
    document.getElementById('host-modal').classList.remove('open');
  }
}

// =====================
//  KPI MODAL (drill-down)
// =====================
function openKpiModal(statusCode, label, icon) {
  let filtered = [];
  
  if (statusCode === 'ack') {
    // Especial: mostrar la lista de problemas que tienen ACK
    loadAckKpiModal(label, icon);
    return;
  }

  filtered = allHosts
    .filter(h => h.status === statusCode)
    .sort((a, b) => a.name.localeCompare(b.name, 'es', { sensitivity: 'base' }));

  // Metadata for export
  window._currentKpiLabel = label;
  window._currentKpiHosts = filtered;

  // Header
  document.getElementById('modal-actions').style.display = 'flex';
  document.getElementById('modal-dot').className = 'modal-host-dot ' +
    (statusCode === 2 ? 'dot-down' : statusCode === 3 ? 'dot-alerts' : statusCode === 1 ? 'dot-alerts' : 'dot-ok');
  document.getElementById('modal-host-name').textContent = icon + ' ' + label;
  document.getElementById('modal-host-meta').textContent = filtered.length + ' host' + (filtered.length !== 1 ? 's' : '');

  const body = document.getElementById('modal-body');

  if (!filtered.length) {
    body.innerHTML = '<div class="modal-no-alerts">✅ Sin hosts en esta categoría</div>';
  } else {
    // Store for sub-click
    window._kpiHosts = filtered;

    body.innerHTML = filtered.map((h, idx) => {
      const { dotClass, txtClass, label: stLabel } = hostStatus(h.status);
      const hostAlerts = allProblems.filter(p => p.host === h.name);
      return `
        <div class="modal-alert-item" style="cursor:pointer;flex-direction:column;gap:6px" onclick="openHostModalFromKpi(${idx})">
          <div style="display:flex;align-items:center;gap:10px">
            <div class="host-dot ${dotClass}" style="width:10px;height:10px;border-radius:50%;flex-shrink:0"></div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(h.name)}</div>
              <div style="font-size:11px;color:var(--text-dim)">${h.service_code ? `<span style="color:var(--accent);font-family:monospace">${esc(h.service_code)}</span> · ` : ''}${esc(h.group)}</div>
            </div>
            <span style="font-size:10px;font-weight:600;color:var(--${dotClass === 'dot-ok' ? 'sev-ok' : dotClass === 'dot-down' ? 'sev-disaster' : 'sev-avg'})">${stLabel}</span>
            ${hostAlerts.length ? `<span class="badge badge-red" style="font-size:10px">${hostAlerts.length}</span>` : ''}
          </div>
          ${hostAlerts.length ? `<div style="font-size:11px;color:var(--text-muted);padding-left:20px">${esc(hostAlerts[0].name)}${hostAlerts.length > 1 ? ` <em>+${hostAlerts.length-1} más</em>` : ''}</div>` : ''}
        </div>
      `;
    }).join('');
  }

  document.getElementById('host-modal').classList.add('open');
}

async function loadAckKpiModal(label, icon) {
  const body = document.getElementById('modal-body');
  document.getElementById('modal-actions').style.display = 'none';
  document.getElementById('modal-dot').className = 'modal-host-dot dot-ok';
  document.getElementById('modal-host-name').textContent = icon + ' ' + label;
  document.getElementById('modal-host-meta').textContent = 'Cargando eventos...';
  body.innerHTML = '<div class="empty-msg">Cargando eventos con ACK…</div>';
  document.getElementById('host-modal').classList.add('open');

  try {
    const ackEvents = await fetch(`${API}?action=acknowledged_events`).then(r => r.json());
    
    document.getElementById('modal-host-meta').textContent = ackEvents.length + ' evento' + (ackEvents.length !== 1 ? 's' : '');

    if (!ackEvents.length) {
      body.innerHTML = '<div class="modal-no-alerts">❌ No hay eventos recientes con ACK</div>';
    } else {
      body.innerHTML = ackEvents.map(ev => {
        const ack = (ev.acknowledges && ev.acknowledges.length > 0) ? ev.acknowledges[0] : null;
        return `
          <div class="modal-alert-item" style="flex-direction:column; gap:4px">
            <div style="display:flex; justify-content:space-between; align-items:flex-start">
              <div style="font-weight:600; font-size:13px">${esc(ev.host)}</div>
              <div class="sev-badge sev-${ev.severity}" style="font-size:9px">${esc(SEV_LABELS[ev.severity])}</div>
            </div>
            <div style="font-size:12px; color:var(--text-main)">${esc(ev.name)}</div>
            ${ack ? `
              <div class="ack-list-item" style="margin-top:4px">
                <strong>${(() => {
                  if (ev.local_user_name) return esc(ev.local_user_name);
                  const match = ack.message.match(/^ACK por (.*?):/);
                  return match ? esc(match[1]) : esc(ack.alias || ack.name || 'Técnico');
                })()}:</strong> 
                ${esc(ack.message.replace(/^ACK por .*?: /, ''))}
                <div style="font-size:10px; opacity:0.7; margin-top:2px">📅 <strong>Fecha:</strong> ${formatDateOnly(ack.clock)} &nbsp;|&nbsp; <strong>Hora:</strong> ${formatTimeOnly(ack.clock)}</div>
              </div>
            ` : ''}
          </div>
        `;
      }).join('');
    }
  } catch (e) {
    body.innerHTML = '<div class="modal-no-alerts" style="color:var(--sev-disaster)">❌ Error cargando eventos</div>';
  }
}

function openHostModalFromKpi(idx) {
  const host = window._kpiHosts[idx];
  if (!host) return;

  document.getElementById('modal-actions').style.display = 'none';
  const { dotClass, txtClass, label } = hostStatus(host.status);
  const alerts = allProblems.filter(p => p.host === host.name);
  renderHostDetailTabs(host, dotClass, label, alerts, document.getElementById('modal-body'));
  // Modal already open from KPI view, just updated content
}

// =====================
//  EXPORT FUNCTIONS
// =====================
function exportNetworkStatusXls() {
  const hostsToExport = window._filteredHosts || allHosts;
  if (!hostsToExport || !hostsToExport.length) return;
  
  const data = hostsToExport.map(h => {
    const alerts = allProblems.filter(p => p.host === h.name);
    return {
      'Host': h.name,
      'Grupo': h.group || '—',
      'Código de Servicio': h.service_code || '—',
      'IP': h.ip || '—',
      'Estado': hostStatus(h.status).label,
      'Alertas Activas': alerts.length,
      'Última Alerta': alerts.length ? alerts[0].name : '—'
    };
  });

  const ws = XLSX.utils.json_to_sheet(data);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Estado_de_la_Red");
  XLSX.writeFile(wb, `Zabbix_Estado_Red_${new Date().toISOString().split('T')[0]}.xlsx`);
}

function exportNetworkStatusPdf() {
  const hostsToExport = window._filteredHosts || allHosts;
  if (!hostsToExport || !hostsToExport.length) return;
  
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  
  doc.setFontSize(18);
  doc.text(`Estado de la Red`, 14, 22);
  doc.setFontSize(11);
  doc.setTextColor(100);
  doc.text(`Generado el ${new Date().toLocaleString()}`, 14, 30);

  const tableData = hostsToExport.map(h => {
    const alerts = allProblems.filter(p => p.host === h.name);
    return [
      h.name.length > 30 ? h.name.substring(0, 27) + "..." : h.name,
      h.group || '—',
      h.service_code || '—',
      hostStatus(h.status).label,
      alerts.length > 0 ? `${alerts.length} alerta(s)` : 'OK'
    ];
  });

  doc.autoTable({
    startY: 35,
    head: [['Host', 'Grupo', 'Servicio', 'Estado', 'Alertas']],
    body: tableData,
    theme: 'striped',
    headStyles: { fillColor: [92, 107, 192] },
    styles: { fontSize: 8, cellPadding: 2 }
  });

  doc.save(`Zabbix_Estado_Red_${new Date().toISOString().split('T')[0]}.pdf`);
}

function exportKpiToXls() {
  if (!window._currentKpiHosts || !window._currentKpiHosts.length) return;
  const data = window._currentKpiHosts.map(h => {
    const alerts = allProblems.filter(p => p.host === h.name);
    return {
      'Host': h.name,
      'Grupo': h.group,
      'Código de Servicio': h.service_code || '—',
      'IP': h.ip || '—',
      'Estado': hostStatus(h.status).label,
      'Alertas Activas': alerts.length,
      'Última Alerta': alerts.length ? alerts[0].name : '—'
    };
  });

  const ws = XLSX.utils.json_to_sheet(data);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Hosts");
  XLSX.writeFile(wb, `Zabbix_Hosts_${window._currentKpiLabel.replace(/\s+/g, '_')}.xlsx`);
}

function exportKpiToPdf() {
  if (!window._currentKpiHosts || !window._currentKpiHosts.length) return;
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  
  doc.setFontSize(18);
  doc.text(`Lista de Hosts: ${window._currentKpiLabel}`, 14, 22);
  doc.setFontSize(11);
  doc.setTextColor(100);
  doc.text(`Generado el ${new Date().toLocaleString()}`, 14, 30);

  const tableData = window._currentKpiHosts.map(h => {
    const alerts = allProblems.filter(p => p.host === h.name);
    return [
      h.name,
      h.group,
      h.service_code || '—',
      hostStatus(h.status).label,
      alerts.length > 0 ? `${alerts.length} alerta(s)` : 'OK'
    ];
  });

  doc.autoTable({
    startY: 35,
    head: [['Host', 'Grupo', 'Servicio', 'Estado', 'Alertas']],
    body: tableData,
    theme: 'striped',
    headStyles: { fillColor: [92, 107, 192] },
    styles: { fontSize: 9 }
  });

  doc.save(`Zabbix_Hosts_${window._currentKpiLabel.replace(/\s+/g, '_')}.pdf`);
}

function exportEventLogToXls() {
  const eventsToExport = window._currentFilteredEvents || allEvents;
  if (!eventsToExport || !eventsToExport.length) return;
  
  const data = eventsToExport.map(ev => {
    const isProblem = ev.value === '1' || ev.value === 1;
    const typeLabel = isProblem ? 'PROBLEM' : 'RESOLVED';
    const startDate = formatDateOnly(ev.clock) + ' ' + formatTimeOnly(ev.clock);
    const endDate = ev.r_clock ? formatDateOnly(ev.r_clock) + ' ' + formatTimeOnly(ev.r_clock) : '—';
    const ackInfo = (Number(ev.acknowledged) === 1 && ev.acknowledges && ev.acknowledges.length > 0) 
        ? ev.acknowledges[0].message
        : 'Sin ACK';
        
    return {
      'Fecha Inicio': startDate,
      'Fecha Fin': endDate,
      'Host': ev.host,
      'Grupo': ev.group || 'Sin grupo',
      'Alerta': ev.name,
      'Severidad': SEV_LABELS[ev.severity] || ev.severity,
      'Tipo': typeLabel,
      'ACK': ackInfo
    };
  });

  const ws = XLSX.utils.json_to_sheet(data);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Eventos");
  XLSX.writeFile(wb, `Zabbix_Eventos_Recientes.xlsx`);
}

function exportEventLogToPdf() {
  const eventsToExport = window._currentFilteredEvents || allEvents;
  if (!eventsToExport || !eventsToExport.length) return;
  
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('landscape');
  
  doc.setFontSize(18);
  doc.text(`Log de Últimos Eventos`, 14, 22);
  doc.setFontSize(11);
  doc.setTextColor(100);
  doc.text(`Generado el ${new Date().toLocaleString()}`, 14, 30);

  const tableData = eventsToExport.map(ev => {
    const isProblem = ev.value === '1' || ev.value === 1;
    const typeLabel = isProblem ? 'PROBLEM' : 'RESOLVED';
    const startDate = formatDateOnly(ev.clock) + ' ' + formatTimeOnly(ev.clock);
    const ackStatus = Number(ev.acknowledged) === 1 ? 'Sí' : 'No';
    
    return [
      startDate,
      ev.host,
      ev.group || 'Sin grupo',
      ev.name.length > 50 ? ev.name.substring(0, 48) + '...' : ev.name,
      SEV_LABELS[ev.severity] || ev.severity,
      typeLabel,
      ackStatus
    ];
  });

  doc.autoTable({
    startY: 35,
    head: [['Fecha', 'Host', 'Grupo', 'Alerta', 'Sever', 'Tipo', 'ACK']],
    body: tableData,
    theme: 'striped',
    headStyles: { fillColor: [92, 107, 192] },
    styles: { fontSize: 8, cellPadding: 2 }
  });

  doc.save(`Zabbix_Eventos_Recientes.pdf`);
}

// =====================
//  MODAL: Host Detail
// =====================
let currentModalHostId = null;
let currentModalHistoryLoaded = false;

function renderHostDetailTabs(host, dotClass, label, alerts, body) {
  document.getElementById('modal-dot').className = 'modal-host-dot ' + dotClass;
  document.getElementById('modal-host-name').textContent = host.name;
  const meta = [];
  if (host.service_code) meta.push(host.service_code);
  if (host.group) meta.push(host.group);
  if (host.ip && host.ip !== '—') meta.push(host.ip);
  meta.push(label);
  document.getElementById('modal-host-meta').textContent = meta.join(' · ');

  currentModalHostId = host.hostid;
  currentModalHistoryLoaded = false;

  let alertsHtml = '';
  if (!alerts.length) {
    alertsHtml = '<div class="modal-no-alerts">✅ Sin alertas activas para este host</div>';
  } else {
    alertsHtml = alerts.map(p => `
      <div class="modal-alert-item">
        <div class="modal-alert-sev">
          <span class="sev-badge sev-${p.severity}">${esc(SEV_LABELS[p.severity] ?? p.severity)}</span>
        </div>
        <div class="modal-alert-info">
          <div class="modal-alert-name">${esc(p.name)}</div>
          <div class="modal-alert-time" style="display:flex;justify-content:space-between;align-items:flex-start">
            <div>
              <span>Hace ${timeAgo(p.clock)}${p.ack ? ' · <span style="color:var(--sev-ok)">✔ Reconocido</span>' : ''}</span>
              ${p.acknowledges && p.acknowledges.length > 0 ? `
                <div class="ack-list-item">
                  <strong>${esc(p.acknowledges[0].alias || p.acknowledges[0].name)}:</strong> ${esc(p.acknowledges[0].message)}
                  <br><span style="font-size:9px;opacity:0.7"><strong>Fecha:</strong> ${formatDateOnly(p.acknowledges[0].clock)} &nbsp;|&nbsp; <strong>Hora:</strong> ${formatTimeOnly(p.acknowledges[0].clock)}</span>
                </div>
              ` : ''}
            </div>
            ${!p.ack ? `<button class="btn-ack" onclick="openAckModal('${p.eventid}', '${esc(host.name).replace(/'/g, "\\'")}', '${esc(p.name).replace(/'/g, "\\'")}', '${p.severity}')">Dar ACK</button>` : ''}

          </div>
        </div>
      </div>
    `).join('');
  }

  body.innerHTML = `
    <div class="modal-tabs">
      <div class="modal-tab active" id="tab-btn-alerts" onclick="switchModalTab('alerts')">Alertas activas</div>
      <div class="modal-tab" id="tab-btn-history" onclick="switchModalTab('history')">Historial</div>
    </div>
    <div class="modal-tab-content active" id="tab-content-alerts">
      ${alertsHtml}
    </div>
    <div class="modal-tab-content" id="tab-content-history"></div>
  `;
}

function switchModalTab(tab) {
  document.getElementById('tab-btn-alerts')?.classList.toggle('active', tab === 'alerts');
  document.getElementById('tab-btn-history')?.classList.toggle('active', tab === 'history');
  document.getElementById('tab-content-alerts')?.classList.toggle('active', tab === 'alerts');
  document.getElementById('tab-content-history')?.classList.toggle('active', tab === 'history');

  if (tab === 'history' && !currentModalHistoryLoaded && currentModalHostId) {
    loadHostHistory(currentModalHostId);
  }
}

async function loadHostHistory(hostid) {
  const container = document.getElementById('tab-content-history');
  container.innerHTML = '<div style="text-align:center;padding:20px"><div style="display:inline-block;width:24px;height:24px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite"></div></div>';
  try {
    const rawEvents = await fetch(`${API}?action=history&hostid=${hostid}`).then(r => r.json());
    currentModalHistoryLoaded = true;
    if (!rawEvents || !rawEvents.length) {
      container.innerHTML = '<div class="modal-no-alerts">✅ Sin historial reciente para este host</div>';
      return;
    }

    // Pair events
    const problems = [];
    const recoveries = {}; // r_eventid -> recovery event
    
    // First pass: index recoveries and collect all problems
    for (const ev of rawEvents) {
      if (ev.value === '0') {
        recoveries[ev.eventid] = ev;
      } else if (ev.value === '1') {
        problems.push(ev);
      }
    }

    // Render paired problems
    if (!problems.length) {
      container.innerHTML = '<div class="modal-no-alerts">✅ Historial limpio, solo eventos de recuperación sueltos encontrados.</div>';
      return;
    }

    let totalDowntimeSecs = 0;
    let resolvedCount = 0;
    const intervals = [];

    // Store for export
    window.currentHistoryData = {
      hostName: document.getElementById('modal-host-name').textContent,
      events: []
    };

    const listHtml = problems.map(p => {
      const rec = recoveries[p.r_eventid]; // Find matching recovery
      
      let downtimeStr = 'Aún activo';
      if (rec) {
        const start = parseInt(p.clock);
        const end = parseInt(rec.clock);
        downtimeStr = timeAgo(end - start, true); // true for raw duration format
        intervals.push([start, end]);
        resolvedCount++;
      }

      window.currentHistoryData.events.push({
        problem: p.name,
        severity: SEV_LABELS[p.severity] || p.severity,
        startClock: parseInt(p.clock),
        endClock: rec ? parseInt(rec.clock) : null,
        downtimeRaw: rec ? (parseInt(rec.clock) - parseInt(p.clock)) : null,
        downtimeStr: downtimeStr,
        ackInfo: Number(p.acknowledged) === 1 && p.acknowledges && p.acknowledges.length 
          ? `ACK: ${p.acknowledges[0].message}` : ''
      });

      const ackInfo = Number(p.acknowledged) === 1 && p.acknowledges && p.acknowledges.length 
              ? `<div style="color:var(--sev-ok);margin-top:4px">✔ ACK por ${(() => {
                  const msg = p.acknowledges[0].message;
                  const match = msg.match(/^ACK por (.*?):/);
                  return match ? esc(match[1]) : esc(p.acknowledges[0].alias || p.acknowledges[0].name || 'Técnico');
                })()}: "${esc(p.acknowledges[0].message.replace(/^ACK por .*?: /, ''))}"</div>` 
              : '';

      const stateBadgeClass = rec ? 'sev-0' : `sev-${p.severity}`;
      const stateLabel = rec ? 'RESOLVED' : 'PROBLEM';
      
      return `
      <div class="modal-alert-item" style="flex-direction:column;gap:8px">
        <div style="display:flex;align-items:flex-start;gap:12px;width:100%">
          <div class="modal-alert-sev">
            <span class="sev-badge ${stateBadgeClass}">${stateLabel}</span>
          </div>
          <div class="modal-alert-info">
            <div class="modal-alert-name">${esc(p.name)}</div>
            <div class="modal-alert-time">
               ⬇ Caída: <strong>${formatDateOnly(p.clock)} ${formatTimeOnly(p.clock)}</strong>
               ${rec ? ` | ⬆ Recuperado: <strong>${formatDateOnly(rec.clock)} ${formatTimeOnly(rec.clock)}</strong>` : ''}
            </div>
            ${ackInfo}
          </div>
          <div style="flex-shrink:0;text-align:right">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;font-weight:600;margin-bottom:2px">Duración</div>
            <div style="font-size:14px;font-weight:700;color:${rec ? 'var(--text-main)' : 'var(--sev-disaster)'}">${downtimeStr}</div>
          </div>
        </div>
      </div>
    `}).join('');

    // Merge overlapping intervals to calculate true total downtime
    intervals.sort((a, b) => a[0] - b[0]);
    if (intervals.length > 0) {
      let currentStart = intervals[0][0];
      let currentEnd = intervals[0][1];
      for (let i = 1; i < intervals.length; i++) {
        if (intervals[i][0] <= currentEnd) {
          currentEnd = Math.max(currentEnd, intervals[i][1]); // Overlap, extend end
        } else {
          totalDowntimeSecs += (currentEnd - currentStart);
          currentStart = intervals[i][0];
          currentEnd = intervals[i][1];
        }
      }
      totalDowntimeSecs += (currentEnd - currentStart);
    }

    const avgSecs = resolvedCount > 0 ? Math.floor(totalDowntimeSecs / resolvedCount) : 0;
    
    // Calculate date range from rawEvents (it's sorted DESC)
    const newestTs = parseInt(rawEvents[0].clock);
    const oldestTs = parseInt(rawEvents[rawEvents.length - 1].clock);
    const newestDate = formatDate(newestTs);
    const oldestDate = formatDate(oldestTs);
    
    // Calculate difference in days (ceiling to ensure at least 1 day if any difference)
    const diffDays = Math.ceil((newestTs - oldestTs) / 86400);

    const kpiHtml = `
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--border)">
          <div style="display:flex;gap:15px;align-items:center;">
            <div style="font-size:12px;color:var(--text-muted);font-weight:600;"><span style="color:var(--text-main)">DESDE:</span> ${oldestDate}</div>
            <div style="font-size:11px;background:rgba(255,255,255,0.05);padding:2px 8px;border-radius:12px;color:var(--text-dim);border:1px solid rgba(255,255,255,0.1)">${diffDays} día${diffDays !== 1 ? 's' : ''}</div>
            <div style="font-size:12px;color:var(--text-muted);font-weight:600;"><span style="color:var(--text-main)">HASTA:</span> ${newestDate}</div>
          </div>
          <div style="display:flex;gap:6px">
            <button onclick="exportHistoryXls()" style="background:#217346;color:#fff;border:none;border-radius:4px;padding:4px 10px;font-size:11px;cursor:pointer;font-weight:600" title="Exportar a Excel">📥 XLS</button>
            <button onclick="exportHistoryPdf()" style="background:#F40F02;color:#fff;border:none;border-radius:4px;padding:4px 10px;font-size:11px;cursor:pointer;font-weight:600" title="Exportar a PDF">📥 PDF</button>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <div style="flex:1;text-align:center">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;font-weight:600;margin-bottom:4px">Eventos (Últimos 100)</div>
            <div style="font-size:18px;font-weight:700;color:var(--text-main)">${problems.length}</div>
          </div>
          <div style="flex:1;text-align:center;border-left:1px solid var(--border)">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;font-weight:600;margin-bottom:4px">Tiempo Total Caído</div>
            <div style="font-size:18px;font-weight:700;color:var(--sev-disaster)">${timeAgo(totalDowntimeSecs, true)}</div>
          </div>
        </div>
      </div>
    `;

    container.innerHTML = kpiHtml + listHtml;
  } catch(err) {
    console.error(err);
    container.innerHTML = '<div class="modal-no-alerts" style="color:var(--sev-disaster)">❌ Error cargando historial</div>';
  }
}

function formatDateOnly(ts) {
  const d = new Date(ts * 1000);
  return d.toLocaleDateString('es-CL', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatTimeOnly(ts) {
  const d = new Date(ts * 1000);
  return d.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' });
}

function formatDate(ts) {
  const d = new Date(ts * 1000);
  return d.toLocaleString('es-CL', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function prepareExportData() {
  if (!window.currentHistoryData || !window.currentHistoryData.events.length) {
    alert('Sin datos para exportar.');
    return null;
  }
  const headers = ['Alerta', 'Severidad', 'Fecha Caída', 'Fecha Recuperación', 'Duración Lógica', 'Downtime (segundos)', 'Comentarios (ACK)'];
  const rows = [];
  window.currentHistoryData.events.forEach(ev => {
    rows.push([
      ev.problem,
      ev.severity,
      formatDate(ev.startClock),
      ev.endClock ? formatDate(ev.endClock) : 'Aún activo',
      ev.downtimeStr,
      ev.downtimeRaw !== null ? ev.downtimeRaw : '',
      ev.ackInfo
    ]);
  });
  return { headers, rows, safeName: window.currentHistoryData.hostName.replace(/[^a-z0-9]/gi, '_').toLowerCase() };
}

function exportHistoryXls() {
  const data = prepareExportData();
  if (!data) return;
  const wsData = [data.headers, ...data.rows];
  const ws = XLSX.utils.aoa_to_sheet(wsData);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Historial " + window.currentHistoryData.hostName.substring(0, 15));
  XLSX.writeFile(wb, `historial_${data.safeName}.xlsx`);
}

function exportHistoryPdf() {
  const data = prepareExportData();
  if (!data) return;
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('landscape');
  doc.text(`Historial de Eventos Zabbix: ${window.currentHistoryData.hostName}`, 14, 15);
  doc.autoTable({
    head: [data.headers],
    body: data.rows,
    startY: 20,
    styles: { fontSize: 8 },
    headStyles: { fillColor: [65, 84, 241] } // var(--accent) approx
  });
  doc.save(`historial_${data.safeName}.pdf`);
}

// Hub Integration: helper classes are handled via JS adding 'in-frame' class to body
// and CSS rules in the <style> block.

// =====================
//  ACK LOGIC
// =====================
let pendingAckEventId = null;
let pendingAckHost = null;
let pendingAckName = null;
let pendingAckSeverity = null;

function openAckModal(eventid, hostName, alertName, severity) {
  pendingAckEventId = eventid;
  pendingAckHost = hostName;
  pendingAckName = alertName;
  pendingAckSeverity = severity;

  document.getElementById('ack-event-name').textContent = hostName + ' - ' + alertName;
  document.getElementById('ack-message').value = '';
  document.getElementById('ack-modal').classList.add('open');
  setTimeout(() => document.getElementById('ack-message').focus(), 100);
}

async function submitAck() {
  if (!pendingAckEventId) return;
  const msgInput = document.getElementById('ack-message');
  const message = `ACK por ${CURRENT_USER}: ${msgInput.value.trim() || 'Sin comentario'}`;
  
  const btn = document.querySelector('.btn-ack-submit');
  btn.disabled = true;
  btn.textContent = 'Procesando...';

  try {
    const fd = new FormData();
    fd.append('eventid', pendingAckEventId);
    fd.append('message', message);
    fd.append('host_name', pendingAckHost);
    fd.append('alert_name', pendingAckName);
    fd.append('severity', pendingAckSeverity);
    
    const resp = await fetch(`${API}?action=acknowledge`, {
      method: 'POST',
      body: fd
    }).then(r => r.json());

    if (resp.result) {
      document.getElementById('ack-modal').classList.remove('open');
      alert('Evento reconocido con éxito');
      loadAll(); // Refresh everything
    } else {
      alert('Error de Zabbix: ' + JSON.stringify(resp.error || 'Desconocido'));
    }
  } catch (e) {
    console.error('ACK Error:', e);
    alert('Error al enviar el reconocimiento');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Confirmar Reconocimiento';
  }
}

// Close with Escape key
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') document.getElementById('host-modal').classList.remove('open');
});

// =====================
//  THEME & UTILS
// =====================
function toggleSound() {
  soundEnabled = !soundEnabled;
  const btn = document.getElementById('btn-sound');
  if (btn) {
    btn.textContent = soundEnabled ? '🔊' : '🔇';
    btn.title = soundEnabled ? 'Silenciar alertas' : 'Activar sonido de alertas';
  }
}

function toggleTheme() {
    const isLight = document.body.classList.toggle('light');
    const btn = document.getElementById('btn-theme');
    if (btn) btn.textContent = isLight ? '🌙' : '☀️';
    localStorage.setItem('zabbix-theme', isLight ? 'light' : 'dark');
    logDebug(`Tema cambiado manualmente a: ${isLight ? 'light' : 'dark'}`);
}

if (window.self !== window.top) {
    document.body.classList.add('in-frame');
}
// =====================
//  INIT
// =====================
document.addEventListener('DOMContentLoaded', () => {
    // Theme logic
    try {
        const savedTheme = localStorage.getItem('zabbix-theme') || 'dark';
        if (savedTheme === 'light') {
            document.body.classList.add('light');
            const btn = document.getElementById('btn-theme');
            if (btn) btn.textContent = '🌙';
        } else {
            document.body.classList.remove('light');
            const btn = document.getElementById('btn-theme');
            if (btn) btn.textContent = '☀️';
        }
    } catch(e) {}

    setTimeout(() => {
        loadAll();
    }, 100);
});


// ════════════════════════════════════════════════════════════════
// SLA
// ════════════════════════════════════════════════════════════════
let slaData = [];
let slaRawData = [];

async function loadSLA() {
  const days = document.getElementById('sla-days').value;
  const tbody = document.getElementById('sla-tbody');
  tbody.innerHTML = '<tr><td colspan="7" style="padding:32px;text-align:center;color:var(--text-muted)">Calculando SLA…</td></tr>';
  document.getElementById('sla-kpis').innerHTML = '';

  const data = await fetch(`${API}?action=sla&days=${days}`).then(r => r.json()).catch(() => []);
  slaRawData = Array.isArray(data) ? data : [];

  // Populate group filter
  const groups = [...new Set(slaRawData.map(h => h.group))].sort();
  const sel = document.getElementById('sla-filter-group');
  sel.innerHTML = '<option value="all">Todos los grupos</option>' +
    groups.map(g => `<option value="${esc(g)}">${esc(g)}</option>`).join('');

  renderSLA();
}

function renderSLA() {
  const groupFilter = document.getElementById('sla-filter-group').value;
  const threshold   = parseFloat(document.getElementById('sla-threshold').value);
  slaData = groupFilter === 'all' ? slaRawData : slaRawData.filter(h => h.group === groupFilter);

  const tbody = document.getElementById('sla-tbody');
  const kpisEl = document.getElementById('sla-kpis');

  if (!slaData.length) {
    tbody.innerHTML = '<tr><td colspan="7" style="padding:32px;text-align:center;color:var(--text-muted)">Sin datos</td></tr>';
    return;
  }

  // KPIs resumen
  const avgSLA     = slaData.reduce((s, h) => s + h.sla_pct, 0) / slaData.length;
  const critical   = slaData.filter(h => h.sla_pct < threshold).length;
  const totalInc   = slaData.reduce((s, h) => s + h.incidents, 0);
  const worstHost  = slaData[0];
  const kpiStyle   = 'background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:14px 18px;';

  kpisEl.innerHTML = `
    <div style="${kpiStyle}">
      <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">SLA Promedio</div>
      <div style="font-size:26px;font-weight:800;color:${avgSLA >= 99 ? 'var(--sev-ok)' : avgSLA >= 95 ? 'var(--sev-warning)' : 'var(--sev-disaster)'}">${avgSLA.toFixed(2)}%</div>
    </div>
    <div style="${kpiStyle}">
      <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Hosts bajo ${threshold}%</div>
      <div style="font-size:26px;font-weight:800;color:${critical > 0 ? 'var(--sev-disaster)' : 'var(--sev-ok)'}">${critical}</div>
    </div>
    <div style="${kpiStyle}">
      <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Total Incidentes</div>
      <div style="font-size:26px;font-weight:800;color:var(--text-main)">${totalInc}</div>
    </div>
    <div style="${kpiStyle}">
      <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Peor Host</div>
      <div style="font-size:14px;font-weight:700;color:var(--sev-disaster);white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${esc(worstHost.name)}">${esc(worstHost.name)}</div>
      <div style="font-size:11px;color:var(--text-muted)">${worstHost.sla_pct}% SLA</div>
    </div>
  `;

  // Table rows
  tbody.innerHTML = slaData.map(h => {
    const slaColor = h.sla_pct >= 99.9 ? 'var(--sev-ok)' :
                     h.sla_pct >= 99   ? '#8bc34a' :
                     h.sla_pct >= 95   ? 'var(--sev-warning)' :
                     h.sla_pct >= 80   ? 'var(--sev-high)' : 'var(--sev-disaster)';
    const badge    = h.sla_pct < threshold
      ? `<span style="background:rgba(244,67,54,.15);color:var(--sev-disaster);font-size:10px;padding:2px 7px;border-radius:10px;font-weight:700">⚠ Bajo umbral</span>`
      : `<span style="background:rgba(76,175,80,.12);color:var(--sev-ok);font-size:10px;padding:2px 7px;border-radius:10px">✔ OK</span>`;

    const mttrStr = h.mttr_sec >= 3600
      ? `${(h.mttr_sec/3600).toFixed(1)}h`
      : h.mttr_sec >= 60
        ? `${Math.round(h.mttr_sec/60)}m`
        : `${h.mttr_sec}s`;

    const barPct = Math.min(100, h.sla_pct);
    return `<tr style="border-top:1px solid var(--border)">
      <td style="padding:9px 12px">
        <div style="font-weight:600;font-size:13px">${esc(h.name)}</div>
        ${h.service_code ? `<div style="font-size:10px;color:var(--text-muted)">${esc(h.service_code)}</div>` : ''}
      </td>
      <td style="padding:9px 12px;font-size:12px;color:var(--text-muted)">${esc(h.group)}</td>
      <td style="padding:9px 12px;text-align:center">
        <div style="font-size:17px;font-weight:800;color:${slaColor}">${h.sla_pct.toFixed(3)}%</div>
        <div style="height:4px;background:var(--bg-dark);border-radius:2px;margin-top:4px;width:80px;margin-inline:auto">
          <div style="height:100%;width:${barPct}%;background:${slaColor};border-radius:2px"></div>
        </div>
      </td>
      <td style="padding:9px 12px;text-align:center;font-size:12px;color:${h.downtime_h > 0 ? 'var(--sev-high)' : 'var(--text-muted)'}">${h.downtime_h}h</td>
      <td style="padding:9px 12px;text-align:center;font-size:13px;font-weight:600">${h.incidents}</td>
      <td style="padding:9px 12px;text-align:center;font-size:12px;color:var(--text-muted)">${h.incidents > 0 ? mttrStr : '—'}</td>
      <td style="padding:9px 12px;text-align:center">${badge}</td>
    </tr>`;
  }).join('');
}

function exportSLAXls() {
  if (!slaData.length) return;
  const rows = [['Host','Código','Grupo','SLA %','Downtime (h)','Incidentes','MTTR']];
  slaData.forEach(h => {
    const mttrStr = h.mttr_sec >= 3600 ? `${(h.mttr_sec/3600).toFixed(1)}h` : h.mttr_sec >= 60 ? `${Math.round(h.mttr_sec/60)}m` : `${h.mttr_sec}s`;
    rows.push([h.name, h.service_code, h.group, h.sla_pct, h.downtime_h, h.incidents, h.incidents > 0 ? mttrStr : '—']);
  });
  const ws = XLSX.utils.aoa_to_sheet(rows);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'SLA');
  XLSX.writeFile(wb, `SLA_${document.getElementById('sla-days').value}dias_${new Date().toISOString().slice(0,10)}.xlsx`);
}

// ════════════════════════════════════════════════════════════════
// TURNO
// ════════════════════════════════════════════════════════════════
async function loadTurno() {
  const hours = document.getElementById('turno-hours').value;
  const kpisEl  = document.getElementById('turno-kpis');
  const techEl  = document.getElementById('turno-tech');
  const recentEl = document.getElementById('turno-recent');

  kpisEl.innerHTML  = '<div style="color:var(--text-muted);font-size:12px">Cargando…</div>';
  techEl.innerHTML  = '';
  recentEl.innerHTML = '';

  const data = await fetch(`${API}?action=shift_report&hours=${hours}`).then(r => r.json()).catch(() => ({}));
  const s = data.summary || {};
  const kpiStyle = 'background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:14px 18px;';

  kpisEl.innerHTML = `
    <div style="${kpiStyle}">
      <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">ACKs Totales</div>
      <div style="font-size:28px;font-weight:800;color:var(--sev-ok)">${s.total || 0}</div>
    </div>
    <div style="${kpiStyle}">
      <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Hosts Afectados</div>
      <div style="font-size:28px;font-weight:800;color:var(--text-main)">${s.hosts_affected || 0}</div>
    </div>
    <div style="${kpiStyle}">
      <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Técnicos activos</div>
      <div style="font-size:28px;font-weight:800;color:var(--accent)">${s.technicians || 0}</div>
    </div>
  `;

  // Por técnico
  const techs = data.by_tech || [];
  if (!techs.length) {
    techEl.innerHTML = '<div style="font-size:12px;color:var(--text-muted);padding:12px 0">Sin actividad en este período</div>';
  } else {
    const maxAcks = Math.max(...techs.map(t => t.acks));
    techEl.innerHTML = techs.map(t => {
      const pct = Math.round((t.acks / maxAcks) * 100);
      return `<div style="margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;margin-bottom:3px">
          <span style="font-size:13px;font-weight:600">${esc(t.user)}</span>
          <span style="font-size:12px;color:var(--sev-ok);font-weight:700">${t.acks} ACK${t.acks !== 1 ? 's' : ''}</span>
        </div>
        <div style="height:6px;background:var(--bg-dark);border-radius:3px">
          <div style="height:100%;width:${pct}%;background:var(--accent);border-radius:3px;transition:width .4s ease"></div>
        </div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:2px">
          Primer ACK: ${new Date(t.first_ack * 1000).toLocaleTimeString('es-CL')} · Último: ${new Date(t.last_ack * 1000).toLocaleTimeString('es-CL')}
        </div>
      </div>`;
    }).join('');
  }

  // Eventos recientes
  const recent = data.recent || [];
  if (!recent.length) {
    recentEl.innerHTML = '<div style="font-size:12px;color:var(--text-muted)">Sin eventos</div>';
  } else {
    recentEl.innerHTML = recent.map(ev => {
      const sevColor = SEV_COLORS[ev.severity] ?? '#607d8b';
      return `<div style="border-left:3px solid ${sevColor};padding:6px 10px;margin-bottom:6px;background:var(--bg-card);border-radius:0 6px 6px 0">
        <div style="font-size:12px;font-weight:600">${esc(ev.host_name)}</div>
        <div style="font-size:11px;color:var(--text-muted)">${esc(ev.alert_name)}</div>
        <div style="font-size:10px;color:var(--text-dim);margin-top:2px">
          ${ev.fecha} ${ev.hora} · <strong style="color:var(--accent)">${esc(ev.user_name)}</strong>
          ${ev.message ? ` · ${esc(ev.message)}` : ''}
        </div>
      </div>`;
    }).join('');
  }
}

// ════════════════════════════════════════════════════════════════
// HISTORIAL FILTRABLE
// ════════════════════════════════════════════════════════════════
let historialUsers = [];
let historialData  = [];

function initHistorial() {
  // Default: últimos 7 días
  const today   = new Date();
  const weekAgo = new Date(today - 7 * 86400000);
  const fmt = d => d.toISOString().slice(0, 10);
  const fromEl = document.getElementById('hist-from');
  const toEl   = document.getElementById('hist-to');
  if (!fromEl.value) fromEl.value = fmt(weekAgo);
  if (!toEl.value)   toEl.value   = fmt(today);

  // Pre-load users list
  if (!historialUsers.length) loadHistorial();
}

async function loadHistorial() {
  const from     = document.getElementById('hist-from').value;
  const to       = document.getElementById('hist-to').value;
  const user     = document.getElementById('hist-user').value;
  const severity = document.getElementById('hist-severity').value;
  const limit    = document.getElementById('hist-limit').value;

  const tbody = document.getElementById('hist-tbody');
  tbody.innerHTML = '<tr><td colspan="7" style="padding:24px;text-align:center;color:var(--text-muted)">Buscando…</td></tr>';

  const params = new URLSearchParams({ action:'event_log_filter', date_from:from, date_to:to, limit, user, severity });
  const data   = await fetch(`${API}?${params}`).then(r => r.json()).catch(() => ({ events:[], users:[] }));

  // Populate users dropdown once
  if (data.users && data.users.length && !historialUsers.length) {
    historialUsers = data.users;
    const sel = document.getElementById('hist-user');
    sel.innerHTML = '<option value="">Todos los técnicos</option>' +
      data.users.map(u => `<option value="${esc(u)}">${esc(u)}</option>`).join('');
  }

  historialData = data.events || [];
  document.getElementById('hist-count').textContent =
    historialData.length ? `${historialData.length} resultado${historialData.length !== 1 ? 's' : ''}` : '';

  if (!historialData.length) {
    tbody.innerHTML = '<tr><td colspan="7" style="padding:32px;text-align:center;color:var(--text-muted)">Sin resultados para este filtro</td></tr>';
    return;
  }

  tbody.innerHTML = historialData.map(ev => {
    const sevColor = SEV_COLORS[ev.severity] ?? '#607d8b';
    const sevLabel = SEV_LABELS[ev.severity] ?? ev.severity;
    return `<tr style="border-top:1px solid var(--border)">
      <td style="padding:8px 12px;white-space:nowrap;font-size:12px">${esc(ev.fecha)}</td>
      <td style="padding:8px 12px;white-space:nowrap;font-size:12px">${esc(ev.hora)}</td>
      <td style="padding:8px 12px;font-size:12px;font-weight:600">${esc(ev.host_name)}</td>
      <td style="padding:8px 12px;font-size:12px">${esc(ev.alert_name)}</td>
      <td style="padding:8px 12px;text-align:center">
        <span style="font-size:10px;font-weight:700;color:${sevColor};background:${sevColor}22;padding:2px 7px;border-radius:10px">${esc(sevLabel)}</span>
      </td>
      <td style="padding:8px 12px;font-size:12px;color:var(--accent);font-weight:600">${esc(ev.user_name)}</td>
      <td style="padding:8px 12px;font-size:11px;color:var(--text-muted);max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${esc(ev.message)}">${esc(ev.message || '—')}</td>
    </tr>`;
  }).join('');
}

function exportHistorialXls() {
  if (!historialData.length) return;
  const rows = [['Fecha','Hora','Host','Alerta','Severidad','Técnico','Comentario']];
  historialData.forEach(ev => rows.push([ev.fecha, ev.hora, ev.host_name, ev.alert_name, SEV_LABELS[ev.severity] ?? ev.severity, ev.user_name, ev.message]));
  const ws = XLSX.utils.aoa_to_sheet(rows);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Historial');
  XLSX.writeFile(wb, `Historial_${document.getElementById('hist-from').value}_${document.getElementById('hist-to').value}.xlsx`);
}

</script>
</body>
</html>