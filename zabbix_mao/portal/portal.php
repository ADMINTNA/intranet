<?php require_once '../z_session.php'; ?>
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

        /* Portal Header */
        .portal-header {
            height: 64px;
            background: var(--header-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            backdrop-filter: blur(20px);
            z-index: 1000;
            position: relative;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: -0.5px;
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent), #26a69a);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .brand-logo:hover { transform: scale(1.1); }

        /* Tab Navigation */
        .nav-tabs {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            background: var(--glass);
            padding: 4px;
            border-radius: 12px;
            border: 1px solid var(--border);
            gap: 4px;
        }

        .nav-btn {
            padding: 8px 20px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn:hover { background: var(--glass-h); color: var(--text-main); }
        .nav-btn.active {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 12px rgba(92, 107, 192, 0.3);
        }

        /* Controls */
        .controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .theme-toggle {
            background: var(--glass);
            border: 1px solid var(--border);
            color: var(--text-muted);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .theme-toggle:hover { background: var(--glass-h); transform: scale(1.05); }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 20px;
            color: var(--text-main);
            font-size: 13px;
            font-weight: 500;
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
            height: calc(100% - 64px);
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
            .nav-tabs { position: static; transform: none; margin: 0 auto; }
            .portal-header { padding: 0 12px; }
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
        </nav>

        <div class="controls">
            <span id="last-update" style="font-size:12px;color:var(--text-muted);margin-right:8px">—</span>
            <select id="global-refresh-interval" onchange="resetGlobalAutoRefresh()" title="Intervalo de actualización" style="background:var(--glass);color:var(--text-main);border:1px solid var(--border);border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer">
                <option value="30">30s</option>
                <option value="60">1m</option>
                <option value="300" selected>5m</option>
                <option value="600">10m</option>
            </select>
            <button class="theme-toggle" id="btn-sound" onclick="toggleGlobalSound()" title="Activar sonido de alertas" style="font-size:16px;">🔇</button>
            <button class="theme-toggle" onclick="toggleTheme()" title="Cambiar tema" style="font-size:16px;">☀️</button>
            <button class="theme-toggle" style="width:auto;padding:0 12px;font-size:12px;font-weight:600;display:flex;gap:6px" onclick="triggerRefresh()">
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
        <iframe src="https://intranet.icontel.cl/zabbix/consumo/index.php" id="frame-bandwidth"></iframe>
    </main>

    <script>
        function switchTab(tab) {
            // Update buttons
            document.querySelectorAll('.nav-btn').forEach(btn => {
                const text = btn.textContent.toLowerCase();
                const isActive = (tab === 'monitoreo' && text.includes('monitoreo')) || 
                                 (tab === 'bandwidth' && text.includes('ancho'));
                btn.classList.toggle('active', isActive);
            });

            // Update iframes
            document.querySelectorAll('iframe').forEach(frame => {
                frame.classList.toggle('active', frame.id === `frame-${tab}`);
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
        let globalRefreshTimer;
        let globalCountdown;
        let soundEnabled = false;

        function updateLastUpdate() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('es-CL');
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
