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
    <title>Zabbix Central Hub — TNA Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base:     #0a0e1a;
            --border:      rgba(255,255,255,0.08);
            --header-bg:   rgba(10,14,26,0.95);
            --accent:      #5c6bc0;
            --text-main:   #e8eaf6;
            --text-muted:  #7986cb;
            --glass:       rgba(255, 255, 255, 0.03);
            --glass-h:     rgba(255, 255, 255, 0.08);
        }

        body.light-theme {
            --bg-base:     #f0f2f8;
            --border:      rgba(0,0,0,0.10);
            --header-bg:   rgba(255,255,255,0.95);
            --accent:      #4c5fc4;
            --text-main:   #1a1f36;
            --text-muted:  #4a5568;
            --glass:       rgba(0, 0, 0, 0.02);
            --glass-h:     rgba(0, 0, 0, 0.05);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        html, body { 
            height: 100%; 
            overflow: hidden; 
            font-family: 'Inter', sans-serif;
            background: var(--bg-base);
            color: var(--text-main);
            transition: background 0.3s;
        }

        /* Portal Header — grid 3 columnas */
        .portal-header {
            height: 56px;
            background: var(--header-bg);
            border-bottom: 1px solid var(--border);
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            padding: 0 16px;
            backdrop-filter: blur(20px);
            z-index: 1000;
            position: relative;
            gap: 12px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: -0.3px;
            min-width: 0;
        }
        .brand span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-logo {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--accent), #26a69a);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .brand-logo:hover { transform: scale(1.1); }

        /* Tab Navigation — columna central */
        .nav-tabs {
            display: flex;
            background: var(--glass);
            padding: 3px;
            border-radius: 10px;
            border: 1px solid var(--border);
            gap: 2px;
        }

        .nav-btn {
            padding: 6px 14px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .nav-btn:hover { background: var(--glass-h); color: var(--text-main); }
        .nav-btn.active {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 12px rgba(92, 107, 192, 0.3);
        }

        /* Controls — columna derecha */
        .controls {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            min-width: 0;
        }

        .theme-toggle {
            background: var(--glass);
            border: 1px solid var(--border);
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            font-size: 14px;
        }
        .theme-toggle:hover { background: var(--glass-h); transform: scale(1.05); }

        .user-info {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 20px;
            color: var(--text-main);
            font-size: 12px;
            font-weight: 500;
            flex-shrink: 0;
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        /* Main Viewport */
        .viewport {
            height: calc(100% - 56px);
            position: relative;
        }

        iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            border: none;
            display: none;
            background: var(--bg-base);
        }

        iframe.active {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .brand span { display: none; }
            .portal-header { padding: 0 8px; gap: 6px; }
            .nav-btn span:first-child { display: none; }
        }
    </style>
</head>
<body>

    <header class="portal-header">
        <div class="brand">
            <div class="brand-logo" onclick="window.top.location.href='https://intranet.icontel.cl/'" title="Volver a Intranet">🏠</div>
            <span>PORTAL ZABBIX</span>
        </div>

        <nav class="nav-tabs">
            <button class="nav-btn active" onclick="switchTab('monitoreo')">
                <span>📊</span> Monitoreo
            </button>
            <button class="nav-btn" onclick="switchTab('bandwidth')">
                <span>🔌</span> Consumo
            </button>
            <button class="nav-btn" onclick="switchTab('anchobanda')">
                <span>📡</span> Ancho de Banda
            </button>
        </nav>

        <div class="controls">
            <span id="last-update" style="font-size:11px;color:var(--text-muted);white-space:nowrap">—</span>
            <select id="global-refresh-interval" onchange="resetGlobalAutoRefresh()" title="Intervalo de actualización" style="background:var(--glass);color:var(--text-main);border:1px solid var(--border);border-radius:6px;padding:3px 6px;font-size:11px;cursor:pointer;height:28px">
                <option value="30">30s</option>
                <option value="60">1m</option>
                <option value="300" selected>5m</option>
                <option value="600">10m</option>
            </select>
            <button class="theme-toggle" id="btn-sound" onclick="toggleGlobalSound()" title="Activar sonido de alertas" style="font-size:16px;">🔇</button>
            <button class="theme-toggle" onclick="toggleTheme()" title="Cambiar tema" style="font-size:16px;">☀️</button>
            <label title="Formato de hora en gráficos" style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--text-muted);cursor:pointer;white-space:nowrap">
              <input type="checkbox" id="chk-24h-global" style="accent-color:var(--accent-color,#3b82f6)" checked onchange="toggleTimeFormat()"> 24h
            </label>
            <button class="theme-toggle" style="width:auto;padding:0 10px;font-size:11px;font-weight:600;display:flex;gap:5px;height:32px" onclick="triggerRefresh()">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
              Actualizar
            </button>
            
            <div style="width:1px;height:24px;background:var(--border);margin:0 4px"></div>

            <div class="user-info" title="Usuario activo">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['cliente'] ?? ($_SESSION['name'] ?? 'T'), 0, 1)); ?></div>
                <span><?php $display_name = $_SESSION['cliente'] ?? ($_SESSION['name'] ?? 'Técnico'); echo htmlspecialchars(explode(' ', trim($display_name))[0]); ?></span>
            </div>
        </div>
    </header>

    <main class="viewport">
        <iframe src="https://intranet.icontel.cl/zabbix/index.php" id="frame-monitoreo" class="active"></iframe>
        <!-- LAZY: src se asigna la primera vez que el usuario abre este tab -->
        <iframe id="frame-bandwidth" data-src="https://intranet.icontel.cl/zabbix/consumo/index.php"></iframe>
        <!-- LAZY: Módulo Ancho de Banda -->
        <iframe id="frame-anchobanda" data-src="https://intranet.icontel.cl/zabbix/bandwidth/index.php"></iframe>
    </main>

    <script>
        function switchTab(tab) {
            // Update buttons
            document.querySelectorAll('.nav-btn').forEach(btn => {
                const onclick = btn.getAttribute('onclick') || '';
                btn.classList.toggle('active', onclick.includes(`'${tab}'`));
            });

            // LAZY LOAD: cargar el iframe de Consumo solo la primera vez que se abre
            const frame = document.getElementById(`frame-${tab}`);
            if (frame && !frame.src && frame.dataset.src) {
                frame.src = frame.dataset.src;
            }

            // Update iframes
            document.querySelectorAll('iframe').forEach(f => {
                f.classList.toggle('active', f.id === `frame-${tab}`);
            });

            // Store preference
            localStorage.setItem('zabbix-portal-tab', tab);
        }

        function initTheme() {
            const savedTheme = localStorage.getItem('zabbix-theme') || 'light';
            const themeBtn = document.querySelector('.theme-toggle[title="Cambiar tema"]');
            if (savedTheme === 'light') {
                document.body.classList.add('light-theme');
                if (themeBtn) themeBtn.textContent = '🌙';
            } else {
                document.body.classList.remove('light-theme');
                if (themeBtn) themeBtn.textContent = '☀️';
            }
        }

        function is24h() {
            return (localStorage.getItem('zabbix-time-format') || '24h') === '24h';
        }

        function toggleTimeFormat() {
            const fmt = document.getElementById('chk-24h-global').checked ? '24h' : '12h';
            localStorage.setItem('zabbix-time-format', fmt);
            // Propagar a todos los iframes via postMessage
            document.querySelectorAll('iframe').forEach(f => {
                try { f.contentWindow.postMessage({ type: 'timeFormat', is24h: fmt === '24h' }, '*'); } catch(e) {}
            });
            // Refrescar last-update en el portal
            updateLastUpdate();
        }

        function initTimeFormat() {
            const saved = localStorage.getItem('zabbix-time-format') || '24h';
            const chk = document.getElementById('chk-24h-global');
            if (chk) chk.checked = (saved === '24h');
        }

        function toggleTheme() {
            document.body.classList.toggle('light-theme');
            const isLight = document.body.classList.contains('light-theme');
            const themeBtn = document.querySelector('.theme-toggle[title="Cambiar tema"]');
            if (themeBtn) themeBtn.textContent = isLight ? '🌙' : '☀️';
            localStorage.setItem('zabbix-theme', isLight ? 'light' : 'dark');
            
            // Re-broadcast theme change to iframes
            document.querySelectorAll('iframe').forEach(f => {
                try {
                    f.contentWindow.localStorage.setItem('zabbix-theme', isLight ? 'light' : 'dark');
                    f.contentWindow.location.reload(); 
                } catch(e) {}
            });
        }

        // Restore tab
        const lastTab = localStorage.getItem('zabbix-portal-tab');
        if (lastTab) switchTab(lastTab);

        initTheme();
        initTimeFormat();
        let globalRefreshTimer;
        let globalCountdown;
        let soundEnabled = false;

        function updateLastUpdate() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('es-CL', { hour12: !is24h() });
            document.getElementById('last-update').textContent = `Actualizado: ${timeStr}`;
        }

        function triggerRefresh() {
            // Trigger refresh in active iframe
            const activeFrame = document.querySelector('iframe.active');
            try {
                if (activeFrame.id === 'frame-monitoreo') {
                    if (typeof activeFrame.contentWindow.loadAll === 'function') {
                        activeFrame.contentWindow.loadAll();
                        updateLastUpdate();
                    }
                } else if (activeFrame.id === 'frame-bandwidth') {
                    if (typeof activeFrame.contentWindow.fetchData === 'function') {
                        activeFrame.contentWindow.fetchData();
                        updateLastUpdate();
                    }
                } else if (activeFrame.id === 'frame-anchobanda') {
                    if (typeof activeFrame.contentWindow.loadGraph === 'function') {
                        activeFrame.contentWindow.loadGraph();
                        updateLastUpdate();
                    }
                }
            } catch(e) {
                console.error("No se pudo refrescar el iframe", e);
            }
            resetGlobalAutoRefresh();
        }

        function resetGlobalAutoRefresh() {
            if (globalRefreshTimer) clearInterval(globalRefreshTimer);
            const intervalSecs = parseInt(document.getElementById('global-refresh-interval').value, 10);
            if (!intervalSecs || intervalSecs <= 0) return;
            globalCountdown = intervalSecs;
            globalRefreshTimer = setInterval(() => {
                globalCountdown--;
                if (globalCountdown <= 0) {
                    triggerRefresh();
                }
            }, 1000);
        }

        function toggleGlobalSound() {
            soundEnabled = !soundEnabled;
            const btn = document.getElementById('btn-sound');
            if (btn) {
                btn.textContent = soundEnabled ? '🔊' : '🔇';
                btn.title = soundEnabled ? 'Silenciar alertas' : 'Activar sonido de alertas';
            }
            // Propagate to monitoreo iframe
            const frameMonitoreo = document.getElementById('frame-monitoreo');
            try {
                if (frameMonitoreo && frameMonitoreo.contentWindow) {
                    // Directly set the variable if toggleSound is not robust enough
                    frameMonitoreo.contentWindow.soundEnabled = soundEnabled;
                }
            } catch(e) {
                console.error("No se pudo cambiar el sonido en el iframe", e);
            }
        }

        // Add cross-origin listener just in case or direct call when iframe loads
        document.getElementById('frame-monitoreo').addEventListener('load', function() {
            try {
                this.contentWindow.soundEnabled = soundEnabled;
                updateLastUpdate(); // Initial load time
            } catch(e) {}
        });

        // Start global refresh immediately
        resetGlobalAutoRefresh();
    </script>
</body>
</html>